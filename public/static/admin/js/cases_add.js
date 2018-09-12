/**
 * Created by chenxh on 2018/7/1.
 */
//封面图上传
if($('#filePicker').length>0){
    var confg = {
        'btntext': '<i class="icon-upload-alt"></i> 上传封面图片',
        'server': up_url,
        'progress': $('#progressbar'),
        'statbar': $('.statusBar'),
        'viewimg': '',
        'inneralert':false,
        'isiframe':false,
        'formData': {'uptype':'cases_cover','typeid':0},
        'afterSuccess': function (file, response) {
            //console.log(response);
            var timenow = new Date().getTime();
            $('#logoimg').attr('src', response['data'] + '?' + timenow);
            $('#logoinput').val(response['info']);
        }

    };
    //插件初始化
    sUpd.init(confg);
}
$(function(){

    $('#add_new_step').on('click',function(){
        var tpl = '<div><div class="input-append"><div class="add-on"><input type="radio" name="active" value="1"></div>' +
            '<input type="text" class="stepname" style="width: 100px;"/></div></div>';
        $(this).before(tpl);
    });


    $('#subbtn').on('click',function(){
        var name = $('input[name="name"]'),
            huxing = $('input[name="huxing"]'),
            mianji = $('input[name="mianji"]'),
            fengge = $('input[name="fengge"]'),
            seijishi = $('input[name="seijishi"]'),
            jingli = $('input[name="jingli"]'),
            jianli = $('input[name="jianli"]'),
            coverimg = $('input[name="coverimg"]');

        if(!checkInputEmpty(name)){
            formValid.showErr(name,'请填写案例名称');
            return false;
        }else{
            formValid.showSuccess(name);
        }

        if(!checkInputEmpty(huxing)){
            formValid.showErr(huxing,'请填写案例户型信息');
            return false;
        }else{
            formValid.showSuccess(huxing);
        }

        if(!checkInputEmpty(mianji)){
            formValid.showErr(mianji,'请填写案例面积信息');
            return false;
        }else{
            formValid.showSuccess(mianji);
        }

        if(!checkInputEmpty(fengge)){
            formValid.showErr(fengge,'请填写案例风格信息');
            return false;
        }else{
            formValid.showSuccess(fengge);
        }

        if(!checkInputEmpty(seijishi)){
            formValid.showErr(seijishi,'请填写案例设计师信息');
            return false;
        }else{
            formValid.showSuccess(seijishi);
        }

        if(!checkInputEmpty(jingli)){
            formValid.showErr(jingli,'请填写案例项目经理信息');
            return false;
        }else{
            formValid.showSuccess(jingli);
        }

        if(!checkInputEmpty(jianli)){
            formValid.showErr(jianli,'请填写案例项目监理信息');
            return false;
        }else{
            formValid.showSuccess(jianli);
        }

        var step_data = [],radio_checked = false,stepname_empty=false,add_btn=$('#add_new_step');
        $('.stepname').each(function(i){
            var _this = $(this),
                tmp = {},
                rad = _this.closest('.input-append').find('input[name="active"]:checked').val();
            if(!_this.val()){
                //console.log('empty');
                stepname_empty = true;
                return false;
            }
            tmp = {"name":_this.val(),"active":0};
            //console.log(rad);
            if(rad){
                radio_checked = true;
                tmp['active'] = 1;
            }

            step_data.push(tmp);
        });
        if(stepname_empty){
            formValid.showErr(add_btn,'请完成阶段的信息');
            return false;
        }else{
            formValid.showSuccess(add_btn);
        }

        if(!radio_checked){
            formValid.showErr(add_btn,'请选取一个当前的阶段');
            return false;
        }else{
            formValid.showSuccess(add_btn);
        }
        $('input[name="step"]').val(JSON.stringify(step_data));

        if(!checkInputEmpty(coverimg)){
            formValid.showErr(coverimg,'请上传案例封面');
            return false;
        }else{
            formValid.showSuccess(coverimg);
        }

        $('#postform').submit();

    });

});