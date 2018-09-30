<?php
/**
 * Created by PhpStorm.
 * User: chenxh
 * Date: 2018/9/25
 * Time: 08:52
 */
namespace app\gerent\controller;
use app\common\model\Imgroups,
    app\common\model\Buser,
    app\gerent\model\Project,
    app\common\library\YunIM,
    app\gerent\library\Imlib;

class Im extends Common{
    /**
     * @var Imgroups
     */
    protected $m;

    public function _initialize($check_login=true)
    {
        parent::_initialize($check_login);
        $this->m = new Imgroups();
    }


    public function index(){
        $where = ['isdel'=>0];

        $count = $this->m->get_count($where);
        $data = $page = $projects = [];
        if($count>0){
            $page = $this->_pagenav($count);
            $field = 'id,p_id,tid,tname,size,createtime';
            $data = $this->m->get_order_list($where,$field,['updatetime'=>'desc'],$page['offset'].','.$page['limit']);
            $p_ids = array2to1($data,'p_id');
            if(!empty($p_ids)){
                $ps = (new Project())->get_list(['id'=>['in',$p_ids]],'id,name');
                $projects = create_kv($ps,'id','name');
            }

        }

        $js = $this->loadJsCss(array('p:common/common','im_index'), 'js', 'admin');

        $this->assign('footjs', $js);
        $this->assign('pagenav',$page);
        $this->assign('data',$data);
        $this->assign('projects',$projects);
        return $this->fetch('index');
    }

    public function add($id=0){

        if($this->request->isPost()){
            return $this->save_imgroup_data();
        }

        $info = $project = $members = [];

        if($id>0){
            $info = $this->m->get_info(['id'=>$id,'isdel'=>0],'id,p_id,tname,announcement,intro,members');
            if(!$info){
                $this->error('该群信息不存在');
            }
            $project = (new Project())->get_info(['id'=>$info['p_id']],'id,name');
            $members = (new Imlib())->get_member_info($info['p_id'],$info['members']);
            //print_r($members);
        }
        $js = $this->loadJsCss(array('p:common/common','im_add'), 'js', 'admin');

        $this->assign('footjs', $js);
        $this->assign('members',$members);
        $this->assign('project',$project);
        $this->assign('info',$info);
        return $this->fetch('add');
    }

    public function edit($id=0){
        if(!$id || $id<=0){
            $this->error('访问错误');
        }

        return $this->add($id);
    }

    public function del($id=0){
        if(!$id || $id<=0){
            $this->error('访问错误');
        }
        $im_info = $this->m->get_info(['id'=>$id,'isdel'=>0],'id,p_id,tid,owner,tname');
        if(!$im_info){
            $this->error('该群数据有误，请检查');
        }

        //解散群api
        if($im_info['tid']){
            $res = (new YunIM())->imobj()->removeGroup($im_info['tid'],$im_info['owner']);
            if($res['code']!=200){
                \extend\Mylog::write([
                    'mesg'=>'解散群失败',
                    'res'=>$res,
                    'info'=>$im_info
                ],'remove_group');
                $this->error('解散群失败：'.json_encode($res));
            }
        }
        $this->m->update_data(['id'=>$id],['isdel'=>1]);
        (new Project())->update_data(['id'=>$im_info['p_id']],['imgroup_id'=>0]);

        $this->success('删除成功');
    }

    public function sendmsg($id=0){
        if(!$id || $id<=0){
            $this->error('访问错误');
        }

        if($this->request->isPost()){
            return $this->send_group_mesg($id);
        }

        $js = $this->loadJsCss(array('p:common/common','im_sendmsg'), 'js', 'admin');

        $this->assign('footjs', $js);
        $this->assign('id', $id);
        return $this->fetch('sendmsg');
    }
    //踢人出群
    public function kikoutgroup(){
        if(!$this->request->isAjax()){
            return ['err'=>1,'msg'=>'访问错误'];
        }
        $id = input('get.id',0,'int');
        $accid = input('get.accid',0,'trim');

        if(!$id || !$accid){
            return ['err'=>1,'msg'=>'参数有误'];
        }

        $im_info = $this->m->get_info(['id'=>$id,'isdel'=>0],'id,tid,owner,size,members');
        if(!$im_info || !$im_info['tid']){
            return ['err'=>1,'msg'=>'该群数据有误，请检查'];
        }
        $yim = new YunIM();
        $res = $yim->imobj()->kickFromGroup($im_info['tid'],$im_info['owner'],$accid);
        if($res['code']==200){
            $members = explode(',',$im_info['members']);
            unset($members[array_search($accid,$members)]);

            $this->m->update_data(
                ['id'=>$id],
                [
                    'size'=>$im_info['size']-1,
                    'members'=>implode(',',array_unique($members)),
                    'updatetime'=>$this->datetime
                ]);

            return ['err'=>0,'msg'=>'踢人成功','res'=>$res];
        }
        return ['err'=>1,'msg'=>'踢人失败','res'=>$res];
    }
    //拉人入群
    public function addintogroup(){
        if(!$this->request->isAjax()){
            return ['err'=>1,'msg'=>'访问错误'];
        }
        $id = input('get.id',0,'int');
        $uid = input('get.uid',0,'int');
        if(!$id || !$uid){
            return ['err'=>1,'msg'=>'参数有误'];
        }
        $im_info = $this->m->get_info(['id'=>$id,'isdel'=>0],'id,tid,owner,size,members');
        if(!$im_info || !$im_info['tid']){
            return ['err'=>1,'msg'=>'该群数据有误，请检查'];
        }
        $info = (new Buser())->get_info(['id'=>$uid,'is_work'=>1,'status'=>1,'isdel'=>0],'id,name,en_name,mobile,head_pic');
        if(!$info){
            return ['err'=>1,'msg'=>'该人员信息不存在，请检查'];
        }
        $yim = new YunIM();
        //
        $res = $yim->createBUserID($info['id']);
        if($res['err']==1){
            return $res;
        }
        $accid = $yim->build_im_userid($info['id'],'b');
        $res = $yim->addIntoGroup($im_info['tid'],$im_info['owner'],[$accid]);
        if($res['code']==200){
            //
            $members = explode(',',$im_info['members']);
            $members[] = $accid;
            $this->m->update_data(
                ['id'=>$id],
                [
                    'size'=>$im_info['size']+1,
                    'members'=>implode(',',array_unique($members)),
                    'updatetime'=>$this->datetime
                ]);

            return ['err'=>0,'msg'=>'加入群聊成功','res'=>$res];
        }
        return ['err'=>1,'msg'=>'加入群聊失败，请检查','res'=>$res];
    }
    //查找人员
    public function search_buser(){
        if(!$this->request->isAjax()){
            return ['err'=>1,'msg'=>'访问错误'];
        }

        $mobile = input('get.mobile','','trim');
        if(!$mobile){
            return ['err'=>1,'msg'=>'请输入手机号码'];
        }
        $list = (new Buser())->get_list(['mobile'=>$mobile,'allow_lg_b'=>1,'isdel'=>0,'is_work'=>1,'status'=>1],'id,name,en_name,mobile,department,post',0);

        return ['err'=>0,'msg'=>'ok','data'=>$list];

    }
    //查找项目成员
    public function get_pmembers(){

    }


    //=======

    protected function send_group_mesg($id){
        $message = input('post.message','','trim');
        if(!$message){
            $this->error('数据有误，请检查');
        }

        $info = $this->m->get_info(['id'=>$id,'isdel'=>0],'id,tid,owner');
        if(!$info || !$info['tid']){
            $this->error('该群数据有误，请检查');
        }


        $res = (new YunIM())->sendTestMsg($info['owner'],$info['tid'],1,$message);
        if($res['code']==200){
            $this->jsalert('发送消息成功',3);
        }
        $this->error('发送消息失败：'.json_encode($res));
    }

    protected function save_imgroup_data(){
        $post = input('post.');
        if(!$post['p_id'] || !$post['tname']){
            $this->error('数据有误');
        }
        if(!$post['announcement']){
            $post['announcement'] = '暂无公告';
        }
        if(isset($post['id']) && $post['id']>0){
            $info = $this->m->get_info(['id'=>$post['id']],'id,tid,owner');
            if(!$info || !$info['tid']){
                $this->error('该群数据有误，请检查');
            }

            $res = (new YunIM())->imobj()->updateGroup($info['tid'],$info['owner'],$post['tname'],[],$post['announcement']);
            if($res['code'] != 200){
                $this->error('更新群信息失败：'.json_encode($res));
            }
        }

        $res = $this->m->save_groups($post);
        if($res){
            $this->success('更新群信息成功',url('Im/index'));
        }
        $this->error('更新群信息失败');
    }

}