<?php

declare(strict_types=1);

namespace mon\thinkOrm;

use Closure;
use think\facade\Db;
use RuntimeException;
use mon\util\Validate;
use mon\util\Container;
use mon\thinkOrm\extend\Query;

/**
 * Db操作Dao对象
 * 
 * @method Query master() 从主服务器读取数据
 * @method Query readMaster(bool $all = false) 后续从主服务器读取数据
 * @method Query table(string $table) 指定数据表（含前缀）
 * @method Query name(string $name) 指定数据表（不含前缀）
 * @method Query where(mixed $field, string $op = null, mixed $condition = null) 查询条件
 * @method Query whereOr($field, $op = null, $condition = null) 指定OR查询条件
 * @method Query whereExp(string $field, string $condition, array $bind = []) 字段表达式查询
 * @method Query when(mixed $condition, mixed $query, mixed $otherwise = null) 条件查询
 * @method Query join(mixed $join, mixed $condition = null, string $type = 'INNER') JOIN查询
 * @method Query view(mixed $join, mixed $field = null, mixed $on = null, string $type = 'INNER') 视图查询
 * @method Query field(mixed $field, boolean $except = false) 指定查询字段
 * @method Query fieldRaw(string $field, array $bind = []) 指定查询字段
 * @method Query union(mixed $union, boolean $all = false) UNION查询
 * @method Query limit(mixed $offset, integer $length = null) 查询LIMIT
 * @method Query order(mixed $field, string $order = null) 查询ORDER
 * @method Query group($group) 指定group查询
 * @method Query having(string $having) 指定having查询
 * @method Query distinct(bool $distinct = true) 指定distinct查询
 * @method Query force(string $force) 指定强制索引
 * @method Query replace(bool $replace = true) 设置是否REPLACE
 * @method Query partition($partition) 设置当前查询所在的分区
 * @method Query duplicate($duplicate) 设置DUPLICATE
 * @method Query cache(mixed $key = null , integer $expire = null) 设置查询缓存
 * @method Query procedure(bool $procedure = true) 是否存储过程调用
 * @method Query inc(string $field, float $step = 1) 字段值增长
 * @method Query dec(string $field, float $step = 1) 字段值减少
 * @method Query lock($lock = false) 指定查询lock
 * @method Query alias($alias) 指定数据表别名
 * @method Query json(array $json = [], bool $assoc = false) 设置JSON字段信息
 * 
 * @method string getLastSql() 获取最近一次查询的sql语句
 * @method string buildSql(bool $sub = true) 创建子查询SQL
 * @method mixed value(string $field) 获取某个字段的值
 * @method array column(string $field, string $key = '') 获取某个列的值
 * @method mixed find(mixed $data = null) 查询单个记录
 * @method mixed select(mixed $data = null) 查询多个记录
 * @method integer insert(array $data, boolean $replace = false, boolean $getLastInsID = false, string $sequence = null) 插入一条记录
 * @method integer insertGetId(array $data, boolean $replace = false, string $sequence = null) 插入一条记录并返回自增ID
 * @method integer insertAll(array $dataSet) 插入多条记录
 * @method integer update(array $data) 更新记录
 * @method integer delete(mixed $data = null) 删除记录
 * @method boolean chunk(integer $count, callable $callback, string $column = null) 分块获取数据
 * @method \Generator cursor(mixed $data = null) 使用游标查找记录
 * @method mixed query(string $sql, array $bind = [], boolean $master = false, bool $pdo = false) SQL查询
 * @method integer execute(string $sql, array $bind = [], boolean $fetch = false, boolean $getLastInsID = false, string $sequence = null) SQL执行
 * @method mixed transaction(callable $callback) 执行数据库事务
 * @method void startTrans() 启动事务
 * @method void commit() 用于非自动提交状态下面的查询提交
 * @method void rollback() 事务回滚
 * @method boolean batchQuery(array $sqlArray) 批处理执行SQL语句
 * @method mixed getLastInsID(string $sequence = null) 获取最近插入的ID
 * 
 * @author Mon <985558837@qq.com>
 * @version 1.0.0
 */
class Dao
{
    /**
     * 操作表名
     *
     * @var string
     */
    protected $table = '';

    /**
     * 链接配置节点
     *
     * @var string
     */
    protected $connection = '';

    /**
     * 更新操作自动完成字段配置
     *
     * @var array
     */
    protected $update = [];

    /**
     * 新增操作自动完成字段配置
     *
     * @var array
     */
    protected $insert = [];

    /**
     * 查询后字段完成的数据
     *
     * @var array
     */
    protected $append = [];

    /**
     * 只读字段，不允许修改
     *
     * @var array
     */
    protected $readonly = [];

    /**
     * 验证器驱动，默认为内置的Validate验证器
     *
     * @var string
     */
    protected $validate = Validate::class;

    /**
     * 只允许操作的字段
     *
     * @var array
     */
    protected $allow = [];

    /**
     * 错误信息
     *
     * @var mixed
     */
    protected $error = null;

    /**
     * 获取Dao操作表名
     *
     * @return string
     */
    public function getTable(): string
    {
        return $this->table;
    }

    /**
     * 获取Dao操作链接配置节点
     *
     * @return string
     */
    public function getConnection(): string
    {
        return $this->connection;
    }

    /**
     * 获取更新操作自动完成字段
     *
     * @return array
     */
    public function getUpdate(): array
    {
        return $this->update;
    }

    /**
     * 获取新增操作自动完成字段
     * 
     * @return array
     */
    public function getInsert(): array
    {
        return $this->insert;
    }

    /**
     * 获取查询后自动完成字段
     *
     * @return array
     */
    public function getAppend(): array
    {
        return $this->append;
    }

    /**
     * 获取只读字段
     *
     * @return array
     */
    public function getReadonly(): array
    {
        return $this->readonly;
    }

    /**
     * 获取错误信息
     *
     * @return mixed 错误信息
     */
    public function getError()
    {
        $error = $this->error;
        $this->error = null;
        return $error;
    }

    /**
     * 获取实例化验证器
     *
     * @return Validate
     */
    public function validate(): Validate
    {
        return Container::instance()->get($this->validate);
    }

    /**
     * 获取DB实例
     *
     * @throws RuntimeException
     * @return Query
     */
    public function db(): Query
    {
        if (!$this->getTable()) {
            throw new RuntimeException('Dao对象数据表名[table]属性必须');
        }
        if ($this->getConnection()) {
            $db = Db::connect($this->getConnection())->table($this->getTable());
        } else {
            $db = Db::table($this->getTable());
        }
        // 绑定Dao对象
        if (!method_exists($db, 'dao')) {
            throw new RuntimeException('Query查询对象必须实现dao方法，需要配置自定义查询类[' . Query::class . ']实现');
        }

        return $db->dao($this);
    }

    /**
     * 设置查询场景, 相当于查询前置条件
     *
     * @param  string|Closure $name 场景名称或者闭包函数
     * @param  mixed $args          可变传参
     * @throws RuntimeException
     * @return Query                查询对象实例
     */
    public function scope($name, ...$args): Query
    {
        // 固定第一个参数为Db实例
        array_unshift($args, $this->db());
        if ($name instanceof Closure) {
            return call_user_func_array($name, (array) $args);
        }
        $method = 'scope' . ucfirst($name);
        if (method_exists($this, $method)) {
            return call_user_func_array([$this, $method], (array) $args);
        }
        throw new RuntimeException("查询场景不存[{$method}]");
    }

    /**
     * 允许新增或修改的字段
     *
     * @param array $field  只允许操作的字段列表
     * @return Dao
     */
    public function allowField(array $field): Dao
    {
        $this->allow = $field;
        return $this;
    }

    /**
     * 获取一条数据
     *
     * @param  array $where where条件
     * @param  Query $query 查询对象实例
     * @return array
     */
    public function get(array $where = [], Query $query = null): array
    {
        // 获取查询对象实例
        if (!$query) {
            $query = $this->db();
        }
        $data = $query->where($where)->findOrEmpty();
        // 查询为空，直接返回空数组
        if (!$data) {
            return [];
        }

        return $this->getDaoData($data);
    }

    /**
     * 获取多条数据
     *
     * @param  array  $where    where条件
     * @param  Query  $query 查询对象实例
     * @return array
     */
    public function all(array $where = [], Query $query = null): array
    {
        // 获取查询对象实例
        if (!$query) {
            $query = $this->db();
        }
        $data = $query->where($where)->select();
        if ($data->isEmpty()) {
            return [];
        }

        $result = [];
        foreach ($data as $k => $value) {
            $result[$k] = $this->getDaoData($value);
        }

        return $result;
    }

    /**
     * 保存数据
     *
     * @param  array    $data           操作数据
     * @param boolean   $forceInsert    是否强制insert
     * @param  boolean  $getLastInsID   insert操作下是否返回自增ID
     * @param  Query    $query          查询对象实例
     * @return integer  影响行数或自增iD
     */
    public function save(array $data, bool $forceInsert = false, bool $getLastInsID = false, Query $query = null)
    {
        // 过滤允许操作的字段
        if (!empty($this->allow)) {
            $allowData = [];
            foreach ($this->allow as $field) {
                if (isset($data[$field])) {
                    $allowData[$field] = $data[$field];
                }
            }
            // 重置操作数据
            $data = $allowData;
            $this->allow = [];
        }
        // 获取查询对象实例
        if (!$query) {
            $query = $this->db();
        }

        if ($forceInsert) {
            return $this->insertData($data, $getLastInsID, $query);
        }

        return $query->isUpdate() ? $this->updateData($data, $query) : $this->insertData($data, $getLastInsID, $query);
    }

    /**
     * 批量写入数据
     *
     * @param array     $data   操作数据
     * @param integer   $limit  每次写入数据限制
     * @param mixed     $query  查询对象实例
     * @return integer 影响行数
     */
    public function saveAll(array $data, int $limit = 0, Query $query = null)
    {
        foreach ($data as $k => $item) {
            // 过滤允许操作的字段
            if (!empty($this->allow)) {
                $allowData = [];
                foreach ($this->allow as $field) {
                    if (isset($item[$field])) {
                        $allowData[$field] = $item[$field];
                    }
                }
                // 重置操作数据
                $item = $allowData;
            }
            $data[$k] = $this->autoCompleteData($this->insert, $item);
        }
        $this->allow = [];
        // 获取查询对象实例
        if (!$query) {
            $query = $this->db();
        }

        return $query->insertAll($data, $limit);
    }

    /**
     * 更新数据
     *
     * @param  array $data  操作数据
     * @param  Query $query 查询对象实例
     * @return integer      影响行数
     */
    protected function updateData(array $data, Query $query = null)
    {
        // 过滤只读字段
        if (!empty($this->readonly)) {
            foreach ($this->readonly as $field) {
                if (isset($data[$field])) {
                    unset($data[$field]);
                }
            }
        }
        // 自动完成
        $updateData = $this->autoCompleteData($this->update, $data);
        // 获取查询对象实例
        if (!$query) {
            $query = $this->db();
        }

        return $query->data($updateData)->update();
    }

    /**
     * 新增数据
     *
     * @param  array   $data            操作数据
     * @param  boolean $getLastInsID    是否返回自增ID
     * @param  Query   $query           查询对象实例
     * @return integer|string           影响行数或自增主机ID
     */
    protected function insertData(array $data, bool $getLastInsID = false, Query $query = null)
    {
        // 自动完成
        $insertData = $this->autoCompleteData($this->insert, $data);
        // 获取查询对象实例
        if (!$query) {
            $query = $this->db();
        }

        return $query->insert($insertData, $getLastInsID);
    }

    /**
     * 数据自动完成
     *
     * @param  array  $auto 自动补全的字段
     * @param  array  $data 数据数据源
     * @return array
     */
    protected function autoCompleteData(array $auto = [], array $data = []): array
    {
        $result = $data;
        // 处理补全数据
        foreach ($auto as $field => $value) {
            if (is_integer($field)) {
                $field = $value;
                $value = null;
            }
            // 处理数据字段
            if (isset($data[$field])) {
                $value = $data[$field];
            }
            $result[$field] = $this->setAttr($field, $value, $data);
        }

        return $result;
    }

    /**
     * 设置器，设置修改操作数据
     *
     * @param string $name  属性名
     * @param mixed  $value 属性值
     * @param array  $data  元数据
     * @return mixed
     */
    protected function setAttr(string $name, $value = null, array $data = [])
    {
        // 检测设置器是否存在
        $method = 'set' . $this->parseAttrName($name) . 'Attr';
        if (method_exists($this, $method)) {
            $value = $this->{$method}($value, $data);
        }
        return $value;
    }

    /**
     * 获取器, 修改获取数据
     *
     * @param  string $name  属性名
     * @param  mixed  $value 属性值
     * @param  array  $data  元数据
     * @return mixed
     */
    protected function getAttr(string $name, $value = null, array $data = [])
    {
        // 检测设置器是否存在
        $method = 'get' . $this->parseAttrName($name) . 'Attr';
        if (method_exists($this, $method)) {
            $value = $this->{$method}($value, $data);
        }

        return $value;
    }

    /**
     * 整理获取Dao数据
     *
     * @param array $data   数据集
     * @return array
     */
    protected function getDaoData(array $data): array
    {
        $result = [];
        foreach ($data as $key => $value) {
            $result[$key] = $this->getAttr($key, $value, $data);
        }
        if ($this->getAppend()) {
            foreach ($this->getAppend() as $field => $val) {
                if (is_integer($field)) {
                    $field = $val;
                    $val = null;
                }
                $result[$field] = $this->getAttr($field, $val, $data);
            }
        }

        return $result;
    }

    /**
     * 检测命名, 转换下划线命名规则为驼峰法命名规则
     *
     * @param  string $name 字段名称
     * @return string
     */
    protected function parseAttrName(string $name): string
    {
        $name = preg_replace_callback('/_([a-zA-Z])/', function ($match) {
            return strtoupper($match[1]);
        }, $name);
        return ucfirst($name);
    }

    /**
     * 动态调用
     * 
     * @param  mixed $method 回调方法
     * @param  mixed $args   参数
     * @return mixed
     */
    public function __call($method, $args)
    {
        return call_user_func_array([$this->db(), $method], (array) $args);
    }

    /**
     * 静态调用
     *
     * @param  mixed $method 回调方法
     * @param  mixed $args   参数
     * @return mixed
     */
    public static function __callStatic($method, $args)
    {
        return call_user_func_array([(new static())->db(), $method], (array) $args);
    }
}
