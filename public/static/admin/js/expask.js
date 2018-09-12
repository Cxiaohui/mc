/**
 * Created by xiaohui on 2015/7/30.
 */
$(function(){
    $('a.showaskinfo').on('click',function(){
        var url = $(this).attr('data-url');
        if(!url){
            return false;
        }
        layeriframe(url, '问题详情', 600, 600);
    });
});