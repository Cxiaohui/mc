/**
 * Created by chenxh on 2018/6/12.
 */
var mc_qiniu = {
    'config':{
        'base_config':{
            useCdnDomain: true,//表示是否使用 cdn 加速域名
            region: null//选择上传域名区域
        },
        'putExtra':{
            fname: "",//文件原文件名
            params: {},//用来放置自定义变量
            mimeType: null//["image/png", "image/jpeg", "image/gif"]
        },
        'uptoken':'',
        'select':'.filepick'
    },
    init:function(uptoken,select,after_upload){
        if(typeof md5 == 'undefined'){
            throw new Error('需要引入md5.js');
        }
        this.config.uptoken = uptoken;
        this.config.select = select;
        this.after_upload = after_upload;
        this.init_upload_event();

    },
    init_upload_event:function(){
        var _this = this;
        $(document).on('change',_this.config.select,function(){
            //$(_this.config.select).change(function(){

            var file = this.files[0];
            var nameinfo = _this.do_filename(file.name);
            var file_dom = $(this),
                press = file_dom.closest('.mcdoc_upload_box').find('.mcdoc_press');

            var observer = {
                next:function(res){
                    //console.log(res);
                    press.css('width',res.total.percent+'%').html(res.total.percent+'%');
                },
                error:function(err){
                    // console.log(err);
                    press.html(err.message);
                },
                complete:function(res){
                    //console.log(res);
                    //console.log(nameinfo);
                    res['filename'] = nameinfo['filename'];
                    _this.after_upload && _this.after_upload(file_dom,res);
                }
            };

            var subscription;
            // 调用sdk上传接口获得相应的observable，控制上传和暂停
            var observable = qiniu.upload(file, nameinfo['q_key'], _this.config.uptoken, _this.config.putExtra, _this.config.base_config);
            observable.subscribe(observer)
        });
    },
    do_filename:function(filename){

        var dtx = filename.toLowerCase().split('.').splice(-1)[0];
        //console.log(dtx);
        return{
            'q_key':'mcdocs-'+md5(filename)+'.'+dtx,
            'filename':filename
        } ;
    },
    after_upload:null
};
/*
mc_qiniu.init(uptoken,'.select',function(obj,res){
    console.log(obj);
    console.log(res);
});*/
