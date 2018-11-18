<?php
/**
 * Created by PhpStorm.
 * User: xiaohui
 * Date: 2017-06-18
 * Time: 18:26
 */
namespace app\common\library;

class Anysizeimg{
    const BASEDIR = './data/image/';
    const DEFIMG = 'default.png';
    /**
     * 待完成
     * @param type $dir 文件目录
     * @param type $srcfile 源文件
     * @param type $w 需要截取的宽
     * @param type $h 需要截取的高
     * 返回截取后的文件绝对路径
     */
    static public function thumb($srcfile,$key=0,$w=0,$h = 0){

        //$dirs = config('image_dirs');

        $res = self::find_disk_path($srcfile,$key);
        if($res['err']!=0){
            if(isset($res['src']) && $res['src']){
                return $res['src'];
            }
            return self::return_def_img($key);
        }
        $src = $res['true_path'];
        //没有宽高，返回源文件
        if(!$w && !$h){
            return self::towebpath($src);
        }
        $thumb = $src . $w . ($h ? '_'.$h : '') . '.jpg';//截取后的文件

        //存在则返回
        if(file_exists($thumb)){
            return self::towebpath($thumb);
        }
        //只填一个尺寸，则是正方形
        $h = $h ? $h : $w;
        try{
            $img = \think\Image::open($src);

            if($img->thumb($w, $h, 6)->save($thumb)){
                return self::towebpath($thumb);
            }else{
                return self::return_def_img($key);
            }
        }catch (\Exception $e){
            return self::return_def_img($key);
        }


    }
    static public function find_disk_path($srcfile,$key=0){
        $dirs = config('image_dirs');

        //不存在则返回默认
        if(!$srcfile){
            return ['err'=>1,'msg'=>'Empty file path.'];
        }

        //是否是外站的
        if(strpos($srcfile,'http')!==false){
            return ['err'=>1,'msg'=>'It\'s a Http file.','src'=>$srcfile];
        }
        //七牛
        if(strpos($srcfile,'mcdocs-')!==false){


            $ext = pathinfo($srcfile,PATHINFO_EXTENSION);
            $fop = '';
            if(in_array($ext,config('img_ext'))){
                $fop = '?imageMogr2/strip/format/webp/interlace/1/quality/80/';//?imageslim
            }

            return ['err'=>1,'msg'=>'It\'s a Qiniu file.','src'=>config('qiniu.host').$srcfile.$fop];
        }
        $src = '';
        //检查是否已经是全路径了
        if(strpos($srcfile,'/data/image')!==false){
            $src = '.'.$srcfile;
        }else{
            if(!isset($dirs[$key])){
                $key = 0;
            }
            //找出相应目录
            $filedir = self::BASEDIR.$dirs[$key];

            //会员头像
            if($key==1 && is_numeric($srcfile) && $srcfile>0){
                $srcfile = floor($srcfile/1000).'/'.$srcfile.'.jpg';
            }

            $src = $filedir . $srcfile;//源文件
        }
        if(!$src || !file_exists($src)){
            return ['err'=>1,'msg'=>'Can\'t find it.'];
        }
        return ['err'=>0,'msg'=>'ok','true_path'=>$src,'dir'=>$dirs[$key]];
    }
    /**
     * 转换路径
     */
    static protected function towebpath($path){
        return 'http://'.request()->host(true).ltrim($path,'.');
    }

    static protected function return_def_img($key){
        //$module = strtolower(request()->module());
        if($key==1){
            return self::towebpath(self::BASEDIR.'wx_def.png');
        }else if($key==7){
            return self::towebpath(self::BASEDIR.'index_module_def.jpg');
        }else{
            return self::towebpath(self::BASEDIR.self::DEFIMG);
        }
    }
}