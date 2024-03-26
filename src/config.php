<?php

/*
|--------------------------------------------------------------------------
| 数据库配置文件
|--------------------------------------------------------------------------
| 定义数据库配置信息
|
*/
return [
    // 默认数据连接标识
    'default' => 'mysql',
    // 数据库连接信息
    'connections' => [
        'mysql' => [
            // 数据库类型
            'type'              => env('DB_TYPE', 'mysql'),
            // 服务器地址
            'hostname'          => env('DB_HOST', '127.0.0.1'),
            // 数据库名
            'database'          => env('DB_NAME', ''),
            // 数据库用户名
            'username'          => env('DB_USER', ''),
            // 数据库密码
            'password'          => env('DB_PASSWORD', ''),
            // 数据库连接端口
            'hostport'          => env('DB_PORT', 3306),
            // 数据库连接参数
            'params'            => [
                // 连接超时3秒
                \PDO::ATTR_TIMEOUT => 3,
            ],
            // 数据库编码默认采用utf8
            'charset'           => 'utf8mb4',
            // 数据库表前缀
            'prefix'            => '',
            // 数据库部署方式:0 集中式(单一服务器),1 分布式(主从服务器)
            'deploy'            => 0,
            // 数据库读写是否分离 主从式有效
            'rw_separate'       => false,
            // 读写分离后 主服务器数量
            'master_num'        => 1,
            // 指定从服务器序号
            'slave_no'          => '',
            // 检查字段是否存在
            'fields_strict'     => true,
            // 自动写入时间戳字段
            'auto_timestamp'    => false,
            // 不自动格式化时间戳
            'datetime_format'   => false,
            // 断线重连
            'break_reconnect'   => true,
            // 是否开启字段缓存
            'fields_cache'      => false,
            // 是否开启SQL监听，默认关闭，如需要开启，则需要调用 Db::setLog 注入日志记录对象，否则常驻进程长期运行会爆内存
            'trigger_sql'       => false,
            // 自定义查询类，支持Dao对象调用
            'query'             => \mon\thinkORM\extend\Query::class,
        ],
    ],
];
