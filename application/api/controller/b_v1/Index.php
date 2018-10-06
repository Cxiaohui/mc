<?php
/**
 * Created by PhpStorm.
 * User: chenxh
 * Date: 2018/10/5
 * Time: 19:40
 */
namespace app\api\controller\b_v1;

class Index extends Common{


    public function __construct($user_type = '')
    {
        parent::__construct($this->user_type);
        //$this->M = new MN();
    }

    public function h5urls_get(){

        return $this -> response([
            'code' => 200,
            'msg' => '成功',
            'data'=>$this->get_public_h5url()
        ]);

    }

    public function somelist_get(){
        $type = input('get.type',0,'int');
        $p_id = input('get.p_id',0,'int');

        //1我家的方案,2我家的图纸,3我家的主材,4施工预算,5验收报告,6采购提醒
        $types = [1,2,3,4,5,6];

        if(!$p_id || $p_id<=0 || !in_array($type,$types)){
            return $this->response(['code' => 201, 'msg' => '参数有误']);
        }

        switch ($type){
            //1我家的方案
            case 1:
                return (new \app\api\controller\b_v1\Show())->caselist_get();
                break;
            //2我家的图纸
            case 2:
                return (new \app\api\controller\b_v1\Show())->drawinglist_get();
                break;
            //3我家的主材
            case 3:
                return (new \app\api\controller\b_v1\Show())->makinglist_get();
                break;
            //4施工预算
            case 4:
                return (new \app\api\controller\b_v1\Offer())->list_get();
                break;
            //5验收报告
            case 5:
                return (new \app\api\controller\b_v1\Report())->list_get();
                break;
            //6采购提醒
            case 6:
                return (new \app\api\controller\b_v1\Purchase())->list_get();
                break;
            default:
                return $this->response(['code' => 201, 'msg' => '参数有误']);
                break;

        }



    }
}