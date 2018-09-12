<?php
/**
 * Created by PhpStorm.
 * User: chenxh
 * Date: 2018/6/12
 * Time: 10:38
 */
namespace app\gerent\controller;
use app\gerent\model\Appnode as mANode,
    app\common\model\Approle as mAR,
    app\common\model\Approlenode as mARN;


class Apppower extends Common{
    /**
     * @var mANode
     */
    protected $Am;
    /**
     * @var null|mAR
     */
    protected $ar = null;
    /**
     * @var null|mARN
     */
    protected $arn = null;

    public function _initialize($check_login=true)
    {
        parent::_initialize($check_login);
        $this->Am = new mANode();
        $this->ar = new mAR();
        $this->arn = new mARN();
    }

    //=====角色
    public function role_index(){
        $where = [];

        $count = $this->ar->get_count($where);
        $data = $page = [];
        if($count>0){
            $page = $this->_pagenav($count);
            $field = '*';
            $data = $this->ar->get_list($where,$field,$page['offset'].','.$page['limit']);
        }

        $js = $this->loadJsCss(array('p:common/common'), 'js', 'admin');

        $this->assign('footjs', $js);
        $this->assign('pagenav',$page);
        $this->assign('data',$data);
        return $this->fetch('role_index');
    }

    public function role_add($id=0){
        if($this->request->isPost()){
            return $this->save_role_data();
        }
        $info = [];

        if($id>0){
            $info = $this->ar->get_info(['id'=>$id]);
            if(!$info){
                $this->error('访问错误');
            }
            $r_nodes = $this->arn->get_list(['role_id'=>$id],'node_id',0);
            $info['nodes'] = array2to1($r_nodes,'node_id');
        }

        $nodes = $this->Am->get_list('1=1','id,remark',0);

        $js = $this->loadJsCss(array('p:common/common','app_role'), 'js', 'admin');
        $this->assign('footjs', $js);
        $this->assign('info', $info);
        $this->assign('nodes', $nodes);
        return $this->fetch('role_add');
    }

    public function role_edit($id=0){
        if(!$id || $id<=0){
            $this->error('访问错误');
        }
        return $this->role_add($id);
    }

    public function role_del(){

    }

    //=====操作节点
    public function node_index(){
        $where = [];

        $count = $this->Am->get_count($where);
        $data = $page = [];
        if($count>0){
            $page = $this->_pagenav($count);
            $field = '*';
            $data = $this->Am->get_list($where,$field,$page['offset'].','.$page['limit']);
        }

        $js = $this->loadJsCss(array('p:common/common','app_nodes'), 'js', 'admin');

        $this->assign('footjs', $js);
        $this->assign('pagenav',$page);
        $this->assign('data',$data);
        return $this->fetch('node_index');

    }

    public function node_add($id=0){

        if($this->request->isPost()){
            return $this->save_node_data();
        }
        $info = [];

        if($id>0){
            $info = $this->Am->get_info(['id'=>$id],'*');
            if(!$info){
                $this->error('该操作不存在');
            }
        }

        $js = $this->loadJsCss(array('p:common/common','app_nodes'), 'js', 'admin');

        $this->assign('footjs', $js);
        $this->assign('info', $info);
        return $this->fetch('node_add');
    }

    public function node_edit($id=0){
        if(!$id || $id<=0){
            $this->error('访问错误');
        }
        return $this->node_add($id);
    }

    public function node_del($id=0){
        if(!$id || $id<=0){
            $this->error('访问错误');
        }

        $res = $this->Am->del_row(['id'=>$id]);
        if($res){
            \app\gerent\model\Adminoperlog::instance()->save_data('删除app节点:'.$id);
            $this->success('删除成功');
        }
        $this->error('删除失败');
        
    }


    //===========

    protected function save_role_data(){
        $post = $this->request->post();
        if(!isset($post['acc']) || empty($post['acc'])){
            $this->error('请配置权限');
        }
        $acc = $post['acc'];
        unset($post['acc']);
        //print_r($post);exit;
        $id = 0;
        if(isset($post['id']) && $post['id']>0){
            $id = $post['id'];
            unset($post['id']);
            $this->ar->update_data(['id'=>$id],$post);
        }else{
            $id = $this->ar->add_data($post,true);
        }
        if(!$id){
            $this->error('保存失败');
        }
        $this->arn->save_access_data($id,$acc);
        $this->error('保存成功',url('Apppower/role_index'));
    }

    protected function save_node_data(){
        $post = input('post.');

        if(!$post){
            $this->error('数据有误');
        }
        $res = $this->Am->save_data($post);
        if($res){
            \app\gerent\model\Adminoperlog::instance()->save_data('编辑app节点：'.$post['remark']);
            $this->jsalert('保存成功',3);
        }
        $this->error('保存失败');
    }
}