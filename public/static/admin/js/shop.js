/**
 * Created by xiaohui on 2015/7/23.
 */
$(function(){
    //推荐
    $('a.openlayerwin').on('click',function(){
        var url = $(this).attr('data-url');
        if(!url){
            return false;
        }
        layeriframe(url, '推荐设置', 600, 550);
    });

    //地区
    if($('#areabox').length>0){
        $("#areabox").cateSelect({
            url:'area.js',
            nodata:"none"
        });

        /*$('#areabox select.lv1select').on('change', function () {
            var val = $(this).val(),url = location.href;
            if(url.indexOf('lv1id')>-1){
                url = url.replace(/lv1id\/\d+/i,'lv1id/'+val);
            }else{
                url = url.replace('.html','/lv1id/'+val+'.html');
            }


            location.href = url;
        });
        $('#areabox select.lv2select').on('change', function () {
            var val = $(this).val(),url = location.href;
            if(url.indexOf('lv2id')>-1){
                url = url.replace(/lv2id\/\d+/i,'lv2id/'+val);
            }else{
                url = url.replace('.html','/lv2id/'+val+'.html');
            }
            //console.log(url);
            location.href = url;
        });
        $('#areabox select.lv3select').on('change', function () {
            var val = $(this).val(),url = location.href;
            if(url.indexOf('lv3id')>-1){
                url = url.replace(/lv3id\/\d+/i,'lv3id/'+val);
            }else{
                url = url.replace('.html','/lv3id/'+val+'.html');
            }
            //console.log(url);
            location.href = url;
        });*/
    }

    //跟进状态
    /*$('select[name="status"]').on('change',function(){
        var sval = $(this).val(),url = location.href,erg=/stat\/\d/i,replae = '';
        if(url.indexOf('stat')>-1){
            if(sval!=''){
                replae = 'stat/'+sval;
            }
        }else{
            erg = '.html';
            replae = '/stat/'+sval+'.html';
        }

        url = url.replace(erg,replae);
        location.href = url;
    });
*/

    $('a.showsfollow').on('click',function(){
        var url = $(this).attr('data-url');
        if(!url){
            return false;
        }
        layeriframe(url, '跟进情况', 600, 600);
    });

    $('a.addbookingbtn').on('click',function(){
        var url = $(this).attr('data-url');
        if(!url){
            return false;
        }
        layeriframe(url, '预约服务', 600, 600);
    });
});