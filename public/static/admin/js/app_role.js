/**
 * Created by chenxh on 2018/6/15.
 */
$(function () {

    //表单检查
    $('#subform').on('click', function () {
        var name = $('input[name="name"]');
        if(!checkInputEmpty(name)){
            formValid.showErr(name,'请填写角色名称');
            return false;
        }else{
            formValid.showSuccess(name);
        }
        //return false;
        $('#roleform').submit();
    });

});