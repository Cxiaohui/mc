<?php
/**
 * Created by PhpStorm.
 * User: xiaohui
 * Date: 2017-06-15
 * Time: 20:36
 */

namespace app\gerent\model;
use app\common\model\Base;
use think\Db;
class Syspower extends  Base{

    protected $node_table = 'admin_node';
    protected $node_group_table = 'admin_node_group';
    protected $acc_table = 'admin_access';



    public function get_my_node_info($role_id){

        $node_ids = $this->get_my_node_ids($role_id);
        $nodes = $this->_get_list($this->node_table,['id'=>['in',$node_ids],'status'=>1,'level'=>['gt',1]],['level'=>'asc'],'id,name,title,level,pid,gid',0);

        $gids = array2to1($nodes,'gid');
        $groups = $this->find_groups(['id'=>['in',$gids]],'id');
        $gids = array2to1($groups,'id');


        $nodes = create_level_tree($nodes,1);
        $gid_node_ids = [];
        $acc_list = [];
        foreach($nodes as $nd){
            $gid_node_ids[$nd['gid']][] = $nd['id'];
            $acc_list[strtolower($nd['name'])] = [$nd['id'],$nd['gid']];
            if(!empty($nd['sub'])){
                foreach($nd['sub'] as $sub1){
                    $gid_node_ids[$sub1['gid']][] = $sub1['id'];
                    $acc_list[strtolower($nd['name'].'_'.$sub1['name'])] = [$sub1['id']];
                    if(!empty($sub1['sub'])){
                        foreach($sub1['sub'] as $sub2){
                            $gid_node_ids[$sub2['gid']][] = $sub2['id'];
                            $acc_list[strtolower($nd['name'].'_'.$sub2['name'])] = [$sub2['id']];
                        }
                    }
                }
            }
        }
        return ['gids'=>$gids,'acc_list'=>$acc_list,'acc_ids'=>$node_ids,'gid_node_ids'=>$gid_node_ids];
    }

    public function find_groups($where,$field='*',$limit=0){
        return $this->_get_list($this->node_group_table,$where,['sort'=>'asc'],$field,$limit);
    }

    //找出当前登录者的权限节点
    public function get_my_node_ids($role_id){
        $acc = $this->_get_list($this->acc_table,['role_id'=>$role_id],[],'node_id',0);
        return array2to1($acc,'node_id');
    }

    public function get_menu_by_ids($ids){
        if(empty($ids)){
            return [];
        }
        $nodes = $this->_get_list($this->node_table,['id'=>['in',$ids],'status'=>1,'ismenu'=>1],['level'=>'asc','sort'=>'asc'],'id,title,name,icon,level,pid',0);
        return create_level_tree($nodes,1);
    }
}