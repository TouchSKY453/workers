<?php
namespace workers\service\crontab;


use think\Log;

class Order {

    public function confirm() {
        $a = Log::write('测试日志信息，这是警告级别，并且实时写入','notice');
        echo $a.PHP_EOL;
    }
}