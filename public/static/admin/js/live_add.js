/**
 * Created by chenxh on 2017/11/2.
 */
$(function(){
    if($("#areabox").length > 0){
        $("#areabox").cateSelect({
            url:'area.js',
            nodata:"none"
        });
    }

    $('#fetch_it').on('click',function(){

        var user_id = $('input[name="user_id"]');

        if(!checkInputEmpty(user_id)){
            formValid.showErr(user_id,'请填写主播ID');
            return false;
        }else{
            formValid.showSuccess(user_id);
        }
        $('#postform').submit();
        return false;
    });

    $('#subbtn').on('click',function(){
        var user_id = $('input[name="user_id"]');

        if(!checkInputEmpty(user_id)){
            formValid.showErr(user_id,'请填写主播ID');
            return false;
        }else{
            formValid.showSuccess(user_id);
        }
        $('#postform').submit();
        return false;
    });

});