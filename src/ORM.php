<?php

declare(strict_types=1);

namespace mon\thinkORM;

use Throwable;
use mon\env\Config;
use Workerman\Timer;
use think\facade\Db;
use think\DbManager;
use think\Container;
use Psr\Log\LoggerInterface;
use Psr\SimpleCache\CacheInterface;

/**
 * ORM注册使用工具
 * 
 * @author Mon <985558837@qq.com>
 * @version 1.0.0
 */
class ORM
{
    /**
     * 注册ORM
     *
     * @param boolean $longLink 是否长链接
     * @param array $config     数据库配置
     * @param LoggerInterface|null $log 记录日志驱动
     * @param CacheInterface|null $cache    缓存驱动
     * @param integer $timer    长链接轮询时间间隔
     * @return void
     */
    public static function register(bool $longLink = false, array $config = [], ?LoggerInterface $log = null, ?CacheInterface $cache = null, int $timer = 55)
    {
        // 定义配置
        $config = $config ?: Config::instance()->get('database', []);
        Db::setConfig($config);
        // 定义日志驱动
        if (!is_null($log)) {
            Db::setLog($log);
        }
        // 定义缓存驱动
        if (!is_null($cache)) {
            Db::setCache($cache);
        }
        // 处理长链接
        if ($longLink) {
            self::heart($timer);
        }
    }

    /**
     * 保持数据库链接心跳
     *
     * @param integer $timer
     * @return void
     */
    public static function heart(int $timer = 55)
    {
        if (class_exists(Container::class, false)) {
            $manager_instance = Container::getInstance()->make(DbManager::class);
        } else {
            $reflect = new \ReflectionClass(Db::class);
            $property = $reflect->getProperty('instance');
            $property->setAccessible(true);
            $manager_instance = $property->getValue();
        }
        Timer::add($timer, function () use ($manager_instance) {
            $instances = [];
            if (method_exists($manager_instance, 'getInstance')) {
                $instances = $manager_instance->getInstance();
            } else {
                $reflect = new \ReflectionClass($manager_instance);
                $property = $reflect->getProperty('instance');
                $property->setAccessible(true);
                $instances = $property->getValue($manager_instance);
            }
            /**  @var \think\db\connector\Mysql $connection */
            foreach ($instances as $connection) {
                if (in_array($connection->getConfig('type'), ['mysql', 'oracle', 'sqlsrv'])) {
                    try {
                        $connection->query('SELECT 1');
                    } catch (Throwable $e) {
                    }
                }
            }
            // 清空内存中的日志，防止错误的配置导致爆内存
            Db::getDbLog(true);
        });
    }
}
