<?php
/**
 * Created by PhpStorm.
 * User: chenxh
 * Date: 2018/6/20
 * Time: 19:42
 */
namespace app\api\controller\c_v1;
use app\common\model\Cuser,

    app\common\library\YunIM,
    app\common\library\Cuserlib,
    app\api\library\Apitoken;
class User extends Common{

    public function __construct($user_type='')
    {
        parent::__construct($this->user_type);
    }

    public function center_get(){
        $info = (new Cuserlib())->get_user_info($this->user_id);

        return $this -> response(['code' => 200, 'msg' => '成功','data'=>[
            'info'=>$info,
            //'ysbz_url'=>$this->h5_base_url().'AcceptanceList.html?type=ysbz_list',
            'ysbz_url'=>$this->h5_base_url().'DetailsPage.html?id=19',
            //'sgbz_url'=>$this->h5_base_url().'AcceptanceList.html?type=sgbz_list',
            'sgbz_url'=>$this->h5_base_url().'DetailsPage.html?id=36',
            'zxbz_url'=>$this->h5_base_url().'DecorateSecurity.html',
            'kfzx_url'=>$this->h5_base_url().'ConsumerLine.html',
        ]]);
    }

    public function refresh_api_token_get(){

        $api_token = Apitoken::create_save_api_token($this->user_type,$this->user_id);

        return $this -> response(['code' => 200, 'msg' => '刷新成功','data'=>[
            'api_token'=>$api_token
        ]]);
    }

    public function info_get(){
        $info = (new Cuserlib())->get_user_info($this->user_id);

        return $this -> response(['code' => 200, 'msg' => '成功','data'=>[
            'info'=>$info
        ]]);
    }

    public function update_post(){
        $allow_update_fileds = [
            'gender','head_pic','jpush_tag','device_token'
        ];

        $puts = $this->req->post();
        //print_r($puts);exit;
        if(empty($puts)){
            return $this -> response(['code' => 201, 'msg' => '无更新']);
        }
        $update = [];
        foreach($puts as $k=>$v){
            if($v && in_array($k,$allow_update_fileds)){

                $update[$k] = $v;
            }
        }
        if(empty($update)){
            return $this -> response(['code' => 201, 'msg' => '无更新']);
        }
        $update['uptime'] = $this->datetime;
        $cuser = new Cuser();
        $cuser->update_data(['id'=>$this->user_id],$update);

        // 更新头像后再更新IM的头像
        if(isset($update['head_pic']) && $update['head_pic'] || (isset($update['gender']) && $update['gender'])){
            $res = (new YunIM())->updateCUserinfo($this->user_id);
            /*$user = $cuser->get_info(['id'=>$this->user_id],'id,uname,im_token');
            $im_update = [
                'icon'=>config('qiniu.host').$update['head_pic']
            ];
            $yim = new YunIM();
            $accid = $yim->build_im_userid($this->user_id,$this->user_type);
            $res = $yim->imobj()->updateUserId($accid,$user['uname'],'{}',$im_update,$user['im_token']);*/

            \extend\Mylog::write([
                'user_id'=>$this->user_id,
                'res'=>$res
            ],'c_user_iminfo');
        }

        return $this -> response(['code' => 200, 'msg' => '更新成功']);
    }



}