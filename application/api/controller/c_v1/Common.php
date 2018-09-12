<?php
/**
 * Created by PhpStorm.
 * User: chenxh
 * Date: 2018/6/14
 * Time: 15:12
 */
namespace app\api\controller\c_v1;
use app\api\controller\Common as BCommon;

class Common extends BCommon{

    protected $user_type = 'c';
    protected $user_type_int = 2;

    public function __construct($user_type='')
    {
        parent::__construct($user_type);
    }

    protected function _get_user(){

        if($this->user_id){
            return \app\api\library\User::get_c_user($this->user_id);
        }
        return false;
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

    /**
     * 检查是否是自己的项目
     * @param $p_id
     * @return int|string
     */
    protected function check_project_onwer($p_id){
        return (new \app\common\model\Project())->get_count(['id'=>$p_id,'owner_user_id'=>$this->user_id,'isdel'=>0]);
    }
}