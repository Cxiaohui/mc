<?php
/**
 * Created by PhpStorm.
 * User: chenxh
 * Date: 2018/6/24
 * Time: 20:06
 */
namespace app\gerent\controller;
use app\gerent\model\Recomd,
    app\common\library\Recomd as lRecomd;
class Recommend extends Common
{
    /**
     * @var Recomd
     */
    public $M;

    public function _initialize($check_login = true)
    {
        parent::_initialize($check_login);
        $this->M = new Recomd();
        //$this->mpj = new mPJ();
    }


    public function index()
    {
        $where = ['isdel'=>0];
        $id = input('get.id',0,'int');

        if($id>0){
            $where['id'] = $id;
        }

        $count = $this->M->get_count($where);
        $data = $page = [];
        if($count > 0){
            $page = $this->_pagenav($count);
            $field = '*';
            $data = $this->M->get_remd_list($where,$field,$page['offset'].','.$page['limit']);
            //print_r($data);exit;
            $l_recomd = new lRecomd();
            foreach($data as $k=>$da){
                $rinfo = $l_recomd->get_recmd_info($da);
                //print_r($rinfo);exit;
                $data[$k] = array_merge($da,$rinfo);
            }
        }

        $js = $this->loadJsCss(array('p:common/common', 'recommend'), 'js', 'admin');
        $this->assign('footjs', $js);
        $this->assign('pagenav', $page);
        $this->assign('data', $data);
        return $this->fetch('index');
    }

    public function add($id = 0)
    {
        if ($this->request->isPost()) {
            return $this->save_recomd_data();
        }
        $stable = input('get.stable', '', 'trim');
        $sid = input('get.sid', 0, 'int');
        $betime = date('Y-m-d');
        $iscustom = true;
        $info = $reminfo = [];

        if($id>0){
            $info = $this->M->get_info(['id'=>$id,'isdel'=>0]);
            if(!$info){
                $this->error('该轮播信息不存在或已删除');
            }
            //print_r($info);
            $iscustom = $info['retype']=='1'?false :true;

            $reminfo = (new lRecomd())->get_recmd_info($info,'table',true);

            $stable = $info['stable'];
            $sid = $info['sid'];
            $betime = $info['betime'];
            //$info['entime'] = $info['entime']>0 ? date('Y-m-d',$info['entime']) : '';

        }else if($stable && $sid){
            $reminfo = (new lRecomd())->get_recmd_info_add(['stable'=>$stable,'sid'=>$sid,'type'=>'3']);
            $iscustom = false;
        }else{
            $stable = 'self';
        }

        $defimg = c_img('');

        $js = $this->loadJsCss(array('p:common/common', 'p:webuper/js/webuploader', 'singleUp', 'recommend_set'), 'js', 'admin');
        $this->assign('footjs', $js);
        $this->assign('info', $info);
        $this->assign('reminfo', $reminfo);
        $this->assign('defimg', $defimg);
        $this->assign('iscustom', $iscustom);
        $this->assign('stable', $stable);
        $this->assign('sid', $sid);
        $this->assign('betime', $betime);

        return $this->fetch('add');
    }

    public function edit($id = 0)
    {
        if(!$id || $id<=0){
            $this->error('访问错误');
        }


        return $this->add($id);
    }

    public function del($id = 0)
    {
        if(!$id || $id<=0){
            $this->error('访问错误');
        }

        $where = ['id'=>$id,'isdel'=>0];
        $info = $this->M->get_info($where,'id,retype,stable,sid');
        if(!$info){
            $this->error('该轮播信息不存在或已删除');
        }
        $res = $this->M->update_data($where,['isdel'=>1]);
        if(!$res){
            $this->error('删除失败');
        }
        //todo 清除相关缓存
        if($info['stable'] && $info['stable']!='self' && $info['sid']) {
            $this->M->set_recmd($info['stable'],$info['sid'],0,0);
        }
        $this->success('删除成功');
    }

    public function opers(){
        $acts = ['sort'];
        $act = input('post.act','','trim');

        if(!$act || !in_array($act,$acts)){
            return ['err'=>1,'mesg'=>'访问错误'];
        }
        switch($act){
            case 'sort':
                $id = input('post.id',0,'int');
                $sort = input('post.sort',0,'int');
                if(!$id || $id<=0){
                    return ['err'=>1,'mesg'=>'访问错误'];
                }
                $res = $this->M->update_data(['id'=>$id],['sort'=>$sort]);
                if($res){
                    return ['err'=>0,'mesg'=>'设置成功'];
                }
                return ['err'=>1,'mesg'=>'设置失败'];
                break;
        }
    }

    //========
    protected function save_recomd_data()
    {
        $post = input('post.');
        unset($post['file']);

        $post['betime'] = date('Y-m-d', strtotime($post['betime']));
        if ($post['entime']) {
            $post['entime'] = date('Y-m-d', strtotime($post['entime']));
        } else {
            $post['entime'] = 0;
        }

        if ($post['retype'] == 1) {
            $post['url'] = '';
        }

        $res = $this->M->save_data($post);
        if ($res) {
            $this->jsalert('保存成功', 3);
        }
        $this->error('保存失败');

    }
}