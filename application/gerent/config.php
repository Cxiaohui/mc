<?php
/**
 * Created by PhpStorm.
 * User: chenxh
 * Date: 2018/1/16
 * Time: 15:28
 */
return [
    'template'=>[
        'layout_on'=>true,
        'layout_name'=>'layout'
    ],
    'paginate'=> [
        'type'     => 'bootstrap',
        'var_page' => 'page',
    ],
    'rbac'=>[
        'rbac_superman_id'=>2,
        'no_auth_node'=>['index_index','sysuser_edit','sysuser_changepwd']
    ],
    'ad_cache_key'=>[
        'admin_in_depart'=>'admin_in_depart'
    ],
    //后台登录状态有效期，3小时
    'admin_lgn_status_expire'=>3*3600
];