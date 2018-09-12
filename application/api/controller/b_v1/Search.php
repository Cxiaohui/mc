<?php
/**
 * Created by PhpStorm.
 * User: chenxh
 * Date: 2018/7/25
 * Time: 11:09
 */
namespace app\api\controller\b_v1;
use app\common\model\Project as Pject;

class Search extends Common{

    public function __construct($user_type='')
    {
        parent::__construct($this->user_type);
    }

    /**
     * B端支持搜索项目
     * todo 1，如果当前账号是项目中配的权限，是否只搜索与自己相关的项目？
     * todo 2，搜索结果中的图片，取项目哪里图片呢？
     */
    public function index_get(){
        $keyword = input('get.kwd','','trim');
        if(!$keyword || $keyword==''){
            return $this->response(['code'=>201,'msg'=>'请输入需要搜索的文字']);
        }

        $list = (new Pject())->get_list(['name'=>['like','%'.$keyword.'%']],'id,name as title,addtime');

        if(!empty($list)){
            foreach($list as $k=>$lt){
                $list[$k]['addtime'] = substr($lt['addtime'],0,10);
                $list[$k]['coverimg'] = '';
            }
        }


        return $this->response([
            'code'=>200,
            'msg'=>'成功',
            'data'=>[
                'total'=>count($list),
                'result'=>$list
            ]
        ]);
    }

}
