<?php
/**
 * Created by PhpStorm.
 * User: xiaohui
 * Date: 2017-06-14
 * Time: 15:45
 */
namespace app\gerent\controller;
use app\gerent\model\Systable as mSystable;

class Syspower extends Common{

    public function _initialize($check_login = true)
    {
        parent::_initialize($check_login);
        $this->admin_model = new mSystable();
    }
    /**
     * 菜单排序
     */
    public function menusort(){

        if($this->request->isPost()){
            return $this->do_menu_post();
        }

        $groups = $this->admin_model->get_node_group_list([],'*',0);
        $js = $this->loadJsCss(array('p:common/common', 'p:dragSort/jquery.dragsort-052','menusort'), 'js', 'admin');
        $this->assign('footjs', $js);
        $this->assign('groups', $groups);
        return $this->fetch('menusort');
    }

    protected function do_menu_post(){
        $acts = ['getsub','savesort'];
        $act = input('post.act','');
        if(!$act || !in_array($act,$acts)){
            return ['err'=>1,'mesg'=>'访问错误'];
        }

        switch($act){
            case 'getsub':
                $isg = input('post.isg',0);
                $pid = input('post.pid',0);
                if(!$pid){
                    return ['err'=>1,'mesg'=>'访问错误'];
                }

                $w = ['ismenu'=>'1','status'=>'1'];
                if($isg){
                    $w['gid'] = $pid;
                    $w['level'] = '2';
                }else{
                    $w['pid'] = $pid;
                }
                $list = $this->admin_model->get_node_list($w,'id,title as name,icon',0);
                return ['err'=>0,'data'=>$list];

                break;
            case 'savesort':
                $post = input('post.');
                $ids = $post['ids'];
                $isg = $post['isg'] + 0;
                //print_r($post);exit;
                if(empty($ids)){
                    return ['err'=>1,'mesg'=>'访问错误'];
                }

                foreach($ids as $k=>$id){
                    if($isg==1){
                        $this->admin_model->update_node_group(['id'=>$id],['sort'=>$k]);
                    }else{
                        $this->admin_model->update_node(['id'=>$id],['sort'=>$k]);
                    }
                }
                return ['err'=>0];
                break;
        }
    }

    //*********************************************权限分组********************************************************
    /**
     * 权限分组
     */
    public function group() {
        $where = [];

        $count = $this->admin_model->get_node_group_count($where);
        $data = $page = [];
        if($count>0){
            $page = $this->_pagenav($count);
            $field = 'id,name,icon,link';
            $data = $this->admin_model->get_node_group_list($where,$field,$page['offset'].','.$page['limit']);
        }
        $js = $this->loadJsCss(array('p:common/common', 'group'), 'js', 'admin');
        $this->assign('footjs', $js);
        $this->assign('pagenav',$page);
        $this->assign('data',$data);
        return $this->fetch('group');
    }

    /**
     * 添加/编辑分组
     */
    public function groupinfo($gid = 0) {
        if($this->request->isPost()){
            $res = $this->admin_model->save_group_data($this->request->post());
            if($res){
                $this->jsalert('保存成功',3);
            }
            $this->error('保存失败');
        }
        $info = [];
        if($gid>0){
            $info = $this->admin_model->get_node_group_info(['id'=>$gid]);
            if(!$info){
                if(!$info){
                    $this->error('该分组信息不存在');
                }
            }
        }

        $js = $this->loadJsCss(array('p:common/common', 'group'), 'js', 'admin');
        $this->assign('footjs', $js);
        $this->assign('info', $info);
        return $this->fetch('groupinfo');
    }

    /**
     * 删除分组
     */
    public function groupdel($gid = 0) {
        if(!$gid || $gid<=0){
            $this->error('访问错误');
        }
        $has = $this->admin_model->get_node_group_count(['id'=>$gid]);
        if($has<=0){
            $this->error('不存在在该分组或已删除');
        }
        $res = $this->admin_model->del_node_group($gid);
        if($res){
            $this->success('删除成功');
        }
        $this->error('删除失败');
    }

    /**
     * 分组的其他操作
     */
    public function groupopers(){

    }

    //*********************************************权限节点********************************************************
    /**
     * 权限列表
     */
    public function nodelist() {

        $where = [];

        $count = $this->admin_model->get_node_count($where);
        $data = $page = [];
        if($count>0){
            //$page = $this->_pagenav($count);
            $all_nodes = $this->admin_model->get_node_list($where,'id,title,name,pid,level,status',0);
            //用数组切割方式进行分页
            //$data = array_slice(create_tree($all_nodes), $page['offset'], $page['limit']);
            $data = create_tree($all_nodes);
            unset($nodelist);
        }

        $js = $this->loadJsCss(array('p:common/common'), 'js', 'admin');
        $this->assign('footjs', $js);
        $this->assign('pagenav',$page);
        $this->assign('data',$data);
        $this->assign('ntype', $this->nodeType());
        return $this->fetch('nodelist');
    }

    /**
     * 添加权限节点
     */
    public function nodeadd($nid = 0) {
        if($this->request->isPost()){
            return $this->save_node_data();
        }
        $pids = $this->admin_model->get_node_pids();
        $pids = create_tree($pids);
        $groups = $this->admin_model->get_node_groups();
        $js   = $this->loadJsCss(array('p:common/common', 'node'), 'js', 'admin');

        $this->assign('footjs', $js);
        $this->assign('groups', $groups);
        $this->assign('pids', $pids);
        $this->assign('ntype', $this->nodeType());
        return $this->fetch('nodeadd');
    }

    /**
     * 编辑权限节点
     */
    public function nodeedit($nid = 0) {
        if($this->request->isPost()){
            return $this->save_node_data(1);
        }
        if(!$nid || $nid<=0){
            $this->error('访问错误');
        }

        $info = $this->admin_model->get_node_info(['id'=>$nid]);
        if(!$info){
            $this->error('该节点信息不存在');
        }
        $pids = $this->admin_model->get_node_pids();
        $pids = create_tree($pids);
        $groups = $this->admin_model->get_node_groups();
        $js   = $this->loadJsCss(array('p:common/common', 'node'), 'js', 'admin');

        $this->assign('footjs', $js);
        $this->assign('groups', $groups);
        $this->assign('pids', $pids);
        $this->assign('info', $info);
        $this->assign('ntype', $this->nodeType());
        $this->assign('ref', get_ref(1));
        return $this->fetch('nodeedit');
    }

    /**
     * 删除权限节点
     */
    public function nodedel($nid = 0) {
        if(!$nid || $nid<=0){
            $this->error('访问错误');
        }
        $childrens = $this->admin_model->get_node_count(['pid'=>$nid]);
        if($childrens>0){
            $this->error('删除的节点中还含有子节点，不能删除！');
        }
        $res = $this->admin_model->del_node($nid);
        if($res){
            $this->success('删除节点数据成功');
        }
        $this->error('删除节点数据失败');
    }

    /**
     * 节点其他操作
     */
    public function nodeopers(){

    }

//*********************************************角色********************************************************

    /**
     * 角色列表
     */
    public function rolelist(){
        $where = [];

        $count = $this->admin_model->get_role_count($where);
        $data = $page = [];
        if($count>0){
            $page = $this->_pagenav($count);
            $field = 'id,name,status,remark';
            $data = $this->admin_model->get_role_list($where,$field,$page['offset'].','.$page['limit']);
        }
        $js = $this->loadJsCss(array('p:common/common'), 'js', 'admin');
        $this->assign('footjs', $js);
        $this->assign('pagenav',$page);
        $this->assign('data',$data);
        return $this->fetch('rolelist');
    }


    /**
     * 添加角色
     */
    public function roleadd($rid=0){
        if($this->request->isPost()){
            return $this->save_role_data();
        }

        $info = [];
        if($rid>0){
            $info = $this->admin_model->get_role_info(['id'=>$rid]);
            if(!$info){
                $this->error('该角色信息不存在');
            }
            $acc = $this->admin_model->get_access_list(['role_id'=>$rid],'node_id');
            $info['acc'] = array2to1($acc,'node_id');
        }
        $allnodes = create_level_tree($this->admin_model->get_all_nodes());

        $js = $this->loadJsCss(array('p:common/common','role'), 'js', 'admin');
        $this->assign('footjs', $js);
        $this->assign('info', $info);
        $this->assign('nodes', $allnodes[0]);
        return $this->fetch('roleadd');
    }

    /**
     * 编辑角色
     */
    public function roleedit($rid=0){
        if($rid<=0){
            $this->error('访问错误');
        }
        return $this->roleadd($rid);
    }
    /**
     * 删除角色
     */
    public function roledel($rid=0){

    }


    /**========================内部方法=================================**/
    /**
     * 节点类型
     * @return array
     */
    protected function nodeType() {
        return array(1 => '项目', 2 => '模块', 3 => '操作');
    }

    /**
     * 保存添加节点数据
     */
    protected function save_node_data($isedit = false) {
        $post = $this->request->post();
        $ref = isset($post['ref'])?base64_decode($post['ref']):url('Syspower/nodelist');
        if ($isedit) {
            unset($post['ref']);
            $res = $this->admin_model->save_edit_node($post);
        } else {
            $res = $this->admin_model->save_add_node($post);
        }

        if ($res['err'] != 0) {
            $this->error($res['mesg']);
        }

        $this->success($res['mesg'], $ref);
    }

    protected function save_role_data(){
        $post = $this->request->post();
        $acc = $post['acc'];
        unset($post['acc']);
        $role_id = $this->admin_model->save_role_data($post);
        if(!$role_id){
            $this->error('保存失败');
        }
        $this->admin_model->save_access_date($role_id,$acc);
        $this->success('保存成功',url('Syspower/rolelist'));
    }

}