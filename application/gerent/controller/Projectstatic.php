<?php
/**
 * Created by PhpStorm.
 * User: chenxh
 * Date: 2018/7/8
 * Time: 17:04
 */
namespace app\gerent\controller;
use app\gerent\model\Project,
    app\common\library\Plog,
    app\common\model\Projectlog,
    app\common\library\Notice as LN,
    app\common\model\Projectstatic as pstatic,
    app\gerent\model\Projectstaticdocs as mPS;

class Projectstatic extends Common{

    /**
     * @var mPS
     */
    protected $M;
    /**
     * @var pstatic
     */
    protected $ps;
    public function _initialize($check_login=true)
    {
        parent::_initialize($check_login);
        $this->M = new mPS();
        $this->ps = new pstatic();
    }

    public function index(){

    }

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
        $log_type = $this->log_type();
        $type = input('get.type',1,'int');
        $where = ['p_id'=>$p_id,'type'=>$type,'isdel'=>0];
        $pstatics = $this->ps->get_list($where,'id,p_id,name,type,status,addtime',0);
        if(!empty($pstatics)){
            $Projectlog = new Projectlog();
            foreach($pstatics as $k=>$pst){
                $pstatics[$k]['docs'] = $this->M->get_order_list(['p_static_id'=>$pst['id'],'isdel'=>0],'id,file_type,file_name,file_path,addtime',['seq'=>'asc'],0);
                $pstatics[$k]['doc_count'] = count($pstatics[$k]['docs'] );
                //5效果图，6cad图，7主材
                $pstatics[$k]['logs'] = $Projectlog->get_list(
                    ['p_id'=>$p_id,'p_step_id'=>$pst['id'],'p_step_type'=>$log_type[$type]],
                    'id,oper_user_name,oper_desc,addtime',0);
            }
        }


        $js = $this->loadJsCss(array('p:common/common'), 'js', 'admin');

        $this->assign('footjs', $js);
        //$this->assign('img_ext', config('img_ext'));
        $this->assign('p_info', $p_info);
        $this->assign('data',$pstatics);
        $this->assign('type',$type);
        $this->assign('qn_host', config('qiniu.host'));
        $this->assign('status',$this->status());
        $this->assign('doc_type',$this->doc_type());
        return $this->fetch('info');
    }

    public function info_bak($p_id=0){
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

        $list = [];

        $w = ['p_id'=>$p_id,'isdel'=>0];
        $data = $this->M->get_list($w,'*',0);
        //print_r($data);
        $doc_types = $this->doc_type();
        $pstatic = (new pstatic())->get_list(['p_id'=>$p_id,'isdel'=>0],'id,name,type',0);


        $q_host = config('qiniu.host');
        foreach($doc_types as $tk=>$v){
            $list[$tk] = ['id'=>0,'name'=>$v,'list'=>[]];
            if(!empty($pstatic)){
                foreach($pstatic as $pst){
                    if($tk==$pst['type']){
                        $list[$tk]['id'] = $pst['id'];
                    }
                }
            }
            if(!empty($data)){

                foreach($data as $k=>$da){

                    $da['file_url'] = quimg($da['sign_complex_path'],$da['file_path'],$q_host);
                    if($da['type'] == $tk){
                        $list[$tk]['list'][] = $da;
                        unset($data[$k]);
                    }

                }
            }
        }
        //print_r($list);
        $js = $this->loadJsCss(array('p:common/common'), 'js', 'admin');

        $this->assign('footjs', $js);
        $this->assign('img_ext', config('img_ext'));
        $this->assign('p_info', $p_info);
        $this->assign('data',$list);
        $this->assign('doc_type',$this->doc_type());
        return $this->fetch('info2');
    }

    public function add($id=0,$p_id=0){
        if($this->request->isAjax()){
            return $this->save_data($p_id,$id);
        }

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
        $type = input('get.type',1,'int');
        $info = [];

        if($id>0){
            $info = $this->ps->get_info(['id'=>$id,'isdel'=>0]);
            if(!$info){
                $this->error('该信息不存在');
            }
            $info['docs'] = $this->M->get_order_list(['p_static_id'=>$id,'isdel'=>0],'*',['seq'=>'asc'],0);
        }
        //print_r($info);
        $policy = ['mimeLimit'=>''];
        $uptoken = \app\common\library\Qiniu::get_uptoken(config('qiniu.bucket1'));
        $js = $this->loadJsCss(array(
            'p:common/common',
            //'https://unpkg.com/qiniu-js@2/dist/qiniu.min.js',
            'p:qiniu/qiniu-2.5.1',
            'p:md5/md5','projectstatic_add'
        ), 'js', 'admin');

        //print_r($cates);
        $this->assign('footjs', $js);
        $this->assign('uptoken', $uptoken);
        $this->assign('info', $info);
        $this->assign('id', $id);
        $this->assign('p_id', $p_id);
        $this->assign('p_info', $p_info);
        $this->assign('type', $type);
        $this->assign('qn_host', config('qiniu.host'));
        $this->assign('img_ext', config('img_ext'));
        $this->assign('doc_type',$this->doc_type());
        $this->assign('upload_alert',$this->upload_alert());
        return $this->fetch('add');
    }

    public function edit($id=0,$p_id=0){
        if(!$id || $id<=0 || !$p_id || $p_id<=0){
            $this->error('访问错误');
        }

        return $this->add($id,$p_id);
    }

    public function del($id=0){
        if(!$id || $id<=0){
            $this->error('访问错误');
        }
        $this->ps->update_data(['id'=>$id],['isdel'=>1]);
        $res = $this->M->update_data(['p_static_id'=>$id],['isdel'=>1]);
        if($res){
            $this->success('删除成功');
        }
        $this->error('删除失败');
    }



    //======

    protected function upload_alert(){
        return [
            1=>'请上传该项目我家方案文档（PDF、WORD、EXCEL）',
            2=>'请上传该项目施工图片（非图片文件无法签名合成）',
            3=>'请上传该项目我家主材文档（PDF、WORD、EXCEL）'
        ];
    }

    protected function doc_type(){
        return [
            1=>'项目方案',
            2=>'项目施工图',
            3=>'项目主材'
        ];
    }
    protected function status(){
        return [
            0=>'待客户确认',
            1=>'客户驳回',
            2=>'客户已确认'
        ];
    }
    //5方案，6施工图，7主材
    protected function log_type(){
        return [
            1=>'5',
            2=>'6',
            3=>'7'
        ];
    }
    // 新增完数据后，mc_project_static表中也需要有相应的数据
    // 编辑后，mc_project_static表中的状态也改为等待确认
    // 新增，编辑后都有通知
    protected function save_data($p_id,$id){
        if(!$p_id || $p_id<=0){
            return ['err'=>1,'mesg'=>'数据丢失'];
        }
        $post['type'] = input('post.type',0,'int');
        $post['name'] = input('post.name','','trim');
        $post['remark'] = input('post.remark','','trim');
        $docs = input('post.upfiles/a',[]);

        if(!$post['type'] || !in_array($post['type'],[1,2,3]) || !$post['name']){
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
            $this->ps->update_data(['id'=>$id,'p_id'=>$p_id],$post);
        }else{
            $post['p_id'] = $p_id;
            $post['addtime'] = $this->datetime;
            $id = $this->ps->add_data($post,true);
        }

        if(!$id){
            return ['err'=>1,'mesg'=>'保存资料失败.'];
        }

        //docs
        if(!empty($docs)){
            $max_seq = $this->M->get_max(['p_static_id'=>$id,'p_id'=>$p_id,'type'=>$post['type'],'isdel'=>0],'seq');
            $inserts = [];
            foreach($docs as $k=>$dc){
                $inserts[] = [
                    'p_static_id'=>$id,
                    'p_id'=>$p_id,
                    'type'=>$post['type'],
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
                $this->M->insert_all($inserts);
            }
        }
        $doc_type = $this->doc_type();
        $log_type = $this->log_type();
        Plog::add_one($p_id,$id,$log_type[$post['type']],['type'=>1,'id'=>session('user_id'),'name'=>session('name')],$log_tag.$doc_type[$post['type']]);

        //成功后通知客户
        $this->send_notice($p_id,$id,$post['type'],$doc_type[$post['type']].'确认提醒','添加了'.$doc_type[$post['type']]);


        return ['err'=>0,'mesg'=>'success','url'=>url('Projectstatic/info',['p_id'=>$p_id]).'?type='.$post['type']];
    }

    protected function send_notice($p_id,$id,$type,$title,$content){
        //$info = $this->M->get_info(['id'=>$id],'id,status');
        $p_info = (new Project())->get_info(['id'=>$p_id],'id,owner_user_id');
        $ndata = [
            'p_id'=>$p_id,
            'type'=>($type+7),//8效果图，9cad图，10主材
            'target_id'=>$id,
            'user_type'=>2,
            'user_id'=> $p_info['owner_user_id'],
            'title'=>$title,
            'content'=>$content
        ];

        LN::add($ndata);

    }
}