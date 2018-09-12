<?php
/**
 * Created by PhpStorm.
 * User: xiaohui
 * Date: 2017-06-13
 * Time: 16:37
 */
namespace app\common\model;
use think\Model;
use think\Db;
class Base extends Model{


    protected function _get_count($table,$where){
        return Db::name($table)->where($where)->count();
    }

    protected function _get_info($table,$where,$field='*'){
        return Db::name($table)->field($field)->where($where)->find();
    }

    protected function _get_list($table,$where,$order,$field='*',$limit=15){
        return Db::name($table)->field($field)->where($where)->order($order)->limit($limit)->select();
    }

    protected function _del_row($table,$where){
        return Db::name($table)->where($where)->delete();
    }

    protected function _update_data($table,$where,$update){
        return Db::name($table)->where($where)->update($update);
    }

    protected function _insert_data($table,$data,$return_id=false){
        if($return_id){
            return Db::name($table)->insertGetId($data);
        }else{
            return Db::name($table)->insert($data);
        }
    }

    protected function _insert_all($table,$data){
        return Db::name($table)->insertAll($data);
    }

    protected function _query($sql,$bind=[]){
        return Db::query($sql,$bind);

    }
    protected function _execute($sql,$bind=[]){
        return Db::execute($sql,$bind);
    }
}