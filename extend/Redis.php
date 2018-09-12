<?php
namespace extend;
/**
 *
 * User: XiaoHui
 * Date: 2016/5/11 14:58
 */
class Redis {

    static protected $host = '127.0.0.1';
    static protected $prot = 6379;
    static private $_instance = null;
    const DB = 0; //0正式站，1测试站

    /**
     * 禁止继承
     */
    public function __construct(){
        //exit('deny construct!');
    }

    /**
     * 禁止克隆
     */
    public function __clone(){
        exit('deny clone!');
    }

    /**
     * 获取redis对象
     * @return \redis
     */
    static public function getInstance($db=0){
        if(is_null(self::$_instance)){
            self::$_instance = self::connect($db);
        }
        return self::$_instance;
    }

    /**
     * 连接redis
     * @return \redis
     */
    static private function connect($db=0){
        if(in_array('redis',get_loaded_extensions())){
            $redis = new \redis();
            $redis->connect(self::$host, self::$prot);
            $redis->select($db);
            return $redis;
        }
        return null;
    }

}