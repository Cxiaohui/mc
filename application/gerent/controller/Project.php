<?php
/**
 * Created by PhpStorm.
 * User: chenxh
 * Date: 2018/6/14
 * Time: 16:59
 */
namespace app\gerent\controller;
use app\common\model\Approle,
    think\Loader,
    app\gerent\model\Systable as Admin,
    app\gerent\model\Project as mPJ,
    app\gerent\model\Projectstep as mPJS,
    app\gerent\model\Projectmark as mPJM,
    app\common\model\Projectlog,
    app\common\model\Imgroups,
    app\common\library\YunIM,
    app\common\model\Projectadmin;
class Project extends Common{
    /**
     * @var mPJM
     */
    protected $mpjm;
    /**
     * @var mPJ
     */
    protected $M;

    public function _initialize($check_login=true)
    {
        parent::_initialize($check_login);
        $this->M = new mPJ();
        $this->mpjm = new mPJM();
    }

    public function index(){
        $sop = $this->dosearch();
        $count = $this->M->get_count($sop['w']);
        $data = $page = [];
        if($count>0){
            $page = $this->_pagenav($count);
            $field = 'id,name,status,address,acreage,house_type,decoration_style,type,owner_user_id,owner_name,owner_mobile,owner_address,addtime';
            $data = $this->M->get_list($sop['w'],$field,$page['offset'].','.$page['limit']);
            $mpjs = new mPJS();
            foreach($data as $k=>$da){
                $data[$k]['step_count'] = $mpjs->get_count(['p_id'=>$da['id'],'isdel'=>0]);
            }
        }

        $js = $this->loadJsCss(array('p:common/common'), 'js', 'admin');

        $this->assign('footjs', $js);
        $this->assign('pagenav',$page);
        $this->assign('data',$data);
        $this->assign('sov',$sop['p']['sov']);
        $this->assign('is_so',$sop['is_so']);
        $this->assign('fselect',$this->filterSelect($sop['p']['sok'],['owner_mobile'=>'业主手机号码','name'=>'项目名称','owner_user_id'=>'关联客户ID','id'=>'项目序号']));
        $this->assign('types', $this->p_type());
        return $this->fetch('index');
    }
    protected function dosearch()
    {

        $is_so = false;
        $w = ['isdel' => 0];
        if(session('cp_power_tag')!=1){
            $w['cpid'] = session('cpid');
        }

        $soks = ['id','name','owner_mobile','owner_user_id'];
        $p['sok'] = input('get.sok','');
        $p['sov'] = input('get.sov','');
        if($p['sov'] && $p['sok'] && in_array($p['sok'],$soks)){
            if($p['sok']!='id' && $p['sok']!='owner_user_id'){
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
            $this->error('操作有误');
        }
        $w = ['id'=>$id,'isdel'=>0];
        if(session('cp_power_tag')!=1){
            $w['cpid'] = session('cpid');
        }
        $info = $this->M->get_info($w);
        if(!$info){
            $this->error('该项目信息不存在或已删除');
        }
        $info['marks'] = $this->mpjm->get_info(['p_id'=>$id],'p_remarks,owner_remarks');
        $aroles = (new Approle())->get_list('status=1','id,name,remark',0);
        $admins = (new Admin())->get_admin_list(['status'=>1,'is_work'=>1,'isdel'=>0],'id,name,post');
        $plogs = (new Projectlog())->get_list(['p_id' => $id], 'id,oper_user_name,oper_desc,addtime');

        $this->assign('info', $info);
        $this->assign('plogs', $plogs);
        $this->assign('aroles', create_kv($aroles,'id',['name','remark']));
        $this->assign('admins', create_kv($admins,'id',['name','post']));
        $this->assign('types', $this->p_type());
        return $this->fetch('info');
    }

    public function add($id=0){


        if($this->request->isPost()){
            return $this->save_project_data();
        }
        $info = [];
        $cuser_id = input('get.cuser_id',0,'int');

        if($id>0){
            $w = ['id'=>$id,'isdel'=>0];
            if(session('cp_power_tag')!=1){
                $w['cpid'] = session('cpid');
            }
            $info = $this->M->get_info($w);
            if(!$info){
                $this->error('该项目信息不存在或已删除');
            }
            $info['marks'] = $this->mpjm->get_info(['p_id'=>$id],'p_remarks,owner_remarks');
            $cuser_id = $info['owner_user_id'];
        }

        $js = $this->loadJsCss(array('p:common/common','p:ueditor/ueditor','project'), 'js', 'admin');
        //print_r($cates);
        $this->assign('footjs', $js);
        $this->assign('info', $info);
        $this->assign('cuser_id', $cuser_id);
        $this->assign('ref', get_ref());
        $this->assign('types', $this->p_type());
        //$this->assign('aroles', (new Approle())->get_list('status=1','id,name,remark',0));
        return $this->fetch('add');
    }

    public function edit($id=0){
        if(!$id || $id<=0){
            $this->error('操作有误');
        }

        return $this->add($id);
    }

    public function del($id=0){
        if(!$id || $id<=0){
            $this->error('操作有误');
        }
        $w = ['id'=>$id,'isdel'=>0];
        if(session('cp_power_tag')!=1){
            $w['cpid'] = session('cpid');
        }
        $res = $this->M->update_data($w,['uptime'=>$this->datetime,'isdel'=>1]);
        if($res){
            //解散群
            $groups = (new Imgroups())->get_list(['p_id'=>$id],'id,p_id,tid,owner',0);
            if(!empty($groups)){
                $yim = new YunIM();
                foreach($groups as $gp){
                    $res = $yim->imobj()->removeGroup($gp['tid'],$gp['owner']);
                    if($res['code']!=200){
                        \extend\Mylog::write([
                            'mesg'=>'解散群失败',
                            'res'=>$res,
                            'info'=>$gp
                        ],'remove_group');
                    }
                }

            }
            $this->success('删除成功');
        }
        $this->error('删除失败');
    }


    //========

    protected function save_project_data(){

        $post = input('post.');
        $ref = $post['ref'];
        unset($post['ref']);
        $scene = 'edit';
        if(!isset($post['id'])){
            $scene = 'add';
        }
        $vProject = Loader::validate('project');
        $res = $vProject->scene($scene)->check($post);
        if(!$res){
            $this->error($vProject->getError());
        }else{

            $marks = [
                'p_remarks'=>$post['p_remarks'],
                'owner_remarks'=>$post['owner_remarks'],
                'uptime'=>$this->datetime
            ];
            unset($post['p_remarks'],$post['owner_remarks']);
            $post['cpid'] = session('cpid');
            //print_r($post);
            $post['uptime'] = $this->datetime;
            $p_id = 0;
            //$mpj = new mPJ();
            if(isset($post['id']) && $post['id']>0){
                $p_id = $post['id'];
                $this->M->update_data(['id'=>$p_id],$post);
            }else{
                $post['addtime'] = $this->datetime;
                $p_id = $this->M->add_data($post,true);
            }

            if(!$p_id){
                $this->error('保存项目信息失败');
            }
            //备注信息
            $w = ['p_id'=>$p_id];
            //$mpjm = new mPJM();
            if($this->mpjm->get_count($w)){
                $this->mpjm->update_data($w,$marks);
            }else{
                $marks['p_id'] = $p_id;
                $this->mpjm->add_data($marks);
            }
            //项目与负责人信息
            $p_admins = $this->project_admin(1);
            $p_admin_datas = [];
            foreach($p_admins as $pk=>$pad){
                $p_admin_datas[] = ['type'=>$pk,'p_id'=>$p_id,'b_user_id'=>$post[$pad]];
            }

            (new Projectadmin())->save_data($p_admin_datas);
            //
            if($scene=='add'){
                // 创建项目成功后，创建群聊天
                \think\Queue::later(2,'app\gerent\job\Projectimgroup',['p_id'=>$p_id,'act'=>'add']);

                $this->success('保存成功',url('Projectstep/add').'?p_id='.$p_id.'&ref='.base64_encode($ref));
            }else{
                //编辑由IM管理中进行人工操作-20180924
                // 编辑后检查人员是否有变化，有则更新群的信息
                //\think\Queue::later(2,'app\gerent\job\Projectimgroup',['p_id'=>$p_id,'act'=>'edit']);

                if($ref){
                    $this->success('保存成功',$ref);
                }

                $this->success('保存成功',url('Project/index'));
            }

        }
    }

}