/**
 * Created by xiaohui on 2015/7/20.
 */
$(function(){
    //添加地区
    $('a.openlayerwin').on('click',function(){
        var url = $(this).attr('data-url');
        if(!url){
            return false;
        }
        layeriframe(url, '添加/编辑分类', 500, 400);
    });

    //生成JS文件
    $('#createjsbtn').on('click',function(){
        var me = $(this), tb = me.attr('data-tb');
        if(!tb){
            return false;
        }
        me.children('i').removeClass('icon-file').addClass('icon-spinner disabled');
        me.children('span').text('正在处理...');
        $.post('/cate/cateopers',{'act':'creatjs','tb':tb,'ajax':1},function(d){
            if(d.err!='0'){
                layeralert(d.mesg,0,'操作提示');
            }else{
                layermsgpos(d.mesg,2,1);
            }
            me.children('i').removeClass('icon-spinner disabled').addClass('icon-file');
            me.children('span').text('生成JS文件');
            return false;
        },'json');
    });

    //保存检查
    $('#savedata').on('click',function(){
        var lv1id = $('select[name="lv1id"]'),
            name = $('#nameinput');
        /*if(!checkSelectEmpty(lv1id)){
            pageMesg.show('请选择上级分类',0);
            lv1id.focus();
            return false;
        }*/

        if(!checkInputEmpty(name)){
            pageMesg.show('请填写分类名称',0);
            name.focus();
            return false;
        }
        $('#postform').submit();
    });
    if($('#citybox').length>0){
        $("#citybox").cateSelect({
            url:'area.js',
            nodata:"none"
        });
    }
    if($('#goodsbox').length>0){
        $("#goodsbox").cateSelect({
            url:'goods.js',
            nodata:"none"
        });
    }
    if($('#carsbox').length>0){
        $("#carsbox").cateSelect({
            url:'cars.js',
            nodata:"none"
        });
    }
    if($('#partsbox').length>0){
        $("#partsbox").cateSelect({
            url:'parts.js',
            nodata:"none"
        });
    }

});