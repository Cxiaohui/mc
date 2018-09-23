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
    app\common\model\Projectlog,
    app\common\library\Plog;
class Show extends Common{

    protected $log_type = [
        1=>5,
        2=>6,
        3=>7
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
        \think\Queue::later(2,'app\gerent\job\Compleximg',['type'=>'static-'.$info['type'],'id'=>0,'p_id'=>$info['p_id']]);
        //add log
        Plog::add_one($p_id,$info['id'],$this->log_type[$info['type']],['type'=>2,'id'=>$this->user_id,'name'=>'业主'],'[通过]');

        return $this->response(['code'=>200,'msg'=>'操作成功']);
    }

    //====

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
                //$data[$k]['file_url'] = $q_host.$da['file_path'];
                if($da['sign_complex_path']){
                    $data[$k]['file_url'] = $q_host.$da['sign_complex_path'];
                }else{
                    $data[$k]['file_url'] = $q_host.$da['file_path'];
                }
                $data[$k]['addtime'] = date("Y-m-d",strtotime($da['addtime']));
                unset($data[$k]['file_path']);
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