<?php
/**
 * Created by PhpStorm.
 * User: chenxh
 * Date: 2018/1/16
 * Time: 16:01
 */
namespace app\api\controller\c_v1;
#use app\api\controller\Common;
use app\common\model\Article as mArt,
    app\common\model\Pcase,
    app\common\library\Recomd;


class Index extends Common{


    //app 首页
    public function index_get(){
        $q_host = config('qiniu.host');
        $h5_base_url = $this->h5_base_url();
        //滚动
        $gundong  = (new Recomd())->get_show_data('app');

        //企业介绍
        $where = ['acid'=>1,'status'=>1,'isdel'=>0];
        $yyjs = (new mArt())->get_art_list($where,'id,view_num,title,summary,coverimg,addtime',['sort'=>'asc'],10);
        foreach($yyjs as $k=>$da){
            $yyjs[$k]['coverimg'] = c_img($da['coverimg'],4,200,200);
            $yyjs[$k]['info_url'] = $h5_base_url .'DetailsPage.html?id='.$da['id'];
        }

        //他们的家
        $tmdj = (new Pcase())->get_list(['isdel'=>0],'id,name,huxing as htype,coverimg',10);
        if(!empty($tmdj)){
            foreach($tmdj as $tk=>$td){
                $tmdj[$tk]['img_url'] = c_img($td['coverimg'],7);
                $tmdj[$tk]['info_url'] = $h5_base_url .'CaseSharing.html?id='.$td['id'];
                unset($tmdj[$tk]['coverimg']);
            }
        }

        return $this -> response([
            'code' => 200,
            'msg' => '成功',
            'data'=>[
                //滚动
                'gundong'=>$gundong,
                //企业介绍
                'yyjs'=>[
                    'title'=>'企业介绍',
                    'yyjs_list'=>$yyjs
                ],
                //为您提供
                'wntg'=>[
                    'title'=>'为您提供',
                    'img_url'=>$q_host.'static/index/app_wntg.jpg',
                    'info_url'=>$h5_base_url .'BecomeProvide.html'
                ],
                //他们的家
                'tmdj'=>[
                    'title'=>'他们的家',
                    'tmdj_list'=>$tmdj
                ],
                //实景样板间
                'sjybj'=>[
                    'title'=>'实景样板间',
                    'img_url'=>$q_host.'static/index/app_sjybj2.jpg',
                    'info_url'=>'http://anli.mzgtuan.com/quanjing/huandiban/'
                ],
                //资讯
                'zx'=>[
                    'title'=>'资讯',
                    'img_url'=>$q_host.'static/index/app_zx2.jpg',
                    'info_url'=>$h5_base_url .'hotTopic.html'
                ]
            ]
        ]);
    }


    public function appmodel_get(){
        $os = input('get.os','','trim');
        $v = input('get.v','','trim');



        return $this->response([
            'code'=>200,
            'msg'=>'成功',
            'data'=>[
                'result'=>1
            ]
        ]);
    }

    public function test_QA_post(){
        if(!request()->isAjax()){
            return ['err'=>1];
        }
        $post = input('post.');
        //print_r($post);
        \extend\Mylog::write($post,'qa_answer.log');

        $testqa = new \app\common\model\TestQA();

        $data = [
            'pageid'=>$post['pageid'],
            'uname'=>$post['uname'],
            'mobile'=>$post['mobile'],
            'addtime'=>$this->datetime
        ];

        foreach($post['answer'] as $answer){
            $data['answer'.$answer['id']] = $answer['answer'];
        }

        $res = $testqa->add_data($data);
        //var_dump($res);

        return ['err'=>0];

    }
}