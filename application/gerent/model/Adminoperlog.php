<?php
/**
 * Created by PhpStorm.
 * User: chenxh
 * Date: 2018/6/14
 * Time: 15:58
 */
namespace app\gerent\model;
use app\common\model\General;
//use think\Db;
class Adminoperlog extends General{

    public $t = 'admin_oper_log';

    protected static $instance;

    static public function instance(){
        if (is_null(self::$instance)) {
            self::$instance = new static();
        }
        return self::$instance;
    }

    public function save_data($content){
        if(!$content){
            return false;
        }
        $data = [
            'user_id'=>session('user_id'),
            'user_name'=>session('name'),
            'content'=>$content,
            'url'=>$_SERVER['REQUEST_URI'],
            'ip'=>request()->ip(),
            'addtime'=>$this->now_datetime
        ];

        return $this->add_data($data);
    }


}
