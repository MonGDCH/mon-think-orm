<?php

use mon\thinkORM\Dao;
use mon\thinkORM\Db;

require_once __DIR__ . '/../vendor/autoload.php';


$config = require_once __DIR__ . '/database.php';

require_once __DIR__ . '/LogDao.php';

Db::setConfig($config);



$log = new LogDao;

// $save = $log->allowField(['content', 'level'])->save(['content' => 123456789, 'level' => 'C', 'ddd' => 123]);

// $save = $log->all();

// $log->startTrans();

$data = $log->order('id')->get();

// $save = $log->where('id', '19')->save(['content' => 123456780, 'level' => 'C']);

// $log->rollback();

dd($data);
// dd($save);

// 批量字段值自增
$increment = $log->batchIncrement([
    ['id' => 1, 'deadline' => 1],
    ['id' => 2, 'deadline' => 2],
]);

dd($increment);

// 批量字段值更新
$increment = $log->batchUpdate([
    ['id' => 1, 'deadline' => 9],
    ['id' => 2, 'deadline' => 50],
]);

dd($increment);
