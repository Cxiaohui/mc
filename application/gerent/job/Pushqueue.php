<?php
/**
 * Created by PhpStorm.
 * User: chenxh
 * Date: 2018/7/22
 * Time: 14:39
 */
namespace app\gerent\job;
use think\queue\Job,
    think\Db,
    app\common\library\Jpush,
    app\gerent\model\Push,
    app\gerent\model\Pushruntime,

    app\common\library\Mylog as mlog;

class Pushqueue{
    protected $log_file = 'jpush_status';

    public function fire(Job $job,$data=[])
    {
        try {
            mlog::write(json_encode($data),$this->log_file);

            if(isset($data['jpush_user_id'])){// && $data['jpush_user_id']
                $res = Jpush::push_mesg(
                    $data['jpush_user_id'],
                    $data['message'],
                    $data['metas']
                );

                // 将这条推送设置为已执行 donetime，result
                if(isset($data['id']) && $data['id']>0){
                    $this->updatePush($data,$res);
                }

            }else{
                Jpush::send(
                    $data['to_user_type'],
                    $data['to_user_id'],
                    $data['message'],
                    $data['extras']
                );
            }


            $job->delete();
            //$delay = 2;
            // 也可以重新发布这个任务
            //$job->release($delay); //$delay为延迟时间
            mlog::write('success:', $this->log_file);
        } catch (\Exception $e) {
            $job->delete();
            // 将这条推送设置为已执行 donetime，result
            if(isset($data['id']) && $data['id']>0){
                $this->updatePush($data,[
                    $e->getFile(),
                    $e->getLine(),
                    $e->getMessage()]);
            }

            mlog::write('Error : '
                . $e->getFile() . '-' . $e->getLine() . PHP_EOL
                . $e->getMessage(),
                $this->log_file);
        }
    }
    public function failed($data=[]){
        mlog::write('Failed:',$this->log_file);
    }



    public function updatePush($data,$res){
        try{
            $nid = isset($data['notice_id'])?$data['notice_id']:0;
            (new Pushruntime())->update_data(
                ['id'=>$data['id']],
                [
                    'metas'=>Db::raw('CONCAT(metas,'.$nid.')'),//['exp','CONCAT(metas,'.$nid.')'],
                    'donetime'=>date('Y-m-d H:i:s'),
                    'result'=>substr(str_replace('/home/wwwroot','',json_encode($res)),0,200)
                ]
            );
        }catch (\Exception $e){
            mlog::write('updatePush Error : '
                . $e->getFile() . '-' . $e->getLine() . PHP_EOL
                . $e->getMessage(),
                $this->log_file);
        }

    }

    /**
     * 推送处理
     * @param $data
     */
    /*public function do_job($data){
        mlog::write(json_encode($data),$this->log_file);
        Jpush::send(
            $data['to_user_type'],
            $data['to_user_id'],
            $data['message'],
            $data['extras']
        );
    }*/
}