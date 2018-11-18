<?php
/**
 * Created by PhpStorm.
 * User: xiaohui
 * Date: 2017-06-13
 * Time: 19:11
 */
namespace app\gerent\controller;
use extend\Str,
    think\Loader,
    //app\cool_admin\validate\Admin as vAdmin,
    app\gerent\model\Company,
    app\gerent\model\Teams,
    app\gerent\model\Project,
    app\common\model\Projectadmin,
    app\common\library\YunIM,
    app\gerent\model\Systable as mSystable;

class Sysuser extends Common{
    /**
     * @var mSystable
     */
    protected $admin_model;
    /**
     * @var Project
     */
    protected $pm;

    /**
     * @var Projectadmin
     */
    protected $apm;

    public function _initialize($check_login=true)
    {
        parent::_initialize($check_login);
        $this->admin_model = new mSystable();
        $this->pm = new Project();
        $this->apm = new Projectadmin();
    }

    public function index(){

        $sop = $this->dosearch();
        $count = $this->admin_model->get_admin_count($sop['w']);
        $data = $page = [];
        if($count>0){
            $page = $this->_pagenav($count);
            $field = 'id,log,role,name,sex,is_work,status,mobile,allow_lg_b,b_power_tag,head_pic,team_id,department,create_time,lgtime,post';
            $data = $this->admin_model->get_admin_list($sop['w'],$field,$page['offset'].','.$page['limit']);
            foreach($data as $k=>$da){
                $data[$k]['projects'] = $this->getadminprojects($da['id']);
            }
        }
        //print_r($page);
        $departs = $this->admin_model->get_depart_list(['isdel'=>0],'id,name',0,true);
        $js = $this->loadJsCss(array('p:common/common'), 'js', 'admin');

        $this->assign('footjs', $js);
        $this->assign('pagenav',$page);
        $this->assign('data',$data);
        $this->assign('user_stat',$this->user_stat());
        $this->assign('log_stat',$this->log_stat());
        $this->assign('b_powers', $this->b_powers());
        $this->assign('teams', (new Teams())->get_list('1=1','id,name',0,true));
        $this->assign('sov',$sop['p']['sov']);
        $this->assign('is_so',$sop['is_so']);
        $this->assign('team_id',$sop['p']['team_id']);
        $this->assign('flink',$this->filterLink($sop['p']['f'],$departs));
        $this->assign('fselect',$this->filterSelect($sop['p']['sok'],['mobile'=>'手机号码','name'=>'姓名','id'=>'序号']));
        return $this->fetch('index');
    }

    protected function getadminprojects($user_id){
        $pids = $this->apm->get_list(['b_user_id'=>$user_id],'p_id',0);
        if(empty($pids)){
            return [];
        }
        $pids = array2to1($pids,'p_id');
        //print_r($pids);
        return $this->pm->get_list(['id'=>['in',$pids],'isdel'=>0],'id,name',0);
    }

    protected function dosearch(){
        $p['f'] = input('get.f',0);
        $p['team_id'] = input('get.team_id',0);
        $is_so = false;
        $w = ['isdel'=>0];
        if(session('cp_power_tag')!=1){
            $w['cpid'] = session('cpid');
        }
        //筛选的条件处理
        if($p['f']>0){
            $w = ['depart_id'=>$p['f']];
            $is_so = true;
        }
        $soks = ['id','name','mobile'];
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
        if($p['team_id']>0){
            $w['team_id'] = $p['team_id'];
        }


        return ['w'=>$w,'p'=>$p,'is_so'=>$is_so];
    }

    public function add($id=0){
        if($this->request->isPost()){
            return $this->save_user_data();
        }
        $info = [];
        if($id>0){
            $where = ['id'=>$id,'isdel'=>0];
            $info = $this->admin_model->get_admin_info($where);
        }
        $roles = $this->admin_model->get_role_list(['status'=>1],'id,name,remark');
        $departs = $this->admin_model->get_depart_list(['isdel'=>0],'id,name',0);
        $cpw = '1=1';
        if(session('cp_power_tag')!=1){
            $cpw = ['id'=>session('cpid')];
        }
        //print_r($departs);
        $js = $this->loadJsCss(array('p:common/common',
            //'p:cate/jquery.cate',
            'p:webuper/js/webuploader','singleUp','user_add'), 'js', 'admin');
        $this->assign('footjs', $js);
        $this->assign('info', $info);
        $this->assign('roles', $roles);
        $this->assign('departs', $departs);
        $this->assign('b_powers', $this->b_powers());
        $this->assign('company', (new Company())->get_list($cpw,'id,name',0));
        $this->assign('teams', (new Teams())->get_list('1=1','id,name',0));
        return $this->fetch('add');
    }

    public function edit($id=0){
        if(!$id || $id<=0){
            $this->error('操作有误');
        }

        $this->_is_super_or_me($id);
        return $this->add($id);
    }

    public function del($id){
        if(!$id || $id<=0){
            $this->error('操作有误');
        }
        //$res=1;
        $res = $this->admin_model->del_admin($id);
        if($res){
            \app\gerent\model\Adminoperlog::instance()->save_data('删除B用户：'.$id);
            $this->success('删除成功');
        }
        $this->error('删除有误');
    }

    public function changepwd($id){

        if(!$id || $id<=0){
            $this->error('操作有误');
        }

        $this->_is_super_or_me($id);

        $user = $this->admin_model->get_admin_info(['id'=>$id],'id,name');
        if (empty($user)) {
            $this->error('用户不存在');
        }
        if($this->request->isPost()){
            $pwd = input('post.pwd','','trim');
            $pwd1 = input('post.pwd1','','trim');
            if (!$pwd || !$pwd1 || $pwd != $pwd1) {
                $this->error('填写的密码有误，请确认');
            }
            $stat = Str::randString(5);
            $pwd = create_pwd($pwd,$stat);
            $res = $this->admin_model->update_admin(['id'=>$id],['pwd'=>$pwd,'stat'=>$stat]);
            if($res){
                \app\gerent\model\Adminoperlog::instance()->save_data('修改B用户：'.$id.'密码');
                $this->success('修改密码成功',base64_decode(input('post.ref')));
            }
            $this->error('修改密码失败，请确认');
        }

        $js = $this->loadJsCss(array('p:common/common', 'user_pwd'), 'js', 'admin');

        $this->assign('footjs', $js);
        $this->assign('user', $user);
        $this->assign('ref', get_ref(1));
        return $this->fetch('changepwd');
    }

    /**
     * 登录控制
     */
    public function lgcontrl($id = 0, $v = 0) {
        if (!$id || $id <= 0) {
            $this->error('操作有误');
        }
        $vals = array('0', '1');
        if (!isset($vals[$v])) {
            $this->error('参数错误');
        }

        if (!$this->admin_model->get_admin_count(['id' => $id])) {
            $this->error('用户不存在');
        }
        $res = $this->admin_model->update_admin(['id'=>$id],['status' => $vals[$v]]);

        if (!$res) {
            $this->error('设置失败');
        }
        \app\gerent\model\Adminoperlog::instance()->save_data('设置B用户：'.$id.'，登录控制：'.$vals[$v]);
        $this->success('设置成功');

    }

    //========================================================

    protected function user_stat(){
        return [
            0=>'<span class="label">离职</span>',
            1=>'<span class="label label-success">在职</span>'
        ];
    }

    protected function log_stat(){
        return [
            0=>'<span class="label label-important">禁止登录</span>',
            1=>'<span class="label label-success">允许登录</span>'
        ];
    }

    protected function save_user_data(){
        $post = $this->request->post();
        unset($post['file']);
        $scene = 'edit';
        if(!isset($post['id'])){
            $post['stat'] = Str::randString(5);
            $scene = 'add';
        }
        $ref = $post['ref'];
        //print_r($post);
        $vAdmin = Loader::validate('admin');
        $res = $vAdmin->scene($scene)->check($post);
        if(!$res){
            $this->error($vAdmin->getError());
        }else{
            unset($post['ref']);
            if($post['role']!=''){
                $tmp = explode('-',$post['role']);
                $post['role_id'] = $tmp[0];
                $post['role'] = $tmp[1];
            }
            if($post['department']!=''){
                $tmp = explode('-',$post['department']);
                $post['depart_id'] = $tmp[0];
                $post['department'] = $tmp[1];
            }
            if($post['is_join_team']>0){
                $post['team_id'] = $post['is_join_team'];
            }else{
                $post['team_id'] = 0;
            }

            $s_res = $this->admin_model->save_admin_data($post);
            if($s_res){
                // 如果是编辑，且用户存在im_token,且用户更新了名称/头像，则都要更新到IM中-20180924

                if($scene == 'edit'){
                    $res = (new YunIM())->updateBUserinfo($post['id']);
                    \extend\Mylog::write([
                        'ref'=>'gerent',
                        'user_id'=>$post['id'],
                        'res'=>$res
                    ],'b_user_iminfo');
                }

                \app\gerent\model\Adminoperlog::instance()->save_data('编辑B用户：'.$post['name']);
                $this->success('保存成功',url('Sysuser/index'));
            }
            $this->error('保存失败');
        }

    }
}