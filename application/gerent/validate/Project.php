<?php
/**
 * Created by PhpStorm.
 * User: chenxh
 * Date: 2018/6/18
 * Time: 22:47
 */
namespace app\gerent\validate;
use think\Validate;

class Project extends Validate{

    protected $rule = [
        'owner_user_id'  =>  'require|gt:0',
        //
        'name'  =>  'require',
        'type'  =>  'require|in:1,2,3',
        'house_type'  =>  'require',
        'acreage'  =>  'require',
        'decoration_style'  =>  'require',
        'address'  =>  'require',
        //
        'customer_manager_user_id'  =>  'require|gt:0',
        //'customer_manager_role_id'  =>  'require|gt:0',
        'desgin_user_id'  =>  'require|gt:0',
        //'desgin_role_id'  =>  'require|gt:0',
        'desgin_assistant_user_id'  =>  'require|gt:0',
        //'desgin_assistant_role_id'  =>  'require|gt:0',
        'manager_user_id'  =>  'require|gt:0',
        //'manager_role_id'  =>  'require|gt:0',
        'supervision_user_id'  =>  'require|gt:0',
        //'supervision_role_id'  =>  'require|gt:0',
        'decorate_butler_user_id'  =>  'require|gt:0',
        //'decorate_butler_role_id'  =>  'require|gt:0',
        //

        'owner_name'  =>  'require',
        'owner_mobile'  =>  'require',
        'owner_address'  =>  'require'
    ];

    protected $message = [
        'owner_user_id.require'  =>  '关联的客户信息丢失，请重新处理',
        'owner_user_id.gt'  =>  '关联的客户信息丢失，请重新处理',

        'name.require'  =>  '请填写项目名称',
        'type.require'  =>  '请选择项目范围',
        'type.in'  =>  '请选择项目范围.',
        'house_type.require'  =>  '请填写项目户型',
        'acreage.require'  =>  '请填写项目面积',
        'decoration_style.require'  =>  '请填写项目装修风格',
        'address.require'  =>  '请填写项目地址',
        //
        'manager_user_id.require'=>'请选择项目经理',
        'manager_user_id.gt'=>'请选择项目经理',
        //'manager_role_id.require'=>'请选择项目经理的权限角色',
        //'manager_role_id.gt'=>'请选择项目经理的权限角色',

        'customer_manager_user_id.require'=>'请选择项目客户经理',
        'customer_manager_user_id.gt'=>'请选择项目客户经理',
        //'customer_manager_role_id.require'=>'请选择项目客户经理的权限角色',
        //'customer_manager_role_id.gt'=>'请选择项目客户经理的权限角色',

        'desgin_user_id.require'=>'请选择项目设计师',
        'desgin_user_id.gt'=>'请选择项目设计师',
        //'desgin_role_id.require'=>'请选择项目设计师的权限角色',
        //'desgin_role_id.gt'=>'请选择项目设计师的权限角色',

        'desgin_assistant_user_id.require'=>'请选择项目设计师助理',
        'desgin_assistant_user_id.gt'=>'请选择项目设计师助理',
        //'desgin_assistant_role_id.require'=>'请选择项目设计师助理的权限角色',
        //'desgin_assistant_role_id.gt'=>'请选择项目设计师助理的权限角色',

        'supervision_user_id.require'=>'请选择项目质检',
        'supervision_user_id.gt'=>'请选择项目质检',
        //'supervision_role_id.require'=>'请选择项目质检的权限角色',
        //'supervision_role_id.gt'=>'请选择项目质检的权限角色',

        'decorate_butler_user_id.require'=>'请选择装修管家',
        'decorate_butler_user_id.gt'=>'请选择装修管家',
        //'decorate_butler_role_id.require'=>'请选择装修管家的权限角色',
        //'decorate_butler_role_id.gt'=>'请选择装修管家的权限角色',
        //

        'owner_name.require'  =>  '请填写业主名称',
        'owner_mobile.require'  =>  '请填写业主电话',
        'owner_address.require'  =>  '请填写业主联系地址',

    ];

    protected $scene = [
        'add','edit'
    ];
}