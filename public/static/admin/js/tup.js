/**
 * Created by chenxh on 2017/7/12.
 */
$(function(){

    $("#file-1").fileinput({
        language:'zh',
        uploadUrl: up_url, // you must set a valid URL here else you will get an error
        uploadAsync: true,
        allowedFileExtensions: ['jpg', 'png', 'gif'],
        overwriteInitial: false,
        maxFileSize: 1000,
        maxFilesNum: 10,
        uploadExtraData:{},
        showUploadedThumbs: false,
        showRemove: false,
        removeIcon:'<i class="icon-trash text-error"></i>',
        uploadIcon:'<i class="icon-upload-alt text-success"></i>',
        zoomIcon:'<i class="icon-zoom-in"></i>',
        fileActionSettings: {
            showRemove: true,
            showUpload: false,
            showZoom: false,
            showDrag: false,
            removeIcon: '<i class="icon-trash text-error"></i>',
            removeClass: 'btn mini'
        },

        //allowedFileTypes: ['image', 'video', 'flash'],
        slugCallback: function (filename) {
            console.log(filename);
            return filename.replace('(', '_').replace(']', '_');
        }
    });



});