<?php
/**
 * Created by PhpStorm.
 * User: chenxh
 * Date: 2018/6/11
 * Time: 23:04
 */
namespace app\gerent\controller;
use app\gerent\model\Teams as mTeam,
    app\gerent\model\Systable as mSystable;
class Teams extends Common{

    public function _initialize($check_login=true)
    {
        parent::_initialize($check_login);
        $this->t_model = new mTeam();
    }

    public function index(){

    }

    public function cate_index(){
        $where = [];

        $count = $this->t_model->get_count($where);
        $data = $page = [];
        if($count>0){
            $page = $this->_pagenav($count);
            $field = 'id,name';
            $data = $this->t_model->get_list($where,$field,$page['offset'].','.$page['limit']);
        }

        $js = $this->loadJsCss(array('p:common/common','team_cate'), 'js', 'admin');

        $this->assign('footjs', $js);
        $this->assign('pagenav',$page);
        $this->assign('data',$data);
        return $this->fetch('cate_index');
    }

    public function cate_add($id=0){
        if($this->request->isPost()){
            return $this->save_cate_data();
        }
        $info = [];

        if($id>0){
            $info = $this->t_model->get_info(['id'=>$id]);
            if(!$info){
                $this->error('该团队不存在');
            }
        }

        $js = $this->loadJsCss(array('p:common/common','team_cate'), 'js', 'admin');

        $this->assign('footjs', $js);
        $this->assign('info', $info);
        return $this->fetch('cate_add');

    }

    /**
     * 编辑文章分类
     */
    public function cate_edit($id=0){
        if(!$id || $id<=0){
            $this->error('访问错误');
        }
        return $this->cate_add($id);
    }
    /**
     * 删除文章分类
     */
    public function cate_del($id=0){
        if(!$id || $id<=0){
            $this->error('访问错误');
        }

        $res = $this->t_model->del_row(['id'=>$id]);
        if($res){
            //$this->t_model->update__data(['acid'=>$id],['acid'=>0]);
            (new mSystable())->update_admin(['team_id'=>$id],['team_id'=>0]);
            \app\gerent\model\Adminoperlog::instance()->save_data('删除团队：'.$id);
            $this->success('删除成功');
        }
        $this->error('删除失败');
    }

    protected function save_cate_data(){
        $name = input('post.name','','trim');
        $id = input('post.id',0,'int');
        if(!$name){
            $this->error('数据有误');
        }
        $res = $this->t_model->save_data(['name'=>$name,'id'=>$id]);
        if($res){
            \app\gerent\model\Adminoperlog::instance()->save_data('编辑团队：'.$name);
            $this->jsalert('保存成功',3);
        }
        $this->error('保存失败');
    }

}