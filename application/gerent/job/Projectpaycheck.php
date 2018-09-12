<?php
/**
 * Created by PhpStorm.
 * User: chenxh
 * Date: 2018/7/22
 * Time: 13:15
 */
namespace app\gerent\job;

use think\queue\Job,
    app\gerent\model\Projectpay,
    app\common\library\Mylog as mlog;

class Projectpaycheck{

    protected $log_file = 'pay_status';

    public function fire(Job $job,$data=[])
    {
        try {
            //mlog::write($job->attempts(),$this->log_file);
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
        //期付款节点日前7天，每天提醒1次。逾期后，每天提醒1次
        $pay = new Projectpay();
        $today = date("Y-m-d");
        $limit_date = date('Y-m-d',strtotime("+7 days"));
        $where = "paied_time=0 and isdel=0 and (payable_time>=$limit_date or payable_time<$today)";
        //$list = $pay->get_list($where,'p_id,name,payable,payable_time',0);
        $list = $pay->get_nopay_list();
        if(empty($list)){
            return;
        }
        foreach($list as $lt){
            //添加消息

            //添加推送
            $data = [
                'to_user_type'=>get_user_type_str(2),
                'to_user_id'=>$lt['owner_user_id'],
                'message'=>'您有未付款项，请及时付款！',
                'extras'=>[
                    'url'=>"https://mokchuen.iytime.com/mch5/mochuan/PaymentSchedule.html?p_id={$lt['p_id']}"
                    //'url'=>"mochuan://com.aysd.mochuan?type=4&p_id={$lt['p_id']}"
                ]
            ];
            \think\Queue::later(2,'app\gerent\job\Pushqueue',$data);
        }
    }

}