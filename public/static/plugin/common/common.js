
//+++++++++++++++++++++layer 弹出层插件相关  begin++++++++++++++++++++++++++++++

//信息显示,2s 后自动关闭
function layermsg(text, n) {
    layer.msg(text, 2, {tcolor: n});
}
//父窗口
function playermsg(text, n) {
    window.parent.layermsg(text, n);
}

//不同位置的信息显示,2s 后自动关闭
function layermsgpos(text, pos, n) {
    var position = {0: 'm-top', 1: 'left-top', 2: 'top', 3: 'right-top', 4: 'right-bottom', 5: 'bottom', 6: 'left-bottom', 7: 'left'};
    layer.msg(text, 3, {
        type: 9, rate: position[pos], shade: [0], tcolor: n
    });
}
//父窗口
function playermsgpos(text, pos, n) {
    window.parent.ayermsgpos(text, pos, n);
}

//确定操作
function layeralert(text, n, title) {
    layer.alert(text, n, title);
}
//子窗口关闭
function closePlayer() {
    window.parent.closelayer();
}
//总的关闭方法
function closelayer() {
    $('a.xubox_close', '.xubox_layer').click();
}
//弹出引用页面
function layeriframe(url, title, w, h) {
    $.layer({
        type: 2,
        title: title,
        shadeClose: true,
        maxmin: false,
        fix: false,
        area: [w + 'px', h + 'px'],
        iframe: {src: url}
    });
}
//操作确认方法
function layerconfirm(text, n, title, fnyes, fnno) {

    $.layer({
        shade: [0],
        area: ['auto', 'auto'],
        tcolor: n,
        title: title,
        dialog: {
            msg: text,
            btns: 2,
            type: 4,
            btn: ['确定', '取消'],
            yes: fnyes,
            no: fnno
        }
    });
}
//测试
function test() {

    layerconfirm('确定要删除吗？', 2, '确认', function () {
        layer.msg('确定', 2, {tcolor: 3});
    }, function () {
        layer.msg('取消', 2, {tcolor: 4});
    });
}

//弹出页面div内容
function layerdiv(options) {
    var defaults = {
        type: 1,
        title: '',
        area: ['200px', '200px'],
        offset: ['', '50%'],
        border: [6, 0.3, '#000'], ////默认边框
        shade: [0.1, '#000'], //遮罩
        fix: true,
        closeBtn: [0, true],
        shift: ['m-top', 300, 1], // || ['bottom', 300, 1]
        html: '',
        pclosebtn: '',
        callback: function () {
        }
    };

    var sets = $.extend({}, defaults, options);

    var fnpop = function (opts) {

        var pageii = $.layer({
            type: opts.type,
            title: opts.title,
            area: opts.area,
            offset: opts.offset,
            border: opts.border,
            shade: opts.shade,
            fix: opts.fix,
            closeBtn: opts.closeBtn,
            shift: opts.shift,
            page: {
                html: opts.html
            }, success: function (elem) {
                elem.find(opts.pclosebtn).on('click', function () {
                    layer.close(pageii);
                    opts.callback && opts.callback();
                });
            }
        });
        return pageii;
    };

    return fnpop(sets);
}

//+++++++++++++++++++++layer 弹出层插件相关  end++++++++++++++++++++++++++++++
//+++++++++++++++++++页内提示
var pageMesg = {
    init: function () {

        if ($('#page-mesg').find('.alert').length <= 0) {
            $('#page-mesg').html('<div class="alert hide"><button class="close" data-dismiss="alert"></button><span></span></div>');
        }
    },
    show: function (text, type) {
        pageMesg.init();
        var _class = ['alert-error', 'alert-success', '', 'alert-info'],
                _alert = $('#page-mesg').find('.alert');
        _alert.addClass(_class[type]).removeClass(_class[1 - type]).children('span').html(text).end().show();
        $('#page-mesg').show();
        scrollTo();
        if (type == 1) {
            setTimeout(function () {
                pageMesg.hide();
            }, 2000);
        }
    },
    hide: function () {
        $('#page-mesg').hide();
    }
};
//++++++++++页内提示
//++++++++++滑动开关操作+++begin++
var toggleBtn = {
    checkOn: function (obj) {
        setTimeout(function () {
            obj.attr('checked', true).parent('div').animate({'left': 0}, 600);
        }, 500);
    },
    checkOff: function (obj) {
        setTimeout(function () {
            obj.attr('checked', false).parent('div').animate({'left': '-50%'}, 600);
        }, 500);
    }
};
//++++++++++滑动开关操作+++end++
//+++++++++++表单验证提示
var formValid = {
    //显示错误
    showErr: function (obj, txt, ele) {

        var parent = obj.parent();
        if (!ele) {
            ele = 'help-inline';
        }
//        console.log(parent);
        if (parent.children('.' + ele).length <= 0) {
            parent.append('<div class="' + ele + ' hide"></div>');
        }
        obj.focus();
        $('.' + ele, parent).text(txt).addClass('error').removeClass('icon-ok hide');
        parent.closest('.control-group').addClass('error').removeClass('success');
        scrollTo(obj, -100);
    },
    //显示成功
    showSuccess: function (obj, ele) {
        var parent = obj.parent();
        if (!ele) {
            ele = 'help-inline';
        }
        if (parent.children('.' + ele).length <= 0) {
            parent.append('<div class="' + ele + ' hide"></div>');
        }

        $('.' + ele, parent).text('').addClass('icon-ok').removeClass('error hide');
        parent.closest('.control-group').addClass('success').removeClass('error');
    }
};

function scrollTo(el, offeset) {
    var pos = el ? el.offset().top : 0;
    jQuery('html,body').animate({
        scrollTop: pos + (offeset ? offeset : 0)
    }, 'slow');
};
//上传iframe 框 
function upiframe(obj) {
    var me = $(obj),
            _title = me.attr('data-title'),
            _w = me.attr('data-w'),
            _h = me.attr('data-h'),
            _url = me.attr('data-url');
    layeriframe(_url, _title, _w, _h);
}
//删除文件
function delFile(obj) {
    layerconfirm('删除后不可恢复，确定要删除吗？', 2, '删除确认', function () {
        var me = $(obj),
                _type = me.attr('data-type'),
                _cate = me.attr('data-cate'),
                _suid = me.attr('data-suid'),
                _file = me.attr('data-file'),
                _other = me.attr('data-other'),
                _target = me.attr('data-target'),
                iscurpage = true;
        if (typeof (me.attr('data-curpage')) == "undefined") {
            iscurpage = false;
        }
        if (!_file) {
            if (iscurpage) {
                layermsg('文件不能为空', 4);
            } else {
                pageMesg.show('文件不能为空', 0);
            }

            return false;
        }
        $.post('/Home-Ajax-delFile', {'file': _file, 'cate': _cate, 'type': _type, 'other': _other, 'suid': _suid, 'ajax': 1}, function (d) {
            if (d.err == '1') {
                if (iscurpage) {
                    layermsg('删除失败', 4);
                } else {
                    pageMesg.show('删除失败', 0);
                }
                return false;
            } else {

                if (_type == 'img') {
                    if (iscurpage) {
                        $(_target, window.document).attr('src', d.deft);
                    } else {
                        $(_target, window.parent.document).attr('src', d.deft);
                    }

                }
                me.addClass('hide');
                closelayer();
            }
        }, 'json');
    }, function () {
        closelayer();
    });

}

/////////////////////////

//中文key值
function getCNkey(k) {
    var keys = ['零', '一', '二', '三', '四', '五', '六', '七', '八', '九', '十'];
    if (k <= 10) {
        return keys[k];
    }
    return k;
}
/**
 * 时间加减
 * @param {type} hours
 * @param {type} add
 * @returns {unresolved}
 */
function timesadd(hours, add) {
    var tmp = hours.split(':');
    var h = Math.floor(add / 60);

    tmp[0] = parseInt(tmp[0]) + h;
    tmp[1] = parseInt(tmp[1]) + add - h * 60;

    if (tmp[1] >= 60) {
        tmp[1] = tmp[1] - 60;
        tmp[0] += 1;
    }

    if (tmp[0] > 23) {
        tmp[0] = 0;
    }
    tmp[0] = tmp[0] < 10 ? '0' + tmp[0] : tmp[0];
    tmp[1] = tmp[1] < 10 ? '0' + tmp[1] : tmp[1];
    return tmp.join(':');
}
/**
 * 获取时间的下拉
 */
function timeSelect(name, seled, sart, end, eclass) {

    var n = 24, m = ['00', '15', '30', '45'];
    if (eclass) {
        eclass = ' class="' + eclass + '"';
    }
    seled = seled == '' ? '07:00' : seled;
    var sels = '<select name="' + name + '"' + eclass + '>', i = sart;

    for (; i < end; i++) {
        sels += getOption(i, m, seled);
    }
    sels = sels + '</select>';
    return sels;
}
function getOption(i, m, seled) {
    var str = '';
    var ii = i < 10 ? '0' + i : i;
    for (var k = 0; k < 4; k++) {
        var val = ii + ':' + m[k];
        str += '<option value="' + val + '"' + (seled == val ? ' selected' : '') + '>' + val + '</option>';
    }
    return str;
}

//同步的ajax
function noasyncajax(url, data, callback) {
    $.ajax({
        'type': 'post', 'url': url,
        'async': false, 'cache': false,
        'dataType': 'json',
        'data': data,
        'success': callback
    });
}
//隐藏表单的提示
function hideFormHelp() {
    var contp = $('.control-group');
    contp.removeClass('success error').find('.valid').removeClass('ok valid');
}
var timer = null, n = 90;
function countDown(selecter, txt) {
    var dids = $(selecter);
    timer = setInterval(function () {
        --n;
        if (n <= 0) {
            clearInterval(timer);
            n = 90;
            dids.text(txt).attr('disabled', false);
            return false;
        }
        dids.text('重新发送(' + n + ')');
    }, 1000);
}
//发送短信
function sendSms(phone, code) {
    $.post(U('Home/Ajax/smsVerify'), {'p': phone, 'code': code}, function (d) {
        if (d.err == '1') {
            alert(d.mesg);
        }
    }, 'json');
}

//鼠标坐标
function mousePos(e) {
    var e = event || window.event;
    var scrollX = document.documentElement.scrollLeft || document.body.scrollLeft;
    var scrollY = document.documentElement.scrollTop || document.body.scrollTop;
    var x = e.pageX || e.clientX + scrollX;
    var y = e.pageY || e.clientY + scrollY;

    return {'x': x, 'y': y};
}
;

/**
 * 浏览器检查
 * ua(window.navigator.userAgent) 
 * @param {type} useragent
 * @returns {String}
 */
function ua(useragent) {
    if (/MicroMessenger/i.test(useragent)) {
        return "wechat";
    }
    if (/firefox/i.test(useragent)) {
        return "firefox";
    }
    if (/chrome/i.test(useragent)) {
        return "chrome";
    }
    if (/opera/i.test(useragent)) {
        return "opera";
    }
    if (/safari/i.test(useragent)) {
        return "safari";
    }
    if (/msie 6/i.test(useragent)) {
        return "IE6";
    }
    if (/msie 7/i.test(useragent)) {
        return "IE7";
    }
    if (/msie 8/i.test(useragent)) {
        return "IE8";
    }
    if (/msie 9/i.test(useragent)) {
        return "IE9";
    }
    if (/msie 10/i.test(useragent)) {
        return "IE10";
    }
    if (/rv\:11/i.test(useragent)) {
        return "IE11";
    }
    return "other";
}
//计算天数差的函数，通用  
function  DateDiff(sDate1, sDate2) {    //sDate1和sDate2是2006-12-18格式  
    var aDate, oDate1, oDate2, iDays;
    aDate = sDate1.split("-");
    oDate1 = new Date(aDate[1] + '-' + aDate[2] + '-' + aDate[0]);   //转换为12-18-2006格式  
    aDate = sDate2.split("-");
    oDate2 = new Date(aDate[1] + '-' + aDate[2] + '-' + aDate[0]);
    iDays = parseInt(Math.abs(oDate1 - oDate2) / 1000 / 60 / 60 / 24);   //把相差的毫秒数转换为天数  
    return  iDays;
}
function updeverimg(obj) {
    var url = obj.attr('src').split('?'),
            timenow = new Date().getTime();
    obj.attr('src', url[0] + '?' + timenow);
}
//日期转为时间戳
function strto_time(str_time) {
    var new_str = str_time.replace(/:/g, '-');
    new_str = new_str.replace(/ /g, '-');
    var arr = new_str.split("-");
//    console.log(new_str);
    var datum = new Date(Date.UTC(arr[0], arr[1] - 1, arr[2], arr[3] - 8, arr[4], arr[5]));
    return strtotime = datum.getTime() / 1000;
}
//时间戳转为日期
function date_time(unixtime) {
    var timestr = new Date(parseInt(unixtime) * 1000);
    var datetime = timestr.toLocaleString().replace(/年|月/g, "-").replace(/日/g, " ");
    return datetime;
}
//比较两个日期的大小
function aGtB(d1,d2){
    //console.log(Date.parse(d1.replace(/-/g, "/")));
    //console.log(Date.parse(d2.replace(/-/g, "/")));
    return Date.parse(d1.replace(/-/g, "/")) > Date.parse(d2.replace(/-/g, "/"));
}

//判断是否在数组中
Array.prototype.S = String.fromCharCode(2);
Array.prototype.in_array = function (e) {
    var r = new RegExp(this.S + e + this.S);
    return (r.test(this.S + this.join(this.S) + this.S));
};

//删除数组中的元素
function delEleInArray(arr, ele) {
    var len = arr.length, i = 0;
    for (; i < len; i++) {
        if (arr[i] == ele) {
            arr.splice(i, 1);
            break;
        }
    }
    return arr;
}

function U(str) {
    var dp = '-', urlstr = [], main = '', parms = '';
    urlstr = str.split('|');
    main = '/' + urlstr[0].replace(/\//ig, dp);
    if (!urlstr[1]) {
        return main + '.html';
    }
    urlstr = urlstr[1].split(',');
    var len = urlstr.length, i = 0;
    for (; i < len; i++) {
        var tmp = urlstr[i].split(':');
        parms += dp + tmp[0] + dp + tmp[1];
    }
    return main + parms + '.html';
}


//全选反选
function checkAll(obj, val) {
    obj.each(function () {
        this.checked = val;
    });
}
//检查input是否为空
function checkInputEmpty(obj) {

    if (!obj.val() || obj.val() == '') {
        return false;
    } else {
        return true;
    }
}
//检查select是否为空
function checkSelectEmpty(obj) {
    var val = obj.find('option:selected').val();
    if (!val || val == '' || val == 0) {
        return false;
    } else {
        return true;
    }
}
//检查radio是否为空
function checkRadioEmpty(obj) {
    var val = obj.filter(':checked').val();
    if (!val || val == '') {
        return false;
    } else {
        return true;
    }
}
//检查checkbox是否为空
function checkCheckboxEmpty(obj) {
    var chedlen = obj.filter(':checked').length;
    if (chedlen <= 0) {
        return false;
    } else {
        return true;
    }
}
//循环检查是否为空
function loopCheckEmpty(obj) {
    var res = true, len = obj.length, th = 0;
    for (var i = 0; i < len; i++) {
        var val = obj.eq(i).val();
        if (!val || val == '') {
            th = i;
            res = false;
        }
    }
    return res;
}

//是否是空的对象
function isEmptyObj(obj) {
    for (var i in obj) {
        if (obj[i]) {
            return false;
        }
    }
    return true;
}


//验证数字
function isNum(obj) {
    return /^\d+/.test(obj.val());
}
//验证数字
function isNum2(obj) {
    return /^[0-9]+/.test(obj.val());
}
//验证手机号
function isPhone(obj) {
    return /^1[3-9]{1}[0-9]{9}$/.test(obj.val());
}
//验证手机号
function isPhone2(p) {
    return /^1[3-9]{1}[0-9]{9}$/.test(p);
}
//验证邮箱
function isMail(obj) {
    var mailreg = /^[0-9a-z][_.0-9a-z-]{0,31}@([0-9a-z][0-9a-z-]{0,30}[0-9a-z]\.){1,4}[a-z]{2,4}$/gi;
    return mailreg.test(obj.val());
}

function isIdcard(card) {
    var Errors = new Array(
            "验证通过!",
            "身份证号码位数不对!",
            "身份证号码出生日期超出范围或含有非法字符!",
            "身份证号码校验错误!",
            "身份证地区非法!"
            );
    var area = {11: "北京", 12: "天津", 13: "河北", 14: "山西", 15: "内蒙古", 21: "辽宁", 22: "吉林", 23: "黑龙江", 31: "上海", 32: "江苏", 33: "浙江", 34: "安徽", 35: "福建", 36: "江西", 37: "山东", 41: "河南", 42: "湖北", 43: "湖南", 44: "广东", 45: "广西", 46: "海南", 50: "重庆", 51: "四川", 52: "贵州", 53: "云南", 54: "西藏", 61: "陕西", 62: "甘肃", 63: "青海", 64: "宁夏", 65: "新疆", 71: "台湾", 81: "香港", 82: "澳门", 91: "国外"}
    var idcard, Y, JYM;
    var S, M;
    var idcard_array = new Array();
    idcard_array = idcard.split("");
    // 地区检验
    if (area[parseInt(idcard.substr(0, 2))] == null) {
        return false;// Errors[4];
    }
// 身份号码位数及格式检验
    switch (idcard.length) {
        case 15:
            if ((parseInt(idcard.substr(6, 2)) + 1900) % 4 == 0 || ((parseInt(idcard.substr(6, 2)) + 1900) % 100 == 0 && (parseInt(idcard.substr(6, 2)) + 1900) % 4 == 0)) {
                ereg = /^[1-9][0-9]{5}[0-9]{2}((01|03|05|07|08|10|12)(0[1-9]|[1-2][0-9]|3[0-1])|(04|06|09|11)(0[1-9]|[1-2][0-9]|30)|02(0[1-9]|[1-2][0-9]))[0-9]{3}$/;// 测试出生日期的合法性
            } else {
                ereg = /^[1-9][0-9]{5}[0-9]{2}((01|03|05|07|08|10|12)(0[1-9]|[1-2][0-9]|3[0-1])|(04|06|09|11)(0[1-9]|[1-2][0-9]|30)|02(0[1-9]|1[0-9]|2[0-8]))[0-9]{3}$/;// 测试出生日期的合法性
            }
            if (ereg.test(idcard)) {
                return true;// Errors[0];
            } else {
                return false;// Errors[2];
            }
            break;


        case 18:
            // 18位身份号码检测
            // 出生日期的合法性检查
            // 闰年月日:((01|03|05|07|08|10|12)(0[1-9]|[1-2][0-9]|3[0-1])|(04|06|09|11)(0[1-9]|[1-2][0-9]|30)|02(0[1-9]|[1-2][0-9]))
            // 平年月日:((01|03|05|07|08|10|12)(0[1-9]|[1-2][0-9]|3[0-1])|(04|06|09|11)(0[1-9]|[1-2][0-9]|30)|02(0[1-9]|1[0-9]|2[0-8]))
            if (parseInt(idcard.substr(6, 4)) % 4 == 0 || (parseInt(idcard.substr(6, 4)) % 100 == 0 && parseInt(idcard.substr(6, 4)) % 4 == 0)) {
                ereg = /^[1-9][0-9]{5}[1-2]{1}[0-9]{3}((01|03|05|07|08|10|12)(0[1-9]|[1-2][0-9]|3[0-1])|(04|06|09|11)(0[1-9]|[1-2][0-9]|30)|02(0[1-9]|[1-2][0-9]))[0-9]{3}[0-9Xx]$/;// 闰年出生日期的合法性正则表达式
            } else {
                ereg = /^[1-9][0-9]{5}[1-2]{1}[0-9]{3}((01|03|05|07|08|10|12)(0[1-9]|[1-2][0-9]|3[0-1])|(04|06|09|11)(0[1-9]|[1-2][0-9]|30)|02(0[1-9]|1[0-9]|2[0-8]))[0-9]{3}[0-9Xx]$/;// 平年出生日期的合法性正则表达式
            }
            // 测试出生日期的合法性
            if (ereg.test(idcard)) {
                // 计算校验位
                S = (parseInt(idcard_array[0]) + parseInt(idcard_array[10])) * 7
                        + (parseInt(idcard_array[1]) + parseInt(idcard_array[11])) * 9
                        + (parseInt(idcard_array[2]) + parseInt(idcard_array[12])) * 10
                        + (parseInt(idcard_array[3]) + parseInt(idcard_array[13])) * 5
                        + (parseInt(idcard_array[4]) + parseInt(idcard_array[14])) * 8
                        + (parseInt(idcard_array[5]) + parseInt(idcard_array[15])) * 4
                        + (parseInt(idcard_array[6]) + parseInt(idcard_array[16])) * 2
                        + parseInt(idcard_array[7]) * 1
                        + parseInt(idcard_array[8]) * 6
                        + parseInt(idcard_array[9]) * 3;
                Y = S % 11;
                M = "F";

                JYM = "10X98765432";
                M = JYM.substr(Y, 1);// 判断校验位
                if (M.toLowerCase() == idcard_array[17].toLowerCase()) {
                    return true;// Errors[0]; //检测ID的校验位
                } else {
                    return false;// Errors[3];
                }
            } else {
                return false;// Errors[2];
            }
            break;
        default:
            return false;// Errors[1];
            break;
    }

}
/**
 * 获取字符串长度中文算两个字符
 * @param str
 * @returns {Boolean}
 */
function getStrAsciLength(str) {
    var re = /([^u4e00-u9fa5]|[^ufe30-uffa0])/;
    var strLen = str.length;
    var strAsicLen = 0;
    for (var i = 0; i < strLen; i++) {
        var ch = str.substr(i, 1);
        //alert(ch);
        if (!re.test(ch)) {
            strAsicLen += 1;
        } else {
            strAsicLen += 2;
        }
    }
    return strAsicLen;
}

//********************小插件类***************************

/* 
 * 模拟ajax提交
 */
$.fn.ajaxform = function (options) {
    var defaults = {
        'target': 'post-iframe',
        'iheight': 0,
        'iwidth': 0
    };

    var opts = $.extend({}, defaults, options),
            ele = $('<iframe>'),
            _this = $(this);

    function init() {

        if (_this.attr('action') == '') {
            _this.attr({'target': opts.target, 'action': location.href});
        } else {
            _this.attr({'target': opts.target});
        }

        createIframe();
    }

    function createIframe() {
        ele.attr({
            'frameborder': 0,
            'height': opts.iheight,
            'width': opts.iwidth,
            'scrolling': 'no',
            'name': opts.target
        });
        $('body').append(ele);
    }
    init();
};

/**
 * a 标签 的单选
 */
$.fn.aRadio = function (options) {
    var defaults = {
        btnele: 'a.btn',
        inputele: '',
        okicon: '<i class="icon-ok"></i> ',
        okclass: 'green',
        checkAfter: function (val) {
        }
    };
    var opts = $.extend({}, defaults, options),
            _this = $(this);

    opts.inputele = _this.children('input' + opts.inputele);
    var daval = $.trim(opts.inputele.val());

    //有值的进行初始化
    if (daval != '') {
        var obj = $(opts.btnele + '[data-val="' + daval + '"]', _this);
//        console.log(daval);
        toggle(obj);
    }
    //事件绑定
    $(opts.btnele, _this).on('click', function () {
        toggle($(this));
        return false;
    });
    //事件处理
    function toggle(obj) {
        var val = obj.attr('data-val'),
                hasiele = obj.children('i').length;
        if (hasiele) {
            return false;
        }
        obj.addClass(opts.okclass).siblings(opts.btnele).removeClass(opts.okclass);
        $(opts.okicon).prependTo(obj);
        obj.siblings(opts.btnele).children('i').remove();
        opts.inputele.val(val);
        opts.checkAfter(val);
    }
};

/**
 * a 标签 的复选
 */
$.fn.aCheckbox = function (options) {
    var defaults = {
        btnele: 'a.btn',
        inputele: '',
        okicon: '<i class="icon-ok"></i> ',
        okclass: 'green',
        max: 0,
        maxerr: '最多可先N个',
        checkAfter: function (val, obj) {
        }
    };
    var opts = $.extend({}, defaults, options),
            _this = $(this);
    opts.inputele = _this.children('input' + opts.inputele);
    var daval = _getVal();
    if (daval.length > 0) {
        var i = 0, len = daval.length;
        for (; i < len; i++) {
            var obj = $(opts.btnele + '[data-val="' + daval[i] + '"]', _this);
            toggle(obj, false);
        }
    }
    //事件绑定
    $(opts.btnele, _this).on('click', function () {
        toggle($(this), true);
        return false;
    });

    //事件处理
    function toggle(obj, isclick) {
        var val = obj.attr('data-val'),
                hasiele = obj.children('i').length;
//        if(!val || val==''){
//             opts.checkAfter(val,obj);
//            return false;
//        }
        if (hasiele) {
            obj.removeClass(opts.okclass).children('i').remove();
            _updateVal(val, '-');
            opts.checkAfter(val, obj);
            return false;
        }
        //添加之前进行max判断
        var daval = _getVal();
        if (opts.max > 0 && ((isclick && daval.length >= opts.max) || (!isclick && daval.length > opts.max))) {
            formValid.showErr($(opts.inputele), opts.maxerr);
            return false;
        }

        obj.addClass(opts.okclass);
        $(opts.okicon).prependTo(obj);
        _updateVal(val, '+');
        opts.checkAfter(val, obj);
    }

    //值的处理
    function _updateVal(v, act) {
        var inputval = _getVal(),
                v = $.trim(v);
        if (!v || v == '') {
            return;
        }
        if (act == '+' && !inputval.in_array(v)) {

            inputval.push(v);
        } else if (act == '-') {
            var i = 0, len = inputval.length;
            for (; i < len; i++) {
                if (inputval[i] == v) {
                    inputval.splice(i, 1);
                }
            }
        }

        if (inputval.length == 0) {
            opts.inputele.val('');
        } else {
            opts.inputele.val(inputval.join('|'));
        }

    }

    //获取值
    function _getVal() {
        var datas = opts.inputele.val();
        if (!datas) {
            datas = '';
        }
        return datas.length == 0 ? [] : datas.split('|');
    }

};
function deldata(obj){
    var _url = $(obj).attr('data-href');
    if(!_url){
        return false;
    }

    layerconfirm('删除后不可恢复，确定要删除吗？', 2, '操作确认', function () {
        location.href = _url;
    }, function () {
        closelayer();
    });
}
function appendInputVal(input,val){
    var bval = input.val(),p = '|';

    if(bval==''){
        p = '';
    }
    input.val(bval+p+val);

}
function get_date_str(y,m,d){

    if(m + 1<10){
        m = '0'+(m + 1);
    }

    if(d<10){
        d = '0'+d;
    }
    return y+'-'+m+'-'+d;
    /*return {
        'en':d+'/'+m+'/'+y,
        'cn':y+'-'+m+'-'+d
    };*/
}

$(function () {
    var mcd = new Date(),
        mc_today = get_date_str(mcd.getFullYear(),mcd.getMonth()+1,mcd.getDate());

    $('input[type="date"]').each(function(i){

        if(!$(this).val()){
            //console.log(today);
            $(this).val(mc_today);
        }

    });


    $('.del_mcfile').on('click',function(){
        var _this = $(this),
            type = _this.attr('data-type'),
            id = _this.attr('data-id');
            if(!type || !id){
                return false;
            }
        del_mcfile({"type":type,"id":id},function(d){
            _this.closest('.docli').remove();
            closelayer();
        });
    });

    function del_mcfile(data,after_act) {
        layerconfirm('删除后不可恢复，确定要删除吗？', 2, '操作确认', function () {
            $.post('/gerent/file/del_doc',data,function(d){
                if(d.err=='0'){
                    after_act && after_act(d);
                }else{
                    layeralert(d.msg,4,'操作提示');
                }
            },'json');
        }, function () {
            closelayer();
        });

    }
    $(document).on('click','.del_qnfile',function(){
        //console.log('del_qnfile');
        //mcdoc_upload_box
        var _div = $(this).closest('.qiniu_doc_line'),
            qn_key = _div.find('.p_doc').attr('data-key');
        if(!qn_key){
            return false;
        }
        del_qn_doc({"qn_key":qn_key},function(d){
            _div.remove();
            closelayer();
        });
    });

    function del_qn_doc(data,after_act) {
        layerconfirm('确定要删除吗？', 2, '操作确认', function () {
            $.post('/gerent/file/del_qndoc',data,function(d){
                if(d.err=='0'){
                    after_act && after_act(d);
                }else{
                    layeralert(d.msg,4,'操作提示');
                }
            },'json');
        }, function () {
            closelayer();
        });
    }
});

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
        'select':'.filepick',
        'fileshowbox':'.mcdoc_upload_box',
        'file_prefix':'test'
    },
    init:function(options){
        if(typeof md5 == 'undefined'){
            throw new Error('需要引入md5.js');
        }
        this.config.uptoken = options.uptoken;
        this.config.select = options.upselect;
        if(options.after_upload){
            this.after_upload = options.after_upload;
        }
        this.file_prefix = options.file_prefix;
        if(options.mime_type){
            this.config.putExtra.mimeType = options.mime_type;
        }
        this.init_upload_event();

    },
    init_upload_event:function(){
        var _this = this;
        $(document).on('change',_this.config.select,function(){
            var len = this.files.length;
            for(var i=0;i<len; i++){
                _this.do_upload(i,this.files[i],len);
            }
        });
    },
    do_upload:function(index,fileobj,file_len){
        var _this = this,
            nameinfo = this.do_filename(fileobj.name),
            press = $('.mcdoc_press');

        var observer = {
            next:function(res){
                //console.log(res);
                press.show().css('width',res.total.percent+'%').html(res.total.percent+'%');
            },
            error:function(err){
                //layer.alert(err.code);
                if(err.message.indexOf('file type doesn\'t match')>-1){

                    layer.alert('上传的文件类型有误，请确认后再上传');
                }else if(err.code && qnErrors[err.code]){
                    //目标资源已存在
                    if(err.code == 614){
                        var res = {
                            "filename":nameinfo['filename'],
                            "key":nameinfo['q_key'],
                            "hash":md5(nameinfo['q_key'])
                        };

                        _this.after_upload && _this.after_upload(res);
                        return false;
                    }

                    layer.alert(qnErrors[err.code]);
                }else{
                    layer.alert(err.message);
                }
                console.log(err);
            },
            complete:function(res){
                //console.log(res);
                //console.log(nameinfo);
                res['filename'] = nameinfo['filename'];
                _this.after_upload && _this.after_upload(res);
                if(index==file_len-1){
                    press.hide();
                }
            }
        };

        var subscription;
        // 调用sdk上传接口获得相应的observable，控制上传和暂停
        var observable = qiniu.upload(fileobj, nameinfo['q_key'], _this.config.uptoken, _this.config.putExtra, _this.config.base_config);
        observable.subscribe(observer)
    },
    do_filename:function(filename){

        var ext = filename.toLowerCase().split('.').splice(-1)[0];
        //console.log(dtx);
        return{//reports
            'q_key':this.file_prefix+'/mcdocs-'+md5(encodeURIComponent(filename)+'-'+(new Date().getTime()))+'.'+ext,
            'filename':filename
        } ;
    },
    after_upload:function(res){

        var ext = (res['filename'].split('.').splice(-1))[0].toLowerCase();

        var html = '<div class="qiniu_doc_line">'
            +'<p><a href="javascript:;" class="text-error del_qnfile"><i class="icon-trash"></i> 删除</a></p>';
        if(ext=='jpg' || ext=='png' || ext=='jpeg' || ext=='gif'){
            html += '<p><img src="'+qu_host+res['key']+'" style="height: 120px;"/> </p>';
        }
        html += '文件名：<input class="p_doc" value="'+res['filename']+'" data-ext="'+ext+'" data-key="'+res['key']+'" data-hash="'+res['hash']+'">'
            +'</div>';
        $(this.config.fileshowbox).append(html);

    }
};

//go-top
var gotop = {
    tagele: '.go-top',
    init: function () {
        if ($(this.tagele).length <= 0) {
            return false;
        }

        $(window).on('scroll', function () {
            var ishide = true;
            if ($(this).scrollTop() > 200 && ishide) {
                $(gotop.tagele).show();
                ishide = false;
            } else {
                $(gotop.tagele).hide();
                ishide = true;
            }
            return false;
        });
    }
};


//删除图片文件
var delFile={
    'url':'',
    'delbtn':'.removefilebtn',
    'postdata':{},
    'imgobj':null,
    'imginput':'',
    init:function(url){
        this.url = url || '/File/del';
        this.imginput = $('#logoinput');
        this._bindEvent();

    },
    _bindEvent:function(){
        $(this.delbtn).on('click',function(){
            var me = $(this),
                dtype = me.attr('data-type'),
                dfile = me.attr('data-file'),
                dsid = me.attr('data-sid');

            if(!dtype || !dfile || !dsid || dfile.indexOf('default')>-1){
                return false;
            }
            delFile.imgobj = me.parent('div').next('img');
            /* console.log(delFile.imgobj);
             return false;*/
            delFile.postdata = {'dtype':dtype,'dfile':dfile,'sid':dsid,'ajax':1};
            layerconfirm('删除后不可恢复，确定要删除吗？', 2, '操作确认', function () {
                delFile.dodel();
                closelayer();
            }, function () {
                closelayer();
            });
        });
    },
    dodel:function(){
        if(!this.postdata){
            return false;
        }
        $.post(this.url,this.postdata,function(d){
            if(d.err=='1'){
                layeralert(d.mesg,0,'操作提示');
                return false;
            }else{
                layermsgpos(d.mesg,2);
                delFile.imgobj.attr('src', d.src);
                delFile.imginput.val('');
            }
        },'json');
    }

};

//
var mFile = {
    'mbox':'#mimgsbox',
    'item':'.uploadimgbox',
    'upbox':'#uploader',
    'rmbtn':'.removeimgbtn',
    'imginput':'',
    'url':'',
    'postdata':{},
    'imgobj':null,
    'tfile':'',
    init:function(url){
        this.url = url || '/File/del';
        this.imginput = $('#imgsinput');
        this._bindEvent();
    },
    _bindEvent:function(){
        $(this.rmbtn).on('click',function(){
            var me = $(this),
                dtype = me.attr('data-type'),
                dfile = me.attr('data-file'),
                dsid = me.attr('data-sid');

            if(!dtype || !dfile || !dsid || dfile.indexOf('default')>-1){
                return false;
            }
            mFile.imgobj = me.parent('div').parent('div');
            mFile.tfile = dfile;
            /* console.log(delFile.imgobj);
             return false;*/
            mFile.postdata = {'dtype':dtype,'dfile':dfile,'sid':dsid,'ajax':1};
            layerconfirm('删除后不可恢复，确定要删除吗？', 2, '操作确认', function () {
                mFile.dodel();
                closelayer();
            }, function () {
                closelayer();
            });
        });
    },
    dodel:function(){
        if(!this.postdata){
            return false;
        }
        $.post(this.url,this.postdata,function(d){
            if(d.err=='1'){
                layeralert(d.mesg,0,'操作提示');
                return false;
            }else{
                layermsgpos(d.mesg,2);
                mFile.imgobj.remove();
                mFile.resetInput();
            }
        },'json');
    },
    resetInput:function(){
        var inputvals = this.imginput.val().split('|'),len = inputvals.length,i= 0,newv=[];
        for(;i<len;i++){
            if(this.tfile.indexOf(inputvals[i])<=-1){
                newv.push(inputvals[i]);
            }
            continue;
        }
        this.imginput.val(newv.join('|'));
        if($(this.item,$(this.mbox)).length<=0){
            $(this.mbox).remove();
            $(this.upbox).show();
        }

    }

};

//https://developer.qiniu.com/kodo/api/3928/error-responses
var qnErrors = {
    "298":"部分操作执行成功",
    "400":"请求报文格式错误(包括上传时，上传表单格式错误)",
    "401":"认证授权失败",
    "403":"权限不足，拒绝访问",
    "404":"资源不存在",
    "405":"请求方式错误",
    "406":"上传的数据 CRC32 校验错误",
    "413":"请求资源大小大于指定的最大值",
    "419":"用户账号被冻结",
    "478":"镜像回源失败(主要指镜像源服务器出现异常)",
    "502":"错误网关",
    "503":"服务端不可用",
    "504":"服务端操作超时",
    "573":"单个资源访问频率过高",
    "579":"上传成功但是回调失败",
    "599":"服务端操作失败",
    "608":"资源内容被修改",
    "612":"指定资源不存在或已被删除",
    "614":"目标资源已存在",
    "630":"已创建的空间数量达到上限，无法创建新空间",
    "631":"指定空间不存在",
    "640":"调用列举资源(list)接口时，指定非法的marker参数",
    "701":"在断点续上传过程中，后续上传接收地址不正确或ctx信息已过期"
};

