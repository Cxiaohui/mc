<?php
/**
 * Created by PhpStorm.
 * User: chenxh
 * Date: 2018/7/25
 * Time: 11:09
 */
namespace app\api\controller\c_v1;
use app\common\model\Article as mArt,
    app\common\model\Pcase;

class Search extends Common{


    /**
     * todo 搜索
     * C端支持搜索资讯、案例
     */
    public function index_get(){
        $keyword = input('get.kwd','','trim');
        if(!$keyword || $keyword==''){
            return $this->response(['code'=>201,'msg'=>'请输入需要搜索的文字']);
        }
        //资讯
        $alist = (new mArt())->get_list(['title'=>['like','%'.$keyword.'%'],'isdel'=>0],'id,title,coverimg,addtime',200);
        $h5_base_url = $this->h5_base_url();

        if(!empty($alist)){
            foreach($alist as $k=>$da){
                $alist[$k]['addtime'] = substr($da['addtime'],0,10);
                $alist[$k]['coverimg'] = c_img($da['coverimg'],4);
                $alist[$k]['info_url'] = $h5_base_url.'DetailsPage.html?id='.$da['id'];
            }
        }

        //案例
        $plist = (new Pcase())->get_list(['name'=>['like','%'.$keyword.'%'],'isdel'=>0],'id,name as title,coverimg,addtime',200);
        if(!empty($plist)){
            foreach($plist as $pk=>$da){
                $plist[$pk]['addtime'] = substr($da['addtime'],0,10);
                $plist[$pk]['coverimg'] = c_img($da['coverimg'],7);
                $plist[$pk]['info_url'] = $h5_base_url .'CaseSharing.html?id='.$da['id'];
            }
        }

        $data = array_merge($alist,$plist);
        //print_r($data);

        return $this->response([
            'code'=>200,
            'msg'=>'成功',
            'data'=>[
                'total'=>count($data),
                'result'=>$data
                /*'news'=>[
                    'title'=>'资讯',
                    'total'=>count($alist),
                    'list'=>$alist
                ],
                'pcase'=>[
                    'title'=>'案例',
                    'total'=>count($plist),
                    'list'=>$plist
                ]*/
            ]
        ]);
    }

}
