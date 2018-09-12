<?php
/**
 * Created by PhpStorm.
 * User: chenxh
 * Date: 2018/7/1
 * Time: 11:00
 */
namespace app\gerent\controller;
use app\gerent\model\Pcase,
    app\gerent\model\Pcasestep,
    app\gerent\model\Pcasestepimg,
    think\Loader;


class Cases extends Common{
    /**
     * @var Pcase
     */
    public $M;
    public function _initialize($check_login=true)
    {
        parent::_initialize($check_login);

        $this->M = new Pcase();
    }


    public function index(){
        $sop = $this->dosearch();
        //print_r($sop);
        $count = $this->M->get_count($sop['w']);
        $data = $page = [];

        if($count > 0){
            $page = $this->_pagenav($count);
            $field='id,name,status,view_num,coverimg,addtime';
            $data = $this->M->get_list($sop['w'],$field,$page['offset'].','.$page['limit']);
            $Pcasestep = new Pcasestep();
            foreach($data as $k=>$da){
                $data[$k]['coverimg'] = c_img($da['coverimg'],7);
                $data[$k]['a_count'] = $Pcasestep->get_count(['case_id'=>$da['id'],'isdel'=>0]);
            }
        }

        //$cates = $this->article_model->get_cate_keydata(0);
        $js = $this->loadJsCss(array('p:common/common'), 'js', 'admin');
        //print_r($cates);
        $this->assign('footjs', $js);
        $this->assign('pagenav',$page);
        $this->assign('data',$data);
        $this->assign('h5_base_url',$this->h5_base_url());
        $this->assign('sov',$sop['p']['sov']);
        $this->assign('is_so',$sop['is_so']);
        $this->assign('fselect',$this->filterSelect($sop['p']['sok'],['name'=>'名称','id'=>'序号']));
        //$this->assign('astatus',$this->astatus());
        return $this->fetch('index');
    }

    /**
     * 筛选处理
     * @return array
     */
    protected function dosearch(){
        $p['f'] = input('get.f',0);
        $is_so = false;
        $w = ['isdel'=>0];
        //筛选的条件处理

        $soks = ['id','name'];
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
            return $this->save_case_data();
        }


        $info = [];

        if($id>0){
            $info = $this->M->get_info(['id'=>$id,'isdel'=>0]);

            if(!$info){
                $this->error('该案例不存在或已删除');
            }

            $info['steps'] = json_decode($info['step_json'],1);
            //print_r($info);exit;
        }


        $js = $this->loadJsCss(array('p:common/common','p:ueditor/ueditor','p:webuper/js/webuploader','singleUp','cases_add'), 'js', 'admin');

        $this->assign('footjs', $js);

        $this->assign('info', $info);
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
        $this->M->update_data(['id'=>$id],['isdel'=>1]);

        $this->success('删除成功');
    }

    public function step_info($case_id=0){
        if(!$case_id || $case_id<=0){
            $this->error('访问错误');
        }

        $data = (new Pcasestep())->get_list(['case_id'=>$case_id,'isdel'=>0],'id,case_id,title,addtime');

        $js = $this->loadJsCss(array('p:common/common'), 'js', 'admin');
        //print_r($cates);
        $this->assign('footjs', $js);
        $this->assign('data', $data);
        $this->assign('case_id', $case_id);
        return $this->fetch('step_info');
    }

    public function step_add($case_id,$id=0){
        if(!$case_id || $case_id<=0){
            $this->error('访问错误');
        }

        if($this->request->isPost()){
            return $this->save_case_step_data($case_id);
        }
        $info = [];

        if($id>0){
            $info = (new Pcasestep())->get_info(['id'=>$id,'case_id'=>$case_id,'isdel'=>0]);
            if(!$info){
                $this->error('该文章不存在或已删除');
            }
        }



        $js = $this->loadJsCss(array('p:common/common','p:ueditor/ueditor','cases_step_add'), 'js', 'admin');

        $this->assign('footjs', $js);
        $this->assign('info', $info);
        $this->assign('case_id', $case_id);
        return $this->fetch('step_add');
    }

    public function step_edit($case_id,$id=0){

        if(!$case_id || $case_id<=0 || !$id || $id<=0){
            $this->error('访问错误');
        }
        return $this->step_add($case_id,$id);
    }

    public function step_del($id=0){
        if(!$id || $id<=0){
            $this->error('访问错误');
        }
        (new Pcasestep())->update_data(['id'=>$id],['isdel'=>1]);

        $this->success('删除成功');
    }

    ///==========

    protected function save_case_data(){
        $post = $this->request->post();
        //print_r($post);


        $data = [
            'name'=>$post['name'],
            'huxing'=>$post['huxing'],
            'mianji'=>$post['mianji'],
            'fengge'=>$post['fengge'],
            'seijishi'=>$post['seijishi'],
            'jingli'=>$post['jingli'],
            'jianli'=>$post['jianli'],
            'step_json'=>$post['step'],
            'coverimg'=>$post['coverimg']
        ];

        $scene = 'add';
        if(isset($post['id']) && $post['id']>0){
            $scene = 'edit';
            $data['id'] = $post['id'];
        }
        $vPcase = Loader::validate('pcase');
        $res = $vPcase->scene($scene)->check($data);
        if(!$res){
            $this->error($vPcase->getError());
        }else {
            $res = $this->M->save_data($data);
            if($res){
                $this->success('保存成功',url('Cases/index'));
            }
        }

        $this->error('保存失败');

    }

    protected function save_case_step_data($case_id){
        $post = $this->request->post();

        if(!$post['title'] || !$post['content']){
            $this->error('数据有误');
        }
        if($post['summary']==''){
            $post['summary'] = cut_content($post['content'],0,120);
        }
        $imgs = getimgtag($post['content']);
        $post['case_id'] = $case_id;
        $case_step_id = (new Pcasestep())->save_data($post);
        if(!$case_step_id){
            $this->error('保存失败');
        }
        //print_r($imgs);
        //print_r($post);
        $Pcasestepimg = new Pcasestepimg();

        $Pcasestepimg->del_row(['case_id'=>$case_id,'case_step_id'=>$case_step_id]);
        if(!empty($imgs)){

            $insert_all = [];
            foreach($imgs as $img){
                $insert_all[] = [
                    'case_id'=>$case_id,
                    'case_step_id'=>$case_step_id,
                    'img_url'=>$img
                ];
            }
            $Pcasestepimg->insert_all($insert_all);
        }

        $this->success('保存成功',url('Cases/step_info',['case_id'=>$case_id]));

    }
}