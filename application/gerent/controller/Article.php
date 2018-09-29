<?php
/**
 * Created by PhpStorm.
 * User: xiaohui
 * Date: 2017-06-20
 * Time: 9:54
 */
namespace app\gerent\controller;
use app\gerent\model\Articles as mArticle,
    app\gerent\model\Pushruntime,
    app\common\model\Pushnews;

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

    //  资讯推送设置= 20180924========================

    public function push_list($nid=0){
        $w = ['isdel'=>0];
        $data = $page = [];
        $title = '';
        if($nid>0){
            $newsinfo = $this->article_model->get_article_info(['id'=>$nid,'isdel'=>0],'id,title');
            if(!$newsinfo){
                $this->error('该文章不存在');
            }
            $title = $newsinfo['title'];
            $w['news_id'] = $nid;
        }

        $pushnews = new Pushnews();
        $count = $pushnews->get_count($w);
        if($count>0){
            $page = $this->_pagenav($count);
            //$field = '*';
            $data = $pushnews->get_list($w,'*',$page['offset'].','.$page['limit']);
        }


        $js = $this->loadJsCss(array('p:common/common'), 'js', 'admin');
        $this->assign('footjs', $js);
        $this->assign('h5_base_url',$this->h5_base_url());
        $this->assign('pagenav',$page);
        $this->assign('data',$data);
        $this->assign('title',$title);
        $this->assign('nid',$nid);
        $this->assign('run_types',$this->run_types());
        return $this->fetch('push_list');
    }

    public function push_add($nid=0,$id=0){
        if(!$nid){
            $this->error('请先选择推送的资讯文章','Article/index');
        }

        if($this->request->isPost()){
            return $this->save_push_data($nid,$id);
        }

        $info = [];
        $ref = get_ref();
        $run_type = 1;
        $geter = 'all_all';
        $run_time = date('Y-m-d');

        if($id>0){
            $info = (new Pushnews())->get_info(['id'=>$id,'isdel'=>0]);
            if(!$info){
                $this->error('该推送设置不存在');
            }
            $run_type = $info['run_type'];
            $run_time = $info['run_time'];
            $geter = $info['geter_user_ids'];
        }

        $newsinfo = $this->article_model->get_article_info(['id'=>$nid,'isdel'=>0],'id,title');
        if(!$newsinfo){
            $this->error('该文章不存在');
        }


        $js = $this->loadJsCss(array('p:common/common','article_push_add'), 'js', 'admin');
        $this->assign('footjs', $js);
        $this->assign('h5_base_url',$this->h5_base_url());
        $this->assign('run_type',$run_type);
        $this->assign('geter',$geter);
        $this->assign('run_time',$run_time);
        $this->assign('newsinfo',$newsinfo);
        $this->assign('info',$info);
        $this->assign('ref',$ref);
        $this->assign('push_geters',$this->push_geters());
        return $this->fetch('push_add');
    }

    public function push_edit($nid=0,$id=0){
        if(!$nid || $id<=0){
            $this->error('访问错误');
        }

        return $this->push_add($nid,$id);
    }

    public function push_del($id=0){
        if(!$id || $id<=0){
            $this->error('访问错误');
        }
        $pushnews = new Pushnews();
        $info = $pushnews->get_info(['id'=>$id,'isdel'=>0],'id,news_id');
        if(!$info){
            $this->error('该推送设置不存在');
        }
        $this->article_model->update_article_data(['id'=>$info['news_id']],['pushid'=>0]);
        $pushnews->update_data(['id'=>$id],['isdel'=>1]);
        (new Pushruntime())->update_data(['pn_id'=>$id],['isdel'=>1]);

        $this->success('删除成功');
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

    private function push_geters(){
        return [
            'all_all'=>'所有人',
            'c_all'=>'仅业主客户',
            'b_all'=>'仅企业内部'
        ];
    }

    private function run_types(){
        return [
            1=>'立即推送送',
            '定时推送',
        ];
    }

    protected function save_push_data($nid,$id){
        $post = input('post.');

        if(!$post['news_id'] || !$post['news_name'] || !$post['geter'] || !$post['run_type']){
            $this->error('数据有误');
        }
        $geter = $this->push_geters();

        $ref = $post['ref']?:url('Article/push_list',['nid'=>$nid]);

        $data = [
            'news_id'=>$post['news_id'],
            'news_name'=>$post['news_name'],
            'geter_users'=>$geter[$post['geter']],
            'geter_user_ids'=>$post['geter'],
            'title'=>$post['news_name'],
            'message'=>'点开看看',
            //'metas'=>'',
            'run_type'=>$post['run_type'],
            'run_time'=>$post['run_type']==2?$post['run_time']:'0',
        ];

        //if(!isset($post['id'])){
        $url = $this->h5_base_url().'DetailsPage.html?id='.$nid;
        $shorturl= \app\common\library\Shorturl::sina_create($url);
        if(!$shorturl){
            $this->error('生成文章短链接失败');
        }
        $data['metas'] = $shorturl;
        //}
        //print_r($data);exit;
        $pn_id = (new Pushnews())->save_push_data($data);
        if(!$pn_id){
            $this->error('保存设置失败');
        }

        $this->article_model->update_article_data(['id'=>$data['news_id']],['pushid'=>$pn_id]);

        $geters = [$post['geter']];
        if($post['geter']=='all_all'){
            $geters = ['c_all','b_all'];
        }


        $pushruntime = new Pushruntime();

        if(isset($post['id']) && $post['id']>0){
            $pushruntime->update_data(['pn_id'=>$pn_id],['isdel'=>1]);
        }

        //立即推送
        if($post['run_type']==1){

            foreach($geters as $ger){
                $push_data = [
                    'jpush_user_id'=>$ger,
                    'message'=>$data['title'],
                    'metas'=>['url'=>$shorturl]
                ];

                \think\Queue::later(1,'app\gerent\job\Pushqueue',$push_data);
            }
            $this->success('消息已经发送发',$ref);
        }

        //
        if($post['run_type']==2){
            $runtime = [];
            foreach($geters as $ger){
                $runtime[] = [
                    'pn_id'=>$pn_id,
                    'jpush_user_id'=>$ger,
                    'message'=>$data['title'],
                    'metas'=>$shorturl,
                    'runtime'=>$post['run_time'].' 10:00:00',
                ];
            }

            $pushruntime->insert_all($runtime);

            $this->success('消息设置成功',$ref);
        }

    }

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