<?php
/**
 * Created by PhpStorm.
 * User: chenxh
 * Date: 2018/6/11
 * Time: 14:43
 */
namespace app\common\library;
use Qiniu\Auth,
    \Qiniu\Config as QConfig,
    Qiniu\Storage\BucketManager,
    Qiniu\Storage\UploadManager;
use think\image\Exception;


class Qiniu{

    static public function save_new_img($w_url){
        try{
            $qn = config('qiniu');
            //$savebuket = $qn['bucket1'];
            $savename = explode('-',str_replace($qn['host'],'',$w_url))[0].'-'.md5($w_url).'.jpg';

            $auth = new Auth($qn['AccessKey'],$qn['SecretKey']);

            $encodedEntryURI = \Qiniu\base64_urlSafeEncode($qn['bucket1'].':'.$savename);

            $newurl = $w_url.'|saveas/'.$encodedEntryURI;

            $safe_sign = $auth->sign(str_replace('http://','',$newurl));


            $finalURL = $newurl.'/sign/'.$safe_sign;

            /**
             * {
            key: "projectstatics/mcdocs-e5b5acea06c78fecdfe4d8f0ca3430f3.jpg",
            hash: "FtFgqyk18N7rWDMv57WALJeq3lRn"
            }
             */
            $res = Http::curl_get($finalURL);

            $res = json_decode($res,1);
            //var_dump($res);
            if(isset($res['key'])){
                return ['err'=>0,'key'=>$res['key'],'hash'=>$res['hash']];
            }
            return ['err'=>1,'msg'=>'保存新图片失败','res'=>$res];

        }catch (\Exception $e){
            return ['err'=>1,'msg'=>'保存新图片失败.','res'=>$e->getMessage()];
        }


    }

    static public function delete_file($file_key,$bucket=null){
        //BucketManager
        $auth = new Auth(config('qiniu.AccessKey'),config('qiniu.SecretKey'));
        $config = new QConfig();
        $manager = new BucketManager($auth,$config);
        if(!$bucket){
            $bucket = config('qiniu.bucket1');
        }
        $error = $manager->delete($bucket,$file_key);
        //var_dump($error);
        if(!$error){
            return true;
        }
        return false;
    }

    static public function upload_mc_file($file_path,$dir_key){
        $res = Anysizeimg::find_disk_path($file_path,$dir_key);
        if($res['err']!=0){
            return $res;
        }
        $src = $res['true_path'];
        $dtx = pathinfo($src, PATHINFO_EXTENSION);
        $q_key = config('qiniu.file_key_prefix').$res['dir'].md5($src).'.'.$dtx;

        //echo $src,'----',$q_key;
        return self::upload_file(config('qiniu.bucket1'),$src,$q_key);
    }

    static public function get_uptoken($bucket,$expires = 7200,$policy = null){
        $auth = new Auth(config('qiniu.AccessKey'),config('qiniu.SecretKey'));
        $cache_key = config('cache_key.qiniu_uptoken');
        $token = cache($cache_key);
        if($token){
            return $token;
        }
        $token = $auth->uploadToken($bucket, null, $expires, $policy);
        cache($cache_key,$token,7000);
        return $token;
    }

    static public function upload_file($bucket,$file_path,$save_name){
        if(!is_readable($file_path)){
            throw new \Exception("文件不可读：$file_path");
        }

        $uptoken = self::get_uptoken($bucket);
        $uploadMgr = new UploadManager();
        list($ret, $err) = $uploadMgr->putFile($uptoken, $save_name, $file_path);
        //echo "\n====> putFile result: \n";
        if ($err !== null) {
            return $err;
        } else {
            //array("hash"=>"FiM14k_0qm01S51zxM7Z4Xb4gFpX","key"=>"mcdocs-9b2ea27e03d96cfe8412e168dbda2a17");
            return $ret;
        }
    }

    static public function download_file($qiniu_key,$save_name=''){
        $url = config('qiniu.host').$qiniu_key;
        if($save_name==''){
            $save_name = './data/'.$qiniu_key;
        }


        $path = pathinfo($save_name);
        //print_r($path);
        if(!is_dir($path['dirname'])){
            mkdir($path['dirname'],0777,true);
        }
        //file_put_contents($save_name,$url);
        $res = Http::curl_get($url);
        //echo $res;
        @file_put_contents($save_name,$res);

        if(file_exists($save_name)){
            return true;
        }

        return false;
    }

    static public function watermark_url($src,$logo,$input_parms=[]){
        //$s_img = 'http://pa5ijfg62.bkt.clouddn.com/app/20180720/mcdocs-15320765635.jpg';
        //$w_img = 'http://pa5ijfg62.bkt.clouddn.com/mcdocs-06c2445980e3a5033a59927f999412cb.jpg';

        if(strpos($src,'mcdocs')===false){
            return false;
        }
        $q_host = config('qiniu.host');
        if(strpos($src,$q_host)===false){
            $src = $q_host.$src;
        }
        if(strpos($logo,$q_host)===false){
            $logo = $q_host.$logo;
        }
        /*print_r([
            '$src'=>$src,
            '$logo'=>$logo
        ]);*/
        $def_parms = [
            'dissolve'=>99,
            'gravity'=>'SouthEast',
            'dx'=>15,
            'dy'=>15,
            'ws'=>'0.8',
            'wst'=>0
        ];
        $parms = array_merge($def_parms,$input_parms);
        $base64_image = str_replace('+','-',str_replace('/','_',base64_encode($logo)));
        $w_parms = [
            '?watermark/1',
            '/image/'.$base64_image,
            '/dissolve/'.$parms['dissolve'],
            '/gravity/'.$parms['gravity'],
            '/dx/'.$parms['dx'],
            '/dy/'.$parms['dy'],
            '/ws/'.$parms['ws'],
            '/wst/'.$parms['wst']
        ];


        return $src.implode('',$w_parms);
    }

    static public function download_upload_watermark($wurl){
        try{
            $path = './data/'.str_replace(config('qiniu.host'),'',substr($wurl,0,strrpos($wurl,'?')));
            //echo $path;
            $pathinfo = pathinfo($path);

            if(!is_dir($pathinfo['dirname'])){
                mkdir($pathinfo['dirname'],0777,true);
            }

            $new_name = 'mcdocs-'.md5($pathinfo['filename'].'-'.time().'-sign').'.'.$pathinfo['extension'];
            $res = Http::curl_get($wurl);
            if(strpos($res,'error')!==false){
                throw new \Exception('获取新图片失败00，$wurl='.$wurl);
            }
            if(!$res){
                throw new \Exception('获取新图片失败01,$wurl='.$wurl);
            }

            $save_name = $pathinfo['dirname'].'/'.$new_name;
            @file_put_contents($save_name,$res);

            if(file_exists($save_name)){
                $q_key = str_replace('./data/','',$save_name);
                self::upload_file(config('qiniu.bucket1'),$save_name,$q_key);

                return $q_key;
            }

        }catch (\Exception $e){
            throw new Exception($e);
        }
        return false;
    }
}