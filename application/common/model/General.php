<?php
/**
 * Created by PhpStorm.
 * User: chenxh
 * Date: 2018/6/13
 * Time: 17:10
 */
namespace app\common\model;
use think\Model;
use think\Db;
class General extends Model{

    public $t = '';
    public $now_datetime = '';

    public function __construct(array $data = [])
    {
        parent::__construct($data);
        $this->now_datetime = date('Y-m-d H:i:s');
    }

    public function get_list($where,$field='*',$limit=15){
        return Db::name($this->t)->field($field)->where($where)->order(['id'=>'desc'])->limit($limit)->select();
        //return $this->_get_list($this->t,$where,['id'=>'desc'],$field,$limit);
    }

    public function get_order_list($where,$field='*',$order=['id'=>'desc'],$limit=15){
        return Db::name($this->t)->field($field)->where($where)->order($order)->limit($limit)->select();
    }

    public function get_count($where){
        return Db::name($this->t)->where($where)->count();
        //return $this->_get_count($this->t,$where);
    }

    public function get_info($where,$field='*'){
        return Db::name($this->t)->field($field)->where($where)->find();
        //return $this->_get_info($this->t,$where,$field);
    }

    public function update_data($where,$update){
        return Db::name($this->t)->where($where)->update($update);
        //return $this->_update_data($this->t,$where,$update);
    }

    public function add_data($data,$return_id=false){
        if($return_id){
            return Db::name($this->t)->insertGetId($data);
        }
        return Db::name($this->t)->insert($data);
    }

    public function del_row($where){
        return Db::name($this->t)->where($where)->delete();
    }


    public function insert_all($data){
        return Db::name($this->t)->insertAll($data);
    }

    public function query_sql($sql,$bind=[]){
        return Db::query($sql,$bind);

    }
    /*public function get_sql($sql,$bind=[]){
        return Db::getRealSql($sql,$bind);
    }*/
    public function execute_sql($sql,$bind=[]){
        return Db::execute($sql,$bind);
    }

    public function set_inc($where,$field){
        return Db::name($this->t)->where($where)->setInc($field);
    }

    protected function _get_list($table,$where,$order,$field='*',$limit=15){
        return Db::name($table)->field($field)->where($where)->order($order)->limit($limit)->select();
    }
    protected function _get_order_info($table,$where,$order,$field='*'){
        return Db::name($table)->field($field)->where($where)->order($order)->find();
    }
}