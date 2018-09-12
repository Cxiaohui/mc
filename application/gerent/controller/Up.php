<?php
/**
 * Created by PhpStorm.
 * User: xiaohui
 * Date: 2017-06-18
 * Time: 17:03
 */
namespace app\gerent\controller;

class Up extends Common{
    const BASEPATH = './data/image/';
    protected $uptypes = array('cases_cover','cuser_heads','admin_heads','artcover','recmdpic','indexmdu','goodsimg');


    public function _initialize($check_login=false)
    {
        parent::_initialize($check_login);
        //$this->member_model = new mMember();
        config('default_return_type','json');
    }

    public function index(){

        if(!$this->request->isPost()){
            return ['data' => '', 'info' => '上传失败.', 'status' => 0];
        }
        $uptype = input('post.uptype','','trim');
        if(!$uptype || !in_array($uptype,$this->uptypes)){
            return ['data' => '', 'info' => '上传失败..', 'status' => 0];
        }
        $post = input('post.');
        $typeid = input('post.typeid',0,'int');

        switch($uptype){
            //上传轮播的展示图片
            case 'recmdpic':
                return $this->_up_file(5);
                break;
            //后台用户头像图片
            case 'admin_heads':
                return $this->_up_file(3);
                break;
            //文章封面
            case 'artcover':
                return $this->_up_file(4);
                break;
            //案例图片
            case 'cases_cover':
                return $this->_up_file(7);
                break;
            //C端用户头像
            case 'cuser_heads':
                return $this->_up_file(2);
                break;
            //商品图片
            case 'goodsimg':
                return $this->_up_file(8);
                break;

        }

    }


    protected function _up_file($cate){

        $file = request()->file('Filedata');
        if(!$file){
            $file = request()->file('file');
        }
        $dirs = config('image_dirs');
        $save_path = self::BASEPATH.$dirs[$cate];
        $info = $file->validate(['size'=>2097152,'ext'=>'jpg,png'])->move($save_path);//
        if($info){
            $save_file = $save_path.DS.($info->getSaveName());
            //\app\gerent\model\Adminoperlog::instance()->save_data('上传图片：'.$save_file);
            return ['data' => ltrim($save_file,'.'), 'info' => $info->getSaveName(), 'status' => 1];
        }else{
            return ['data' => '', 'info' => $file->getError(), 'status' => 0];
        }
    }
}