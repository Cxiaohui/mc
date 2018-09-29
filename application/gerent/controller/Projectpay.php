<?php
/**
 * Created by PhpStorm.
 * User: chenxh
 * Date: 2018/6/24
 * Time: 12:37
 */
namespace app\gerent\controller;
use app\gerent\model\Projectpay as mpy,
    app\gerent\model\Project as mPJ;

class Projectpay extends Common{
    /**
     * @var mpy
     */
    protected $M;
    /**
     * @var mPJ
     */
    protected $mpj;
    public function _initialize($check_login=true)
    {
        parent::_initialize($check_login);
        $this->M = new mpy();
        $this->mpj = new mPJ();
    }

    public function info($p_id=0){
        if(!$p_id || $p_id<=0){
            $this->error('访问错误');
        }
        $p_w = ['id'=>$p_id,'isdel'=>0];
        if(session('cp_power_tag')!=1){
            $p_w['cpid'] = session('cpid');
        }
        $p_info = $this->mpj->get_info($p_w,'id,name,status,type');
        if(!$p_info){
            $this->error('请先完成项目基本信息');
        }

        $w = ['p_id'=>$p_id,'p_type'=>1,'isdel'=>0];
        $type1 = $this->M->get_list($w,'*');
        $w['p_type'] = 2;
        $type2 = $this->M->get_list($w,'*');
        $count = count($type1) + count($type2);

        $js = $this->loadJsCss(array('p:common/common'), 'js', 'admin');

        $this->assign('footjs', $js);
        $this->assign('p_info', $p_info);
        $this->assign('type1', $type1);
        $this->assign('type2', $type2);
        $this->assign('count', $count);
        return $this->fetch('info');
    }

    public function step_info($p_id=0,$id=0){
        if(!$p_id || $p_id<=0 || !$id || $id<=0){
            $this->error('访问错误');
        }

        if($this->request->isPost()){
            return $this->save_step_info($p_id,$id);
        }

        $p_w = ['id'=>$p_id,'isdel'=>0];
        if(session('cp_power_tag')!=1){
            $p_w['cpid'] = session('cpid');
        }
        $p_info = $this->mpj->get_info($p_w,'id,name,status,type');
        if(!$p_info){
            $this->error('请先完成项目基本信息');
        }
        $info = $this->M->get_info(['id'=>$id,'p_id'=>$p_id,'isdel'=>0]);
        if(!$info){
            $this->error('该信息不存在');
        }
        $js = $this->loadJsCss(array('p:common/common','projectpay_step_info'), 'js', 'admin');
        //print_r($cates);
        $this->assign('footjs', $js);
        $this->assign('info', $info);
        $this->assign('p_info', $p_info);
        return $this->fetch('step_info');
    }

    public function add($p_id=0,$act='add'){
        if($this->request->isAjax()){
            return $this->save_pay_data($p_id);
        }
        if(!$p_id || $p_id<=0){
            $this->error('访问错误');
        }
        $p_w = ['id'=>$p_id,'isdel'=>0];
        if(session('cp_power_tag')!=1){
            $p_w['cpid'] = session('cpid');
        }
        $p_info = $this->mpj->get_info($p_w,'id,name,status,type');
        if(!$p_info){
            $this->error('请先完成项目基本信息');
        }
        $type1 = $type2 = [];
        if($act = 'edit'){
            $w = ['p_id'=>$p_id,'p_type'=>1];
            $type1 = $this->M->get_list($w,'id,name,payable,payable_time');
            $w['p_type'] = 2;
            $type2 = $this->M->get_list($w,'id,name,payable,payable_time');
        }

        $js = $this->loadJsCss(array('p:common/common','projectpay'), 'js', 'admin');
        //print_r($cates);
        $this->assign('footjs', $js);
        $this->assign('p_info', $p_info);
        $this->assign('p_id', $p_id);
        $this->assign('mindate', date('Y-m-d'));
        $this->assign('type1', $type1);
        $this->assign('type2', $type2);
        return $this->fetch('add');
    }

    public function edit($p_id=0){
        if(!$p_id || $p_id<=0){
            $this->error('访问错误');
        }
        
        return $this->add($p_id,'edit');
    }

    public function del($id=0){
        if(!$id || $id<=0){
            $this->error('访问错误');
        }

        $info = $this->M->get_info(['id'=>$id,'isdel'=>0],'id,paied');
        if(!$info){
            $this->error('该付款记录不存在或已删除');
        }
        if($info['paied']>0){
            $this->error('该信息已有付款记录，不能删除');
        }
        //return 'ok';
        $res = $this->M->update_data(['id'=>$id,'isdel'=>0],['isdel'=>1]);
        if($res){
            $this->success('删除成功');
        }
        $this->error('删除失败');
    }


    // ===========

    protected function save_step_info($p_id,$id){

        $post['paied'] = input('post.paied','','trim');
        $post['paied_time'] = input('post.paied_time','','trim');
        $post['remark'] = input('post.remark','','trim');

        if(!$post['paied'] || !$post['paied_time']){
            $this->error('数据有误');
        }


        $res = $this->M->update_data(['id'=>$id,'p_id'=>$p_id],$post);
        if($res){
            $this->success('保存成功');
        }
        $this->error('保存失败');
    }

    protected function save_pay_data($p_id){
        if(!$p_id || $p_id<=0){
            return ['err'=>1,'mesg'=>'数据丢失'];
        }
        $data = input('post.data/a',[]);
        //print_r($data);exit;
        if(empty($data)){
            return ['err'=>1,'mesg'=>'数据丢失'];
        }
        foreach($data as $da){

            if(empty($da['li'])){
                continue;
            }
            foreach($da['li'] as $k=>$dali){

                $save = [
                    'id'=>$dali['id'],
                    'p_id'=>$p_id,
                    'p_type'=>$da['type'],
                    'sort'=>$k,
                    'name'=>$dali['name'],
                    'payable'=>$dali['payable'],
                    'payable_time'=>$dali['time']
                ];
                $this->M->save_data($save);
            }
        }

        return ['err'=>0,'mesg'=>'success'];
    }
}