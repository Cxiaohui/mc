<?php
/**
 * Created by PhpStorm.
 * User: chenxh
 * Date: 2018/7/24
 * Time: 13:56
 */
namespace app\gerent\job;
use think\image\Exception;
use think\queue\Job,
    app\common\model\Projectoffer,
    app\common\model\Projectofferdoc,
    app\common\model\Projectreport,
    app\common\model\Projectreportdoc,
    app\common\library\Qiniu,
    app\common\library\Mylog as mlog;

class Compleximg{
    protected $log_file = 'compleximg_status';

    public function fire(Job $job,$data=[])
    {
        try {
            try{
                $this->do_job($data);
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

    /**
     *  处理验收报告，施工预算的签名合成
     * @param $data ['type'=>'report|offer','id'=>$id]
     */
    public function do_job($data){
        mlog::write(json_encode($data),$this->log_file);
        $types = ['offer','report'];
        if(!in_array($data['type'],$types) && !$data['id']){
            return false;
        }
        $m=null;
        $mdoc = null;
        $w = [];
        switch($data['type']){
            case 'offer':
                $m = new Projectoffer();
                $mdoc = new Projectofferdoc();
                $w = ['p_offer_id'=>$data['id']];
                break;
            case 'report':
                $m = new Projectreport();
                $mdoc = new Projectreportdoc();
                $w = ['p_rep_id'=>$data['id']];
                break;
        }

        $info = $m->get_info(['id'=>$data['id']],'id,sign_img');
        if(!$info || !$info['sign_img']){
            mlog::write('info not found',$this->log_file);
            return false;
        }
        //mlog::write($info,$this->log_file);
        $w['isdel']  = 0;
        $docs = $mdoc->get_list($w,'id,file_type,file_path',0);

        if(empty($docs)){
            mlog::write('empty doc',$this->log_file);
            return false;
        }
        try{
            $img_exts = config('img_ext');
            $img_exts[] = 'pdf';
            foreach($docs as $doc){
                if(!in_array($doc['file_type'],$img_exts)){
                    mlog::write('not image or pdf;file_type='.$doc['file_type'].';'.json_encode($data).';doc id='.$doc['id'],$this->log_file);
                    continue;
                }
                $w_url = Qiniu::watermark_url($doc['file_path'],$info['sign_img']);
                if(!$w_url){
                    mlog::write('watermark_url=>false;data:'.json_encode($data).';doc id='.$doc['id'],$this->log_file);
                    continue;
                }
                mlog::write('$w_url='.$w_url,$this->log_file);
                $q_key = Qiniu::download_upload_watermark($w_url);
                if(!$q_key){
                    mlog::write('download_upload_watermark=>false;data:'.json_encode($data).';doc id='.$doc['id'],$this->log_file);
                    continue;
                }
                mlog::write('$q_key='.$q_key,$this->log_file);
                $mdoc->update_data(['id'=>$doc['id']],['sign_complex_path'=>$q_key]);
            }
        }catch(\Exception $e){
            throw new \Exception($e);

        }

    }
}