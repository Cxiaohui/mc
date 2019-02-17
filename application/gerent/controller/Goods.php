<?php
/**
 * Created by PhpStorm.
 * User: chenxh
 * Date: 2018/7/10
 * Time: 19:55
 */
namespace app\gerent\controller;
use app\common\model\Goodscate as Gcate,
    app\common\model\Goodscont,
    app\common\model\Goodsimg,
    app\common\model\Goods as MG;

class Goods extends Common{
    /**
     * @var Gcate
     */
    public $GC=null;
    /**
     * @var MG
     */
    public $M;

    public function _initialize($check_login=true)
    {
        parent::_initialize($check_login);
        //$this->article_model = new mArticle();
        $this->GC = new Gcate();
        $this->M = new MG();
    }

    public function index(){
        $sop = $this->dosearch();
        //print_r($sop);
        $count = $this->M->get_count($sop['w']);
        $data = $page = [];

        if($count > 0){
            $page = $this->_pagenav($count);
            $field='*';
            $data = $this->M->get_list($sop['w'],$field,$page['offset'].','.$page['limit']);
        }

        $cates = $this->GC->get_list('1=1','id,name');

        $cates = create_kv($cates,'id','name');

        $js = $this->loadJsCss(array('p:common/common'), 'js', 'admin');
        //print_r($cates);
        $this->assign('footjs', $js);
        $this->assign('pagenav',$page);
        $this->assign('data',$data);
        $this->assign('cates',$cates);
        $this->assign('h5_base_url',$this->h5_base_url());
        $this->assign('sov',$sop['p']['sov']);
        $this->assign('is_so',$sop['is_so']);
        $this->assign('flink',$this->filterLink($sop['p']['f'],$cates));
        $this->assign('fselect',$this->filterSelect($sop['p']['sok'],['title'=>'名称','id'=>'序号']));
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
        if($p['f']>0){
            $w = ['cate_id'=>$p['f']];
            $is_so = true;
        }
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
            return $this->save_goods_data();
        }

        $info = [];

        if($id>0){

            $info = $this->M->get_info(['id'=>$id,'isdel'=>0]);
            if(!$info){
                $this->error('该商品不存在或已删除');
            }
            $imgs = (new Goodsimg())->get_order_list(['g_id'=>$id],'id,file_path as src',['sort'=>'asc'],0);
            $info['imgs'] = $imgs;
            $cont = (new Goodscont())->get_info(['g_id'=>$id],'content');
            $info['content'] = $cont['content'];
        }

        $cates = $this->GC->get_list('1=1','id,name');

        //$cates = create_kv($cates,'id','name');
        //print_r($cates);
        $uptoken = \app\common\library\Qiniu::get_uptoken(config('qiniu.bucket1'));
        $js = $this->loadJsCss(array(
            'p:common/common',
            //'p:tagsinput/jquery.tagsinput',
            'p:ueditor/ueditor',
            //'p:cate/jquery.cate',
            //'p:bootstrap/bootstrap-datetimepicker.min',
//            'p:webuper/js/webuploader','singleUp',
            'p:qiniu/qiniu-2.5.1',
            'p:md5/md5',
            'p:dragSort/jquery.dragsort-052',
            'goods'), 'js', 'admin');
        $this->assign('footjs', $js);

        $this->assign('uptoken', $uptoken);
        $this->assign('qn_host', config('qiniu.host'));
        $this->assign('cates', $cates);
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
        $res = $this->M->update_data(['id'=>$id],['uptime'=>$this->datetime,'isdel'=>1]);
        if($res){
            $this->success('删除成功');
        }
        $this->error('删除失败');
    }

    ///

    public function cate_index(){
        $where = '1=1';

        $count = $this->GC->get_count($where);
        $data = $page = [];
        if($count>0){
            $page = $this->_pagenav($count);
            $field = 'id,name';
            $data = $this->GC->get_list($where,$field,$page['offset'].','.$page['limit']);
        }

        $js = $this->loadJsCss(array('p:common/common','goods_cate'), 'js', 'admin');

        $this->assign('footjs', $js);
        $this->assign('pagenav',$page);
        $this->assign('data',$data);
        return $this->fetch('cate_index');

    }

    public function cate_add($id=0){

        if($this->request->isPost()){
            return $this->save_cate_data();
        }

        $info = [];

        if($id>0){
            $info = $this->GC->get_info(['id'=>$id]);
            if(!$info){
                $this->error('该分类不存在');
            }
        }


        $js = $this->loadJsCss(array('p:common/common','article_cate'), 'js', 'admin');

        $this->assign('footjs', $js);
        $this->assign('info', $info);
        return $this->fetch('cate_add');
    }

    public function cate_edit($id=0){
        if(!$id || $id<=0){
            $this->error('访问错误');
        }
        return $this->cate_add($id);
    }

    public function cate_del($id=0){
        if(!$id || $id<=0){
            $this->error('访问错误');
        }
        $res = $this->GC->del_row(['id'=>$id]);
        if($res){
            $this->M->update_data(['cate_id'=>$id],['cate_id'=>0]);
            \app\gerent\model\Adminoperlog::instance()->save_data('删除商品分类：'.$id);
            $this->success('删除成功');
        }
        $this->error('删除失败');
    }

    //=================

    protected function save_cate_data(){

        $name = input('post.name','','trim');
        $id = input('post.id',0,'int');
        if(!$name){
            $this->error('数据有误');
        }
        if($id>0){
            $this->GC->update_data(['id'=>$id],['name'=>$name]);
        }else{
            $data = ['name'=>$name,'addtime'=>$this->datetime];
            $id = $this->GC->add_data($data,true);
        }
        if($id){
            \app\gerent\model\Adminoperlog::instance()->save_data('编辑商品分类：'.$id);
            $this->jsalert('保存成功',3);
        }
        $this->error('保存失败');
    }

    protected function save_goods_data(){
        $post = $this->request->post();
        $main = [];
        $goods_imgs = [];

        $imgs = explode('|',$post['imgs']);
        if(!$post['name'] || !$post['cate_id'] || empty($imgs)){
            $this->error('数据有误');
        }
        foreach($imgs as $img){
            $goods_imgs[] = explode('?',$img)[0];
        }

        $main['cate_id'] = $post['cate_id'];
        $main['name'] = $post['name'];
        $main['coverimg'] = $goods_imgs[0];
        $main['uptime'] = $this->datetime;

        //商品详情
        $goods_info = [
            'content'=>$post['content']
        ];
        $goods_id = 0;
        if(isset($post['id']) && $post['id']>0){
            $goods_id = $post['id'];
            $this->M->update_data(['id'=>$post['id']],$main);
        }else{
            $main['addtime'] = $this->datetime;
            $goods_id = $this->M->add_data($main,true);
        }
        if($goods_id<=0){
            $this->error('保存失败');
        }
        //Goodscont
        $g_cont = new Goodscont();
        $goods_info['g_id'] = $goods_id;
        $goods_info['uptime'] = $this->datetime;
        $w = ['g_id'=>$goods_id];
        if($g_cont->get_count($w)){
            $g_cont->update_data($w,$goods_info);
        }else{
            $g_cont->add_data($goods_info);
        }
        //Goodsimg
        $g_img = new Goodsimg();
        $g_img->del_row($w);
        $inserts = [];
        foreach($goods_imgs as $gk=>$gimg){
            $inserts[] = [
                'g_id'=>$goods_id,
                'sort'=>$gk,
                'file_path'=>$gimg,
                'qiniu_status'=>0,
                'addtime'=>$this->datetime
            ];
        }

        $g_img->insert_all($inserts);
        $this->success('保存成功',url('Goods/index'));
    }
}