<?php

declare(strict_types=1);


use mon\thinkOrm\Dao;
use mon\util\Instance;

/**
 * 管理员模型
 *
 * Class Admin
 * @copyright 2021-03-24 mon-console
 * @version 1.0.0
 */
class LogDao extends Dao
{
    use Instance;

    /**
     * 操作表
     *
     * @var string
     */
    protected $table = 'log';

    /**
     * 新增自动写入字段
     *
     * @var array
     */
    protected $insert = ['create_time', 'update_time'];

    /**
     * 更新自动写入字段
     *
     * @var array
     */
    protected $update = ['update_time'];

    /**
     * 查询后字段完成的数据
     *
     * @var array
     */
    protected $append = ['count'];

    /**
     * 只读字段，不允许修改
     *
     * @var array
     */
    protected $readonly = ['level'];

    /**
     * 格式化获取创建时间
     *
     * @param mixed $v
     * @return void
     */
    protected function getCreateTimeAttr($v)
    {
        return date('Y-m-d H:i:s', $v);
    }

    protected function getCountAttr($v, $data)
    {
        return count($data);
    }

    /**
     * 自动完成create_time字段
     *
     * @return integer
     */
    protected function setCreateTimeAttr(): int
    {
        return time();
    }

    /**
     * 自动完成update_time字段
     *
     * @return integer
     */
    protected function setUpdateTimeAttr(): int
    {
        return time();
    }
}
