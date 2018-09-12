<?php
/**
 * Created by PhpStorm.
 * User: xiaohui
 * Date: 2017-06-20
 * Time: 9:54
 */
namespace app\gerent\controller;
use app\gerent\model\Articles as mArticle;
    //app\common\model\Area as mArea;
class Article extends Common{
    /**
     * @var mArticle
     */
    private $article_model;
    public function _initialize($check_login=true)
    {
        parent::_initialize($check_login);
        $this->article_model = new mArticle();
    }

    /**
     * 文章列表
     */
    public function index(){

        $sop = $this->dosearch();
        //print_r($sop);
        $count = $this->article_model->get_article_count($sop['w']);
        $data = $page = [];

        if($count > 0){
            $page = $this->_pagenav($count);
            $field='id,acid,addtime,view_num,title,proveid,prove,city,isrecmd,reid,status';
            $data = $this->article_model->get_article_list($sop['w'],$field,$page['offset'].','.$page['limit']);
        }

        $cates = $this->article_model->get_cate_keydata(0);
        $js = $this->loadJsCss(array('p:common/common','article'), 'js', 'admin');
        //print_r($cates);
        $this->assign('footjs', $js);
        $this->assign('pagenav',$page);
        $this->assign('data',$data);
        $this->assign('cates',$cates);
        $this->assign('h5_base_url',$this->h5_base_url());
        $this->assign('sov',$sop['p']['sov']);
        $this->assign('is_so',$sop['is_so']);
        $this->assign('flink',$this->filterLink($sop['p']['f'],$cates));
        $this->assign('fselect',$this->filterSelect($sop['p']['sok'],['title'=>'标题','id'=>'序号']));
        $this->assign('astatus',$this->astatus());
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
            $w['acid'] = $p['f'];
            $is_so = true;
        }
        $soks = ['id','title'];
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
    /**
     * 添加文章
     * @param int $aid
     */
    public function artadd($id=0){

        if($this->request->isPost()){
            $this->save_article_data();
        }
        $info = [];
        if($id>0){
            $info = $this->article_model->get_article_info(['id'=>$id]);
            if(!$info){
                $this->error('该文章不存在');
            }
            $desn = $this->article_model->get_article_cont_info(['artid'=>$id],'content');
            $info['content'] = gzuncompress($desn['content']);
        }

        $cates = $this->article_model->get_cate_keydata();
        $js = $this->loadJsCss(array('p:common/common','p:ueditor/ueditor','p:webuper/js/webuploader','singleUp','p:cate/jquery.cate','article'), 'js', 'admin');

        $this->assign('footjs', $js);
        $this->assign('cates', $cates);
        $this->assign('info', $info);
        return $this->fetch('artadd');
    }

    /**
     * 编辑文章
     * @param int $aid
     */
    public function artedit($id=0){

        if(!$id || $id<=0){
            $this->error('访问错误');
        }


        return $this->artadd($id);
    }

    /**
     * 删除文章
     * @param int $aid
     */
    public function artdel($id=0){
        if(!$id || $id<=0){
            $this->error('访问错误');
        }
        //优化：还需要删除文中的图片
        $res = $this->article_model->del_art(['id'=>$id]);
        if($res){
            //$this->article_model->del_cont(['artid'=>$id]);
            \app\gerent\model\Adminoperlog::instance()->save_data('删除文章：'.$id);
            $this->success('删除成功');
        }
        $this->error('删除失败');
    }


    //==================================

    /**
     * 文章分类
     */
    public function cate_index(){

        $where = [];

        $count = $this->article_model->get_cate_count($where);
        $data = $page = [];
        if($count>0){
            $page = $this->_pagenav($count);
            $field = 'id,name';
            $data = $this->article_model->get_cate_list($where,$field,$page['offset'].','.$page['limit']);
        }

        $js = $this->loadJsCss(array('p:common/common','article_cate'), 'js', 'admin');

        $this->assign('footjs', $js);
        $this->assign('pagenav',$page);
        $this->assign('data',$data);
        return $this->fetch('cate_index');
    }
    /**
     * 添加文章分类
     */
    public function cate_add($id=0){

        if($this->request->isPost()){
            return $this->save_cate_data();
        }
        $info = [];

        if($id>0){
            $info = $this->article_model->get_cate_info(['id'=>$id]);
            if(!$info){
                $this->error('该分类不存在');
            }
        }

        $js = $this->loadJsCss(array('p:common/common','article_cate'), 'js', 'admin');

        $this->assign('footjs', $js);
        $this->assign('info', $info);
        return $this->fetch('cate_add');
    }
    /**
     * 编辑文章分类
     */
    public function cate_edit($id=0){
        if(!$id || $id<=0){
            $this->error('访问错误');
        }
        return $this->cate_add($id);
    }
    /**
     * 删除文章分类
     */
    public function cate_del($id=0){
        if(!$id || $id<=0){
            $this->error('访问错误');
        }

        $res = $this->article_model->del_cate(['id'=>$id]);
        if($res){
            $this->article_model->update_article_data(['acid'=>$id],['acid'=>0]);
            \app\gerent\model\Adminoperlog::instance()->save_data('删除文章分类：'.$id);
            $this->success('删除成功');
        }
        $this->error('删除失败');
    }


    //======================

    protected function astatus(){
        return array(
            0=>'<span class="label">未发布</span>',
            1=>'<span class="label label-success">已发布</span>'
        );
    }

    protected function save_article_data(){
        $post = input('post.');

        //print_r($post);
        $cont = $post['content'];
        unset($post['file'],$post['content']);
        //$area = new mArea();
        /*$post['all_site'] = 0;
        if(isset($post['proveid']) && $post['proveid']){
            $prove = $area->get_area_info(['id'=>$post['proveid']],'name');
            $post['prove'] = $prove['name'];
            $post['all_site'] = 1;
        }
        if(isset($post['cityid']) && $post['cityid']){
            $city = $area->get_area_info(['id'=>$post['cityid']],'name');
            $post['city'] = $city['name'];
        }*/

        if($post['summary']==''){
            $post['summary'] = cut_content($cont,0,120);
        }

        $artid = $this->article_model->save_article_data($post);
        if(!$artid){
            $this->error('保存失败');
        }
        $this->article_model->save_article_cont_data($artid,$cont);
        \app\gerent\model\Adminoperlog::instance()->save_data('编辑文章：'.$artid);
        $this->success('保存成功',url('Article/index'));
    }

    protected function save_cate_data(){
        $name = input('post.name','','trim');
        $id = input('post.id',0,'int');
        if(!$name){
            $this->error('数据有误');
        }
        $res = $this->article_model->save_cate_data(['name'=>$name,'id'=>$id]);
        if($res){
            \app\gerent\model\Adminoperlog::instance()->save_data('编辑文章分类：'.$id);
            $this->jsalert('保存成功',3);
        }
        $this->error('保存失败');
    }
}