/**
 * Created by chenxh on 2018/9/25.
 */
$(function(){

    $('a.openlayerwin').on('click',function(){
        var url = $(this).attr('data-url');
        if(!url){
            return false;
        }
        layeriframe(url, '发送消息', 400, 300);
    });
});