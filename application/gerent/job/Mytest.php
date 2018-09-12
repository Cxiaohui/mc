<?php
/**
 * Created by PhpStorm.
 * User: chenxh
 * Date: 2018/7/15
 * Time: 17:01
 */
namespace app\gerent\job;
use think\queue\Job,
    app\common\library\Mylog as mlog;

class Mytest{
    protected $log_file = 'mytest';
    public function fire(Job $job,$data=[]){
        try{
            //mlog::write($job->attempts(),$this->log_file);
            $this->do_job();
            $job->delete();
            $delay = 10+rand(0,50);
            // 也可以重新发布这个任务
            $job->release($delay); //$delay为延迟时间
            mlog::write('success:',$this->log_file);
        }catch(\Exception $e){
            mlog::write('Error:'.$e->getFile().'-'.$e->getLine().PHP_EOL.'-'.$e->getMessage(),$this->log_file);
        }
    }

    public function failed($data=[]){
        mlog::write('Failed:',$this->log_file);
    }

    public function do_job(){
        mlog::write('do_job:'.date("Y-m-d H:i:s"),$this->log_file);
    }
}