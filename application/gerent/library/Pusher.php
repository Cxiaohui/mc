<?php
/**
 * Created by PhpStorm.
 * User: chenxh
 * Date: 2018/9/12
 * Time: 17:21
 */
namespace app\gerent\library;
use app\common\model\Project;
class Pusher{

    private $p_id = 0;
    private $type = 0;
    private $type_id = 0;
    private $type_class = [
        'pstep'=>'\app\gerent\model\Projectstep',
        'ppay'=>'\app\gerent\model\Projectpay',
        'booking'=>'\app\common\model\Booking',
        'offer'=>'\app\gerent\model\Projectoffer',
        'report'=>'\app\gerent\model\Projectreport',
        'pstatic'=>'\app\common\model\Projectstatic',
        'purchase'=>'\app\common\model\Purchase'
    ];

    public function __construct($p_id,$type,$type_id)
    {
        $this->p_id = $p_id;
        $this->type = $type;
        $this->type_id = $type_id;
    }

    public function update_type_id($pushid){
        if(!$this->type_id){
            return false;
        }
        $update = ['pushid'=>$pushid];
        switch ($this->type){
            //项目阶段
            case 1:
            case 6:
                (new $this->type_class['pstep']())->update_data(['p_id'=>$this->p_id,'id'=>$this->type_id],$update);
                break;
            //付款
            case 2:
                (new $this->type_class['ppay']())->update_data(['p_id'=>$this->p_id,'id'=>$this->type_id],$update);
                break;
            //预约
            case 3:
                (new $this->type_class['booking']())->update_data(['id'=>$this->type_id],$update);
                break;
            //施工预算
            case 4:
                (new $this->type_class['offer']())->update_data(['id'=>$this->type_id],$update);
                break;
            //验收方案
            case 5:
                (new $this->type_class['report']())->update_data(['id'=>$this->type_id],$update);
                break;
            //方案\图纸\主材
            case 7:
                (new $this->type_class['pstatic']())->update_data(['id'=>$this->type_id],$update);
                break;
            //采购信息
            case 8:
                if($this->type_id){
                    (new $this->type_class['purchase']())->update_data(['id'=>$this->type_id],$update);
                }

                break;
        }
        return true;
    }
    

    public function get_title_message(){
        $title = '';
        $message = '';

        if($this->type!=8 && !$this->type_id){
            return [
                'err'=>0,
                'title'=>$title,
                'message'=>$message,
                'limittime'=>''
            ];
        }
        switch ($this->type){
            //项目阶段
            case 1:
            case 6:
                $info = (new $this->type_class['pstep']())->get_info(['p_id'=>$this->p_id,'id'=>$this->type_id],'id,name');
                if(!$info){
                    return ['err'=>1,'msg'=>'该项目阶段信息不存在'];
                }

                $title = '项目验收节点提醒';
                $message = '您的项目'.$info['name'].'阶段工作内容，已到了验收节点，快去验收吧！';
                $limittime = '';
                break;
            //付款
            case 2:
                $info = (new $this->type_class['ppay']())->get_info(['p_id'=>$this->p_id,'id'=>$this->type_id],'id,name,payable,payable_time');
                if(!$info){
                    return ['err'=>1,'msg'=>'该项目付款信息不存在'];
                }
                $title = 'PNAME项目-'.$info['name'].'付款提醒';
                $message = '您的PNAME项目'.$info['name'].'付款阶段，应在'.$info['payable_time'].'，付款'.$info['payable'].'元，请及时付款';
                $limittime = $info['payable_time'];
                break;
            //预约
            case 3:
                $info = (new $this->type_class['booking']())->get_info(['id'=>$this->type_id],'id,booking_time,booking_content');
                if(!$info){
                    return ['err'=>1,'msg'=>'该预约信息不存在'];
                }
                $time = date('m月d号H:i',strtotime($info['booking_time']));
                $title = '你在'.$time.'有项目预约';
                $message = '你在'.$time.'有项目预约:'.$info['booking_content'].',请准时到。';
                $limittime = $info['booking_time'];
                break;
            //施工预算
            case 4:
                $info = (new $this->type_class['offer']())->get_info(['id'=>$this->type_id],'id,name');
                if(!$info){
                    return ['err'=>1,'msg'=>'该施工预算信息不存在'];
                }
                $title = '施工预算确认提醒';
                $message = '您的项目施工预算《'.$info['name'].'》已有更新，快去确认吧！';
                $limittime = '';
                break;
            //验收方案
            case 5:
                //
                $info = (new $this->type_class['report']())->get_info(['id'=>$this->type_id],'id,name');
                if(!$info){
                    return ['err'=>1,'msg'=>'该验收方案信息不存在'];
                }
                $title = '验收方案确认提醒';
                $message = '您的项目验收方案《'.$info['name'].'》已有更新，快去确认吧！';
                $limittime = '';
                break;
            //效果图\CAD图\主材
            case 7:
                $info = (new $this->type_class['pstatic']())->get_info(['id'=>$this->type_id],'id,name,type');
                if(!$info){
                    return ['err'=>1,'msg'=>'该信息不存在'];
                }

                $title = '项目'.$info['name'].'确认提醒';
                $message = '您的项目'.$info['name'].'已有更新，快去确认吧！';
                $limittime = '';
                break;
            //采购信息
            case 8:
                //$info = (new $this->type_class['purchase']())->get_info(['id'=>$this->type_id],'id,name');
                $info = (new Project())->get_info(['id'=>$this->p_id,'isdel'=>0],'id,name');
                if(!$info){
                    return ['err'=>1,'msg'=>'该信息不存在'];
                }

                $title = $info['name'].'项目采购提醒';
                $message = $info['name'].'项目采购提醒，请在管理后台添加该项目的采购信息';
                $limittime = '';
                break;
            default:

                break;
        }

        return [
            'err'=>0,
            'title'=>$title,
            'message'=>$message,
            'limittime'=>$limittime
        ];
    }

    public function has_send_one(){

        if($this->type_id>0) {
            return cache($this->push_set_key()) ? true : false;
        }
        return false;
    }

    public function tag_send_one(){
        if($this->type_id>0){
            cache($this->push_set_key(),1);
        }

    }

    protected function push_set_key($add_date=true){
        return $this->p_id.'-'.$this->type.'-'.$this->type_id.($add_date?'-'.date('Ymd'):'');
    }
}