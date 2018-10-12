<?php
namespace app\api\controller;

//use extend\Redis;
use think\App;
use think\controller\Rest;
use think\Response;
use app\api\library\Apitoken;
use think\Request;
class Common extends Rest {
    protected $restDefaultType   = 'json';
    protected $app_model = 1;
    protected $user_id = 0;
    protected $user_type = '';
    /**
     * @var null|Request
     */
    protected $req = null;
    protected $datetime = '';

    public function __construct($user_type='') {

        parent::__construct();

        $this->req = Request::instance();

        if($user_type){
            $this->_check_api_safe($user_type);
        }

        $this->datetime = date('Y-m-d H:i:s');
    }

    public function _empty($method)
    {
        if (method_exists($this, $method . '_' . $this->method . '_' . $this->type)) {
            // RESTFul方法支持
            $fun = $method . '_' . $this->method . '_' . $this->type;
        } elseif ($this->method == $this->restDefaultMethod && method_exists($this, $method . '_' . $this->type)) {
            $fun = $method . '_' . $this->type;
        } elseif ($this->type == $this->restDefaultType && method_exists($this, $method . '_' . $this->method)) {
            $fun = $method . '_' . $this->method;
        }elseif($this->type == 'xml' && method_exists($this, $method . '_' . $this->method)){
            $fun = $method . '_' . $this->method;
        }
        if (isset($fun)) {
            return App::invokeMethod([$this, $fun]);
        } else {
            // 抛出异常
            //$this->response(['code' => 101, 'msg' => 'error action :' . $method]);
            $this->response(['code' => 101, 'msg' => '访问错误404']);
            //throw new \Exception('error action :' . $method);
            exit;
        }
    }
    protected function _check_api_safe($user_type){

        $Authorization = $this->req->header('Authorization');

        if(!$Authorization){
            return $this -> response(['code' => 101, 'msg' => '无法访问']);
        }
        //print_r($Authorization);

        //C:11212:dfsdfsdfsdfsdfsdf
        $auth_list = explode(':',$Authorization);
        if(count($auth_list)!=3){
            return $this -> response(['code' => 101, 'msg' => '无法访问.']);
        }
        if(strtolower($auth_list[0])!=$user_type || !is_numeric($auth_list[1])){
            return $this -> response(['code' => 101, 'msg' => '无法访问..']);
        }
        $res_user_id = Apitoken::check_api($user_type,$auth_list[1],$auth_list[2]);
        if($res_user_id<0){
            //
            $resean = [
                -1 => '您的账户已在其他设备登录，请重新登录',
                -2 =>'Token过期，请重新登录',
                -3 =>'Token过期，请重新登录.'
            ];
            return $this -> response([
                'code' => 401,
                'msg' => isset($resean[$res_user_id])?$resean[$res_user_id]:$resean[-3]
            ]);
        }
        $this->user_id = $res_user_id;
        return true;
    }

    public function h5_base_url(){
        return 'https://'.$this->req->host() .'/mch5/mochuan/';
    }

    protected function get_public_h5url(){
        return [
            'pzsm_url'=>$this->h5_base_url().'DetailsPage.html?id=20',
            'ysbz_url'=>$this->h5_base_url().'DetailsPage.html?id=19',
            'sgbz_url'=>$this->h5_base_url().'DetailsPage.html?id=18',
            'zxbz_url'=>$this->h5_base_url().'DecorateSecurity.html',
            'kfzx_url'=>$this->h5_base_url().'ConsumerLine.html',
            'mctk_url'=>$this->h5_base_url().'DetailsPage.html?id=21',
        ];
    }

    protected function get_base_url(){
        return $this->req->scheme().'://'.$this->req->host();
    }

    protected function get_page_list($count,$page=1,$size=20){
        $total = ceil($count/$size);
        if($page<=0 || $page>$total){
            return false;
        }
        return [
            'limit'=>($page-1)*$size.','.$size,
            'has_next'=>$page>=$total?false:true
        ];
        //return ($page-1)*$size.','.$size;
    }

    protected function response($data, $type = 'json', $code = 200)
    {
        $code = (string)$code;
        $data = tostring($data);
        return Response::create($data, $type)->code($code)->send();
    }
    protected function report_offer_type(){
        return [
            '',
            1=>'设计师',2=>'项目经理',3=>'业主'
        ];
    }
    /*protected function _check_api_safe($check_sign){
        //验证接口
        if(!Apisign::verifyKey()){
            return $this -> response(['code' => 201, 'msg' => '无效操作'])->send();
        }
        //检查用户登录信息
        $user_token = isset($_POST['api_key'])?$_POST['api_key']:'';
        if($check_sign==2 && strpos($user_token,'.')===false){
            return $this -> response(['code' => 201, 'msg' => '无效操作'])->send();
        }
        $user_data = Logintoken::read($user_token,$check_sign);
        if($check_sign==2 && (empty($user_data) || !isset($user_data['user_id']) || $user_data['user_id']<=0)){
            return $this -> response(['code' => 201, 'msg' => '无效操作'])->send();
        }
        $this->user_id = isset($user_data['user_id'])?$user_data['user_id']:0;
        $this->_check_forzen_user($this->user_id);
    }

    protected function _check_forzen_user($user_id){
        if($user_id>0){
            $redis = Redis::getInstance(0);

            if(!$redis){
                return false;
            }
            $redis->select(0);
            $key = 'frozen_users:'.$user_id;
            $data = $redis->get($key);
            if($data){
                return $this -> response(['code' => 201, 'msg' => '您的账号已被其他用户举报，现已被冻结不能再次使用。'])->send();
            }
        }

    }*/
}
