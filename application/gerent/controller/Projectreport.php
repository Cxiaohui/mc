<?php
/**
 * Created by PhpStorm.
 * User: chenxh
 * Date: 2018/7/4
 * Time: 14:14
 */
namespace app\gerent\controller;

use app\gerent\model\Project,
    app\common\model\Project as CPject,
    app\common\library\Plog,
    app\gerent\model\Projectreport as Preport,
    app\gerent\model\Projectreportmodify,
    app\common\library\Notice as LN,
    app\gerent\model\Projectreportdoc;

class Projectreport extends Common{
    /**
     * @var Preport
     */
    public $M;
    /**
     * @var Projectreportdoc
     */
    public $MD;

    public function _initialize($check_login=true)
    {
        parent::_initialize($check_login);
        $this->M = new Preport();
        $this->MD = new Projectreportdoc();
    }


    public function index(){


    }

    public function info($p_id){
        if(!$p_id || $p_id<=0){
            $this->error('访问错误');
        }

        $p_w = ['id'=>$p_id,'isdel'=>0];
        if(session('cp_power_tag')!=1){
            $p_w['cpid'] = session('cpid');
        }
        $p_info = (new Project())->get_info($p_w,'id,name,status,type');
        if(!$p_info){
            $this->error('请先完成项目基本信息');
        }
        $is_sejishi = false;
        $is_jingli = false;
        $list = $this->M->get_list(['p_id'=>$p_id,'isdel'=>0]);
        if(!empty($list)){
            $projectreportmodify = new Projectreportmodify();
            foreach($list as $k=>$v){
                $list[$k]['doc_count'] = $this->MD->get_count(['p_id'=>$p_id,'p_rep_id'=>$v['id']]);
                $list[$k]['modifys'] = $projectreportmodify->get_list(['p_id'=>$p_id,'p_rep_id'=>$v['id'],'isdel'=>0],'id,type,content,addtime');
            }

            //t odo 检查当前用户是负责该项目的哪个职能：设计师or项目经理？
            $cpject = new CPject();
            //是设计师
            $is_sejishi = $cpject->is_sejishi($p_id,session('user_id'));

            //是项目经理
            $is_jingli = $cpject->is_jingli($p_id,session('user_id'));
        }
        $js = $this->loadJsCss(array('p:common/common','projectreport'), 'js', 'admin');

        $this->assign('footjs', $js);
        $this->assign('p_info', $p_info);
        $this->assign('data',$list);
        $this->assign('is_sejishi',$is_sejishi);
        $this->assign('is_jingli',$is_jingli);
        $this->assign('status',$this->status());
        $this->assign('report_type',$this->report_type());
        return $this->fetch('info');
    }

    public function add($p_id,$id=0){

        if($this->request->isAjax()){

            return $this->add_report_data($p_id,$id);
        }

        if(!$p_id || $p_id<=0){
            $this->error('访问错误');
        }
        $info = [];
        if($id>0){
            $info = $this->M->get_info(['id'=>$id,'isdel'=>0]);
            if(!$info){
                $this->error('该验收报告不存在');
            }
            $info['docs'] = $this->MD->get_list(['p_rep_id'=>$id,'isdel'=>0],'*',0);
        }

        $uptoken = \app\common\library\Qiniu::get_uptoken(config('qiniu.bucket1'));
        $js = $this->loadJsCss(array('p:common/common',
            'https://unpkg.com/qiniu-js@2/dist/qiniu.min.js','p:md5/md5','projectreport_add'), 'js', 'admin');
        //print_r($cates);
        $this->assign('footjs', $js);
        $this->assign('uptoken', $uptoken);
        $this->assign('info', $info);
        $this->assign('id', $id);
        $this->assign('img_ext', config('img_ext'));
        $this->assign('qn_host', config('qiniu.host'));
        return $this->fetch('add');
    }

    public function edit($p_id,$id=0){
        if(!$p_id || $p_id<=0 || !$id || $id<=0){
            $this->error('访问错误');
        }

        return $this->add($p_id,$id);
    }

    public function del($p_id,$id=0){
        if(!$p_id || $p_id<=0 || !$id || $id<=0){
            $this->error('访问错误');
        }
        //return 'ok';
        $this->M->update_data(['id'=>$id],['isdel'=>1]);
        $this->MD->update_data(['p_rep_id'=>$id],['isdel'=>1]);
        (new Projectreportmodify())->update_data(['p_rep_id'=>$id],['isdel'=>1]);

        $this->success('删除成功');
    }

    public function opers(){

        if(!$this->request->isAjax()){
            return ['err'=>1,'mesg'=>'访问错误'];
        }

        $acts = ['checkreport'];
        $act = input('post.act','','trim');
        if(!$act || !in_array($act,$acts)){
            return ['err'=>1,'mesg'=>'访问错误.'];
        }

        switch ($act){
            case 'checkreport':
                $id = input('post.id',0,'int');
                $value = input('post.value',-1,'int');
                if(!$id || $id<=0 || !in_array($value,[1,2])){
                    return ['err'=>1,'mesg'=>'数据有误'];
                }
                $where = ['id'=>$id,'isdel'=>0];
                $info = $this->M->get_info($where,'id,p_id,name,status');
                if(!$info){
                    return ['err'=>1,'mesg'=>'报告内容不存在'];
                }
                if($info['status'] == $value){
                    return ['err'=>1,'mesg'=>'报告状态无须变更'];
                }
                $cpject = new CPject();
                $update = ['status'=>$value];
                //是设计师
                if($value==1){
                    $is_sejishi = $cpject->is_sejishi($info['p_id'],session('user_id'));
                    if(!$is_sejishi){
                        return ['err'=>1,'mesg'=>'无权限'];
                    }
                    $update['checktime1'] = $this->datetime;
                }
                if($value==2){
                    //是项目经理
                    $is_jingli = $cpject->is_jingli($info['p_id'],session('user_id'));
                    if(!$is_jingli){
                        return ['err'=>1,'mesg'=>'无权限'];
                    }
                    $update['checktime2'] = $this->datetime;
                }

                $res = $this->M->update_data($where,$update);

                if(!$res){
                    return ['err'=>1,'mesg'=>'确认失败'];
                }
                $log_tag = '';
                //$p_info = $cpject->get_info(['id'=>$info['p_id']],'id,desgin_user_id,manager_user_id,owner_user_id');
                //todo 通知项目经理
                if($value==1){
                    $log_tag = '[设计师确认]';
                }

                //todo 通知业主
                if($value==2){
                    $log_tag = '[项目经理确认]';
                }

                //add log
                Plog::add_one($info['p_id'],$id,3,['type'=>1,'id'=>session('user_id'),'name'=>session('name')],$log_tag.'验收报告：'.$info['name']);

                $this->send_notice($info['p_id'],$id,'验收报告确认提醒','验收报告：<<'.$info['name'].">>");

                return ['err'=>0,'mesg'=>'确认成功'];
                break;
        }
    }

    //------------

    protected function status(){
        return [
            0=>'待设计师确认',
            1=>'待项目经理确认',
            2=>'待业主确认',
            3=>'业主已确认',
            4=>'业主修改'
        ];
    }

    protected function report_type(){
        return [
            '',
            1=>'设计师',2=>'项目经理',3=>'业主'
        ];
    }

    protected function add_report_data($p_id,$id=0){
        if(!$p_id || $p_id<=0){
            return ['err'=>1,'mesg'=>'数据丢失'];
        }
        $post['name'] = input('post.name','','trim');
        $post['remark'] = input('post.remark','','trim');
        $docs = input('post.upfiles/a',[]);

        if(!$post['name']){
            return ['err'=>1,'mesg'=>'数据丢失.'];
        }
        if($id==0){
            if(empty($docs)){
                return ['err'=>1,'mesg'=>'数据丢失.'];
            }
        }
        $log_tag = '[添加]';


        if($id>0){

            if(!empty($docs)) {
                $post['status'] = 1;
            }

            $log_tag = '[编辑]';
            $this->M->update_data(['id'=>$id,'p_id'=>$p_id],$post);
        }else{
            $post['status'] = 1;
            $post['p_id'] = $p_id;
            $post['addtime'] = $this->datetime;
            $id = $this->M->add_data($post,true);
        }

        if(!$id){
            return ['err'=>1,'mesg'=>'保存资料失败.'];
        }

        //docs
        if(!empty($docs)){
            $inserts = [];
            foreach($docs as $dc){
                $inserts[] = [
                    'p_id'=>$p_id,
                    'p_rep_id'=>$id,
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
                $this->MD->insert_all($inserts);
            }
        }

        //add log
        Plog::add_one($p_id,$id,3,['type'=>1,'id'=>session('user_id'),'name'=>session('name')],$log_tag.'验收报告：'.$post['name']);

        //todo 添加成功后通知设计师审核
        //todo 编辑后，如果有哪个环结没通知就再发送审核通知到
        $this->send_notice($p_id,$id,'验收报告确认提醒','验收报告：<<'.$post['name'].">>:".$post['remark']);

        return ['err'=>0,'mesg'=>'success','url'=>url('Projectreport/info',['p_id'=>$p_id])];
    }

    protected function send_notice($p_id,$id,$title,$content){
        $info = $this->M->get_info(['id'=>$id],'id,status');
        $p_info = (new CPject())->get_info(['id'=>$p_id],'id,desgin_user_id,manager_user_id,desgin_assistant_user_id,owner_user_id');
        $ndata = [
            'p_id'=>$p_id,
            'type'=>5,
            'target_id'=>$id,
            'user_type'=>1,
            'user_id'=>0,
            'title'=>$title,
            'content'=>$content
        ];
        if($info['status']==0 || $info['status']==4){
            $ndata['user_id'] = $p_info['desgin_user_id'];
        }else if($info['status']==1){
            $ndata['user_id'] = $p_info['manager_user_id'];
        }else if($info['status']==2){
            $ndata['user_type'] = 2;
            $ndata['user_id'] = $p_info['owner_user_id'];
        }

        LN::add($ndata);

    }
}
