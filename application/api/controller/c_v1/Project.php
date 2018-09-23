<?php
/**
 * Created by PhpStorm.
 * User: chenxh
 * Date: 2018/6/20
 * Time: 19:56
 */
namespace app\api\controller\c_v1;
use app\common\model\Project as Pject,
    app\common\model\Projectstep,
    app\common\model\Stepmodify,
    app\common\model\Projectpay,
    app\common\model\Projectlog,
    app\common\library\Plog,
    app\common\library\Notice as LN,
    app\api\library\Project as APject,
    app\api\library\Steptime,
    app\common\model\Projectdoc;

class Project extends Common{

    public function __construct($user_type='')
    {
        parent::__construct($this->user_type);
    }

    /**
     * 项目列表
     * @return mixed|\think\Response
     */
    public function list_get(){
        //owner_user_id
        $list = (new Pject())->get_list(['owner_user_id'=>$this->user_id,'isdel'=>0],'id,name,address',0);
        return $this->response([
            'code'=>200,
            'msg'=>'成功',
            'data'=>[
                'list'=>$list
            ]
        ]);
    }

    /**
     * 项目日历页面
     */
    public function info_get()
    {
        $p_id = input('get.p_id', 0, 'int');
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
        $pstep = new Projectstep();
        $pdoc = new Projectdoc();
        //获取所有主阶段信息,各阶段时间

        $main_steps = $pstep->get_step_list(['p_id' => $p_id, 'pid' => 0, 'isdel' => 0], 'id,type,name,plan_time,realtime');

        if (!empty($main_steps)) {
            $main_steps = Steptime::get_mainstep_color($main_steps);
            $step_color_date = Steptime::get_color_days($main_steps,$p_id,2);
            if(!$step_color_date){
                return $this->response(['code' => 201, 'msg' => '项目的时间有误']);
            }
            //
            $step1_docs = APject::get_step_type_docs($pstep, $p_id, 1);
            $step2_docs = APject::get_step_type_docs($pstep, $p_id, 2);
            //logs
            $plogs = (new Projectlog())->get_list(['p_id' => $p_id], 'id,oper_user_name,oper_desc,addtime');

            return $this->response([
                'code' => 200,
                'msg' => '成功',
                'data' => [
                    'project' => $p_info,
                    'step_color_date' => $step_color_date,
                    'step_times' => $main_steps,
                    'design_step_docs' => $step1_docs,
                    'construction_step_docs' => $step2_docs,
                    //'design_step_last_doc'=>$step1_last_docs,
                    //'construction_step_last_doc'=>$step2_last_docs,
                    'project_log' => $plogs
                ]
            ]);

        }

        return $this->response(['code' => 201,'msg' => '没有阶段信息']);
    }

    /**
     * 项目付款信息
     * @return mixed|\think\Response
     */
    public function pay_list_get(){
        $p_id = input('get.p_id',0,'int');
        if(!$p_id || $p_id<=0){
            return $this->response(['code'=>201,'msg'=>'参数有误']);
        }

        if(!$this->check_project_onwer($p_id)){
            return $this->response(['code'=>201,'msg'=>'该项目无法访问']);
        }

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
            $data['design_pay'] = new \stdClass();
        }
        if(!empty($type2)){
            $data['construction_pay'] = [
                'name'=>'施工款项',
                'pay_list'=>$type2
            ];
        }else{
            $data['construction_pay'] = new \stdClass();
        }

        return $this->response([
            'code'=>200,
            'msg'=>'成功',
            'data'=>$data
        ]);
    }

    /**
     * 项目各阶段信息
     * @return mixed|\think\Response
     */
    public function steps_get(){
        $p_id = input('get.p_id',0,'int');
        if(!$p_id || $p_id<=0){
            return $this->response(['code'=>201,'msg'=>'参数有误']);
        }
        if(!$this->check_project_onwer($p_id)){
            return $this->response(['code'=>201,'msg'=>'该项目无法访问']);
        }
        $p_info = (new Pject())->get_info(['id'=>$p_id,'isdel'=>0],'id,type,name');

        if(!$p_info){
            return $this->response(['code'=>201,'msg'=>'该项目不存在']);
        }
        $p_status = $this->p_status();
        //$p_info['status_name'] = $p_status[$p_info['status']];

        $pstep = new Projectstep();
        $s_w = ['p_id'=>$p_id,'isdel'=>0];
        if(!$pstep->get_count($s_w)){
            return $this->response(['code'=>201,'msg'=>'该项目没有阶段信息']);
        }
        $step_fileds = 'id,p_id,pid,type,name,plan_time,realtime,status';
        $s_w['type'] = 1;
        $type1_steps_list = $pstep->get_step_list($s_w,$step_fileds);
        $type1_steps = APject::create_type_steps($type1_steps_list,$p_status);;


        $s_w['type'] = 2;
        $type2_steps_list = $pstep->get_step_list($s_w,$step_fileds);
        $type2_steps = APject::create_type_steps($type2_steps_list,$p_status);


        return $this->response([
            'code'=>200,
            'msg'=>'成功',
            'data'=>[
                'project'=>$p_info,
                'design_steps'=>$type1_steps,
                'construction_steps'=>$type2_steps
            ]
        ]);
    }

    /**
     *
     * 项目阶段详情信息
     * @return mixed|\think\Response
     */
    public function step_info_get(){
        $p_id = input('get.p_id',0,'int');
        $step_id = input('get.step_id',0,'int');

        if(!$p_id || $p_id<=0 || !$step_id || $step_id<=0){
            return $this->response(['code'=>201,'msg'=>'参数有误']);
        }
        if(!$this->check_project_onwer($p_id)){
            return $this->response(['code'=>201,'msg'=>'该项目无法访问']);
        }

        $pstep = new Projectstep();

        $s_w = ['id'=>$step_id,'p_id'=>$p_id,'isdel'=>0];
        $step_fileds = 'id,p_id,pid,type,name,plan_time,realtime,status,b_user_name,b_user_mesg,uptime';
        $step_info = $pstep->get_info($s_w,$step_fileds);
        if(!$step_info){
            return $this->response(['code'=>201,'msg'=>'该项目没有阶段信息']);
        }
        $step_info['plan_time'] = str_replace('-','.',$step_info['plan_time']);
        $plan_time = explode('|',$step_info['plan_time']);

        $step_info['plan_day'] = ceil((strtotime($plan_time[1])-strtotime($plan_time[0]))/(24*3600))+1;
        $step_info['plan_time'] = implode('-',$plan_time);
        $primary_doc = [];
        $docs = (new Projectdoc())->get_list(['p_id'=>$p_id,'p_step_id'=>$step_id],'id,is_primary,file_type,file_name,file_path,addtime',0);
        if(!empty($docs)){
            $q_host = config('qiniu.host');
            $img_ext = config('img_ext');
            foreach($docs as $k=>$d){
                $docs[$k]['file_url'] = $q_host.$d['file_path'];
                $docs[$k]['addtime'] = date('Y-m-d',strtotime($d['addtime']));
                unset($docs[$k]['file_path']);
                //施工
                if($step_info['type']==2){
                    if($d['is_primary']==1 && in_array($d['file_type'],$img_ext)){
                        $primary_doc = $docs[$k];
                        unset($docs[$k]);
                    }
                }

            }
        }
        //logs,'p_step_type'=>$step_info['type']
        $plogs = (new Projectlog())->get_list(['p_id'=>$p_id,'p_step_id'=>$step_id,'p_step_type'=>$step_info['type']],'id,oper_user_name,oper_desc,addtime');

        $data = [
            'step_info'=>$step_info,
            //将主图与其他图分开传
            'doc_title'=>$step_info['type']==1?"设计资料":'施工照片',
            'doc_count'=>count($docs),
            'docs'=>$docs,
            'project_log'=>$plogs
        ];

        if($primary_doc){
            $data['primary_doc'] = $primary_doc;
        }

        return $this->response([
            'code'=>200,
            'msg'=>'成功',
            'data'=>$data
        ]);
    }

    /**
     * 业主项目修改意见
     * @return mixed|\think\Response
     */
    public function step_modify_post(){
        $puts = $this->req->post();
        if(!$puts['p_id'] || !$puts['step_id'] || !isset($puts['img']) || !isset($puts['content']) || !$puts['img'] || !$puts['content']){
            return $this->response(['code'=>201,'msg'=>'参数有误']);
        }
        if(!$this->check_project_onwer($puts['p_id'])){
            return $this->response(['code'=>201,'msg'=>'该项目无法访问']);
        }
        //$p_info = (new Pject())->get_info(['id'=>$puts['p_id']],'id,manager_user_id,desgin_user_id');

        $pstep = new Projectstep();
        $s_w = ['id'=>$puts['step_id'],'p_id'=>$puts['p_id'],'isdel'=>0];
        $step_info = $pstep->get_info($s_w,'id,p_id,type,name,b_user_id');
        if(!$step_info){
            return $this->response(['code'=>201,'msg'=>'该项目没有阶段信息']);
        }

        $pstep->update_data(['id'=>$puts['step_id'],'p_id'=>$puts['p_id'],'status'=>2],['status'=>3,'uptime'=>$this->datetime]);

        $save_data = [
            'p_id'=>$puts['p_id'],
            'p_step_id'=>$puts['step_id'],
            'c_user_id'=>$this->user_id,
            'img'=>$puts['img'],
            'content'=>$puts['content'],
            'addtime'=>$this->datetime
        ];
        (new Stepmodify())->add_data($save_data);
        //事务提醒中有没有相关的通知，有则设为'已处理'
        $nwhere = [
            'p_id'=>$puts['p_id'],
            'type'=>$step_info['type']==1?1:6,
            'target_id'=>$puts['step_id']
        ];

        LN::set_done($nwhere);
        $ndata = [
            'p_id'=>$puts['p_id'],
            'type'=>$step_info['type']==1?1:6,
            'target_id'=>$puts['step_id'],
            'user_type'=>1,
            'user_id'=>$step_info['b_user_id'],// B端人员id
            'title'=>'项目验收被驳回',
            'content'=>'<<'.$step_info['name'].'>>:'.$puts['content']
        ];
        LN::add($ndata);
        //add log
        Plog::add_one($puts['p_id'],$puts['step_id'],$step_info['type'],['type'=>2,'id'=>$this->user_id,'name'=>'业主'],'[修改]'.$puts['content']);

        return $this->response(['code'=>200,'msg'=>'保存成功']);
    }

    /**
     * 业主通过
     * @return mixed|\think\Response
     */
    public function step_pass_post(){
        $puts = $this->req->post();
        if(!$puts['p_id'] || !$puts['step_id']){
            return $this->response(['code'=>201,'msg'=>'参数有误']);
        }
        if(!$this->check_project_onwer($puts['p_id'])){
            return $this->response(['code'=>201,'msg'=>'该项目无法访问']);
        }
        $pstep = new Projectstep();
        $s_w = ['id'=>$puts['step_id'],'p_id'=>$puts['p_id'],'isdel'=>0];
        $step_info = $pstep->get_info($s_w,'id,pid,status,type,plan_time');
        if(!$step_info){
            return $this->response(['code'=>201,'msg'=>'该项目没有阶段信息']);
        }
        $times = explode('|',$step_info['plan_time']);
        $update = [
            'status'=>4,
            'pass_time'=>$this->datetime,
            'uptime'=>$this->datetime,
            'realtime'=>$times[0].'|'.date('Y-m-d')
        ];
        $pstep->update_data(['id'=>$puts['step_id'],'p_id'=>$puts['p_id']],$update);
        //如果有子阶段，则判断子阶段是否全部完成
        if($step_info['pid']>0){
            if($pstep->is_commplete($step_info['pid'])==1){
                $s_w['id'] = $step_info['pid'];
                $step_info = $pstep->get_info($s_w,'id,plan_time');
                $times = explode('|',$step_info['plan_time']);
                $update['realtime'] = $times[0].'|'.date('Y-m-d');
                $pstep->update_data(['id'=>$step_info['pid'],'p_id'=>$puts['p_id']],$update);
            }
        }
        //todo 通过时再检查，事务提醒中有没有相关的通知，有则设为'已处理'
        $nwhere = [
            'p_id'=>$puts['p_id'],
            'type'=>$step_info['type']==1?1:6,
            'target_id'=>$puts['step_id']
        ];

        LN::set_done($nwhere);
        //add log
        Plog::add_one($puts['p_id'],$puts['step_id'],$step_info['type'],['type'=>2,'id'=>$this->user_id,'name'=>'业主'],'[通过]');
        return $this->response(['code'=>200,'msg'=>'保存成功']);
    }
}