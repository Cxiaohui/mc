<?php
/**
 * Created by PhpStorm.
 * User: chenxh
 * Date: 2018/7/13
 * Time: 15:01
 */
namespace app\common\library;
use app\common\model\Notices,
    app\gerent\model\Push;

class Notice {

    static public function add($data){
        $data['addtime'] = date('Y-m-d H:i:s');
        $nid = (new Notices())->add_data($data,true);
        if(!$nid){
            return false;
        }
        //同时还需要发推送

        /**
         * `type` '1设计验收2付款，3预约，4施工预算,5验收方案，6施工验收 ',
        `target_id`
        `user_type` tinyint(4) NOT NULL DEFAULT '1' COMMENT '1-B端，2-C端',
        `user_id` int(10) unsigned NOT NULL DEFAULT '0',
        `status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '0待处理，1已处理',
        `title` varchar(64) NOT NULL DEFAULT '',
         */
        if($data['status']==0){
            $push_data = [
                'to_user_type'=>$data['user_type']==1?'b':'c',
                'to_user_id'=>$data['user_id'],
                'message'=>$data['title'],
                'extras'=>[
                    'url'=>'mochuan://com.aysd.mochuan?type=8&p_id='.$data['p_id'].'&id='.$nid,
                ]
            ];

            \think\Queue::later(1,'app\gerent\job\Pushqueue',$push_data);
        }


    }

    static public function set_done($where){
        $n = new Notices();
        $where['status'] = 0;
        if($n->get_count($where)){
            $n->update_data($where,['status'=>1,'donetime'=>date('Y-m-d H:i:s')]);
        }
        return true;
    }

    static public function addNoticeFromPush($data){
        //type:1设计验收2付款，3预约，4施工预算,5验收方案，6施工验收
        $pushinfo = (new Push())->get_info(['id'=>$data['not_id']]);
        $jpush_user = explode('_',$data['jpush_user_id']);
        $notice_data = [
            'p_id'=>$pushinfo['p_id'],
            'type'=>$pushinfo['type'],
            'target_id'=>$pushinfo['type_id'],
            'user_type'=>$jpush_user[0]=='b'?1:2,
            'user_id'=>$jpush_user[1],
            'status'=>0,
            'title'=>$pushinfo['title'],
            'content'=>$pushinfo['message'],
            'addtime'=>date('Y-m-d H:i:s')
        ];
        $nid = (new Notices())->add_data($notice_data,true);
        if(!$nid){
            return ['err'=>1,'msg'=>'Notice add Failed:'.json_encode($data)];
        }
        $data['notice_id'] = $nid;
        //将新增的消息 id 拼接
        $data['metas'] = [
            'url'=>'mochuan://com.aysd.mochuan?'.$data['metas'].$nid
        ];
        return ['err'=>0,'msg'=>'ok','data'=>$data];
    }
}