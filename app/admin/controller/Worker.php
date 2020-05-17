<?php
namespace app\admin\controller;

use think\worker\Server;
use Workerman\Lib\Timer;
use workers\util\Crontab;

class Worker extends Server {
    //TODO 多进程待优化
    protected $processes = 1;
    protected $interval  = 1;
    protected $socket    = 'http://0.0.0.0:2346';
    protected $crontab   = [];
    /**
     * 服务启动启动
     */
    public function onWorkerStart() {
        $crontab = [
            (object)[
                'crontab_id' => 1,
                'crontab_name' => '订单自动确认',
                'frequency' => '*/5 * * * * *',
                'performer' => 'Order',
                'function'  => 'confirm',
                'status'    => 1,
                'error'     => '',
            ]
        ];
        foreach($crontab as $key => $value) {
            $class    = 'workers\\service\\crontab\\'.$value->performer;
            $function = $value->function;
            $instance = new $class();
            $this->crontab[] = (object)[
                'name'      => $value->crontab_name,
                'instance'  => $instance,
                'function'  => $value->function,
                'frequency' => $value->frequency,
            ];
        }
        //系统定时任务
        if(!empty($this->crontab)) {
            Timer::add($this->interval, array($this, 'crontab'));
        }
    }
    /**
     * 定时任务方法
     */
    public function crontab() {
        $time = time();
        foreach($this->crontab as $key => $value) {
            if(Crontab::check($value->frequency, $time)) {
                $instance = $value->instance;
                $function = $value->function;
                try {
                    $instance->$function($value, $time);
                    $this->logger($time, 'crontab', "{$value->function}");
                }
                catch (Exception $e) {
                    $message = $e->getMessage();
                    $this->logger($time, 'crontab', "{$value->function}", $message);
                }
            }
        }
    }
    /**
     * 日志记录
     */
    protected function logger($time, $service, $executor, $exception = '') {
        $time = date('Y-m-d H:i:s', $time);
        $content   = "[{$time}] {$service} {$executor}\n";
        $exception = !empty($exception) ? "{$exception}\n" : '';

        file_put_contents(RUNTIME_PATH."/daemon/{$service}.{$executor}", $content, FILE_APPEND);
    }
}
?>