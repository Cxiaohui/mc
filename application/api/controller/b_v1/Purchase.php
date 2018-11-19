<?php
/**
 * Created by PhpStorm.
 * User: chenxh
 * Date: 2018/9/26
 * Time: 14:54
 */
namespace app\api\controller\b_v1;

use app\common\model\Project as Pject,
    app\common\model\Purchase as mPure,
    app\common\model\Purchasedoc,
    app\common\model\Projectlog;

class Purchase extends Common
{

    public function __construct($user_type = '')
    {
        parent::__construct($this->user_type);
    }

    public function list_get(){
        $p_id = input('get.p_id',0,'int');
        if (!$p_id || $p_id <= 0) {
            return $this->response(['code' => 201, 'msg' => '参数有误']);
        }
        $this->_check_project_power($p_id);

        $p_info = (new Pject())->get_info(['id' => $p_id, 'isdel' => 0], 'id,type,name');

        if (!$p_info) {
            return $this->response(['code' => 201, 'msg' => '该项目不存在']);
        }
        $purchase = new mPure();

        $list = $purchase->get_list(['p_id'=>$p_id,'isdel'=>0],'id,name,status',0);
        if(empty($list)){
            return $this->response(['code'=>200,'msg'=>'没有数据','data'=>['list'=>[]]]);
        }
        $status = [0=>'待确认',1=>'等待修改',2=>'已确认'];
        foreach($list as $k=>$v){
            $list[$k]['status_name'] = $status[$v['status']];
        }
        return $this->response([
            'code'=>200,
            'msg'=>'成功',
            'data'=>[
                'project' => $p_info,
                'list'=>$list
            ]
        ]);
    }

    public function info_get(){
        $id = input('get.id',0,'int');
        $p_id = input('get.p_id',0,'int');
        if (!$id || $id <= 0 || !$p_id || $p_id <= 0) {
            return $this->response(['code' => 201, 'msg' => '参数有误']);
        }

        $this->_check_project_power($p_id);

        $p_info = (new Pject())->get_info(['id' => $p_id, 'isdel' => 0], 'id,type,name');

        if (!$p_info) {
            return $this->response(['code' => 201, 'msg' => '该项目不存在']);
        }

        $purchase = new mPure();
        $rep_info = $purchase->get_info(['id'=>$id,'p_id'=>$p_id,'isdel'=>0],'id,name,status,remark,passtime,addtime');
        if(!$rep_info){
            return $this->response(['code' => 201, 'msg' => '该采购信息不存在']);
        }
        $status = [0=>'待确认',1=>'已驳回',2=>'已确认'];
        $rep_info['status_name'] = $status[$rep_info['status']];

        $docs = (new Purchasedoc())->get_order_list(
            ['p_id'=>$p_id,'pu_id'=>$id,'isdel'=>0],
            'id,file_type,file_name,file_path,file_path_thumb,addtime',
            ['seq'=>'asc'],
            0);

        if(!empty($docs)){
            $qiniu_host = config('qiniu.host');
            foreach($docs as $dk=>$dv){
                $docs[$dk]['addtime'] = date('Y-m-d',strtotime($dv['addtime']));

                $docs[$dk]['file_url'] = quimg($dv['file_path_thumb'],$dv['file_path'],$qiniu_host);

                unset($docs[$dk]['file_path'],$docs[$dk]['file_path_thumb']);
            }
        }
        $plogs = (new Projectlog())->get_list(['p_id'=>$p_id,'p_step_id'=>$id,'p_step_type'=>8],'id,oper_user_name,oper_desc,addtime',0);

        return $this->response([
            'code'=>200,
            'msg'=>'成功',
            'data'=>[
                'project' => $p_info,
                'purchase_info'=>$rep_info,
                'docs'=>$docs,
                'purchase_log'=>$plogs
            ]
        ]);

    }
}