<?php
/**
 * Created by PhpStorm.
 * User: chenxh
 * Date: 2018/6/20
 * Time: 19:42
 */
namespace app\api\controller\b_v1;
use app\common\model\Buser,
    app\api\library\Apitoken;
class User extends Common{

    public function __construct($user_type='')
    {
        parent::__construct($this->user_type);
    }

    public function center_get(){
        //mch5/mochuan/AcceptanceList.html?type=ysbz_list
        $info = $this->get_user_info();
        return $this -> response(['code' => 200, 'msg' => '成功','data'=>[
            'info'=>$info,
            'ysbz_url'=>$this->h5_base_url().'AcceptanceList.html?type=ysbz_list',
            'sgbz_url'=>$this->h5_base_url().'AcceptanceList.html?type=sgbz_list',
            'zxbz_url'=>$this->h5_base_url().'DecorateSecurity.html',
        ]]);
    }

    public function refresh_api_token_get(){

        $api_token = Apitoken::create_save_api_token($this->user_type,$this->user_id);

        return $this -> response(['code' => 200, 'msg' => '刷新成功','data'=>[
            'api_token'=>$api_token
        ]]);
    }

    public function info_get(){
        $info = $this->get_user_info();
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
            if(in_array($k,$allow_update_fileds)){
                if($k=='gender'){
                    $update['sex'] = $v;
                }else{
                    $update[$k] = $v;
                }

            }
        }
        if(empty($update)){
            return $this -> response(['code' => 201, 'msg' => '无更新']);
        }
        //$update['uptime'] = $this->datetime;

        (new Buser())->update_data(['id'=>$this->user_id],$update);

        //todo 更新头像后再更新IM的头像

        return $this -> response(['code' => 200, 'msg' => '更新成功']);
    }


    //

    protected function get_user_info(){
        $info = (new Buser())->get_info(['id'=>$this->user_id],'id,name,name as uname,en_name,sex as gender,mobile,head_pic,depart_id,remark');
        $info['head_pic'] = c_img($info['head_pic'],3,600);
        // 20180923 部门信息
        $company = [];
        if($info['depart_id']>0){
            $company = (new \app\gerent\model\Company())->get_company_depart('d.id='.$info['depart_id'].' limit 1');
        }

        $info['company_name'] = $company?$company[0]['company_name']:'无';
        $info['depart_name'] = $company?$company[0]['depart_name']:'无';
        return $info;
    }
}