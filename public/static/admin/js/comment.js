/**
 * Created by xiaohui on 2015/7/29.
 */
$(function(){
    $('a.showcinfo').on('click',function(){
        var url = $(this).attr('data-url');
        if(!url){
            return false;
        }
        layeriframe(url, '评论详情', 600, 600);
    });
});