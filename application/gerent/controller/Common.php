<?php
/**
 * Created by PhpStorm.
 * User: xiaohui
 * Date: 2017-06-13
 * Time: 16:42
 */
namespace app\gerent\controller;
use think\Request,
    app\gerent\library\Login;
class Common extends \app\common\controller\Base{
    /**
     * @var null|Request
     */
    protected $request = null;
    protected $M = null;
    protected $datetime = '';

    public function _initialize($check_login=true){
        parent::_initialize();
        $this->request = Request::instance();
        $this->datetime = date('Y-m-d H:i:s');
        if($check_login){
            $this->_check_acc();
        }
    }

    protected function _check_acc(){

        if(!$this->_check_login()){
            $this->redirect(url('Lgn/index').'?ref='.get_full_url());
        }

        if($this->_is_super_admin()){
            return true;
        }

        $contr = $this->request->controller();
        $act = $this->request->action();
        $acc_key = strtolower($contr.'_'.$act);

        if(in_array($acc_key,config('rbac.no_auth_node'))){
            return true;
        }

        $acc = session('_ACCESS_LIST.'.$acc_key);

        if(!$acc || empty($acc)){
            if($this->request->isAjax()){
                return ['err'=>1,'mesg'=>'权限不足'];
            }else{
                $this->error('权限不足');
            }
        }
    }

    protected function _is_super_admin(){
        return session('superadmin')?true:false;
    }

    protected function _check_login(){
        $ck_user_id = cookie('mcuser');
        if(!$ck_user_id){
            return false;
        }
        //var_dump(session('user_id'));
        if(is_null(session('user_id')) || !session('user_id')){

            return Login::login_by_userid($ck_user_id);
        }

        return true;
    }

    protected function _is_super_or_me($user_id){
        if(!session('superadmin') && session('user_id')!=$user_id){
            $this->error('你无权限修改该用户密码');
        }
    }

    /**
     * 后台公共分页方法
     * @param     $count
     * @param int $rowsize
     * @return array
     */
    protected function _pagenav($count, $rowsize = 15) {
        static $P = null;
        if (is_null($P)) {
            $P = new \extend\Page($count, $rowsize);
        }
        $config = array(
            'first' => '首页',
            'last' => '末页',
            'prev'=>'上一页',
            'next'=>'下一页',
            'theme' => '%first% %upPage% %linkPage% %downPage% %end%'
        );
        $P->replaceConfig($config);
        return array(
            'offset'=>$P->firstRow,
            'limit' =>$P->listRows,
            'txt' =>$P->getHeader(),
            'links' =>$P->show2()
        );
    }

    /**
     * 筛选的链接处理
     * @param int $f
     * @return string
     */
    protected function filterLink($p,$cate,$parm=array()){

        $default = array(
            'k'=>'f',
            'allkv'=>0,
            'alltxt'=>'全部'
        );

        $parm = empty($parm)?$default:array_merge($default,$parm);

        $class = ' btn-success';
        $links = array(
            array('p'=>'?'.$parm['k'].'='.$parm['allkv'],'t'=>$parm['alltxt'],'v'=>$parm['allkv'])
        );
        foreach($cate as $kd=>$v){
            $links[] = array('p'=>'?'.$parm['k'].'='.$kd,'t'=>$v,'v'=>$kd);
        }
        $tpl = '<a href="{P}" class="btn{C}">{T}</a>';
        $str = '';
        foreach($links as $k=>$lk){
            $c = $p==$lk['v']?$class:'';
            $str .= str_replace(array('{P}','{C}','{T}'),array($lk['p'],$c,$lk['t']),$tpl).'&nbsp;';
        }
        return $str;
    }
    /**
     * 筛选的下拉处理
     * @param $k
     * @return string
     */
    protected function filterSelect($sk,$parm){
        if(empty($parm)){
            return '';
        }
        $cked = ' selected';
        $opts = array();
        foreach($parm as $k=>$p){
            $opts[] = array('v'=>$k,'t'=>$p);
        }

        $tpl = '<option value="{V}"{S}>{T}</option>';
        $str = '';
        foreach($opts as $op){
            $c = $op['v']==$sk?$cked:'';
            $str .= str_replace(array('{V}','{S}','{T}'),array($op['v'],$c,$op['t']),$tpl);
        }
        return $str;
    }


    protected function b_powers(){
        return [
            0=>'* 没有权限',1=>'* 查看所有项目',2=>'* 查看+编辑所有项目',3=>'* 项目中分配的权限'
        ];
    }

    protected function do_status($t=1){
        if($t==1){
            return [
                0=>'未处理',
                1=>'已处理'
            ];
        }
        return [
            0=>'<span class="label label-important">未处理</span>',
            1=>'<span class="label">已处理</span>',
        ];

    }

    public function h5_base_url(){
        return 'https://'.$this->request->host() .'/mch5/mochuan/';
    }

    protected function b_power_tags()
    {
        return [
            0=>''
        ];
    }

    protected function p_type(){
        return [
            1=>'施工+设计',
            2=>'仅施工',
            3=>'仅设计'
        ];
    }
}
