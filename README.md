# 后台管理系统

基于dcat-admin、与laravel-wechat的后台管理系统，可以快速开发搭建小程序及其后管平台。  

### 环境与包
* php7.4
* mysql5.7
* laravel: 8.7.5
* dcat-admin: ^2.0
* laravel-wechat: ^6.0
* jwt-auth: ^1.0
* telescope: ^4.0

### 框架部署
进入框架根目录执行：
```shell
# 创建和修改配置文件
cp .env.example .env

# 安装依赖 
composer install

# 执行数据库迁移
php artisan migrate

# 安装与发布admin
php artisan admin:install
php artisan admin:publish

# 发布telescope
php artisan telescope:publish

# 生成APP_KEY
php artisan key:generate
# 生成jwt密钥
php artisan jwt:secret

# storage目录分配权限
chmod -R 777 storage

```

### 主要集成项及能力

#### dcat-admin
[dcat-admin](https://learnku.com/docs/dcat-admin/2.x)是一个只需很少的代码即可快速构建出一个功能完善的高颜值后台系统。  
其内置了代码生成器，配合laravel自带的migrate可以快速生成表以及其相关的各类文件（模型、控制器、数据仓库等）。  
生成的migration文件，编辑后执行迁移：  
```php
php artisan make:migration create-table-users
php artisan migrate
```
然后使用代码生成器：  
![生成器](https://cdn.learnku.com/uploads/images/202004/26/38389/guQd6nFQIF.png!large)
增加路由并前往后台增加菜单后可以访问整个模型的管理界面。  
```php
$router->resource('users', 'UserController');
```
更多信息可以参考[dcat-admin中文文档](https://learnku.com/docs/dcat-admin/2.x/quick-start/8082)。

#### laravel-wechat
laravel-wechat是基于[EasyWeChat](https://easywechat.com/5.x/)的laravel框架SDK。  
该SDK封装了各类微信生态开放API，极大程度上简化了微信相关业务的开发工作。  
在配置文件`config/wechat.php`中增加相关配置：  
```php
'mini_program' => [
    'default' => [
        'app_id'  => env('WECHAT_MINI_PROGRAM_APPID', ''),
        'secret'  => env('WECHAT_MINI_PROGRAM_SECRET', ''),
        'token'   => env('WECHAT_MINI_PROGRAM_TOKEN', ''),
        'aes_key' => env('WECHAT_MINI_PROGRAM_AES_KEY', ''),
    ],
],
```
即可通过门面使用小程序相关能力：  
```php
use Overtrue\LaravelWeChat\Facade;

// 回调事件
Facade::miniProgram()->server->push(function($message){
    return "欢迎关注 overtrue！";
});

// 发送模板消息
Facade::miniProgram()->template_message->send($params);
// 新增或修改二维码
Facade::miniProgram()->qr_code->set($params);
// 素材上传
Facade::miniProgram()->media->upload($type, $path);
...

```
其他能力(需在配置文件中增加对应的配置)：  
```php
Facade::officialAccount();  // 公众号
Facade::openPlatform();     // 开放平台
Facade::payment();          // 支付
Facade::work();             // 企业微信

Facade::officialAccount('foo'); // `foo` 为配置文件中的名称，默认为 `default`
```
更新信息和能力参考[laravel-wechat](https://github.com/overtrue/laravel-wechat/tree/6.x)。  

### jwt
集成`jwt-auth`实现了`api`的权限校验。可以直接使用中间件`auth`或者`auth:api`对api进行鉴权。  

```php
Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
```
目前已经在`api`增加了默认启用`jwt`鉴权的组，可以将需要鉴权的`router`放入组内：  
```php
Route::group([
    'namespace' => '\App\Api',
    'middleware' => 'auth:api'
], function(Router $router) {

    $router->any('test', function() {
        return 'test';
    });
});
```
也实现了基本的登录、刷新token、登出api：  
```php
// 登录
$router->post('/auth/login', [AuthController::class, 'login']);

$router->group([
    'prefix' => 'auth'
], function (Router $router) {
    // 刷新token
    $router->post('refresh', [AuthController::class, 'refresh'])->name('refresh');
    // 登出
    $router->post('logout', [AuthController::class, 'logout'])->name('logout');
});
```
#### telescope调试工具
[Laravel Telescope](https://learnku.com/docs/laravel/10.x/telescopemd/14917#introduction) 是 Laravel 本地开发环境的绝佳伴侣。Telescope 可以方便查看应用程序的请求、异常、日志条目、数据库查询、排队的作业、邮件、消息通知、缓存操作、定时计划任务、变量打印等。  
![telescope](https://laravel.com/img/docs/telescope-example.png)
目前已集成在`dcat-admin`中，在管理后台顶部可以找到入口。  
> 生产环境可以考虑隐藏或者不安装。  

#### Api标准化输出
增加了针对`Api`接口的标准化输出，默认对整个`api`启用。将所有的可`json`序列化的数据统一转化为标准化输出。  
编写`api`代码时无需关注返回结构，只需要返回对应的数据（即`data`节点）即可。  
```php
$router->any('test', function() {
    return 'test api';      // 字符串
    return 11111;           // 数组
    return ['a' => 1];      // 数组
    return User::find(1);   // 对象
    return User::all();     // 集合
});
```
将标准化输出：  
```json
{
    "code": 0,
    "message": "",
    "data": "test api"
    // "data": 11111
    // "data": {
    //     "a" : 1
    // }
    // "data": {
    //     "id" : 1,
    //     "name": "test",
    //     "mobile": "18888888888",
    //     ...
    // }
    // "data": [
    //     {
    //         "id": 1,
    //         ...
    //     },
    //     ...
    // ]
}
```
#### 异常处理
对整个`api`的异常在`app\Exceptions\Handler.php`统一捕获转换为标准化输出，例如未授权访问的异常会输出：  
```json
{
    "code": 401,
    "message": "未授权的访问",
    "data": null
}
```
系统异常类的错误码主要使用`HTTP code`，未捕获的则统一使用`code: 500`。  
```php
public function register()
{
    $this->renderable(function(Throwable $e, Request $request) {
        if (strstr($request->getRequestUri(), '/api') || $request->expectsJson()) {
            if ($e instanceof QueryException) {
                return ApiResponse::error(Response::HTTP_INTERNAL_SERVER_ERROR, '数据错误');
            } else {
                return ApiResponse::error(500, '系统繁忙');
            }
            ...
        }
    });
    ...
}
```

业务异常则创建了`app\Exceptions\BizException.php`进行管理，不同场景的异常推荐在`app\Exceptions`目录中按照业务创建文件夹进行归类管理。
业务异常继承`BizException`，需要覆写异常返回的`code`和`message`属性，比如归类到`Auth`的用户名或密码错误异常`AccountOrPassWordInvalidException`  
```php
<?php

namespace App\Exceptions\Auth;

use App\Exceptions\BizException;

class AccountOrPassWordInvalidException extends BizException
{

    protected $code = 2001;

    protected $message = '用户名或密码错误';

}
```
抛出异常后将输出：  
```json
{
    "code": 2001,
    "message": "用户名或密码错误",
    "data": null
}
```
业务异常可以自定义输出内容，需要重写`BizExcetion`中的`render()`  
```php
public function render(Request $request)
{
    return ApiResponse::error($this->getCode(), $this->getMessage(), [
        //some things about exception
    ]);
}
```
业务异常默认不上报，但可以按需自定义上报内容（如日志上报或告警），需要重写`BizExcetion`中的`report()`  
```php
public function report()
{
    Log::error('出错了', request()->only('phone', 'password'));
    // other report
}
```
