<?php
/**
 * Created by PhpStorm.
 * User: xiaohui
 * Date: 2017-06-13
 * Time: 20:15
 */
namespace app\gerent\model;
use app\common\model\Base;
use think\Db;
class Systable extends  Base{
    protected $admin_table = 'admin';
    protected $depart_table = 'admin_department';
    protected $node_table = 'admin_node';
    protected $node_group_table = 'admin_node_group';
    protected $acc_table = 'admin_access';
    protected $role_table = 'admin_role';
    protected $role_user_table = 'admin_role_user';

    //=====================admin=======================

    public function get_admin_list($where,$field='*',$limit=15){
        return $this->_get_list($this->admin_table,$where,['id'=>'desc'],$field,$limit);
    }

    public function get_admin_count($where){
        return $this->_get_count($this->admin_table,$where);
    }

    public function get_admin_info($where,$field='*'){
        return $this->_get_info($this->admin_table,$where,$field);
    }

    public function save_admin_data($data){
        if(isset($data['id']) && $data['id']>0){
            return Db::name($this->admin_table)->where(['id'=>$data['id']])->update($data);
        }else{
            $data['create_time'] = time();
            $data['pwd'] = create_pwd($data['pwd'],$data['stat']);
            return Db::name($this->admin_table)->insert($data);
        }
    }

    public function update_admin($where,$update){
        return $this->_update_data($this->admin_table,$where,$update);
    }

    public function del_admin($id){
        return $this->update_admin(['id'=>$id],['is_del'=>1]);
        //return $this->_del_row($this->admin_table,['id'=>$id]);
        //return $this->_update_data($this->admin_table,['id'=>$id],['status'=>0,'role'=>'','role_id'=>0,'isdel'=>1]);
    }

    //============================admin_department

    public function get_depart_list($where,$field='*',$limit=15,$kv=false){
        $list = $this->_get_list($this->depart_table,$where,['id'=>'asc'],$field,$limit);
        if(!$kv){
            return $list;
        }
        return create_kv($list,'id','name');
    }

    public function get_depart_count($where){
        return $this->_get_count($this->depart_table,$where);
    }

    public function get_depart_info($where,$field='*'){
        return $this->_get_info($this->depart_table,$where,$field);
    }

    public function save_depart_data($data){
        if(isset($data['id']) && $data['id']>0){
            return Db::name($this->depart_table)->where(['id'=>$data['id']])->update($data);
        }else{
            $data['addtime'] = time();
            return Db::name($this->depart_table)->insert($data);
        }
    }

    public function del_depart($where){
        return $this->_del_row($this->depart_table,$where);
    }

    //=============================admin_node

    public function get_all_nodes(){
        return $this->_get_list($this->node_table,['status'=>1],['level'=>'asc'],'id,title,level,pid',0);
    }
    /**
     * 获取菜单项
     * @return array
     */
    public function get_node_pids(){
        return $this->_get_list($this->node_table,['ismenu'=>1,'status'=>1],['level'=>'asc','sort'=>'asc'],'id,title,level,pid',0);
    }
    public function get_node_list($where,$field='*',$limit=15){
        return $this->_get_list($this->node_table,$where,['level'=>'asc','sort'=>'asc'],$field,$limit);
    }

    public function get_node_count($where){
        return $this->_get_count($this->node_table,$where);
    }

    public function get_node_info($where,$field='*'){
        return $this->_get_info($this->node_table,$where,$field);
    }

    public function del_node($nid){
        return $this->_del_row($this->node_table,['id'=>$nid]);
    }

    public function update_node($where,$update){
        return $this->_update_data($this->node_table,$where,$update);
    }

    public function save_add_node($data){
        //如果类型被修改成’项目‘，则检查是不是已经有了项目数据，有则不能修改
        if ($data['level'] == 1) {
            $module = $this->get_node_count(['level' => '1']);
            if ($module) {
                return array('err'=>1,'mesg'=>'项目只能有一个，不能再添加项目类型');
            }
        }

        //判断插入的level值，与其父节点的level值之差，不能大于等于2级或0
        $pidn = $this->get_node_info(['id'=>$data['pid']],'level,gid');
        $res      = $data['level'] - $pidn['level'];
        if ($res > 1) {
            return array('err'=>1,'mesg'=>'父节点与类型选择不匹配');
        }

        if($data['level']==3){
            $post['gid'] = $pidn['gid'];
        }
        $res = Db::name($this->node_table)->insert($data);
        if (!$res) {
            return array('err'=>1,'mesg'=>'添加权限节点失败');
        }
        return array('err'=>0,'mesg'=>'添加权限节点成功');
    }

    public function save_edit_node($data){

        if (!$data['id'] || $data['id']<=0) {
            return array('err'=>1,'mesg'=>'系统错误，请重试');
        }

        $pid = $data['pid'];
        $oldPid = $data['oldPid'];
        $level = $data['level'];
        $oldLevel = $data['oldLevel'];
        unset($data['oldPid'],$data['oldLevel']);
        if ($data['id'] == $pid) {
            return array('err'=>1,'mesg'=>'父节点不能修改为自身');
        }

        //如果类型被修改成’项目‘，则检查是不是已经有了项目数据，有则不能修改
        if ($oldLevel != 1 && $level == 1) {
            $module = $this->get_node_count(['level' => '1']);
            if ($module) {
                return array('err'=>1,'mesg'=>'项目只能有一个，不能再修改为项目类型');
            }
        }

        //如果修改了父节点或类型信息
        $pidn = $this->get_node_info(['id'=>$data['pid']],'level,gid');

        if ($pid != $oldPid || $level != $oldLevel) {
            //查找是否有子节点
            $childs = $this->get_node_count(['pid' => $data['id']]);;
            if ($childs) {
                return array('err'=>1,'mesg'=>'该节点下面还有子节点，不能修改类型或父节点');
            }

            //判断修改后的level值，与其父节点的level值之差，不能大于等于2级或0

            $res = $level - $pidn['level'];
            if ($res > 1) {
                return array('err'=>1,'mesg'=>'父节点与类型选择不匹配');
            }
        }

        if($level==3){
            $post['gid'] = $pidn['gid'];
        }
        Db::name($this->node_table)->where(['id'=>$data['id']])->update($data);
        return array('err'=>0,'mesg'=>'编辑成功');
    }

    //=============================admin_node_group
    /**
     * 获取分组
     * @param string $fields
     * @return mixed
     */
    public function get_node_groups($fields = 'id,name'){
        return Db::name($this->node_group_table)->field($fields)->order('sort asc')->select();
    }

    public function get_node_group_list($where,$field='*',$limit=15){
        return $this->_get_list($this->node_group_table,$where,['sort'=>'asc'],$field,$limit);
    }

    public function get_node_group_count($where){
        return $this->_get_count($this->node_group_table,$where);
    }

    public function get_node_group_info($where,$field='*'){
        return $this->_get_info($this->node_group_table,$where,$field);
    }

    public function del_node_group($gid){
        return $this->_del_row($this->node_group_table,['id'=>$gid]);
    }

    public function update_node_group($where,$update){
        return $this->_update_data($this->node_group_table,$where,$update);
    }

    public function save_group_data($data){
        if(isset($data['id']) && $data['id']>0){
            return Db::name($this->node_group_table)->where(['id'=>$data['id']])->update($data);
        }else{
            return Db::name($this->node_group_table)->insert($data);
        }
    }

    //===================================admin_role

    public function get_role_list($where,$field='*',$limit=15){
        return $this->_get_list($this->role_table,$where,['id'=>'asc'],$field,$limit);
    }

    public function get_role_count($where){
        return $this->_get_count($this->role_table,$where);
    }

    public function get_role_info($where,$field='*'){
        return $this->_get_info($this->role_table,$where,$field);
    }

    public function del_role_group($gid){
        return $this->_del_row($this->role_table,['id'=>$gid]);
    }

    public function save_role_data($data){
        if(isset($data['id']) && $data['id']>0){
            Db::name($this->role_table)->where(['id'=>$data['id']])->update($data);
            return $data['id'];
        }else{
            return Db::name($this->role_table)->insertGetId($data);
        }
    }


    //===================================admin_access

    public function get_access_list($where,$field='*'){
        return $this->_get_list($this->acc_table,$where,[],$field,0);
    }

    public function save_access_date($role_id,$access){
        $where = ['role_id'=>$role_id];
        $this->_del_row($this->acc_table,$where);
        if(empty($access)){
            return false;
        }
        $data = [];
        foreach($access as $acc){
            $data[] = ['role_id'=>$role_id,'node_id'=>$acc];
        }
        return Db::name($this->acc_table)->insertAll($data);
    }


    //===================================admin_role_user
}