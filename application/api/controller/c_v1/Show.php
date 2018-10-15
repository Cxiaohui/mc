<?php
/**
 * Created by PhpStorm.
 * User: chenxh
 * Date: 2018/7/9
 * Time: 16:25
 */
namespace app\api\controller\c_v1;
use app\common\model\Projectstaticdocs,
    app\common\model\Projectstatic as pstatic,
    app\common\model\Projectstaticmodify as smodify,
    app\common\model\Project as Pject,
    app\common\model\Projectlog,
    app\common\library\Plog;
class Show extends Common{

    protected $log_type = [
        1=>5,
        2=>6,
        3=>7
    ];
    protected $status = [
        0=>'待确认',1=>'等待修改',2=>'已确认'
    ];

    public function __construct($user_type='')
    {
        parent::__construct($this->user_type);
    }



    //我家的方案
    public function cases_get(){
        return $this->get_static_datas(1);
    }

    //我家的施工图
    public function drawings_get(){
        return $this->get_static_datas(2);
    }

    //我家的主材
    public function makings_get(){
        return $this->get_static_datas(3);
    }

    //我家的方案-列表
    public function caselist_get(){
        return $this->get_list_datas(1);
    }
    //我家的施工图-列表
    public function drawinglist_get(){
        return $this->get_list_datas(2);
    }
    //我家的主材-列表
    public function makinglist_get(){
        return $this->get_list_datas(3);
    }
    //详情
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
        $static_info = (new pstatic())->get_info(['id'=>$id,'p_id'=>$p_id,'isdel'=>0],'id,type,name,status,remark,pass_time,addtime');
        if(!$static_info){
            return $this->response(['code' => 201, 'msg' => '该信息不存在']);
        }

        $static_info['status_name'] = $this->status[$static_info['status']];

        $docs = (new Projectstaticdocs())->get_list(['p_static_id'=>$id,'isdel'=>0],'id,file_type,file_name,file_path,sign_complex_path,addtime');
        if(!empty($docs)){
            $qiniu_host = config('qiniu.host');
            foreach($docs as $dk=>$dv){
                $docs[$dk]['addtime'] = date('Y-m-d',strtotime($dv['addtime']));
                //print_r($dv);
                $docs[$dk]['file_url'] = quimg($dv['sign_complex_path'],$dv['file_path'],$qiniu_host);

                unset($docs[$dk]['file_path']);
            }
        }
        // 输出操作记录 - 20181005
        $plogs = (new Projectlog())->get_list(['p_id'=>$p_id,'p_step_id'=>$id,'p_step_type'=>$this->log_type[$static_info['type']]],'id,oper_user_name,oper_desc,addtime');

        return $this->response([
            'code'=>200,
            'msg'=>'成功',
            'data'=>[
                'project' => $p_info,
                'static_info'=>$static_info,
                'docs'=>$docs,
                'static_log'=>$plogs
            ]
        ]);

    }

    /**
     * 修改意见/驳回
     * @return mixed|\think\Response
     */
    public function modify_post(){
        $post = $this->req->post();
        //$types = [1,2,3];
        if(!$post['p_id'] || !$post['id'] || !$post['content']){
            return $this->response(['code'=>201,'msg'=>'参数有误']);
        }
        if(!$this->check_project_onwer($post['p_id'])){
            return $this->response(['code'=>201,'msg'=>'该项目无法访问']);
        }
        $pstatic = new pstatic();
        $where = ['id'=>$post['id']];
        $info = $pstatic->get_info($where,'id,p_id,name,type,status');
        if(!$info){
            return $this->response(['code'=>201,'msg'=>'该项目无法访问']);
        }
        $main_data = [
            'status'=>1,
            'reject_reason'=>$post['content'],
            'reject_time'=>$this->datetime,
            'uptime'=>$this->datetime
        ];

        $res = $pstatic->update_data($where,$main_data);
        if(!$res){
            return $this->response(['code'=>201,'msg'=>'保存失败，请稍后再试']);
        }

        $modify_data = [
            'p_id'=>$post['p_id'],
            'p_static_id'=>$info['id'],
            'type'=>3,
            'content'=>$post['content'],
            'addtime'=>$this->datetime
        ];

        (new smodify())->add_data($modify_data);

        //add log

        Plog::add_one($post['p_id'],$info['id'],$this->log_type[$info['type']],['type'=>2,'id'=>$this->user_id,'name'=>'业主'],'[修改]'.$post['content']);

        return $this->response(['code'=>200,'msg'=>'修改意见已提交成功']);
    }

    /**
     * 确认通过/签字确认
     * @return mixed|\think\Response
     */
    public function pass_post(){
        $id = input('post.id',0,'int');
        $p_id = input('post.p_id',0,'int');
        $sign_img = input('post.sign_img','','trim');
        if (!$id || $id <= 0 || !$p_id || $p_id <= 0 ) {
            return $this->response(['code' => 201, 'msg' => '参数有误']);
        }
        if (!$this->check_project_onwer($p_id)) {
            return $this->response(['code' => 201, 'msg' => '该项目无法访问']);
        }

        $pstatic = new pstatic();
        $where = ['id'=>$id];
        $info = $pstatic->get_info($where,'id,p_id,name,type,status');
        if(!$info){
            return $this->response(['code'=>201,'msg'=>'该项目无法访问']);
        }

        if($info['type']==2 && !$sign_img){
            return $this->response(['code' => 201, 'msg' => '请先签字']);
        }

        $main_data = [
            'status'=>2,
            'sign_img'=>$sign_img,
            'pass_time'=>$this->datetime,
            'uptime'=>$this->datetime
        ];
        $res = $pstatic->update_data($where,$main_data);
        if(!$res){
            return $this->response(['code'=>201,'msg'=>'操作失败，请稍后再试']);
        }
        //添加一个定时任务，合成图片
        //施工图签字确认
        if($info['type']==2 && $sign_img){
            \think\Queue::later(2,'app\gerent\job\Createsignimg',['type'=>'static_'.$info['type'],'id'=>$id,'sign_type'=>1]);
        }

        //add log
        Plog::add_one($p_id,$info['id'],$this->log_type[$info['type']],['type'=>2,'id'=>$this->user_id,'name'=>'业主'],'[通过]');

        return $this->response(['code'=>200,'msg'=>'操作成功']);
    }

    //====


    protected function get_list_datas($type){
        $p_id = input('get.p_id',0,'int');

        if(!$p_id || $p_id<=0 ){
            return $this->response(['code'=>201,'msg'=>'参数有误']);
        }

        if(!$this->check_project_onwer($p_id)){
            return $this->response(['code'=>201,'msg'=>'该项目无法访问']);
        }

        $p_info = (new Pject())->get_info(['id' => $p_id, 'isdel' => 0], 'id,type,name');

        if (!$p_info) {
            return $this->response(['code' => 201, 'msg' => '该项目不存在']);
        }

        $w = ['p_id'=>$p_id,'type'=>$type,'isdel'=>0];

        $pstatic = new pstatic();
        $list = $pstatic->get_list($w,'id,name,status',0);
        if(empty($list)){
            return $this->response(['code'=>200,'msg'=>'没有数据','data'=>['list'=>[]]]);
        }
        foreach($list as $k=>$lt){
            $list[$k]['status_name'] = $this->status[$lt['status']];
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

    protected function get_static_datas($type){

        $p_id = input('get.p_id',0,'int');
        //$step_id = input('get.step_id',0,'int');
        //|| !$step_id || $step_id<=0

        if(!$p_id || $p_id<=0 ){
            return $this->response(['code'=>201,'msg'=>'参数有误']);
        }

        if(!$this->check_project_onwer($p_id)){
            return $this->response(['code'=>201,'msg'=>'该项目无法访问']);
        }



        $w = ['p_id'=>$p_id,'type'=>$type,'isdel'=>0];
        $data = (new Projectstaticdocs())->get_list($w,'id,file_type,file_name,file_path,sign_complex_path,addtime',0);

        if(empty($data)){
            return $this->response(['code'=>201,'msg'=>'没有数据']);
        }
        $q_host = config('qiniu.host');
        if(!empty($data)){

            foreach($data as $k=>$da){

                $data[$k]['file_url'] = quimg($da['sign_complex_path'],$da['file_path'],$q_host);
                $data[$k]['addtime'] = date("Y-m-d",strtotime($da['addtime']));
                unset($data[$k]['file_path'],$data[$k]['sign_complex_path']);
            }
        }
        $pstatic = new pstatic();
        $info = $pstatic->get_info($w,'id,p_id,name,type,status,sign_img');
        $status_name = [
            0=>'待确认',
            1=>'已驳回',
            2=>'已通过'
        ];
        $info['status_name'] = isset($this->status[$info['status']])?$this->status[$info['status']]:'';
        if($info['sign_img']){
            $info['sign_img'] = $q_host.$info['sign_img'];
        }
        //日志
        $pstatic = new pstatic();
        $where = ['p_id'=>$p_id,'type'=>$type];
        $info = $pstatic->get_info($where,'id,p_id,name,type,status');

        $plogs = (new Projectlog())->get_list(['p_id'=>$p_id,'p_step_id'=>$info['id'],'p_step_type'=>$this->log_type[$info['type']]],'id,oper_user_name,oper_desc,addtime');


        return $this->response([
            'code'=>200,
            'msg'=>'成功',
            'data'=>[
                'info'=>$info,
                'show_list'=>$data,
                'show_log'=>$plogs
            ]
        ]);
    }
}