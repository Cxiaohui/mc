<?php
/**
 * Created by PhpStorm.
 * User: xiaohui
 * Date: 2017-06-13
 * Time: 16:42
 */
namespace app\gerent\controller;
use app\gerent\model\Syspower;
class Index extends Common{

    public function _initialize($check_login=true)
    {
        parent::_initialize($check_login);
    }

    public function index(){

        $p = new Syspower();
        $role_id = 2;
        $p->get_my_node_info($role_id);

        $this->assign('date',date('Y-m-d H:i'));
        return $this->fetch('index');
    }


    public function tup(){

        $js = $this->loadJsCss(array('p:common/common',
            'p:fileinput/fileinput','p:fileinput/locales/zh',
            'p:fileinput/themes/explorer/theme','tup'), 'js', 'admin');

        $this->assign('footjs', $js);
        return $this->fetch('tup');
    }
}
