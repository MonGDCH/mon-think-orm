<?php

use mon\thinkOrm\Dao;
use think\facade\Db;

require_once __DIR__ . '/../vendor/autoload.php';


$config = require_once __DIR__ . '/database.php';

require_once __DIR__ . '/LogDao.php';

Db::setConfig($config);



$log = new LogDao;

$save = $log->allowField(['content', 'level'])->save(['content' => 123456789, 'level' => 'C', 'ddd' => 123]);

// $save = $log->all();

// $log->startTrans();

$data = $log->where('id', 11)->get();

$save = $log->where('id', '19')->save(['content' => 123456780, 'level' => 'C']);

// $log->rollback();

dd($data);
dd($save);
