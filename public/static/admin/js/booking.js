/**
 * Created by xiaohui on 2015/7/27.
 */

$(function(){
    $('a.showbookinfo').on('click',function(){
        var url = $(this).attr('data-url');
        if(!url){
            return false;
        }
        layeriframe(url, '预约详情', 600, 600);
    });
});
