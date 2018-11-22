<?php
/**
 * Created by PhpStorm.
 * User: chenxh
 * Date: 2018/6/14
 * Time: 15:12
 */
namespace app\api\controller\b_v1;
use app\api\controller\Common as BCommon;

class Common extends BCommon{

    protected $user_type = 'b';
    protected $user_type_int = 1;

    protected $user = [];

    public function __construct($user_type='')
    {
        parent::__construct($user_type);
        if($user_type){
            $this->_check_user();
        }

    }

    protected function _check_user(){
        if(!$this->user_id){
            return $this -> response(['code' => 201, 'msg' => '0账号异常']);
        }
        $this->user = $this->_get_user();

        if(!$this->user){
            return $this -> response(['code' => 201, 'msg' => '账号异常.']);
        }
        if($this->user['b_power_tag']==0){
            return $this -> response(['code' => 201, 'msg' => '无权限']);
        }
    }

    protected function _get_user(){

        if($this->user_id){
            return \app\api\library\User::get_b_user($this->user_id);
        }
        return false;
    }

    protected function _check_project_power($p_id){
        if(in_array($this->user['b_power_tag'],[1,2])){
            return true;
        }
        //todo 还需要处理分公司情况

        if($this->user['b_power_tag']==3){
            $count = (new \app\common\model\Projectadmin)->get_count(['p_id'=>$p_id,'b_user_id'=>$this->user_id]);
            if($count>0){
                return true;
            }
        }

        return $this -> response(['code' => 201, 'msg' => '无权限']);
    }

    protected function p_status(){
        return [
            0=>'未开始',
            1=>'进行中',
            2=>'待客户确认',
            3=>'已驳回',
            4=>'已完成'
        ];
    }
}