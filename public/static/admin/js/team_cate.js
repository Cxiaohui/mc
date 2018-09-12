/**
 * Created by chenxh on 2018/6/11.
 */
$(function () {

    //添加/编辑部门
    $('a.openlayerwin').on('click',function(){
        var url = $(this).attr('data-url');
        if(!url){
            return false;
        }
        layeriframe(url, '团队', 400, 300);
    });

    //保存检查
    $('#savedata').on('click',function(){
        var name = $('input[name="name"]');
        if(!checkInputEmpty(name)){
            pageMesg.show('请填写团队名称',0);
            name.focus();
            return false;
        }
        $('#postform').submit();
    });

});