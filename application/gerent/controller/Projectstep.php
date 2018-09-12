<?php
/**
 * Created by PhpStorm.
 * User: chenxh
 * Date: 2018/6/18
 * Time: 21:55
 */
namespace app\gerent\controller;
use app\gerent\model\Project as mPJ,
    app\gerent\model\Projectdoc,
    app\gerent\model\Projectstep as mPJS,
    app\common\library\Plog,
    app\common\model\Projectlog,
    app\common\library\Notice as LN,
    app\common\model\Stepmodify;

class Projectstep extends Common{


    /**
     * @var mPJ
     */
    protected $mpj;
    /**
     * @var mPJS
     */
    protected $M;

    public function _initialize($check_login=true)
    {
        parent::_initialize($check_login);
        $this->M = new mPJS();
        $this->mpj = new mPJ();
    }


    public function index(){


    }

    public function info(){
        $p_id = input('get.p_id',0,'int');
        if(!$p_id || $p_id<=0){
            $this->error('访问错误');
        }
        $p_w = ['id'=>$p_id,'isdel'=>0];
        if(session('cp_power_tag')!=1){
            $p_w['cpid'] = session('cpid');
        }
        if(!$this->mpj->get_count($p_w)){
            $this->error('请先完成项目基本信息');
        }
        $s_w = ['p_id'=>$p_id,'isdel'=>0];
        /*if(!$this->M->get_count($s_w)){
            $this->error('该项目没有阶段信息，请先添加');
        }*/

        $p_info = $this->mpj->get_info($p_w,'id,name,status,type');

        $s_w['type'] = 1;
        $type1_steps_list = $this->M->get_step_list($s_w,'*');
        $type1_steps = $this->create_step_info($p_id,$type1_steps_list);


        $s_w['type'] = 2;
        $type2_steps_list = $this->M->get_step_list($s_w,'*');
        $type2_steps = $this->create_step_info($p_id,$type2_steps_list);

        $count = count($type1_steps) + count($type2_steps);
        //print_r($type1_steps);
        //print_r($type2_steps);
        $this->assign('p_info', $p_info);
        $this->assign('type1_steps', $type1_steps);
        $this->assign('type2_steps', $type2_steps);
        $this->assign('count', $count);
        $this->assign('status', $this->status());
        return $this->fetch('info');
    }

    public function add($act='add'){

        $p_id = input('get.p_id',0,'int');

        if($this->request->isAjax()){
            return $this->save_step_data($p_id);
        }

        if(!$p_id || $p_id<=0){
            $this->error('访问错误');
        }
        //
        $p_w = ['id'=>$p_id,'isdel'=>0];
        if(session('cp_power_tag')!=1){
            $p_w['cpid'] = session('cpid');
        }
        if(!$this->mpj->get_count($p_w)){
            $this->error('请先完成项目基本信息');
        }
        //
        $s_w = ['p_id'=>$p_id,'isdel'=>0];
        if($act=='add' && $this->M->get_count($s_w)){
            $this->error('该项目已经有阶段信息了');
        }
        $type1_steps = $type2_steps = [];
        if($act == 'edit'){
            $s_w['type'] = 1;
            $type1_steps = $this->M->get_step_list($s_w,'id,pid,type,name,plan_time');
            $type1_steps = $this->create_type_steps($type1_steps);
            $s_w['type'] = 2;
            $type2_steps = $this->M->get_step_list($s_w,'id,pid,type,name,plan_time');
            $type2_steps = $this->create_type_steps($type2_steps);

        }
        //print_r($type1_steps);
        //print_r($type2_steps);


        $p_info = $this->mpj->get_info($p_w,'id,name,status,type');
        $js = $this->loadJsCss(array('p:common/common','projectstep'), 'js', 'admin');
        //print_r($cates);
        $this->assign('footjs', $js);
        $this->assign('p_info', $p_info);
        $this->assign('p_id', $p_id);
        $this->assign('act', $act);
        $this->assign('mindate', date('Y/m/d'));
        $this->assign('type1_steps', $type1_steps);
        $this->assign('type2_steps', $type2_steps);
        return $this->fetch('add');

    }

    public function edit(){

        return $this->add('edit');

    }

    public function del(){
        $id = input('post.id',0,'int');
        $p_id = input('get.p_id',0,'int');

        if(!$id || $id<=0 || !$p_id || $p_id<=0){
            return ['err'=>1,'mesg'=>'数据丢失'];
        }
        $m_w = ['id'=>$id,'p_id'=>$p_id,'isdel'=>0];

        $count = $this->M->get_count($m_w);
        if($count<=0){
            return ['err'=>1,'mesg'=>'操作有误'];
        }
        $m_w['status'] = ['gt',0];
        if($this->M->get_count($m_w)>0){
            return ['err'=>1,'mesg'=>'该阶段正在进行，不能删除'];
        }
        unset($m_w['status']);
        $this->M->update_data($m_w,['isdel'=>1]);
        unset($m_w['id']);
        $m_w['pid'] = $id;
        if($this->M->get_count($m_w)){
            $this->M->update_data($m_w,['isdel'=>1]);
        }

        return ['err'=>0,'mesg'=>'success'];
    }


    //========

    public function docedit($stepid=0){

        if($this->request->isAjax()){

            return $this->add_stepdoc_data($stepid);
        }

        if(!$stepid || $stepid<=0){
            $this->error('操作有误');
        }

        $info = $this->M->get_info(['id'=>$stepid,'isdel'=>0],'id,name');
        if(!$info){
            $this->error('该阶段不存在');
        }

        $uptoken = \app\common\library\Qiniu::get_uptoken(config('qiniu.bucket1'));

        $js = $this->loadJsCss(array('p:common/common',
            'https://unpkg.com/qiniu-js@2/dist/qiniu.min.js','p:md5/md5','stepdocs'), 'js', 'admin');
        //print_r($cates);
        $this->assign('footjs', $js);
        $this->assign('info', $info);
        $this->assign('uptoken', $uptoken);
        return $this->fetch('docedit');
    }

    public function docinfo($stepid=0){
        if(!$stepid || $stepid<=0){
            $this->error('操作有误');
        }
        $info = $this->M->get_info(['id'=>$stepid,'isdel'=>0],'*');
        if(!$info){
            $this->error('该阶段不存在');
        }
        $docs = (new Projectdoc())->get_list(['p_id'=>$info['p_id'],'p_step_id'=>$stepid,'isdel'=>0],'id,file_type,file_name,file_path',0);
        $p_info = $this->mpj->get_info(['id'=>$info['p_id']],'id,name');

        $modify = (new Stepmodify())->get_list(['p_id'=>$info['p_id'],'p_step_id'=>$stepid],'img,content,addtime');
        foreach($modify as $k=>$md){
            if($md['img']){
                $modify[$k]['img'] = config('qiniu.host').$md['img'];
            }
        }

        $plogs = (new Projectlog())->get_list(['p_id' => $info['p_id'],'p_step_id'=>$stepid], 'id,oper_user_name,oper_desc,addtime');

        $js = $this->loadJsCss(['p:common/common'], 'js', 'admin');
        //print_r($cates);
        $this->assign('footjs', $js);
        $this->assign('info', $info);
        $this->assign('p_info', $p_info);
        $this->assign('status', $this->status());
        $this->assign('docs', $docs);
        $this->assign('modify', $modify);
        $this->assign('plogs', $plogs);
        $this->assign('img_ext', config('img_ext'));
        $this->assign('qn_host', config('qiniu.host'));
        return $this->fetch('docinfo');
    }
    //=========

    protected function create_step_info($p_id,$steps_list){
        $type_steps = [];
        if(!empty($steps_list)){
            create_tree2($steps_list,0,$type_steps);
            $modify = (new Stepmodify());
            foreach($type_steps as $k=>$sp){
                if($sp['pid']==0){
                    $type_steps[$k]['sub_count'] = $this->M->get_count(['pid'=>$sp['id'],'isdel'=>0]);
                }else{
                    $type_steps[$k]['sub_count'] = 0;
                }
                $type_steps[$k]['c_oper'] = '';
                if($sp['status']==4){
                    $type_steps[$k]['c_oper'] = '通过';
                }else{
                    $lastoper = $modify->get_list(['p_id'=>$p_id,'p_step_id'=>$sp['id']],'content',1);
                    if(!empty($lastoper)){
                        $type_steps[$k]['c_oper'] = $lastoper[0]['content'];
                    }
                }

            }
        }
        return $type_steps;
    }

    protected function create_type_steps($steps){
        if(!empty($steps)){
            foreach($steps as $k=>$v){
                $steps[$k]['plan_time'] = explode('|',$v['plan_time']);
                if(!isset($steps[$k]['plan_time'][1])){
                    $steps[$k]['plan_time'][1] = '';
                }
            }
            return create_level_tree($steps);
        }
        return [];
    }

    protected function status(){
        return [
            0=>'未开始',
            1=>'进行中',
            2=>'待客户确认',
            3=>'已驳回',
            4=>'已完成'
        ];
    }

    protected function add_stepdoc_data($stepid){

        if(!$stepid || $stepid<=0){
            return ['err'=>1,'mesg'=>'数据丢失'];
        }

        $usermesg = input('post.usermesg','','trim');
        $docs = input('post.upfiles/a',[]);

        if(!$usermesg || empty($docs)){
            return ['err'=>1,'mesg'=>'数据丢失.'];
        }
        $step_info = $this->M->get_info(['id'=>$stepid],'id,p_id,pid,type,status,b_user_mesg');
        if(!$step_info){
            return ['err'=>1,'mesg'=>'数据丢失..'];
        }


        $update = [
            'b_user_id'=>session('user_id'),
            'b_user_name'=>session('name'),
            'b_user_mesg'=>$usermesg,//$step_info['b_user_mesg'].PHP_EOL.
            'uptime'=>$this->datetime
        ];
        if($step_info['status']==0){
            $update['status'] = 2;
        }

        $res = $this->M->update_data(['id'=>$stepid],$update);
        if(!$res){
            return ['err'=>1,'mesg'=>'数据丢失'];
        }
        if($step_info['pid']>0){
            $this->M->update_data(['id'=>$step_info['pid']],['status'=>1,'uptime'=>$this->datetime]);
        }

        $inserts = [];
        $mdc = new Projectdoc();
        $has_primary = $mdc->get_count(['p_id'=>$step_info['p_id'],'p_step_id'=>$stepid,'is_primary'=>1,'isdel'=>0]);
        $is_primary = 0;
        foreach($docs as $k=>$dc){
            if(!$has_primary){
                $is_primary = $k==0?1:0;
            }

            $inserts[] = [
                'p_id'=>$step_info['p_id'],
                'p_step_id'=>$stepid,
                'is_primary'=>$is_primary,
                //'file_type'=>strtolower(pathinfo($dc['filename'])['extension']),
                'file_type'=>$dc['ext'],
                'file_name'=>$dc['filename'],
                'file_path'=>$dc['key'],
                'file_hash'=>$dc['hash'],
                'qiniu_status'=>1,
                'addtime'=>$this->datetime
            ];
        }
        if(!empty($inserts)){
            $mdc->insert_all($inserts);
        }
        //add log
        Plog::add_one($step_info['p_id'],$stepid,$step_info['type'],['type'=>1,'id'=>session('user_id'),'name'=>session('name')],'[上传]'.$usermesg);
        $p_info = $this->mpj->get_info(['id'=>$step_info['p_id']],'id,owner_user_id');
        //todo 保存后发送相关通知
        $ndata = [
            'p_id'=>$step_info['p_id'],
            'type'=>$step_info['type']==1?1:6,
            'target_id'=>$stepid,
            'user_type'=>2,
            'user_id'=>$p_info['owner_user_id'],
            'title'=>'项目阶段确认提醒',
            'content'=>$usermesg
        ];
        LN::add($ndata);

        return ['err'=>0,'mesg'=>'success','url'=>url('Projectstep/docinfo',['stepid'=>$stepid])];
    }

    protected function save_step_data($p_id){
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
              //主阶段
              $save = [
                  'id'=>$dali['id'],
                  'p_id'=>$p_id,
                  'pid'=>0,
                  'level'=>1,
                  'type'=>$da['type'],
                  'name'=>$dali['name'],
                  'step_sort'=>$k,
                  'plan_time'=>$dali['time1'].'|'.$dali['time2']
              ];
              $step_id = $this->M->save_data($save);
              if(!$step_id){
                  continue;
              }
              if(empty($dali['subs'])){
                  continue;
              }
              foreach($dali['subs'] as $sk=>$sub){
                  //子阶段
                  $save = [
                      'id'=>$sub['id'],
                      'p_id'=>$p_id,
                      'pid'=>$step_id,
                      'level'=>2,
                      'type'=>$da['type'],
                      'name'=>$sub['name'],
                      'step_sort'=>$sk,
                      'plan_time'=>$sub['time1'].'|'.$sub['time1']
                  ];
                  $sub_step_id = $this->M->save_data($save);
                  if(!$sub_step_id){
                      continue;
                  }
              }
          }
        }

        return ['err'=>0,'mesg'=>'success'];
    }
}