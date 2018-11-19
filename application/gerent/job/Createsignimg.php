<?php
/**
 * Created by PhpStorm.
 * User: chenxh
 * Date: 2018/10/7
 * Time: 15:19
 */
namespace app\gerent\job;
use think\image\Exception;
use think\queue\Job,
    app\common\model\Projectoffer,
    app\common\model\Projectofferdoc,
    app\common\model\Projectreport,
    app\common\model\Projectreportdoc,
    app\common\model\Projectstatic,
    app\common\model\Projectstaticdocs,
    app\common\library\Qiniu,
    app\common\library\Shorturl,
    app\common\library\Mylog as mlog;

class Createsignimg{
    protected $log_file = 'createsignimg_status';

    public function fire(Job $job,$data=[])
    {

        try {
            try{
                $res = $this->do_job($data);
                if(!$res){
                    $job->delete();
                }
            }catch (\Exception $e){
                $job->delete();
                mlog::write('Error : '
                    . $e->getFile() . '-' . $e->getLine() . PHP_EOL
                    . $e->getMessage(),
                    $this->log_file);
            }

            $job->delete();
            //$delay = 24*3600;//一天执行一次
            // 也可以重新发布这个任务
            //$job->release($delay); //$delay为延迟时间
            mlog::write('success:', $this->log_file);

        } catch (\Exception $e) {
            mlog::write('Error:' . $e->getFile() . '-' . $e->getLine() . PHP_EOL . '-' . $e->getMessage(), $this->log_file);
        }



    }

    public function failed($data=[]){
        mlog::write('Failed:',$this->log_file);
    }


    protected function do_job($data){

        /**
         * $data = ['type'=>'offer','id'=>2,'sign_type'=>1]
         */

        mlog::write(json_encode($data),$this->log_file);

        if(!$data['id']){
            mlog::write('error id',$this->log_file);
            return false;
        }

        $types = ['offer','report','static_2'];//'static-1',,'static-3'
        if(!in_array($data['type'],$types)){
            mlog::write('error type:'.$data['type'],$this->log_file);
            return false;
        }
        $m=null;
        $mdoc = null;
        $w = [];
        $fileds = '';
        $slim_type = '';
        switch($data['type']) {
            case 'offer':
                $m = new Projectoffer();
                $mdoc = new Projectofferdoc();
                $w = ['p_offer_id'=>$data['id'],'isdel'=>0];
                $fileds = 'id,sign_img,sejishi_sign_img,jingli_sign_img';
                $slim_type = 'offer_doc';
                break;

            case 'report':
                $m = new Projectreport();
                $mdoc = new Projectreportdoc();
                $w = ['p_rep_id'=>$data['id'],'isdel'=>0];
                $fileds = 'id,sign_img,jingli_sign_img';
                $slim_type = 'report_doc';
                break;

            case 'static_2':
                $m = new Projectstatic();
                $mdoc = new Projectstaticdocs();
                $w = ['p_static_id'=>$data['id'],'isdel'=>0];
                $fileds = 'id,sign_img';
                $slim_type = 'static_doc';
                break;
        }

        $info = $m->get_info(['id'=>$data['id']],$fileds);
        if(!$info){
            mlog::write('info not found',$this->log_file);
            return false;
        }
        $sign_img = '';
        $watermark_option = [
            'dissolve'=>80,
            'gravity'=>'SouthEast',
            'dx'=>15,
            'dy'=>15,
            'ws'=>'0.3',//还需要计算
            'wst'=>2
        ];
        //业主电签
        if($data['sign_type']==1 && isset($info['sign_img']) && $info['sign_img']){

            $sign_img = $info['sign_img'];
            $watermark_option['gravity'] = 'SouthEast';

        }//设计师电签
        elseif($data['sign_type']==2 && isset($info['sejishi_sign_img']) && $info['sejishi_sign_img']){

            $sign_img = $info['sejishi_sign_img'];
            $watermark_option['gravity'] = 'SouthWest';

        }//项目经理电签
        elseif($data['sign_type']==3 && isset($info['jingli_sign_img']) && $info['jingli_sign_img']){

            $sign_img = $info['jingli_sign_img'];
            $watermark_option['gravity'] = 'South';

        }

        if(!$sign_img){
            mlog::write('sign_img not found',$this->log_file);
            return false;
        }

        $docs = $mdoc->get_list($w,'id,file_type,file_path,sign_complex_path',0);

        if(empty($docs)){
            mlog::write('empty doc',$this->log_file);
            return false;
        }

        try{
            $img_exts = config('img_ext');
            $q_host = config('qiniu.host');
            foreach($docs as $doc){

                if(!in_array($doc['file_type'],$img_exts)){
                    mlog::write('not image or pdf;file_type='.$doc['file_type'].';'.json_encode($data).';doc id='.$doc['id'],$this->log_file);
                    continue;
                }
                $img_path = $doc['sign_complex_path'];
                if(!$img_path){
                    $img_path = $doc['file_path'];
                }
                if(!$img_path){
                    mlog::write('$img_path empty',$this->log_file);
                    continue;
                }

                //根据图片，计算ws的值
                $pic_info = \app\common\library\Http::curl_get($q_host.$img_path.'?imageInfo');
                if(!$pic_info){
                    mlog::write('image info not found;data:'.json_encode($data).';doc id='.$doc['id'],$this->log_file);
                    continue;
                }
                $pic_info = json_decode($pic_info,1);
                //水印宽度180px
                $ws = '0.3';
                if($pic_info['width']>0){
                    $ws = sprintf("%.2f",180/$pic_info['width']);
                }
                $watermark_option['ws'] = $ws;
                //获取签名合成图片的url
                $w_url = Qiniu::watermark_url($img_path,$sign_img,$watermark_option);
                if(!$w_url){
                    mlog::write('watermark_url=>false;data:'.json_encode($data).';doc id='.$doc['id'],$this->log_file);
                    continue;
                }

                mlog::write('$w_url='.$w_url,$this->log_file);
                //将新图片保存到七牛
                $saveres = Qiniu::save_new_img($w_url);

                if($saveres['err']==0){
                    $mdoc->update_data(['id'=>$doc['id']],['sign_complex_path'=>$saveres['key']]);


                    mlog::write('success!',$this->log_file);
                }else{
                    mlog::write([
                        'save new img failed!',
                        $saveres
                    ],$this->log_file);
                    continue;
                }
                usleep(4);
            }

            //继续
            \think\Queue::later(2,'app\gerent\job\Imageslim',['type'=>$slim_type,'id'=>$data['id']]);

        }catch(\Exception $e){
            throw new \Exception($e);
        }

    }
}