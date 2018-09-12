<?php
/**
 * Created by PhpStorm.
 * User: chenxh
 * Date: 2018/6/13
 * Time: 21:33
 */
namespace app\gerent\controller;

use app\common\model\Consultation as mCon;

class Consultation extends Common{

    public function _initialize($check_login=true)
    {
        parent::_initialize($check_login);
        $this->M = new mCon();
    }

    public function index(){
        $sop = $this->dosearch();

        $count = $this->M->get_count($sop['w']);
        $data = $page = [];
        if($count>0){
            $page = $this->_pagenav($count);
            //$field = '*';
            $data = $this->M->get_list($sop['w'],'*',$page['offset'].','.$page['limit']);
        }
        $do_status = $this->do_status();
        $js = $this->loadJsCss(array('p:common/common'), 'js', 'admin');

        $this->assign('footjs', $js);
        $this->assign('pagenav',$page);
        $this->assign('data',$data);
        $this->assign('do_status',$this->do_status(2));
        $this->assign('slink',$this->filterLink($sop['p']['ds'],$do_status,['k'=>'ds','allkv'=>-1]));
        return $this->fetch('index');
    }

    /**
     * 筛选处理
     * @return array
     */
    protected function dosearch(){
        //$p['f'] = input('get.f',0);
        $p['ds'] = input('get.ds',-1);
        $is_so = false;
        $w = [];
        //筛选的条件处理
        /*if($p['f']>0){
            $w['type'] = $p['f'];
            $is_so = true;
        }*/
        if($p['ds']>-1){
            $w['do_status'] = $p['ds'];
            $is_so = true;
        }
        $soks = ['id','mobile'];
        $p['sok'] = input('get.sok','');
        $p['sov'] = input('get.sov','');
        if($p['sov'] && $p['sok'] && in_array($p['sok'],$soks)){
            if($p['sok']!='id'){
                $w[$p['sok']] = ['like','%'.$p['sov'].'%'];
            }else{
                $w[$p['sok']] = $p['sov'];
            }
            $is_so = true;

        }

        return ['w'=>$w,'p'=>$p,'is_so'=>$is_so];
    }

    public function info($id=0){
        if(!$id || $id<=0){
            $this->error('访问错误');
        }

        $info = $this->M->get_info(['id'=>$id]);
        if(!$info){
            $this->error('该信息不存在');
        }

        $js = $this->loadJsCss(array('p:common/common'), 'js', 'admin');

        $this->assign('footjs', $js);
        $this->assign('info', $info);
        return $this->fetch('info');
    }
    //处理
    public function do_it(){
        if(!$this->request->isPost()){
            $this->error('访问错误');
        }
        $id = input('post.id',0,'int');
        $do_remark = input('post.do_remark','','trim');
        if(!$id || $id<=0 ||!$do_remark){
            $this->error('数据有误');
        }
        $save_data = [
            'do_user_id'=>session('user_id'),
            'do_user_name'=>session('name'),
            'do_status'=>1,
            'do_remark'=>$do_remark,
            'do_time'=>date('Y-m-d H:i:s')
        ];

        $res = $this->M->update_data(['id'=>$id],$save_data);
        if($res){
            \app\gerent\model\Adminoperlog::instance()->save_data('处理咨询：'.$id);
            $this->success('处理成功',url('Consultation/info',['id'=>$id]));
        }
        $this->error('处理失败');
    }


}