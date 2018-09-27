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

    /**
     * @var Notice
     */
    static protected $not=null;

    static public function add($data){
        if(!isset($data['status'])){
            $data['status'] = 0;
        }
        $data['addtime'] = date('Y-m-d H:i:s');

        //检查是否有未处理的相同项目
        $nid = self::check_same_and_update($data);
        if(!$nid){

            $nid = self::get_notobj()->add_data($data,true);
            if(!$nid){
                return false;
            }
        }
        //同时还需要发推送
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
        //$n = new Notices();
        $where['status'] = 0;
        if(self::get_notobj()->get_count($where)){
            self::get_notobj()->update_data($where,['status'=>1,'donetime'=>date('Y-m-d H:i:s')]);
        }
        return true;
    }


    static public function addNoticeFromPush($data){
        if(!isset($data['not_id']) || !$data['not_id']){
            return ['err'=>0,'msg'=>'ok','data'=>$data];
        }
        //notice表：type:1设计验收2付款，3预约，4施工预算,5验收方案，6施工验收 7采购，8效果图，9cad图，10主材
        //push表 type:0自定义1设计验收2付款，3预约，4施工预算,5验收方案，6施工验收,7文章,8采购
        //push:type=>notice:type
        $push_type_maps = [
            0=>0,
            1=>1,
            2=>2,
            3=>3,
            4=>4,
            5=>5,
            6=>6,
            7=>0,//文章
            8=>7//采购
        ];


        $pushinfo = (new Push())->get_info(['id'=>$data['not_id']]);
        $jpush_user = explode('_',$data['jpush_user_id']);
        $notice_data = [
            'p_id'=>$pushinfo['p_id'],
            'type'=>$push_type_maps[$pushinfo['type']],
            'target_id'=>$pushinfo['type_id'],
            'user_type'=>$jpush_user[0]=='b'?1:2,
            'user_id'=>$jpush_user[1],
            'status'=>0,
            'title'=>$pushinfo['title'],
            'content'=>$pushinfo['message'],
            'addtime'=>date('Y-m-d H:i:s')
        ];
        //检查是否有未处理的相同项目
        $nid = self::check_same_and_update($notice_data);

        if(!$nid){
            $nid = self::get_notobj()->add_data($notice_data,true);
        }

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

    static protected function check_same_and_update($data){
        $w = [
            'p_id'=>$data['p_id'],
            'type'=>$data['type'],
            'target_id'=>$data['target_id'],
            'user_type'=>$data['user_type'],
            'user_id'=>$data['user_id'],
            'status'=>0
        ];
        $nid = 0;
        //$now_date = date('Y-m-d H:i:s');

        $ninfo = self::get_notobj()->get_info($w,'id');
        if($ninfo) {
            $update = [
                'title' => $data['title'],
                'content' => $data['content'],
                'addtime' => $data['addtime']
            ];
            self::get_notobj()->update_data(['id' => $ninfo['id']], $update);
            $nid = $ninfo['id'];
        }
        return $nid;
    }

    static protected function get_notobj(){
        if(is_null(self::$not)){
            self::$not = new Notices();
        }
        return self::$not;
    }
}