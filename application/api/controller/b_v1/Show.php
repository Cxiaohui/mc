<?php
/**
 * Created by PhpStorm.
 * User: chenxh
 * Date: 2018/7/9
 * Time: 16:25
 */
namespace app\api\controller\b_v1;
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

    //====

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