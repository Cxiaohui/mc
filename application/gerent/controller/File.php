<?php
/**
 * Created by PhpStorm.
 * User: chenxh
 * Date: 2017/11/9
 * Time: 21:46
 */
namespace app\gerent\controller;
class File extends Common{
    public function _initialize($check_login=false)
    {
        parent::_initialize($check_login);
        //$this->member_model = new mMember();
        config('default_return_type','json');
    }

    function del($path=''){
        if(!$this->request->isAjax()){
            return ['err'=>1,'msg'=>'文件有误0'];
        }
        if(!$path){
            $path = input('post.path','');
        }

        //$path = '/data/image/goods/20171109/578e12908853c5a00b7a37085e1c560a.jpg?1510234929085';
        if(!$path){
            return ['err'=>1,'msg'=>'文件有误1'];
        }
        //qn
        if(strpos($path,'/mcdocs-')!==false){
            //$key = str_replace('http://'.config('qiniu.host').'/','',$path);
            return $this->del_qndoc($path);
        }

        if(strpos($path,'default')!==false){
            return ['err'=>0,'msg'=>'默认图片'];
        }


        if(strpos($path,'?')!==false){
           $path = explode('?',$path)[0];
        }
        $path = '.'.ltrim($path,'.');

        if(!file_exists($path)){

            return ['err'=>0,'msg'=>'文件有误3'];
        }
        unlink($path);
        //优化：把文件的缩略图也删除
        return ['err'=>0,'msg'=>'删除成功'];
    }

    function del_doc(){
        if(!$this->request->isAjax()){
            return ['err'=>1,'msg'=>'删除失败'];
        }

        $type = input('post.type',0,'int');
        $id = input('post.id',0,'int');
        if(!$type  || !$id){
            return ['err'=>1,'msg'=>'删除失败'];
        }
        $m = null;
        $w = ['id'=>$id];
        switch ($type){
            case 1://project doc
                $m = new \app\gerent\model\Projectdoc();
                //$w = ['id'=>$id];
                break;
            case 2://offer doc
                $m = new \app\gerent\model\Projectofferdoc();
            break;
            case 3://report doc
                $m = new \app\gerent\model\Projectreportdoc();
        }

        $m->update_data($w,['isdel'=>1]);
        return ['err'=>0,'msg'=>'删除成功'];

    }

    function del_qndoc($qn_key=''){
        if(!$this->request->isAjax()){
            return ['err'=>1,'msg'=>'删除失败'];
        }
        if(!$qn_key){
            $qn_key = input('post.qn_key','','trim');
        }



        if(!$qn_key){
            return ['err'=>1,'msg'=>'删除失败'];
        }

        if(strpos($qn_key,'mcdocs-')===false){
            return $this->del($qn_key);
        }

        $res = \app\common\library\Qiniu::delete_file($qn_key);

        return ['err'=>0,'msg'=>'删除成功qn','res'=>$res];
    }



}