<?php
/**
 * Created by PhpStorm.
 * User: chenxh
 * Date: 2018/9/11
 * Time: 10:07
 */
namespace app\cron\controller;
use app\common\controller\Base;
use think\Request;

class Common extends Base{

    private $user = [
        'uname'=>'mcabc',
        'upaswd'=>'mctest'
    ];

    public function _initialize(){
        $this->checkAuth();
    }

    public function checkAuth(){
        if (!session('ls_admin_login')) {

            if (!isset($_SERVER['PHP_AUTH_USER'])) {
                header('WWW-Authenticate: Basic realm="LS Event Reg Datas"');
                header('HTTP/1.0 401 Unauthorized');
                echo("Please enter a valid username and password");
                exit();
            } else if (($_SERVER['PHP_AUTH_USER'] == $this->user['uname']) && ($_SERVER['PHP_AUTH_PW'] == $this->user['upaswd'])) {
                session('ls_admin_login',1);
            } else {
                header('WWW-Authenticate: Basic realm="LS Event Reg Datas"');
                header('HTTP/1.0 401 Unauthorized');
                echo("Please enter a valid username and password");
                exit();
            }
        }
    }

}