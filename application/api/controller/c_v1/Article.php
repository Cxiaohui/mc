<?php
/**
 * Created by PhpStorm.
 * User: chenxh
 * Date: 2018/6/15
 * Time: 15:32
 */
namespace app\api\controller\c_v1;

use app\common\model\Article as mArt,
    app\common\model\Articlecont as mArtc;

class Article extends Common{



    public function list_get(){

        $cate_id = input('cateid',0,'int');
        $page = input('page',1,'int');
        $pagesize = input('pagesize',15,'int');
        $akey = input('get.akey','','trim');

        $akeys = $this->cate_key_map();
        if($akey && in_array($akey,$akeys)){
            $cate_id = $akeys[$akey];
        }

        if(!$cate_id || $cate_id<=0){
            return $this->response(['code'=>201,'msg'=>'参数有误']);
        }

        $limit = ($page-1)*$pagesize.','.$pagesize;

        $art = new mArt();

        $where = ['acid'=>$cate_id,'status'=>1,'isdel'=>0];
        $count = $art->get_count($where);
        if($count==0){
            return $this->response(['code'=>201,'msg'=>'没有数据']);
        }

        $fileds = 'id,view_num,title,summary,coverimg,addtime';
        //$list = $art->get_list($where,$fileds,$limit);
        if($cate_id==4){
            $order = ['isrecmd'=>'desc','id'=>'desc'];
        }else{
            $order = ['sort'=>'asc'];
        }
        $list = $art->get_art_list($where,$fileds,$order,$limit);
        $h5_base_url = $this->h5_base_url();
        foreach($list as $k=>$da){
            $list[$k]['addtime'] = substr($da['addtime'],0,10);
            $list[$k]['coverimg'] = c_img($da['coverimg'],4);
            $list[$k]['url'] = $h5_base_url.'DetailsPage.html?id='.$da['id'];
        }

        return $this->response([
            'code'=>200,
            'msg'=>'成功',
            'data'=>[
                'list'=>$list
            ]
        ]);
    }

    public function info_get(){
        $id = input('get.id',0,'int');
        $akey = input('get.akey','','trim');


        $akeys = $this->cont_key_map();
        if($akey && in_array($akey,$akeys)){
            $id = $akeys[$akey];
        }
        if(!$id || $id<=0){
            return $this->response(['code'=>201,'msg'=>'参数有误']);
        }

        $art = new mArt();

        $info = $art->get_info(['id'=>$id,'status'=>1,'isdel'=>0],'id,title,view_num,coverimg,addtime');
        if(!$info){
            return $this->response(['code'=>201,'msg'=>'该内容不存在']);
        }
        $info['coverimg'] = c_img($info['coverimg'],4);
        $cont= (new mArtc())->get_info(['artid'=>$id],'content');
        $info['content'] = gzuncompress($cont['content']);
        $info['addtime'] = date('m月d日',strtotime($info['addtime']));
        //浏览数加1
        $art->set_inc(['id'=>$id],'view_num');//->setInc('view_num');

        return $this->response([
            'code'=>200,
            'msg'=>'成功',
            'data'=>[
                'info'=>$info
            ]
        ]);
    }


    //==============

    protected function cate_key_map(){
        return [
            //企业介绍
            'jieshao'=>1,
                //关于我们
                'gywm'=>1,
                //设计
                'sheji'=>0,
                //施工
                'shigong'=>0,
                //软装
                'ruanzhuang'=>0,
                //权威
                'quanwei'=>0,
            //资讯
            'news'=>4,
            //为您提供
            'wntg'=>5,
                //整装套餐
                'zztc'=>0,
                //设计套餐
                'sjtc'=>0,
                //软装套餐
                'rztc'=>0,
            //施工标准
            'sgbz'=>9,
            //验收标准
            'ysbz'=>2,
            //装修保障
            'xsbz'=>3

        ];
    }

    protected function cont_key_map(){
        return [
            //企业介绍
            //'jieshao'=>0,
            //关于我们
            'gywm'=>1,
            //设计
            'sheji'=>0,
            //施工
            'shigong'=>0,
            //软装
            'ruanzhuang'=>0,
            //权威
            'quanwei'=>0,
            //资讯
            //'news'=>0,
            //为您提供
            //'wntg'=>0,
            //整装套餐
            'zztc'=>0,
            //设计套餐
            'sjtc'=>0,
            //软装套餐
            'rztc'=>0,
            //装修保障
            'xsbz'=>0
        ];
    }
}