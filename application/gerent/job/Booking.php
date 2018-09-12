<?php
/**
 * Created by PhpStorm.
 * User: chenxh
 * Date: 2018/7/22
 * Time: 14:40
 */
namespace app\gerent\job;
use think\queue\Job,
    app\common\model\Booking as mBk,
    app\common\model\Notices,
    app\common\library\Mylog as mlog;

class Booking{

    protected $log_file = 'booking_status';

    public function fire(Job $job,$data=[])
    {
        try {
            $this->do_job();
            $job->delete();
            $delay = 24*3600;//一天执行一次
            // 也可以重新发布这个任务
            $job->release($delay); //$delay为延迟时间
            mlog::write('success:', $this->log_file);

        } catch (\Exception $e) {
            mlog::write('Error:' . $e->getFile() . '-' . $e->getLine() . PHP_EOL . '-' . $e->getMessage(), $this->log_file);
        }
    }

    public function failed($data=[]){
        mlog::write('Failed:',$this->log_file);
    }

    public function do_job(){
        $today = date('Y-m-d');
        $where = "DATE(bk.booking_time)={$today}";
        $list = (new mBk())->get_booking_notices($where);
        if(empty($list)){
            return;
        }
        foreach($list as $lt){
            $data = [
                'to_user_type'=>get_user_type_str($lt['user_type']),
                'to_user_id'=>$lt['user_id'],
                'message'=>$lt['booking_time'].' '.$lt['p_name'].' 预约快到时了！',
                'extras'=>[
                    'url'=>"mochuan://com.aysd.mochuan?type=5&id={$lt['id']}"
                ]
            ];

            \think\Queue::later(2,'app\gerent\job\Pushqueue',$data);
        }
        //
    }
}