<?php
/**
 * Created by PhpStorm.
 * User: chenxh
 * Date: 2018/6/20
 * Time: 10:24
 */
namespace app\api\controller\c_v1;

use app\common\library\YunIM,
    app\common\model\IM as mIM;

class Im extends Common{


    public function __construct($user_type='')
    {
        parent::__construct($this->user_type);
    }


    public function token_get(){
        $yim = new YunIM();

        $res = $yim->createCUserID($this->user_id);
        if($res['err']==1){
            return $this->response(['code'=>201,'msg'=>$res['msg']]);
        }
        $token = $res['token'];
        $im_user_id = $yim->build_im_userid($this->user_id,$this->user_type);

        return $this->response([
            'code'=>200,
            'msg'=>'成功',
            'data'=>[
                'im_token'=>$token,
                'im_user_id'=>$im_user_id
            ]
        ]);
    }

    public function list_get(){
        $m = new mIM();
        $w = [
            'user_type'=>$this->user_type_int,
            'user_id'=>$this->user_id,
            'isdel'=>0
        ];

        $list = $m->get_list($w,'id,im_type,target_type,target_tag as target_id,target_icon,name,uptime',0);
        if(!empty($list)){
            foreach($list as $k=>$lt){
                $list[$k]['target_icon'] = c_img($lt['target_icon'],6);
            }
        }

        return $this->response([
            'code'=>200,
            'msg'=>'成功',
            'data'=>[
                'list'=>$list
            ]
        ]);
    }

    public function create_list_post(){
        $target_id = input('post.target_id',0,'int');
        $target_type = input('post.target_type','','trim|strtolower');

        if($target_id == $this->user_id){
            return $this->response(['code'=>201,'msg'=>'无法与自己创建聊天']);
        }

        $yim = new YunIM();
        //$res = [];

        if($target_type=='b'){
            $res = $yim->createBUserID($target_id);
        }else{
            $res = $yim->createCUserID($target_id);
        }
        if($res['err']==1){
            return $this->response(['code'=>201,'msg'=>$res['msg']]);
        }
        $m = new mIM();
        //me
        $save_data = [
            'user_type' => $this->user_type_int,
            'user_id' => $this->user_id,
            'im_type' => 1,
            'target_type'=>$target_type=='b'?1:2,
            'target_tag' => $target_id,
            'name' => $res['name']
        ];
        $m->save_data($save_data);
        //target
        $save_data = [
            'user_type' => $target_type=='b'?1:2,
            'user_id' => $target_id,
            'im_type' => 1,
            'target_type'=>$this->user_type_int,
            'target_tag' => $this->user_id,
            'name' => $res['name']
        ];
        $m->save_data($save_data);

        return $this->response(['code'=>200,'msg'=>'创建成功']);
    }

}