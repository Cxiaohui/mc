/**
 * Created by chenxh on 2018/7/10.
 */
$(function () {

    //添加/编辑商品分类
    $('a.openlayerwin').on('click',function(){
        var url = $(this).attr('data-url');
        if(!url){
            return false;
        }
        layeriframe(url, '商品分类', 400, 300);
    });

    //保存检查
    $('#savedata').on('click',function(){
        var name = $('input[name="name"]');
        if(!checkInputEmpty(name)){
            pageMesg.show('请填写分类名称',0);
            name.focus();
            return false;
        }
        $('#postform').submit();
    });

});