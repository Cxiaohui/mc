/**
 * Created by xiaohui on 2015/7/16.
 */
$(function () {

    //添加/编辑部门
    $('a.openlayerwin').on('click',function(){
        var url = $(this).attr('data-url');
        if(!url){
            return false;
        }
        layeriframe(url, '部门资料', 400, 300);
    });

    //保存检查
    $('#savedata').on('click',function(){
        var cpid = $('select[name="cpid"]'),
            name = $('input[name="name"]');
        if(!checkSelectEmpty(cpid)){
            pageMesg.show('请选择所属公司',0);
            cpid.focus();
            return false;
        }

        if(!checkInputEmpty(name)){
            pageMesg.show('请填写部门名称',0);
            name.focus();
            return false;
        }
        $('#postform').submit();
    });

});