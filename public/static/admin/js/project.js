
//编辑器
if($('#p_remarks').length>0){
    ue1 = UE.getEditor('p_remarks', {toolbars: [myueditorconfig],serverUrl:ed_url});
}
if($('#owner_remarks').length>0){
    ue2 = UE.getEditor('owner_remarks', {toolbars: [myueditorconfig],serverUrl:ed_url});
}


$(function(){
    $('#subbtn').on('click',function(){
            //项目基本信息
        var name = $('input[name="name"]'),
            type = $('select[name="type"]'),
            house_type = $('input[name="house_type"]'),
            acreage = $('input[name="acreage"]'),
            decoration_style = $('input[name="decoration_style"]'),
            address = $('input[name="address"]'),
            //p_remarks = $('#p_remarks'),
            //项目主要负责人信息
            manager_user_id = $('select[name="manager_user_id"]'),
            //manager_role_id = $('select[name="manager_role_id"]'),
            customer_manager_user_id = $('select[name="customer_manager_user_id"]'),
            //customer_manager_role_id = $('select[name="customer_manager_role_id"]'),
            desgin_user_id = $('select[name="desgin_user_id"]'),
            //desgin_role_id = $('select[name="desgin_role_id"]'),
            desgin_assistant_user_id = $('select[name="desgin_assistant_user_id"]'),
            //desgin_assistant_role_id = $('select[name="desgin_assistant_role_id"]'),
            supervision_user_id = $('select[name="supervision_user_id"]'),
            //supervision_role_id = $('select[name="supervision_role_id"]'),
            decorate_butler_user_id = $('select[name="decorate_butler_user_id"]'),
            //decorate_butler_role_id = $('select[name="decorate_butler_role_id"]'),
            //业主信息
            owner_name = $('input[name="owner_name"]'),
            owner_mobile = $('input[name="owner_mobile"]'),
            owner_address = $('input[name="owner_address"]');
            //owner_remarks = $('#owner_remarks');

        if(!checkInputEmpty(name)){
            formValid.showErr(name,'请填写项目名称');
            return false;
        }else{
            formValid.showSuccess(name);
        }
        if(!checkSelectEmpty(type)){
            formValid.showErr(type,'请选择项目范围');
            return false;
        }else{
            formValid.showSuccess(type);
        }
        if(!checkInputEmpty(house_type)){
            formValid.showErr(house_type,'请填写项目户型');
            return false;
        }else{
            formValid.showSuccess(house_type);
        }

        if(!checkInputEmpty(acreage)){
            formValid.showErr(acreage,'请填写项目面积');
            return false;
        }else{
            formValid.showSuccess(acreage);
        }

        if(!checkInputEmpty(decoration_style)){
            formValid.showErr(decoration_style,'请填写项目装修风格');
            return false;
        }else{
            formValid.showSuccess(decoration_style);
        }

        if(!checkInputEmpty(address)){
            formValid.showErr(address,'请填写项目地址');
            return false;
        }else{
            formValid.showSuccess(address);
        }

        if(!checkSelectEmpty(type)){
            formValid.showErr(type,'请选择项目范围');
            return false;
        }else{
            formValid.showSuccess(type);
        }
        if(!checkSelectEmpty(manager_user_id)){
            formValid.showErr(manager_user_id,'请选择项目经理');
            return false;
        }else{
            formValid.showSuccess(manager_user_id);
        }
        /*if(!checkSelectEmpty(manager_role_id)){
            formValid.showErr(manager_role_id,'请选择项目经理的权限角色');
            return false;
        }else{
            formValid.showSuccess(manager_role_id);
        }*/

        if(!checkSelectEmpty(customer_manager_user_id)){
            formValid.showErr(customer_manager_user_id,'请选择项目客户经理');
            return false;
        }else{
            formValid.showSuccess(customer_manager_user_id);
        }
        /*if(!checkSelectEmpty(customer_manager_role_id)){
            formValid.showErr(customer_manager_role_id,'请选择项目客户经理的权限角色');
            return false;
        }else{
            formValid.showSuccess(customer_manager_user_id);
        }*/

        if(!checkSelectEmpty(desgin_user_id)){
            formValid.showErr(desgin_user_id,'请选择项目设计师');
            return false;
        }else{
            formValid.showSuccess(desgin_user_id);
        }
        /*if(!checkSelectEmpty(desgin_role_id)){
            formValid.showErr(desgin_role_id,'请选择项目设计师的权限角色');
            return false;
        }else{
            formValid.showSuccess(desgin_role_id);
        }*/

        if(!checkSelectEmpty(desgin_assistant_user_id)){
            formValid.showErr(desgin_assistant_user_id,'请选择项目设计师助理');
            return false;
        }else{
            formValid.showSuccess(desgin_assistant_user_id);
        }
        /*if(!checkSelectEmpty(desgin_assistant_role_id)){
            formValid.showErr(desgin_assistant_role_id,'请选择项目设计师助理的权限角色');
            return false;
        }else{
            formValid.showSuccess(desgin_assistant_role_id);
        }*/

        if(!checkSelectEmpty(supervision_user_id)){
            formValid.showErr(supervision_user_id,'请选择项目质检');
            return false;
        }else{
            formValid.showSuccess(supervision_user_id);
        }
        /*if(!checkSelectEmpty(supervision_role_id)){
            formValid.showErr(supervision_role_id,'请选择项目质检的权限角色');
            return false;
        }else{
            formValid.showSuccess(supervision_role_id);
        }*/

        if(!checkSelectEmpty(decorate_butler_user_id)){
            formValid.showErr(decorate_butler_user_id,'请选择装修管家');
            return false;
        }else{
            formValid.showSuccess(decorate_butler_user_id);
        }
        /*if(!checkSelectEmpty(decorate_butler_role_id)){
            formValid.showErr(decorate_butler_role_id,'请选择装修管家的权限角色');
            return false;
        }else{
            formValid.showSuccess(decorate_butler_role_id);
        }*/
        if(!checkInputEmpty(owner_name)){
            formValid.showErr(owner_name,'请填写业主名称');
            return false;
        }else{
            formValid.showSuccess(owner_name);
        }
        if(!checkInputEmpty(owner_mobile)){
            formValid.showErr(owner_mobile,'请填写业主电话');
            return false;
        }else{
            formValid.showSuccess(owner_mobile);
        }
        if(!checkInputEmpty(owner_address)){
            formValid.showErr(owner_address,'请填写业主联系地址');
            return false;
        }else{
            formValid.showSuccess(owner_address);
        }
    });
});


