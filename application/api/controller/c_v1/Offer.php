<?php
/**
 * Created by PhpStorm.
 * User: chenxh
 * Date: 2018/7/5
 * Time: 16:47
 */
namespace app\api\controller\c_v1;

use app\common\model\Project as Pject,
    app\common\model\Projectofferdoc,
    app\common\model\Projectoffermodify,
    app\common\library\Plog,
    app\common\model\Projectlog,
    app\common\library\Notice as LN,
    app\common\model\Projectoffer;

class Offer extends Common{

    public function __construct($user_type='')
    {
        parent::__construct($this->user_type);
    }
    //施工预算
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
        $poffer = new Projectoffer();
        //todo 状态
        //0未确认，1设计确认，2项目经理确认，3业主确认，4业主修改
        //,'status'=>['in','1,2,3,4']
        $list = $poffer->get_list(['p_id'=>$p_id,'isdel'=>0],'id,name,status',0);
        if(empty($list)){
            return $this->response(['code'=>200,'msg'=>'没有数据','data'=>['list'=>[]]]);
        }
        $status = $this->offer_status();

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
    //施工预算内容
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

        $offer = new Projectoffer();
        $offer_info = $offer->get_info(['id'=>$id,'p_id'=>$p_id,'isdel'=>0],'id,name,status,remark,passtime,checktime1,checktime2,addtime');
        if(!$offer_info){
            return $this->response(['code' => 201, 'msg' => '该预算内容不存在']);
        }
        $status = $this->offer_status();
        $offer_info['status_name'] = $status[$offer_info['status']];
        $checks = [];
        if($offer_info['checktime1']>0){
            $checks[] = ['title'=>'设计师已确认','isok'=>1,'check_date'=>$offer_info['checktime1'],'content'=>''];
        }
        if($offer_info['checktime2']>0){
            $checks[] = ['title'=>'项目经理已确认','isok'=>1,'check_date'=>$offer_info['checktime2'],'content'=>''];
        }
        if($offer_info['passtime']>0){
            $checks[] = ['title'=>'业主已确认','isok'=>1,'check_date'=>$offer_info['passtime'],'content'=>''];
        }

        $modifys = (new Projectoffermodify())->get_list(['p_id'=>$p_id,'p_offer_id'=>$id,'isdel'=>0],'id,type,content,addtime',0);
        if(!empty($modifys) ){
            $report_offer_type = $this->report_offer_type();
            foreach($modifys as $mfy){
                $checks[] = ['title'=>$report_offer_type[$mfy['type']].'提出修改','isok'=>0,'check_date'=>$mfy['addtime'],'content'=>$mfy['content']];
            }
        }

        $docs = (new Projectofferdoc())->get_order_list(
            ['p_id'=>$p_id,'p_offer_id'=>$id,'isdel'=>0],
            'id,file_type,file_name,file_path,file_path_thumb,sign_complex_path,sign_complex_path_thumb,addtime',
            ['seq'=>'asc'],
            0);

        if(!empty($docs)){
            $qiniu_host = config('qiniu.host');
            foreach($docs as $dk=>$dv){
                $docs[$dk]['addtime'] = date('Y-m-d',strtotime($dv['addtime']));

                $docs[$dk]['file_url'] = quimg(
                    [$dv['sign_complex_path_thumb'],$dv['sign_complex_path']],
                    [$dv['file_path_thumb'],$dv['file_path']],
                    $qiniu_host);

                unset($docs[$dk]['file_path'],$docs[$dk]['file_path_thumb'],$docs[$dk]['sign_complex_path_thumb'],$docs[$dk]['sign_complex_path']);
            }
        }

        // 输出操作记录 - 20181005
        $plogs = (new Projectlog())->get_list(['p_id'=>$p_id,'p_step_id'=>$id,'p_step_type'=>4],'id,oper_user_name,oper_desc,addtime',0);

        return $this->response([
            'code'=>200,
            'msg'=>'成功',
            'data'=>[
                'project' => $p_info,
                'offer_info' =>$offer_info,
                'docs'=>$docs,
                'check_logs'=>$checks,
                'offer_logs'=>$plogs
            ]
        ]);

    }
    //业主确认施工预算
    public function pass_post(){
        $id = input('post.id',0,'int');
        $p_id = input('post.p_id',0,'int');
        $sign_img = input('post.sign_img','','trim');

        if (!$id || $id <= 0 || !$p_id || $p_id <= 0) {
            return $this->response(['code' => 201, 'msg' => '参数有误']);
        }
        if(!$sign_img){
            return $this->response(['code' => 201, 'msg' => '请先签字']);
        }
        if (!$this->check_project_onwer($p_id)) {
            return $this->response(['code' => 201, 'msg' => '该项目无法访问']);
        }
        //$p_info = (new Pject())->get_info(['id'=>$p_id],'id,manager_user_id,desgin_user_id');
        $poffer = new Projectoffer();
        $w = ['id'=>$id,'p_id'=>$p_id];
        $pr_info = $poffer->get_info($w,'id,name,status');
        if(!$pr_info){
            return $this->response(['code' => 201, 'msg' => '该项目无法访问.']);
        }

        $update = ['status'=>3,'passtime'=>$this->datetime,'sign_img'=>$sign_img];
        $res = $poffer->update_data($w,$update);

        if($res){
            //添加一个定时任务，合成图片
            //\think\Queue::later(2,'app\gerent\job\Compleximg',['type'=>'offer','id'=>$id]);
            \think\Queue::later(2,'app\gerent\job\Createsignimg',['type'=>'offer','id'=>$id,'sign_type'=>1]);
            //添加日志
            //add log
            Plog::add_one($p_id,$id,4,
                ['type'=>2,'id'=>$this->user_id,'name'=>'业主'],
                '[通过]施工预算<<'.$pr_info['name'].'>>');

            //通过时再检查，事务提醒中有没有相关的通知，有则设为'已处理'
            $nwhere = [
                'p_id'=>$p_id,
                'type'=>4,
                'target_id'=>$id
            ];
            
            LN::set_done($nwhere);

            return $this->response(['code' => 200, 'msg' => '确认成功']);
        }
        return $this->response(['code' => 201, 'msg' => '确认失败']);
    }
    //业主修改施工预算
    public function modify_post(){
        $id = input('post.id',0,'int');
        $p_id = input('post.p_id',0,'int');
        $content = input('post.content','','trim');
        if (!$id || $id <= 0 || !$p_id || $p_id <= 0 || !$content) {
            return $this->response(['code' => 201, 'msg' => '参数有误']);
        }
        if (!$this->check_project_onwer($p_id)) {
            return $this->response(['code' => 201, 'msg' => '该项目无法访问']);
        }//
        $p_info = (new Pject())->get_info(['id'=>$p_id],'id,manager_user_id,desgin_user_id');
        $poffer = new Projectoffer();
        $w = ['id'=>$id,'p_id'=>$p_id];
        $pr_info = $poffer->get_info($w,'id,name');
        if(!$pr_info){
            return $this->response(['code' => 201, 'msg' => '该项目无法访问.']);
        }
        $data = [
            'p_id'=>$p_id,
            'p_offer_id'=>$id,
            'type'=>3,
            'content'=>$content,
            'addtime'=>$this->datetime
        ];

        $res = (new Projectoffermodify())->add_data($data);
        if($res){
            $poffer->update_data($w,['status'=>4]);
            // 添加日志
            Plog::add_one($p_id,$id,4,
                ['type'=>2,'id'=>$this->user_id,'name'=>'业主'],
                '[修改]施工预算<<'.$pr_info['name'].'>>:'.$content);
            // 通知相关人员查看修改信息
            $ndata = [
                'p_id'=>$p_id,
                'type'=>4,
                'target_id'=>$pr_info['id'],
                'user_type'=>1,
                'user_id'=>$p_info['manager_user_id'],// B端人员id
                'title'=>'施工预算被驳回',
                'content'=>'施工预算<<'.$pr_info['name'].'>>:'.$content
            ];
            LN::add($ndata);
            return $this->response(['code' => 200, 'msg' => '修改意见已提交成功']);
        }

        return $this->response(['code' => 201, 'msg' => '提交失败']);
    }
    
    
    protected function offer_status(){
        return [0=>'待设计师确认',1=>'待项目经理确认',2=>'待业主确认',3=>'已处理',4=>'已提交修改'];
    }
}