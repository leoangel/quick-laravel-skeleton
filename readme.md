本工程可以让你快速启动一个全新的API WEB服务的项目开发，quick-laravel-skeleton封装了简单的API路由逻辑，你只需要在api模块下构建正常的controller和action即可，编写方式参考ThinkPHP的模式，这种API/模块/动作的方式很容易构造URL请求，同时本框架也集成了多数据库访问支持。只要你按照规则设定好数据库访问配置和连接，同时在model中对$_conn变量进行指向，就可以轻而易举的访问不通的数据库连接了。当然本骨架也包含composer支持，可以快速集成各种第三方插件。最后要说一下，本框架推荐使用原生SQL来实现各种数据库查询，同时简单的查询我们是支持Eloquent ORM 特性的，你可以在BaseModel中看到这些封装的实现。


Notice:
This Skeleton Base on Laravel 5.5 + PHP7 ,But You can Update to Laravel 5.6+PHP7.1

Step 1：
`composer update`

Step 2:
`composer dumpautoload`

Step 3:
`php artisan ide-helper:generate`

Step 4:
deploy redis and set redis configuration in `.env`

Step 5:
When More MySQL Database You Have to use, You can Configure multiple Database In `.env` AND `database.php`.
For example ,You can configure like current file.

Step 6:
The skeleton develops implementations for the API Service,So you must be sure Your project is based on API services.

Step 7:
API URL uniform `/api/`  prefix.

