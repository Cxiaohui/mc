/**
 * Created by chenxh on 2018/9/25.
 */
$(function () {
   $('#savedata').on('click',function(){
       var mesg = $('#message');

       if(!checkInputEmpty(mesg)){
           pageMesg.show('请填写消息内容',0);
           mesg.focus();
           return false;
       }

       if(mesg.val().length>140){
           pageMesg.show('消息内容太长了',0);
           mesg.focus();
           return false;
       }

       $('#postform').submit();
   });
});