<?php
/**
 * Created by PhpStorm.
 * User: chenxh
 * Date: 2018/7/9
 * Time: 16:25
 */
namespace app\api\controller\b_v1;
use app\common\model\Project as Pject,
    app\common\model\Projectstaticdocs,
    app\common\model\Projectstatic as pstatic,
    app\common\model\Projectstaticmodify as smodify,
    app\common\model\Projectlog,
    app\common\library\Plog;
class Show extends Common{

    protected $log_type = [
        1=>5,
        2=>6,
        3=>7
    ];
    protected $status = [
        0=>'待确认',
        1=>'已驳回',
        2=>'已通过'
    ];

    public function __construct($user_type='')
    {
        parent::__construct($this->user_type);
    }
    //我家的方案
    public function cases_get(){
        return $this->get_static_datas(1);
    }

    //我家的图纸
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
    //我家的图纸-列表
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

        $this->_check_project_power($p_id);

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

    //====

    protected function get_list_datas($type){
        $p_id = input('get.p_id',0,'int');

        if(!$p_id || $p_id<=0 ){
            return $this->response(['code'=>201,'msg'=>'参数有误']);
        }

        $this->_check_project_power($p_id);

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
        $this->_check_project_power($p_id);
        /*if(!$this->check_project_onwer($p_id)){
            return $this->response(['code'=>201,'msg'=>'该项目无法访问']);
        }*/

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
        $info['status_name'] = isset($status_name[$info['status']])?$status_name[$info['status']]:'';
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