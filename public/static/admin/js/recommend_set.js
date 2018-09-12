/**
 * Created by xiaohui on 2015/8/14.
 */
$(function(){
    /*$("#areabox").cateSelect({
        url:'area.js',
        nodata:"none"
    });
    var site = $('select[name="site"]').val();
    if(site==2){
        $('#area_box').show();
    }
    //站点选择
    $('select[name="site"]').on('change',function(){
        var val = $(this).val();
        if(val==1){
            $('#area_box').hide();
        }else{
            $('#area_box').show();
        }
    });
    //
    */
    $('input[name="retype"]').on('change',function(){
        var val = $(this).val();
        if(val==1){
            $('#custombox').hide();
        }else{
            $('#custombox').show();
        }

    });
    //展示图上传
    var confg = {
        'btntext': '<i class="icon-upload-alt"></i> 上传展示图片',
        'server': uppath,
        'progress': $('#progressbar'),
        'statbar': $('.statusBar'),
        'viewimg': '',
        'inneralert':false,
        'isiframe':true,
        'formData': {'uptype':'recmdpic','typeid':0},
        'afterSuccess': function (file, response) {
            //console.log(response);
            var timenow = new Date().getTime();
            $('#picimg').attr('src', response['data'] + '?' + timenow);
            $('#picinput').val(response['data']);
        }

    };
    //插件初始化
    sUpd.init(confg);



    //分站的初始化
    //subsiteInit();

    $('#savedata').on('click',function(){

        var stb = $('input[name="stable"]').val(),
            sid = $('input[name="sid"]').val(),
            title = $('input[name="title"]'),
            pic = $('input[name="pic"]'),
            //type = $('select[name="type"]').val(),
            site = $('select[name="site"]').val(),
            sitecode = $('select[name="proveid"]'),
            betime = $('input[name="betime"]');


        var retype = 1;

        if(sid>0){
            //console.log('ddddd');
            retype = $('input[name="retype"]:checked').val();
        }else{
            retype = 2;
        }

        if(retype==1 && (!stb || stb=='' || !sid || sid=='')){
            pageMesg.show('数据有误，请关闭后重试',0);
            return false;
        }

        /*if(!checkInputEmpty(title)){
            pageMesg.show('请填写显示的标题',0);
            title.focus();
            return false;
        }*/

        if((!checkInputEmpty(pic) || pic.val().indexOf('def')>-1)){
            pageMesg.show('请上传显示的图片',0);
            pic.focus();
            return false;
        }

        /*if(site==2 && !checkSelectEmpty(sitecode)){
         pageMesg.show('选择分站地区',0);
         sitecode.focus();
         return false;
         }*/

        if(!checkInputEmpty(betime)){
            pageMesg.show('请填写开始时间',0);
            betime.focus();
            return false;
        }
        console.log(retype);
        if(retype==2){
            /*var title = $('input[name="title"]'),
                pic = $('input[name="pic"]'),
                url = $('input[name="url"]');*/
            var url = $('input[name="url"]');
            /**/

            if(!checkInputEmpty(url)){
                pageMesg.show('请填写跳转链接',0);
                url.focus();
                return false;
            }
            if(url.val().indexOf('http')<=-1){
                pageMesg.show('跳转链接不正确',0);
                url.focus();
                return false;
            }
        }
        //return false;

       $('#postform').submit();

    })

});
//分站下拉的初始化
/*
function subsiteInit(){
    var me = $('select[name="sitecode"]'),
        _val = $.trim(me.attr('data-val'));
    if(!_val || _val==''){
        return false;
    }
    me.val(_val).show();
}*/
