<?php
/**
 * Created by PhpStorm.
 * User: chenxh
 * Date: 2018/7/29
 * Time: 15:53
 */
namespace app\api\controller\c_v1;

use app\common\model\Project as Pject,
    app\common\model\Projectpay;

class Payrecord extends Common{

    /**
     * 项目付款信息
     * @return mixed|\think\Response
     */
    public function pay_list_get(){
        $p_id = input('get.p_id',0,'int');
        if(!$p_id || $p_id<=0){
            return $this->response(['code'=>201,'msg'=>'参数有误']);
        }

        /*if(!$this->check_project_onwer($p_id)){
            return $this->response(['code'=>201,'msg'=>'该项目无法访问']);
        }*/

        $p_info = (new Pject())->get_info(['id'=>$p_id,'isdel'=>0],'id,type,name');

        if(!$p_info){
            return $this->response(['code'=>201,'msg'=>'该项目不存在']);
        }

        $pay = new Projectpay();

        $w = ['p_id'=>$p_id,'p_type'=>1];
        $fields = 'id,name,payable,payable_time,paied,paied_time,remark';
        $type1 = $pay->get_list($w,$fields);
        $w['p_type'] = 2;
        $type2 = $pay->get_list($w,$fields);
        //$count = count($type1) + count($type2);
        $data = [];
        $data['project'] = $p_info;
        if(!empty($type1)){
            $data['design_pay'] = [
                'name'=>'设计款项',
                'pay_list'=>$type1
            ];
        }else{
            $data['design_pay'] = '';
        }
        if(!empty($type2)){
            $data['construction_pay'] = [
                'name'=>'施工款项',
                'pay_list'=>$type2
            ];
        }else{
            $data['construction_pay'] = '';
        }

        return $this->response([
            'code'=>200,
            'msg'=>'成功',
            'data'=>$data
        ]);
    }

}