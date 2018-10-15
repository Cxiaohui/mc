<?php
/**
 * Created by PhpStorm.
 * User: chenxh
 * Date: 2018/7/5
 * Time: 16:47
 */
namespace app\api\controller\b_v1;

use app\common\model\Project as Pject,
    app\common\model\Projectreportdoc,
    app\common\model\Projectreportmodify,
    app\common\library\Plog,
    app\common\model\Projectlog,
    app\common\library\Notice as LN,
    app\common\model\Projectreport;

class Report extends Common{

    public function __construct($user_type='')
    {
        parent::__construct($this->user_type);
    }
    protected $status = [0=>'待设计师确认',1=>'待项目经理确认',2=>'待业主确认',3=>'已处理',4=>'业主修改'];

    //验收报告
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
        $preport = new Projectreport();
        //,'status'=>['in','0,1,2,3,4']
        $list = $preport->get_list(['p_id'=>$p_id,'isdel'=>0],'id,name,status');
        if(empty($list)){
            return $this->response(['code'=>200,'msg'=>'没有数据','data'=>['list'=>[]]]);
        }
        //0未确认，1设计确认，2经理确认，3业主确认，4业主修改
        //$status = [0=>'待确认',1=>'待确认',2=>'待确认',3=>'已处理',4=>'等待修改'];
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
    //验收报告内容
    public function info_get(){
        $id = input('get.id',0,'int');
        $p_id = input('get.p_id',0,'int');
        if (!$id || $id <= 0 || !$p_id || $p_id <= 0) {
            return $this->response(['code' => 201, 'msg' => '参数有误']);
        }

        $this->_check_project_power($p_id);
        $pject = new Pject();
        $p_info = $pject->get_info(['id' => $p_id, 'isdel' => 0], 'id,type,name');

        if (!$p_info) {
            return $this->response(['code' => 201, 'msg' => '该项目不存在']);
        }

        $report = new Projectreport();
        $rep_info = $report->get_info(['id'=>$id,'p_id'=>$p_id,'isdel'=>0],'id,name,status,remark,passtime,checktime1,checktime2,addtime');
        if(!$rep_info){
            return $this->response(['code' => 201, 'msg' => '该验收报告不存在']);
        }
        $checks = [];
        $rep_info['status_name'] = $this->status[$rep_info['status']];
        if($rep_info['checktime1']>0){
            $checks[] = ['title'=>'设计师已确认','isok'=>1,'check_date'=>$rep_info['checktime1'],'content'=>''];
        }
        if($rep_info['checktime2']>0){
            $checks[] = ['title'=>'项目经理已确认','isok'=>1,'check_date'=>$rep_info['checktime2'],'content'=>''];
        }
        if($rep_info['passtime']>0){
            $checks[] = ['title'=>'业主已确认','isok'=>1,'check_date'=>$rep_info['passtime'],'content'=>''];
        }

        $modifys = (new Projectreportmodify())->get_list(['p_id'=>$p_id,'p_rep_id'=>$id,'isdel'=>0],'id,type,content,addtime',0);
        if(!empty($modifys) ){
            $report_offer_type = $this->report_offer_type();
            foreach($modifys as $mfy){
                $checks[] = ['title'=>$report_offer_type[$mfy['type']].'提出修改','isok'=>0,'check_date'=>$mfy['addtime'],'content'=>$mfy['content']];
            }
        }

        $docs = (new Projectreportdoc())->get_list(['p_id'=>$p_id,'p_rep_id'=>$id,'isdel'=>0],'id,file_type,file_name,file_path,sign_complex_path,addtime');
        if(!empty($docs)){
            $qiniu_host = config('qiniu.host');
            foreach($docs as $dk=>$dv){
                $docs[$dk]['addtime'] = date('Y-m-d',strtotime($dv['addtime']));

                $docs[$dk]['file_url'] = quimg($dv['sign_complex_path'],$dv['file_path'],$qiniu_host);
                unset($docs[$dk]['file_path'],$docs[$dk]['sign_complex_path']);
            }
        }


        // 输出操作记录 - 20181005
        $plogs = (new Projectlog())->get_list(['p_id'=>$p_id,'p_step_id'=>$id,'p_step_type'=>3],'id,oper_user_name,oper_desc,addtime');

        return $this->response([
            'code'=>200,
            'msg'=>'成功',
            'data'=>[
                'project' => $p_info,
                'report_info'=>$rep_info,
                'is_jingli' => $pject->is_jingli($p_id,$this->user_id),
                'docs'=>$docs,
                'check_logs'=>$checks,
                'report_logs'=>$plogs
            ]
        ]);

    }
    //确认报告
    // 项目经理签字确认 - 20181006
    public function pass_post(){
        $id = input('post.id',0,'int');
        $p_id = input('post.p_id',0,'int');
        $sign_img = input('post.sign_img','','trim');
        if (!$id || $id <= 0 || !$p_id || $p_id <= 0 ) {//|| !$sign_img
            return $this->response(['code' => 201, 'msg' => '参数有误']);
        }
        $this->_check_project_power($p_id);

        $pject = new Pject();
        $pr = new Projectreport();
        $update = [];
        $prw = ['id'=>$id,'p_id'=>$p_id];
        $pr_info = $pr->get_info($prw,'id,status,name');
        if(!$pr_info){
            return $this->response(['code' => 201, 'msg' => '该项目无法访问']);
        }
        $p_info = $pject->get_info(['id'=>$p_id],'id,manager_user_id,owner_user_id');
        //是设计师
        $is_sejishi = $pject->is_sejishi($p_id,$this->user_id);
        if($is_sejishi && $pr_info['status']==0){
            $update = ['status'=>1,'checktime1'=>$this->datetime];
        }
        //是项目经理
        $is_jingli = $pject->is_jingli($p_id,$this->user_id);
        if($is_jingli && $pr_info['status']==1){
            $update = [
                'status'=>2,
                'jingli_sign_img'=>$sign_img?:'',
                'checktime2'=>$this->datetime
            ];
        }

        if(empty($update)){
            return $this->response(['code' => 201, 'msg' => '当前用户无法确认']);
        }

        $res = $pr->update_data($prw,$update);
        if($res){
            // 处理项目经理电签图片 - 20181006
            if($is_jingli && $pr_info['status']==1 && $sign_img){
                \think\Queue::later(2,'app\gerent\job\Createsignimg',['type'=>'report','id'=>$id,'sign_type'=>3]);
            }

            // 添加日志
            Plog::add_one($p_id,$id,3,
                ['type'=>1,'id'=>$this->user_id,'name'=>$this->user['name']],
                '[通过]验收报告<<'.$pr_info['name'].'>>');
            // 通过时再检查，事务提醒中有没有相关的通知，有则设为'已处理'
            $nwhere = [
                'p_id'=>$p_id,
                'type'=>5,
                'target_id'=>$id,
                'user_type'=>$this->user_type_int,
                'user_id'=>$this->user_id
            ];

            LN::set_done($nwhere);
            // 通知确认
            $ndata = [
                'p_id'=>$p_id,
                'type'=>5,
                'target_id'=>$id,
                'user_type'=>$this->user_type_int,
                //'user_id'=>$p_info['owner_user_id'],//业主
                'title'=>'验收报告确认提醒',
                'content'=>'验收报告<<'.$pr_info['name'].'>>等待确认'
            ];
            if($is_sejishi){
                $ndata['user_id'] = $p_info['manager_user_id'];
            }
            if($is_jingli){
                $ndata['user_type'] = 2;
                $ndata['user_id'] = $p_info['owner_user_id'];
            }
            LN::add($ndata);
            return $this->response(['code' => 200, 'msg' => '确认成功']);
        }
        return $this->response(['code' => 201, 'msg' => '确认失败']);
    }
    //提出修改意见
    public function modify_post(){
        $id = input('post.id',0,'int');
        $p_id = input('post.p_id',0,'int');
        $content = input('post.content','','trim');
        if (!$id || $id <= 0 || !$p_id || $p_id <= 0 || !$content) {
            return $this->response(['code' => 201, 'msg' => '参数有误']);
        }
        if (!$this->_check_project_power($p_id)) {
            return $this->response(['code' => 201, 'msg' => '该项目无法访问']);
        }

        //$prw = ['id'=>$id,'p_id'=>$p_id];
        $pr_info = (new Projectreport())->get_info(['id'=>$id,'p_id'=>$p_id],'id,status,name');
        if(!$pr_info){
            return $this->response(['code' => 201, 'msg' => '该项目无法访问']);
        }

        $pject = new Pject();
        //$data = [];
        $data = [
            'p_id'=>$p_id,
            'p_rep_id'=>$id,
            //'type'=>3,
            'content'=>$content,
            'addtime'=>$this->datetime
        ];
        $p_info = $pject->get_info(['id'=>$p_id],'id,desgin_user_id,manager_user_id,desgin_assistant_user_id,owner_user_id');
        //是设计师
        $is_sejishi = $pject->is_sejishi($p_id,$this->user_id);
        if($is_sejishi){
            $data['type'] = 1;
        }

        $is_jingli = $pject->is_jingli($p_id,$this->user_id);
        if($is_jingli){
            $data['type'] = 2;
        }
        if(!$is_sejishi && !$is_jingli){
            return $this->response(['code' => 201, 'msg' => '当前用户无法修改']);
        }

        $res = (new Projectreportmodify())->add_data($data);
        if($res){
            // 添加日志
            Plog::add_one($p_id,$id,3,
                ['type'=>1,'id'=>$this->user_id,'name'=>$this->user['name']],
                '[修改]验收报告<<'.$pr_info['name'].'>>:'.$content);
            // 通知相关人员查看修改信息

            $ndata = [
                'p_id'=>$p_id,
                'type'=>5,
                'target_id'=>$id,
                'user_type'=>$this->user_type_int,
                //'user_id'=>$p_info['owner_user_id'],//业主
                'title'=>'验收报告被驳回',
                'content'=>'验收报告<<'.$pr_info['name'].'>>:'.$content
            ];
            if($is_sejishi){
                $ndata['user_id'] = $p_info['desgin_assistant_user_id'];
            }
            if($is_jingli){
                $ndata['user_id'] = $p_info['desgin_user_id'];
            }
            LN::add($ndata);

            return $this->response(['code' => 200, 'msg' => '修改意见已提交成功']);
        }

        return $this->response(['code' => 201, 'msg' => '提交失败']);
    }
}