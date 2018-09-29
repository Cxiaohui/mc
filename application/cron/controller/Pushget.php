<?php
/**
 * Created by PhpStorm.
 * User: chenxh
 * Date: 2018/9/11
 * Time: 10:07
 */
namespace app\cron\controller;
use app\gerent\model\Pushruntime,
    app\common\library\Notice,
    extend\Mylog as mlog;

class Pushget extends Common{

    private $log_file = 'cron_pushget';

    public function getRunList(){
        $time = date('Y-m-d H').':00:00';
        //echo $time;
        //$time = '2018-09-17 23:00:00';
        $list = (new Pushruntime())->get_list(
            ['runtime'=>$time,'donetime'=>['eq','0000-00-00 00:00:00'],'isdel'=>0],
            'id,not_id,pn_id,jpush_user_id,message,metas',
            0);

        //print_r($list);exit;

        if(empty($list)){
            mlog::write($time.':empty list',$this->log_file);
            exit();
        }

        foreach($list as $lt){
            // 读取消息内空，并将消息内容，写到 notices 表中
            $data = Notice::addNoticeFromPush($lt);
            if($data['err']==1){
                mlog::write($data['msg'],$this->log_file);
                continue;
            }
            if(!isset($data['data']['metas']['url'])){
                $data['data']['metas'] = ['url'=>$data['data']['metas']];
            }
            \think\Queue::later(1,'app\gerent\job\Pushqueue',$data['data']);
        }
        mlog::write($time.':data list:'.count($list),$this->log_file);
        exit();
    }




}