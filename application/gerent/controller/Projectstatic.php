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
    app\common\library\Notice as LN,
    app\common\model\Projectstatic as pstatic,
    app\gerent\model\Projectstaticdocs as mPS;

class Projectstatic extends Common{

    /**
     * @var mPS
     */
    protected $M;
    public function _initialize($check_login=true)
    {
        parent::_initialize($check_login);
        $this->M = new mPS();
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

        $list = [];

        $w = ['p_id'=>$p_id,'isdel'=>0];
        $data = $this->M->get_list($w,'*',0);
        //print_r($data);
        $doc_types = $this->doc_type();
        $pstatic = (new pstatic())->get_list(['p_id'=>$p_id,'isdel'=>0],'id,name,type');


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
        return $this->fetch('info');
    }

    public function add($p_id=0,$id=0){
        if($this->request->isAjax()){

            return $this->save_data($p_id,$id);
        }

        if(!$p_id || $p_id<=0){
            $this->error('访问错误');
        }
        $type = input('get.type',0,'int');
        $p_w = ['id'=>$p_id,'isdel'=>0];
        if(session('cp_power_tag')!=1){
            $p_w['cpid'] = session('cpid');
        }
        $p_info = (new Project())->get_info($p_w,'id,name,status,type');
        if(!$p_info){
            $this->error('请先完成项目基本信息');
        }
        $info = [];

        if($id>0){

        }

        $uptoken = \app\common\library\Qiniu::get_uptoken(config('qiniu.bucket1'));
        $js = $this->loadJsCss(array('p:common/common',
            'https://unpkg.com/qiniu-js@2/dist/qiniu.min.js','p:md5/md5','projectstatic_add'), 'js', 'admin');
        //print_r($cates);
        $this->assign('footjs', $js);
        $this->assign('uptoken', $uptoken);
        $this->assign('info', $info);
        $this->assign('id', $id);
        $this->assign('p_id', $p_id);
        $this->assign('p_info', $p_info);
        $this->assign('type', $type);
        $this->assign('doc_type',$this->doc_type());
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

        $res = $this->M->update_data(['id'=>$id],['isdel'=>1]);
        if($res){
            $this->success('删除成功');
        }
        $this->error('删除失败');
    }



    //======


    protected function doc_type(){
        return [
            1=>'效果图',
            2=>'CAD图',
            3=>'主材'
        ];
    }
    // 新增完数据后，mc_project_static表中也需要有相应的数据
    // 编辑后，mc_project_static表中的状态也改为等待确认
    // 新增，编辑后都有通知
    protected function save_data($p_id,$id){
        if(!$p_id || $p_id<=0){
            return ['err'=>1,'mesg'=>'数据丢失'];
        }
        $type = input('post.type',0,'int');

        $docs = input('post.upfiles/a',[]);

        if(!$type){
            return ['err'=>1,'mesg'=>'数据丢失.'];
        }
        if($id==0){
            if(empty($docs)){
                return ['err'=>1,'mesg'=>'数据丢失.'];
            }
        }
        $pstatic = new pstatic();
        $types = $this->doc_type();
        $w = ['p_id'=>$p_id,'type'=>$type];
        $save_data = [
            'name'=>$types[$type],
            'status'=>0,
            'uptime'=>$this->datetime
        ];
        $res = true;
        if($pstatic->get_count($w)){
            $pstatic->update_data($w,$save_data);
        }else{
            $save_data['p_id'] = $p_id;
            $save_data['type'] = $type;
            $save_data['addtime'] = $type;
            $res = $pstatic->add_data($save_data,true);

            //add log
            //5效果图，6cad图，7主材
            if($res){
                Plog::add_one($p_id,$res,($type+4),['type'=>1,'id'=>session('user_id'),'name'=>session('name')],'添加项目'.$types[$type]);
            }
            
        }
        if(!$res){
            return ['err'=>1,'mesg'=>'保存失败.'];
        }

        /*$log_tag = '[添加]';
        if($id>0){
            $log_tag = '[编辑]';
            $this->M->update_data(['id'=>$id,'p_id'=>$p_id],$post);
        }else{
            $post['p_id'] = $p_id;
            $post['addtime'] = $this->datetime;
            $id = $this->M->add_data($post,true);
        }

        if(!$id){
            return ['err'=>1,'mesg'=>'保存资料失败.'];
        }*/

        //docs
        if(!empty($docs)){
            $inserts = [];
            foreach($docs as $dc){
                $inserts[] = [
                    'p_id'=>$p_id,
                    'type'=>$type,
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


        //成功后通知客户
        $this->send_notice($p_id,$id,$type,$types[$type].'确认提醒','添加了项目的'.$types[$type]);


        return ['err'=>0,'mesg'=>'success','url'=>url('Projectstatic/info',['p_id'=>$p_id])];
    }

    protected function send_notice($p_id,$type,$id,$title,$content){
        //$info = $this->M->get_info(['id'=>$id],'id,status');
        $p_info = (new Project())->get_info(['id'=>$p_id],'id,owner_user_id');
        $ndata = [
            'p_id'=>$p_id,
            'type'=>($type+1),//8效果图，9cad图，10主材
            'target_id'=>$id,
            'user_type'=>2,
            'user_id'=> $p_info['owner_user_id'],
            'title'=>$title,
            'content'=>$content
        ];

        LN::add($ndata);

    }
}