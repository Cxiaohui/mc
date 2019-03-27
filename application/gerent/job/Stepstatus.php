<?php
/**
 * Created by PhpStorm.
 * User: chenxh
 * Date: 2018/7/22
 * Time: 12:45
 */
namespace app\gerent\job;

use think\queue\Job,
    app\gerent\model\Projectstep,
    app\common\library\Mylog as mlog;

class Stepstatus{
    protected $log_file = 'step_status';

    public function fire(Job $job,$data=[])
    {
        try {
            //mlog::write($job->attempts(),$this->log_file);
            $this->do_job();
            $job->delete();
            //$delay = 24*3600;//一天执行一次
            // 也可以重新发布这个任务
            //$job->release($delay); //$delay为延迟时间
            mlog::write('success:', $this->log_file);
        } catch (\Exception $e) {
            $job->delete();
            mlog::write('Error:' . $e->getFile() . '-' . $e->getLine() . PHP_EOL . '-' . $e->getMessage(), $this->log_file);
        }
    }
    public function failed($data=[]){
        mlog::write('Failed:',$this->log_file);
    }

    public function do_job(){
        $ps = new Projectstep();
//        $where = ['isdel'=>0,'realtime'=>''];
        $where = ['status'=>0,'isdel'=>0,'plan_time1'=>['elt',date('Y-m-d')]];
        $list = $ps->get_list($where,'id,plan_time1,status',0);
        $today = strtotime(date('Y-m-d'));
        if(!empty($list)){
            foreach($list as $lt){

                //$times = explode('|', $lt['plan_time']);
                if(strtotime($lt['plan_time1'])<=$today){
                    //todo 运行一段时间 后，取消 realtime 的赋值 20180929
                    $update = ['realtime'=>$lt['plan_time1'],'realtime1'=>$lt['plan_time1']];
                    if($lt['status']==0){
                        $update['status'] = 1;
                    }
                    $ps->update_data(['id'=>$lt['id']],$update);
                }else{
                    continue;
                }

            }
        }
    }
}