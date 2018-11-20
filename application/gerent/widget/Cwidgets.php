<?php
/**
 * Created by PhpStorm.
 * User: xiaohui
 * Date: 2017-06-13
 * Time: 17:14
 */
namespace app\gerent\widget;
use app\gerent\controller\Common;
use app\gerent\model\Adminoperlog;
use app\gerent\model\Systable as mSystable;
use app\gerent\model\Syspower as mSyspower,
    app\gerent\library\Admin;
class Cwidgets extends Common{

    public function _initialize($check_login=true)
    {
        parent::_initialize($check_login);
        $this->power_model = new mSyspower();
        $this->admin_model = new mSystable();
    }

    public function left(){
        //找出当前的控制器属于哪个组
        $contr = strtolower($this->request->controller());
        $act = strtolower($this->request->action());
        $node = session('_ACCESS_LIST.'.$contr);
        $nodes = [];
        if($node[1]>0){
            $node_ids = session('gid_node_ids.'.$node[1]);
            $nodes = $this->power_model->get_menu_by_ids($node_ids);
        }
        //print_r($nodes);
        $this->assign('nodes',$nodes);
        $this->assign('contr',$contr);
        $this->assign('act',$act);
        return $this->fetch('widget/left');
    }


    public function top(){
        $gids = session('gids');
        $groups = $this->power_model->find_groups(['id'=>['in',$gids]]);

        //找出当前的控制器属于哪个组
        $contr = strtolower($this->request->controller());
        $node = session('_ACCESS_LIST.'.$contr);

        $this->assign('groups',$groups);
        $this->assign('cgp',$node[1]);
        return $this->fetch('widget/top');
    }


    public function select_group_admins($selected=''){
        $cpid = 0;
        if(session('cp_power_tag')!=1){
            $cpid = session('cpid');
        }


        $data = Admin::admin_in_depart($cpid);
        $this->assign('selected',$selected);
        $this->assign('data',$data);
        return $this->fetch('widget/select_group_admins');
    }
}