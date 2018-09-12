/**
 * Created by xiaohui on 2015/8/8.
 */
$(function(){


    $('#showbtn').on('click',function(){
        var box = $('#oginfobox'),dis = box.css('display');
        if(dis=='none'){
            $(this).text('- 收起');
            box.show();
        }else{
            $(this).text('+ 展开');
            box.hide();
        }


    });


});