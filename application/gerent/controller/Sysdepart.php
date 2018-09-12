<?php
/**
 * Created by PhpStorm.
 * User: xiaohui
 * Date: 2017-06-14
 * Time: 15:07
 */
namespace app\gerent\controller;
use app\gerent\model\Systable as mSystable,
    app\gerent\model\Company;
class Sysdepart extends Common{
    /**
     * @var mSystable
     */
    protected $admin_model;
    public function _initialize($check_login=true)
    {
        parent::_initialize($check_login);
        $this->admin_model = new mSystable();
    }

    public function index(){
        $where = ['isdel'=>0];

        $count = $this->admin_model->get_depart_count($where);
        $data = $page = [];
        if($count>0){
            //$page = $this->_pagenav($count);
            $field = 'id,cpid,name';
            $data = $this->admin_model->get_depart_list($where,$field,0);
        }
        //print_r($page);
        $cpw = '1=1';
        if(session('cp_power_tag')!=1){
            $cpw = ['id'=>session('cpid')];
        }
        $companys = (new Company())->get_list($cpw,'id,name',0);
        foreach($companys as $k=>$cpy){

            foreach($data as $dk => $da){
                if($da['cpid'] == $cpy['id']){
                    $companys[$k]['departs'][] = $da;
                    unset($data[$dk]);
                }
            }
        }

        $js = $this->loadJsCss(array('p:common/common', 'department'), 'js', 'admin');
        //$this->assign('pagenav',$page);
        $this->assign('data',$data);
        $this->assign('footjs', $js);
        $this->assign('companys', $companys);
        return $this->fetch('index');
    }

    public function add($id=0){
        if($this->request->isPost()){
            return $this->save_depart_data();
        }
        $info = [];
        if($id>0){
            $info = $this->admin_model->get_depart_info(['id'=>$id]);
            if(!$info){
                $this->error('该部门信息不存在');
            }
        }
        $cpw = '1=1';
        if(session('cp_power_tag')!=1){
            $cpw = ['id'=>session('cpid')];
        }
        $js = $this->loadJsCss(array('p:common/common', 'department'), 'js', 'admin');
        $this->assign('footjs', $js);
        $this->assign('info', $info);
        $this->assign('company', (new Company())->get_list($cpw,'id,name',0));
        return $this->fetch('add');
    }

    public function edit($id=0){
        if(!$id || $id<=0){
            $this->jsalert('访问错误',7);
        }
        return $this->add($id);
    }

    public function del($id=0){
        //
        if(!$id || $id<=0){
            $this->error('访问错误');
        }
        $res = $this->admin_model->del_depart(['id'=>$id]);
        if($res){
            $this->admin_model->update_admin(['depart_id'=>$id],['depart_id'=>0,'department'=>'']);
            $this->success('删除成功');
        }
        $this->error('删除失败');
    }


    //==========================

    protected function save_depart_data(){
        $post = $this->request->post();

        $res = $this->admin_model->save_depart_data($post);
        if($res){
            $this->jsalert('保存部门资料成功',3);
        }
        $this->error('保存失败');
    }
}