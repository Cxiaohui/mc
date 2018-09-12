<?php
/**
 * Created by PhpStorm.
 * User: xiaohui
 * Date: 2017-06-13
 * Time: 16:43
 */
namespace app\gerent\controller;
use app\gerent\model\Systable as mSystable,
    app\gerent\model\Company,
    app\gerent\library\Login,
    app\gerent\model\Syspower as mSyspower;
class Lgn extends Common{

    public function _initialize($check_login=false)
    {
        parent::_initialize(false);
        $this->admin_model = new mSystable();
        $this->power_model = new mSyspower();

    }

    public function index(){

        if($this->request->isPost()){
            return $this->do_login();
        }

        return $this->fetch('index');
    }

    public function lgnout(){
        \app\gerent\model\Adminoperlog::instance()->save_data('退出后台系统');
        Login::clear_session();
        $this->redirect('Lgn/index');
    }

    protected function do_login(){
        $username = input('post.uname','');
        $pwd = input('post.pwsd','');
        $verify = input('post.verify','');
        $jump = input('get.ref',url('Index/index'));

        if(!$username || !$pwd || !$verify){
            return ['err'=>1,'mesg'=>'信息有误，请重试'];
        }
        $ref =get_ref();
        if(strpos($ref,'.local')===false && !captcha_check($verify)){
            return ['err'=>1,'mesg'=>'验证码不正确'];
        }

        $user = $this->admin_model->get_admin_info(['log'=>$username,'is_work'=>1,'isdel'=>0],'id as user_id,cpid,log,pwd,stat,role_id,name,sex,status,depart_id,loginip,lgtime');
        if(!$user){
            return ['err'=>1,'mesg'=>'账号不存在'];
        }
        if($user['status']!=1 || $user['role_id']==0){
            return ['err'=>1,'mesg'=>'账号不可用，请与管理员联系'];
        }
        $in_pwd = create_pwd($pwd,$user['stat']);
        if($in_pwd !== $user['pwd']){
            return ['err'=>1,'mesg'=>'密码不正确'];
        }
        unset($user['pwd'],$user['stat'],$pwd);
        //检查所属角色状态
        if(!$this->admin_model->get_role_count(['id'=>$user['role_id'],'status'=>1])){
            return ['err'=>1,'mesg'=>'账号不可用，请与管理员联系'];
        }

        //更新登录时间和IP
        $update = [
            'id'=>$user['user_id'],
            'loginip'=>$this->request->ip(),
            'lgtime'=>time()
        ];
        $this->admin_model->save_admin_data($update);

        $p = (new Company())->get_info(['id'=>$user['cpid']],'id,power_tag');
        $user['cp_power_tag'] = $p['power_tag'];
        //记录到session
        Login::login_session($user);
        \app\gerent\model\Adminoperlog::instance()->save_data('登录后台系统');

        return ['err'=>0,'mesg'=>'登录成功！','url'=>$jump];
    }
}