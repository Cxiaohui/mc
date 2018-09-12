<?php
/**
 * Created by PhpStorm.
 * User: chenxh
 * Date: 2018/7/29
 * Time: 14:29
 */
namespace app\api\controller\b_v1;

class Setting extends Common{

    public function index_get(){

        return $this->response([
            'code'=>200,
            'msg'=>'成功',
            'data'=>[
                'aboutus_url'=>$this->h5_base_url().'DetailsPage.html?id=1',
                'app_download_url'=>$this->h5_base_url().'AppDownload.html'
            ]
        ]);
    }

}