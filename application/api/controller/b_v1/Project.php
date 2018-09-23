<?php
/**
 * Created by PhpStorm.
 * User: chenxh
 * Date: 2018/6/20
 * Time: 21:55
 */
namespace app\api\controller\b_v1;
use app\common\model\Projectadmin,
    app\common\model\Project as Pject,
    app\common\model\Projectstep,
    //app\common\model\Stepmodify,
    //app\common\model\Projectpay,
    app\common\model\Projectlog,
    app\common\library\Plog,
    app\common\library\Notice as LN,
    app\api\library\Project as APject,
    app\api\library\Steptime,
    app\common\model\Projectdoc;
class Project extends Common{

    protected $PA = null;
    public function __construct($user_type='')
    {
        parent::__construct($this->user_type);

        $this->PA = new Projectadmin();
    }
    //我负责的项目列表
    public function list_get(){


        $where = ['isdel'=>0];
        if($this->user['cpid']>0){
            $where['cpid'] = $this->user['cpid'];
        }
        //print_r($where);
        //项目中分配的权限
        if($this->user['b_power_tag']==3){
            $my_ps = $this->_get_my_ps();
            if(!$my_ps){
                return $this -> response(['code' => 201, 'msg' => '你当前没有负责任何项目']);
            }
            $p_ids = array2to1($my_ps,'p_id');
            //print_r($p_ids);
            $where['id'] = ['in',$p_ids];
        }

        $list = (new Pject())->get_list($where,'id,name,address',0);
        return $this->response([
            'code'=>200,
            'msg'=>'成功',
            'data'=>[
                'list'=>$list
            ]
        ]);


    }
    //项目详情（日历页面）
    public function info_get(){
        $p_id = input('get.p_id', 0, 'int');
        if (!$p_id || $p_id <= 0) {
            return $this->response(['code' => 201, 'msg' => '参数有误']);
        }

        $this->_check_project_power($p_id);

        $p_info = (new Pject())->get_info(['id' => $p_id, 'isdel' => 0], 'id,type,name');

        if (!$p_info) {
            return $this->response(['code' => 201, 'msg' => '该项目不存在']);
        }
        $pstep = new Projectstep();
        //$pdoc = new Projectdoc();
        //获取所有主阶段信息,各阶段时间

        $main_steps = $pstep->get_step_list(['p_id' => $p_id, 'pid' => 0, 'isdel' => 0], 'id,type,name,plan_time,realtime');

        if (!empty($main_steps)) {
            $main_steps = Steptime::get_mainstep_color($main_steps);
           //print_r($main_steps);
            $step_color_date = Steptime::get_color_days($main_steps,$p_id,1);
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
    //项目阶段信息
    public function steps_get(){
        $p_id = input('get.p_id', 0, 'int');
        if (!$p_id || $p_id <= 0) {
            return $this->response(['code' => 201, 'msg' => '参数有误']);
        }

        $this->_check_project_power($p_id);

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
        $this->_check_project_power($p_id);

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
        $docs = (new Projectdoc())->get_list(['p_id'=>$p_id,'p_step_id'=>$step_id,'isdel'=>0],'id,is_primary,file_type,file_name,file_path,addtime',0);
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
        //logs
        $plogs = (new Projectlog())->get_list(['p_id'=>$p_id,'p_step_id'=>$step_id,'p_step_type'=>$step_info['type']],'id,oper_user_name,oper_desc,addtime');

        $data =  [
            'step_info'=>$step_info,
            //将主图与其他图分开传
            'doc_title'=>$step_info['type']==1?"设计资料":'施工照片',
            'doc_count'=>count($docs),
            'docs'=>$docs,
            'project_log'=>$plogs,
            //加上拍照说明文章的链接
            'photo_desn_url'=>''
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

    //提交项目阶段的施工信息
    public function step_submit_post(){
        $p_id = input('post.p_id', 0, 'int');
        $step_id = input('post.step_id', 0, 'int');
        if (!$p_id || $p_id <= 0 || $step_id<=0) {
            return $this->response(['code' => 201, 'msg' => '参数有误']);
        }
        $message = input('post.message','','trim');
        $primary_img = input('post.primary_img','','trim');
        $imgs = input('post.imgs','','trim');


        $this->_check_project_power($p_id);
        $p_info = (new Pject())->get_info(['id'=>$p_id],'id,owner_user_id');
        $pstep = new Projectstep();
        $w = ['id'=>$step_id,'p_id'=>$p_id];
        $step_info = $pstep->get_info($w,'id,name,type');
        if(!$step_info){
            return $this->response(['code' => 201, 'msg' => '阶段信息不存在']);
        }
        $isupdate = false;
        $log_mesg = '[上传]'.$step_info['name'].'现场照片';
        if($message){
            $step_update = [
                'status'=>2,
                'b_user_id'=>$this->user_id,
                'b_user_name'=>$this->user['name'],
                'b_user_mesg'=>$message,
                'uptime'=>$this->datetime
            ];

            $res = $pstep->update_data($w,$step_update);
            if(!$res){
                return $this->response(['code' => 201, 'msg' => '保存信息失败']);
            }
            $isupdate = true;
            $log_mesg = $message;
        }

        $doc_inserts = [];
        $projectdoc = new Projectdoc();

        $where = ['p_id'=>$p_id,'p_step_id'=>$step_id];
        if($primary_img){
            $projectdoc->update_data($where,['is_primary'=>0]);

            $ext = strtolower(pathinfo($primary_img)['extension']);
            $doc_inserts[] = [
                'p_id'=>$p_id,
                'p_step_id'=>$step_id,
                'is_primary'=>1,
                'file_type'=>$ext,
                //'file_name'=>'',
                'file_path'=>$primary_img,
                //'file_hash'=>'',
                'qiniu_status'=>1,
                'addtime'=>$this->datetime
            ];
        }
        if(!empty($imgs)){
            $imgs = explode(',',$imgs);
            foreach($imgs as $img){
                $ext = strtolower(pathinfo($img)['extension']);
                $doc_inserts[] = [
                    'p_id'=>$p_id,
                    'p_step_id'=>$step_id,
                    'is_primary'=>0,
                    'file_type'=>$ext,
                    //'file_name'=>'',
                    'file_path'=>$img,
                    //'file_hash'=>'',
                    'qiniu_status'=>1,
                    'addtime'=>$this->datetime
                ];

            }
        }
        if(!empty($doc_inserts)){
            $projectdoc->insert_all($doc_inserts);
            $isupdate = true;
        }

        //添加到操作日志
        if($isupdate){
            Plog::add_one($p_id,$step_id,$step_info['type'],
                ['id'=>$this->user_id,'name'=>$this->user['name'],'type'=>1],
                $log_mesg
            );

        }
        return $this->response(['code' => 200, 'msg' => '保存信息成功']);
    }
    //通知业主
    public function notice_check_post(){
        $p_id = input('post.p_id', 0, 'int');
        $step_id = input('post.step_id', 0, 'int');
        if (!$p_id || $p_id <= 0 || $step_id<=0) {
            return $this->response(['code' => 201, 'msg' => '参数有误']);
        }
        $p_info = (new Pject())->get_info(['id'=>$p_id],'id,owner_user_id');

        $pstep = new Projectstep();

        $w = ['id'=>$step_id,'p_id'=>$p_id];
        $step_info = $pstep->get_info($w,'id,name,type,b_user_mesg');
        if(!$step_info){
            return $this->response(['code' => 201, 'msg' => '阶段信息不存在']);
        }

        //通知业主
        $ndata = [
            'p_id'=>$p_id,
            'type'=>$step_info['type']==1?1:6,
            'target_id'=>$step_id,
            'user_type'=>2,
            'user_id'=>$p_info['owner_user_id'],//业主
            'title'=>'项目阶段确认提醒',
            'content'=>$this->user['name'].'上传了 '.$step_info['name'].'的现场照片:'.$step_info['b_user_mesg']
        ];
        LN::add($ndata);

        $type = $step_info['type']==1?2:3;
        $data = [
            'to_user_type'=>get_user_type_str(2),
            'to_user_id'=>$p_info['owner_user_id'],
            'message'=>'项目阶段有新进展，请前往确认提醒',
            'extras'=>[
                'url'=>"mochuan://com.aysd.mochuan?type={$type}&p_id={$p_id}&id={$step_id}"
            ]
        ];

        \think\Queue::later(2,'app\gerent\job\Pushqueue',$data);
        return $this->response(['code' => 200, 'msg' => '已通知业主']);
    }

    //设置阶段的主图
    public function set_primary_doc_post(){
        $p_id = input('post.p_id', 0, 'int');
        $step_id = input('post.step_id', 0, 'int');
        $doc_id = input('post.doc_id', 0, 'int');
        $img = input('post.img', '', 'trim');
        if (!$p_id || $p_id <= 0 || $step_id<=0 || !$img) {
            return $this->response(['code' => 201, 'msg' => '参数有误']);
        }

        $this->_check_project_power($p_id);

        $projectdoc = new Projectdoc();

        $where = ['p_id'=>$p_id,'p_step_id'=>$step_id];
        $projectdoc->update_data($where,['is_primary'=>0]);

        $ext = strtolower(pathinfo($img)['extension']);
        $data = [
            'is_primary'=>1,
            'file_type'=>$ext,
            //'file_name'=>'',
            'file_path'=>$img,
            //'file_hash'=>'',
            'qiniu_status'=>1
        ];
        if($doc_id>0){
            $where['id'] = $doc_id;
            $res = $projectdoc->update_data($where,$data);
            if(!$res){
                return $this->response(['code' => 201, 'msg' => '设置失败']);
            }
        }else{
            $data['p_id'] = $p_id;
            $data['p_step_id'] = $step_id;
            $data['addtime'] = $this->datetime;
            $doc_id = $projectdoc->add_data($data,true);
            if(!$doc_id){
                return $this->response(['code' => 201, 'msg' => '设置失败']);
            }
        }
        return $this->response(['code' => 200, 'msg' => '设置成功','data'=>[
            'doc_id'=>$doc_id
        ]]);
    }

    //删除阶段图片
    public function del_doc_post(){
        $p_id = input('post.p_id', 0, 'int');
        $step_id = input('post.step_id', 0, 'int');
        $doc_id = input('post.doc_id', 0, 'int');
        //$img = input('post.img', '', 'trim');
        if (!$p_id || $p_id <= 0 || $step_id<=0 || $doc_id<=0) {
            return $this->response(['code' => 201, 'msg' => '参数有误']);
        }

        $this->_check_project_power($p_id);

        $projectdoc = new Projectdoc();

        $where = ['id'=>$doc_id,'p_id'=>$p_id,'p_step_id'=>$step_id];
        $projectdoc->update_data($where,['isdel'=>1]);

        return $this->response(['code' => 200, 'msg' => '删除成功']);
    }


    //=======

    protected function _get_my_ps(){
        return $this->PA->get_my_project(['b_user_id'=>$this->user_id]);
    }



}