/**
 * Created by chenxh on 2017/7/11.
 */
$(function(){

    $('#edit_btn').on('click',function () {
        $('#view_tr').hide();
        $('#edit_tr').show();
    });

    $('#cancel_btn').on('click',function () {
        $('#view_tr').show();
        $('#edit_tr').hide();
    });
    $('#save_btn').on('click',function(){
        var cont = $('textarea[name="content"]');
        if(!checkInputEmpty(cont)){
            formValid.showErr(cont,'请填写回复内容');
            return false;
        }
        $('#replyform').submit();
    });
});