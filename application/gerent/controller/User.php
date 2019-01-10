<?php
/**
 * Created by PhpStorm.
 * User: chenxh
 * Date: 2018/6/12
 * Time: 09:17
 */
namespace app\gerent\controller;
use extend\Str,
    think\Loader,
    app\gerent\model\Project as mPJ,
    app\gerent\model\Company,
    app\common\library\YunIM,
    app\gerent\model\User as mUser;
class User extends Common{
    /**
     * @var mUser
     */
    protected $Um;

    public function _initialize($check_login=true)
    {
        parent::_initialize($check_login);
        $this->Um = new mUser();
    }

    public function index(){

        $sop = $this->dosearch();
        $count = $this->Um->get_count($sop['w']);
        $data = $page = [];
        if($count>0){
            $page = $this->_pagenav($count);
            $field = '*';
            $data = $this->Um->get_list($sop['w'],$field,$page['offset'].','.$page['limit']);
            $mpj = new mPJ();
            foreach($data as $k=>$da){
                $data[$k]['p_count'] = $mpj->get_count(['owner_user_id'=>$da['id'],'isdel'=>0]);
            }
        }

        $js = $this->loadJsCss(array('p:common/common'), 'js', 'admin');

        $this->assign('footjs', $js);
        $this->assign('pagenav',$page);
        $this->assign('data',$data);
        $this->assign('sov',$sop['p']['sov']);
        $this->assign('is_so',$sop['is_so']);
        $this->assign('fselect',$this->filterSelect($sop['p']['sok'],['mobile'=>'手机号码','uname'=>'姓名','id'=>'序号']));
        return $this->fetch('index');
    }

    protected function dosearch(){

        $is_so = false;
        $w = ['isdel'=>0];
        if(session('cp_power_tag')!=1){
            $w['cpid'] = session('cpid');
        }

        $soks = ['id','uname','mobile'];
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

    public function add($id=0){
        if($this->request->isPost()){
            return $this->save_user_data();
        }
        $info = [];
        if($id>0){
            $where = ['id'=>$id,'isdel'=>0];
            $info = $this->Um->get_info($where);
        }
        $cpw = '1=1';
        if(session('cp_power_tag')!=1){
            $cpw = ['id'=>session('cpid')];
        }
        $js = $this->loadJsCss(array('p:common/common',
            //'p:cate/jquery.cate',
            'p:webuper/js/webuploader','singleUp','cuser_add'), 'js', 'admin');
        $this->assign('footjs', $js);
        $this->assign('info', $info);
        $this->assign('company', (new Company())->get_list($cpw,'id,name',0));
        return $this->fetch('add');
    }

    public function edit($id=0){
        if(!$id || $id<=0){
            $this->error('操作有误');
        }

        //$this->_is_super_or_me($id);
        return $this->add($id);
    }

    public function del($id){
        if(!$id || $id<=0){
            $this->error('操作有误');
        }
        //$res=1;
        $res = $this->Um->del_user($id);
        if($res){
            \app\gerent\model\Adminoperlog::instance()->save_data('删除C用户：'.$id);
            $this->success('删除成功');
        }
        $this->error('删除有误');
    }

    protected function save_user_data(){
        $post = $this->request->post();
        unset($post['file']);
        $scene = 'edit';
        if(!isset($post['id'])){
            $post['lgstat'] = Str::randString(5);
            $scene = 'add';
        }
        //$ref = $post['ref'];
        //print_r($post);
        $vAdmin = Loader::validate('users');
        $res = $vAdmin->scene($scene)->check($post);
        if(!$res){
            $this->error($vAdmin->getError());
        }else{
            unset($post['ref']);

            if(isset($post['id']) && $post['id']>0){
                $exists = $this->Um->get_count(['id'=>['neq',$post['id']],'mobile'=>$post['mobile'],'isdel'=>0]);
                if($exists>0){
                    $this->error('新的手机号已经存在，请换一个');
                }
            }

            $s_res = $this->Um->save_user_data($post);
            if($s_res){
                // 如果是编辑，且用户存在im_token,且用户更新了名称/头像，则都要更新到IM中-20180924
                if($scene=='edit'){
                    $res = (new YunIM())->updateCUserinfo($post['id']);
                    \extend\Mylog::write([
                        'ref'=>'gerent',
                        'user_id'=>$post['id'],
                        'res'=>$res
                    ],'c_user_iminfo');
                }


                \app\gerent\model\Adminoperlog::instance()->save_data('编辑C用户：'.$post['mobile']);
                $this->success('保存成功',url('User/index'));
            }
            $this->error('保存失败');
        }

    }
}