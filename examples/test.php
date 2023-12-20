<?php

use mon\thinkOrm\Dao;
use think\facade\Db;

require_once __DIR__ . '/../vendor/autoload.php';


$config = require_once __DIR__ . '/../src/config.php';

require_once __DIR__ . '/LogDao.php';

Db::setConfig($config);


$log = new LogDao;

// $save = $log->where('id', 20)->data(['aa' => 33, 'level' => 'D+'])->allowField(['content', 'level'])->save(['content' => 123456789, 'level' => 'C', 'create_time' => 456]);

$save = $log->all();

dd($save);
