<?php
/**
 * Created by PhpStorm.
 * User: chenxh
 * Date: 2018/6/18
 * Time: 14:30
 */
namespace app\api\controller\c_v1;
use app\common\model\Projectadmin,
    app\common\library\Notice as LN,
    app\common\model\Booking as BK;
class Booking extends Common{

    /*public function __construct($user_type='')
    {
        //parent::__construct($this->user_type);
    }*/

    public function submit_post(){

        $post['p_id'] = input('post.p_id',0,'int');
        $uid = input('post.uid',0,'int');
        if($uid>0){
            $this->user_id = $uid;
        }
        $post['booking_user_id'] = $this->user_id;//input('post.booking_user_id',0,'int');
        //$post['to_user_id'] = input('post.to_user_id',0,'int');
        $post['booking_time'] = input('post.booking_time','','trim');
        $post['booking_content'] = input('post.booking_content','','trim');
        //被预约人的标识，1设计师，2工程监理，3质检员
        $post['to_users'] = input('post.booking_users','','trim');
        //print_r($post);
        if(!$post['to_users'] || !$post['booking_time'] || !$post['booking_content']){
            return $this->response(['code'=>201,'msg'=>'数据有误']);
        }

        $userinfo = $this->_get_user();
        if($userinfo){
            $post['cpid'] = $userinfo['cpid'];
        }

        $post['addtime'] = $this->datetime;
        $bid = (new BK)->add_data($post,true);
        if($bid){
            //发送相应通知,
            if($post['p_id']>0){
                $user_tags = explode(',',$post['to_users']);
                $types = [];
                if(in_array(1,$user_tags)){
                    $types[] = 3;
                }
                if(in_array(2,$user_tags)){
                    $types[] = 1;
                }
                if(in_array(3,$user_tags)){
                    $types[] = 5;
                }
                //,'owner_user_id'=>$this->user_id,'1,3,5'
                $pinfo = (new Projectadmin())->get_list(['p_id'=>$post['p_id'],'type'=>['in',$types]],'id,b_user_id',0);
                if($pinfo){
                    $b_user_ids = array2to1($pinfo,'b_user_id');

                    foreach($b_user_ids as $buid){
                        $ndata = [
                            'p_id'=>$post['p_id'],
                            'type'=>3,
                            'target_id'=>$bid,
                            'user_type'=>1,
                            'user_id'=>$buid,
                            'title'=>'预约提醒',
                            'content'=>$post['booking_content']
                        ];

                        LN::add($ndata);
                    }
                }
            }


            return $this->response(['code'=>200,'msg'=>'保存成功']);
        }
        return $this->response(['code'=>201,'msg'=>'保存失败']);
    }

}