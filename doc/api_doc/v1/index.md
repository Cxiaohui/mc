# ZX API
### 版本号：V1
---

### 1、总体技术要求

#### 1.1 协议
> 采用 HTTP+二进制字符串 传输模式实现接口信息传输，返回json
> PS:无特殊说明，接口参数传递全部采用POST，如果需要GET或者其他HTTP方式会在接口中说明


##### 1.2 接口规范
>1. 接口统一使用 UTF-8 编码返回数据编码。
>2. base_url
|环境|base_url|
|---|---|
|线上|http://host/api/v1
|测试|http://host/api/v1
|开发|http://host/api/v1
>3. 路由规则定义: base_url + 接口地址


##### 1.3 公共参数
> 
|参数名 | 数据类型 |说明|
|--- |---|---|
|sign|string(32)|签名|
|time|int(4)|时间戳（10位）|
|lang|int(2)|显示的语言ID，语言id：1英语, 2繁體, 3简体, 默认为1|
|model|int(2)|app版本环境，主要针对iOS，0为测试版，1为正式版，默认为正式版|
>

##### 1.4 App登录后参数
> 
|参数名 | 数据类型 |说明|
|--- |---|---|
|api_key|string|api key|
>

---

##### 1.5 返回数据说明
> ######返回数据示例
```php
{
	"code":200,
    "msg":"返回数据的提示",
    "data"{
    	//返回的数据内容
    }
}
```
> CODE 说明
>
|code值|说明|
|:---:|:---:|
|200|成功处理|
|201|api key 异常|
|202|账号异常|
|301|缺少必要参数|
|302|参数有误|
|410|手机号码相关错误|
|411|密码相关错误信息|
|412|验证码相关错误信息|
|XXX|其他的在此添加|

##### 1.5 加密规则
```php
公共秘钥 appkey：e89513b56de32c4eb00e2c16170cedfe

PS:后缀 _bin 表示转二进制串，后缀 _bin_base64 表示先转二进制再转base64；加号 ‘+’ 仅表示连接

步骤：
    1、公共参数 lang 转二进制串 2个字节 lang_bin;
    2、公共参数 model 转二进制串 2个字节 model_bin;
    3、请求参数由App登录参数和具体接口参数，以及公共参数time组成，不含公共参数。参数字符串由键值对构成，参数间用&连接；然后转二进制，再转base64；
        例：请求参数 a=1，b=2, param = a=1&b=4；转二进制 param_bin, 然后转base64即param_bin_base64;
    4、公共参数 sign 签名规则：
        sign = MD5(MD5(time+param+lang_bin+model_bin)+appkey);
        然后将sign转二进制串 sign_bin(32字节);
    5、POST提交字符串poststr_base64组成：
        poststr = sign_bin + lang_bin + model_bin + param_bin_base64
        最后 转base64即poststr_base64


```
---



*****

#### 目录

---
- [注册登录]


