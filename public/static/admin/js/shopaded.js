/**
 * Created by xiaohui on 2015/7/23.
 */

$(function(){
    //地区
    $("#areabox").cateSelect({
        url:'area.js',
        nodata:"none"
    });
    //logo上传
    var confg = {
        'btntext': '<i class="icon-upload-alt"></i> 上传机构Logo图片',
        'server': uppath,
        'progress': $('#progressbar'),
        'statbar': $('.statusBar'),
        'viewimg': '',
        'inneralert':false,
        'isiframe':false,
        'formData': {'uptype':'hosp_logo','typeid':0},
        'afterSuccess': function (file, response) {
            //console.log(response);
            var timenow = new Date().getTime();
            $('#logoimg').attr('src', response['data'] + '?' + timenow);
            $('#logoinput').val(response['info']);
        }

    };
    //插件初始化
    sUpd.init(confg);

    if($('#editor').length>0){
        ue = UE.getEditor('editor', {toolbars: [['insertimage']],serverUrl:ed_url});
    }


    //carsel.init();
    //pros.init();
    valid.init();
    //delFile.init();
    //mFile.init();
});

//数据验证
var valid = {
    'form':'#postform',
    'subbtn':'#subbtn',
    init:function(){
        $(this.form).attr({'action':location.href});
        $(this.subbtn).on('click',function(){
            valid._checkVaild();
        });
    },
    _checkVaild:function(){
        var name = $('input[name="name"]'),
            lv1id = $('select[name="proveid"]'),
            lv2id = $('select[name="cityid"]'),
            lv3id = $('select[name="areaid"]'),
            lv4id = $('select[name="distid"]'),
            address = $('input[name="address"]'),
            //man = $('input[name="man"]'),
            //phone = $('input[name="phone"]'),
            type = $('input[name="type"]'),
            total_price = $('input[name="total_price"]'),
            booking_price = $('input[name="booking_price"]'),
            open_time = $('input[name="open_time"]'),
            tel = $('input[name="tel"]'),
            //qq = $('input[name="qq"]'),
            //pros = $('input[name="pro[]"]'),
            //carsel = $('input[name="isallcars"]'),
            //cars = $('input[name="cars[]"]'),
            localinput = $('input[name="localinput"]'),
            //onsale = $('textarea[name="onsale"]'),
            about = $('textarea[name="desn"]'),
            logo = $('input[name="logo"]'),
            imgs = $('textarea[name="imgs"]');

        if(!checkInputEmpty(name)){
            formValid.showErr(name,'请填写机构名称');
            return false;
        }else{
            formValid.showSuccess(name);
        }
        if(!checkInputEmpty(type)){
            formValid.showErr(type,'请填写机构类型');
            return false;
        }else{
            formValid.showSuccess(type);
        }

        if(!checkInputEmpty(total_price)){
            formValid.showErr(total_price,'请填写预约总价格');
            return false;
        }else{
            formValid.showSuccess(total_price);
        }
        if(!isNum(total_price) || total_price.val()<=0){
            formValid.showErr(total_price,'价格必须为大于0的数字');
            return false;
        }else{
            formValid.showSuccess(total_price);
        }

        if(!checkInputEmpty(booking_price)){
            formValid.showErr(booking_price,'请填写预约定金');
            return false;
        }else{
            formValid.showSuccess(booking_price);
        }

        if(!isNum(booking_price) || booking_price.val()<=0){
            formValid.showErr(booking_price,'价格必须为大于0的数字');
            return false;
        }else{
            formValid.showSuccess(booking_price);
        }

        if(!checkInputEmpty(open_time)){
            formValid.showErr(open_time,'请填写营业时间');
            return false;
        }else{
            formValid.showSuccess(open_time);
        }


        if(!checkInputEmpty(tel)){
            formValid.showErr(tel,'请填写联系电话');
            return false;
        }else{
            formValid.showSuccess(tel);
        }
        if(!checkInputEmpty(logo) || logo.val().indexOf('default')>=0){
            formValid.showErr($('#logoerr'),'请上传机构logo图片');
            return false;
        }else{
            formValid.showSuccess($('#logoerr'));
        }
        /*if(!checkSelectEmpty(lv1id)){
            formValid.showErr(lv1id,'请选择机构区域1');
            return false;
        }else{
            formValid.showSuccess(lv1id);
        }
        if(!checkSelectEmpty(lv2id)){
            formValid.showErr(lv2id,'请选择机构区域2');
            return false;
        }else{
            formValid.showSuccess(lv2id);
        }
        if(lv3id.css('display')!='none' && !checkSelectEmpty(lv3id)){
            formValid.showErr(lv3id,'请选择机构区域3');
            return false;
        }else{
            formValid.showSuccess(lv3id);
        }*/


        if(!checkInputEmpty(address)){
            formValid.showErr(address,'请填写机构详细地址');
            return false;
        }else{
            formValid.showSuccess(address);
        }

        if(!checkInputEmpty(localinput) || !/^\d+\.\d+,\d+\.\d+$/.test($.trim(localinput.val()))){

            formValid.showErr(localinput,'请选择机构的地图坐标');
            return false;
        }else{
            formValid.showSuccess(localinput);
        }

        if(!checkInputEmpty(about) ||about.val().length>5000){
            formValid.showErr(about,'请填写机构介绍/字数超过5000字');
            return false;
        }else{
            formValid.showSuccess(about);
        }



        if(!checkInputEmpty(imgs)){
            formValid.showErr(imgs,'请上传机构展示图片');
            return false;
        }else{
            formValid.showSuccess(imgs);
        }
        $(this.form).submit();
    }

};
