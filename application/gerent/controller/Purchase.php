<?php
/**
 * Created by PhpStorm.
 * User: chenxh
 * Date: 2018/9/24
 * Time: 22:21
 */
namespace app\gerent\controller;
use app\gerent\model\Project,
    app\common\library\Plog,
    app\common\library\Notice as LN,
    app\common\model\Purchase as mPur,
    app\common\model\Projectlog,
    app\common\model\Purchasemodify,
    app\common\model\Purchasedoc;

//采购
class Purchase extends Common{
    /**
     * @var mPur
     */
    protected $m;

    public function _initialize($check_login=true)
    {
        parent::_initialize($check_login);
        $this->m = new mPur();
    }


    public function index(){}

    public function info($p_id=0){
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


        $list = $this->m->get_list(['p_id'=>$p_id,'isdel'=>0],'*',0);
        if(!empty($list)){
            //$purchasemodify = new Purchasemodify();
            $purchasedoc = new Purchasedoc();
            $Projectlog = new Projectlog();
            foreach($list as $k=>$v){

                $list[$k]['docs'] = $purchasedoc->get_order_list(['p_id'=>$p_id,'pu_id'=>$v['id'],'isdel'=>0],'id,file_type,file_name,file_path,addtime',['seq'=>'asc'],0);
                $list[$k]['doc_count'] = count($list[$k]['docs']);
                $list[$k]['logs']  = $Projectlog->get_list(['p_id'=>$p_id,'p_step_id'=>$v['id'],'p_step_type'=>8],'id,oper_user_name,oper_desc,addtime',0);
            }
        }


        $js = $this->loadJsCss(array('p:common/common','purchase'), 'js', 'admin');

        $this->assign('footjs', $js);
        $this->assign('p_info', $p_info);
        $this->assign('data',$list);
        //$this->assign('is_sejishi',$is_sejishi);
        //$this->assign('is_jingli',$is_jingli);
        $this->assign('status',$this->status());
        $this->assign('qn_host', config('qiniu.host'));
        //$this->assign('report_type',$this->report_type());
        return $this->fetch('info');
    }

    public function add($p_id,$id=0){

        if(!$p_id || $p_id<=0){
            $this->error('访问错误');
        }

        if($this->request->isAjax()){

            return $this->add_purchase_data($p_id,$id);
        }
        $info = [];
        if($id>0){
            $info = $this->m->get_info(['id'=>$id,'isdel'=>0]);
            if(!$info){
                $this->error('该采购信息不存在');
            }
            $info['docs'] = (new Purchasedoc())->get_order_list(['pu_id'=>$id,'isdel'=>0],'*',['seq'=>'asc'],0);
        }

        $uptoken = \app\common\library\Qiniu::get_uptoken(config('qiniu.bucket1'));
        $js = $this->loadJsCss(array(
            'p:common/common',
            //'https://unpkg.com/qiniu-js@2/dist/qiniu.min.js',
            'p:qiniu/qiniu-2.5.1',
            'p:md5/md5',
            'purchase_add'), 'js', 'admin');
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
        $this->m->update_data(['id'=>$id],['isdel'=>1]);
        (new Purchasedoc())->update_data(['pu_id'=>$id],['isdel'=>1]);
        (new Purchasedoc())->update_data(['pu_id'=>$id],['isdel'=>1]);

        $this->success('删除成功');
    }

    //采购提醒
    public function notice_info(){

    }
    //采购提醒设置
    public function notice_setting(){

    }

    protected function status(){
        return [
            0=>'待客户确认',
            1=>'客户驳回',
            2=>'客户已确认'
        ];
    }


    protected function add_purchase_data($p_id,$id=0){
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
        //有更新就重置状态
        $post['status'] = 0;
        $log_tag = '[添加]';
        if($id>0){
            $log_tag = '[编辑]';
            $this->m->update_data(['id'=>$id,'p_id'=>$p_id],$post);
        }else{
            $post['p_id'] = $p_id;
            $post['addtime'] = $this->datetime;
            $id = $this->m->add_data($post,true);
        }

        if(!$id){
            return ['err'=>1,'mesg'=>'保存资料失败.'];
        }

        //docs
        if(!empty($docs)){
            $mp = new Purchasedoc();
            $max_seq = $mp->get_max(['p_id'=>$p_id,'pu_id'=>$id,'isdel'=>0],'seq');
            $inserts = [];
            foreach($docs as $k=>$dc){
                $inserts[] = [
                    'p_id'=>$p_id,
                    'pu_id'=>$id,
                    'seq'=>$max_seq+$k+1,
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
                $mp->insert_all($inserts);
            }
        }

        //add log
        Plog::add_one($p_id,$id,8,['type'=>1,'id'=>session('user_id'),'name'=>session('name')],$log_tag.'采购信息：'.$post['name']);

        //成功后通知客户
        $this->send_notice($p_id,$id,'采购信息确认提醒','采购信息：<<'.$post['name'].">>:".$post['remark']);

        return ['err'=>0,'mesg'=>'success','url'=>url('Purchase/info',['p_id'=>$p_id])];
    }

    protected function send_notice($p_id,$id,$title,$content){
        //$info = $this->M->get_info(['id'=>$id],'id,status');
        $p_info = (new Project())->get_info(['id'=>$p_id],'id,owner_user_id');
        $ndata = [
            'p_id'=>$p_id,
            'type'=>7,
            'target_id'=>$id,
            'user_type'=>2,
            'user_id'=> $p_info['owner_user_id'],
            'title'=>$title,
            'content'=>$content
        ];

        LN::add($ndata);

    }
}