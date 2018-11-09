<?php
/**
 * Created by PhpStorm.
 * User: chenxh
 * Date: 2018/9/26
 * Time: 14:37
 */
namespace app\api\controller\c_v1;

use app\common\model\Project as Pject,
    app\common\model\Purchase as mPure,
    app\common\model\Purchasedoc,
    app\common\model\Projectlog,
    app\common\model\Purchasemodify,
    app\common\library\Plog,
    app\common\library\Notice as LN;

class Purchase extends Common{

    protected $status = [0=>'待确认',1=>'等待修改',2=>'已确认'];

    public function __construct($user_type='')
    {
        parent::__construct($this->user_type);
    }


    public function list_get(){
        $p_id = input('get.p_id',0,'int');
        if (!$p_id || $p_id <= 0) {
            return $this->response(['code' => 201, 'msg' => '参数有误']);
        }
        if (!$this->check_project_onwer($p_id)) {
            return $this->response(['code' => 201, 'msg' => '该项目无法访问']);
        }
        $p_info = (new Pject())->get_info(['id' => $p_id, 'isdel' => 0], 'id,type,name');

        if (!$p_info) {
            return $this->response(['code' => 201, 'msg' => '该项目不存在']);
        }
        $purchase = new mPure();

        $list = $purchase->get_list(['p_id'=>$p_id,'isdel'=>0],'id,name,status',0);
        if(empty($list)){
            return $this->response(['code'=>200,'msg'=>'没有数据','data'=>['list'=>[]]]);
        }

        foreach($list as $k=>$v){
            $list[$k]['status_name'] = $this->status[$v['status']];
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

        if (!$this->check_project_onwer($p_id)) {
            return $this->response(['code' => 201, 'msg' => '该项目无法访问']);
        }
        $p_info = (new Pject())->get_info(['id' => $p_id, 'isdel' => 0], 'id,type,name');

        if (!$p_info) {
            return $this->response(['code' => 201, 'msg' => '该项目不存在']);
        }

        $purchase = new mPure();
        $rep_info = $purchase->get_info(['id'=>$id,'p_id'=>$p_id,'isdel'=>0],'id,name,status,remark,passtime,addtime');
        if(!$rep_info){
            return $this->response(['code' => 201, 'msg' => '该采购信息不存在']);
        }

        $rep_info['status_name'] = $this->status[$rep_info['status']];
        /*$checks = [];
        if($rep_info['checktime1']>0){
            $checks[] = ['title'=>'设计师已确认','isok'=>1,'check_date'=>$rep_info['checktime1'],'content'=>''];
        }
        if($rep_info['checktime2']>0){
            $checks[] = ['title'=>'项目经理已确认','isok'=>1,'check_date'=>$rep_info['checktime2'],'content'=>''];
        }
        if($rep_info['passtime']>0){
            $checks[] = ['title'=>'业主已确认','isok'=>1,'check_date'=>$rep_info['passtime'],'content'=>''];
        }*/

        /*$modifys = (new Purchasemodify())->get_list(['p_id'=>$p_id,'p_rep_id'=>$id],'id,type,content,addtime',0);
        if(!empty($modifys) ){

            foreach($modifys as $mfy){
                $checks[] = ['title'=>'业主提出修改','isok'=>0,'check_date'=>$mfy['addtime'],'content'=>$mfy['content']];
            }
        }*/

        $docs = (new Purchasedoc())->get_order_list(
            ['p_id'=>$p_id,'pu_id'=>$id,'isdel'=>0],
            'id,file_type,file_name,file_path,addtime',
            ['seq'=>'asc'],
            0);

        if(!empty($docs)){
            $qiniu_host = config('qiniu.host');
            foreach($docs as $dk=>$dv){
                $docs[$dk]['addtime'] = date('Y-m-d',strtotime($dv['addtime']));
                //print_r($dv);
                $docs[$dk]['file_url'] = quimg('',$dv['file_path'],$qiniu_host);

                unset($docs[$dk]['file_path']);
            }
        }
        $plogs = (new Projectlog())->get_list(['p_id'=>$p_id,'p_step_id'=>$id,'p_step_type'=>8],'id,oper_user_name,oper_desc,addtime');

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

    public function pass_post(){
        $id = input('post.id',0,'int');
        $p_id = input('post.p_id',0,'int');
        //$sign_img = input('post.sign_img','','trim');//|| !$sign_img
        if (!$id || $id <= 0 || !$p_id || $p_id <= 0 ) {
            return $this->response(['code' => 201, 'msg' => '参数有误']);
        }
        if (!$this->check_project_onwer($p_id)) {
            return $this->response(['code' => 201, 'msg' => '该项目无法访问']);
        }

        $purchase = new mPure();
        $w = ['id'=>$id,'p_id'=>$p_id];
        $pr_info = $purchase->get_info($w,'id,name');
        if(!$pr_info){
            return $this->response(['code' => 201, 'msg' => '该项目无法访问.']);
        }

        $update = ['status'=>2,'passtime'=>$this->datetime,];
        $res = $purchase->update_data($w,$update);

        if($res){

            //add log
            Plog::add_one($p_id,$id,8,
                ['type'=>2,'id'=>$this->user_id,'name'=>'业主'],
                '[通过]采购信息<<'.$pr_info['name'].'>>');
            // 通过时再检查，事务提醒中有没有相关的通知，有则设为'已处理'
            $nwhere = [
                'p_id'=>$p_id,
                'type'=>8,
                'target_id'=>$id
            ];

            LN::set_done($nwhere);
            return $this->response(['code' => 200, 'msg' => '确认成功']);
        }
        return $this->response(['code' => 201, 'msg' => '确认失败']);
    }


    public function modify_post(){
        $id = input('post.id',0,'int');
        $p_id = input('post.p_id',0,'int');
        $content = input('post.content','','trim');
        if (!$id || $id <= 0 || !$p_id || $p_id <= 0 || !$content) {
            return $this->response(['code' => 201, 'msg' => '参数有误']);
        }
        if (!$this->check_project_onwer($p_id)) {
            return $this->response(['code' => 201, 'msg' => '该项目无法访问']);
        }
        $p_info = (new Pject())->get_info(['id'=>$p_id],'id,manager_user_id,desgin_user_id');
        $purchase = new mPure();
        $w = ['id'=>$id,'p_id'=>$p_id];
        $pr_info = $purchase->get_info($w,'id,name');
        if(!$pr_info){
            return $this->response(['code' => 201, 'msg' => '该项目无法访问.']);
        }
        $data = [
            'p_id'=>$p_id,
            'pu_id'=>$id,
            'type'=>1,
            'content'=>$content,
            'addtime'=>$this->datetime
        ];

        $res = (new Purchasemodify())->add_data($data);
        if($res){
            // 添加日志
            Plog::add_one($p_id,$id,8,
                ['type'=>2,'id'=>$this->user_id,'name'=>'业主'],
                '[修改]采购信息<<'.$pr_info['name'].'>>:'.$content);
            // 通知相关人员查看修改信息
            $ndata = [
                'p_id'=>$p_id,
                'type'=>8,
                'target_id'=>$id,
                'user_type'=>1,
                'user_id'=>$p_info['manager_user_id'],// B端人员id
                'title'=>'采购信息被驳回',
                'content'=>'<<'.$pr_info['name'].'>>:'.$content
            ];
            LN::add($ndata);
            return $this->response(['code' => 200, 'msg' => '修改意见已提交成功']);
        }

        return $this->response(['code' => 201, 'msg' => '提交失败']);
    }
}