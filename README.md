# laravel-bbs
如果你觉得还可以的话，请Star , Fork给作者鼓励一下

## 说明
    2020年基于learnKu社区的《Laravel 教程 - Web 开发实战进阶》开发, 后边缘于各种原因,停了下来,现在回归来做一波知识的汇总和巩固，做一些新功能的开发

## 链接
    https://learnku.com/courses/laravel-intermediate-training/9.x

## 较大的变动
- 升级版本 6.2 => 8

## 后续添加的功能模块：
- 第三方授权登陆
- 支付功能（支付宝、微信）
- 评价商品
- 优惠券功能

## 第三方接口汇总
- [`GaoDeiGeo`]&nbsp;&nbsp;    [高德地图](app/ThirdParty/Service/GaoDeiGeo.php)
- [`TrieTree`]&nbsp;&nbsp;&nbsp;&nbsp;     [TireTree字典树](app/ThirdParty/Service/TrieTree.php)
- [`AliyunOTS`]&nbsp;&nbsp;&nbsp;    [阿里云OTS](app/Models/OTS/AliyunOTS.php)
- [`TententIM`]&nbsp;&nbsp;&nbsp;    [TencentIM](app/ThirdParty/Service/TencentIm.php)
- [`TpnsService`]  [腾讯信鸽服务](app/ThirdParty/Service/TpnsService.php) 

## 功能模块汇总：
- [QueryCacheable 模型缓存](app/Package/QueryCache/Traits/QueryCacheable.php)
- [ES 搜索功能](app/Http/Controllers/TopicsController.php)
- [ES ScrollApi](app/Console/Commands/Elasticsearch/TestScrollApi.php)
- [RabbitMQ 消息队列](app/Common/Components/RabbitMQ/RabbitMQ.php)
- [PM2 进程管理](pm2/delayed_message.json)


- [Sign In 签到模块](app/Package/Sign/SignServiceProvider.php)
- [Lottery 抽奖模块](app/Package/Lottery/LotteryServiceProvider.php)
- [Mission 任务模块](app/Package/Mission/MissionServiceProvider.php)


- [SQL 慢查询监听](app/Providers/DBServiceProvider.php)
- [SQL 日志打印](app/Providers/EventServiceProvider.php)
- [Log JSON格式化](app/Logging/CustomizeFormatter.php)


- [CSV 百万级导出](app/Handlers/CSVExportHandler.php)
- [CollectorFactory 京东/淘宝/天猫](app/Package/Collector/CollectorFactory.php)

## 环境需求

* Composer
* PHP >= 7.3
* OpenSSL PHP 扩展
* PDO PHP 扩展
* Mysql 5.7+

## 安装步骤

```
* git clone
* composer install
* npm install
* 修改.env文件，关于邮件发送请参考官网文档配置（修改.env以及email.php）
* 启动mysql，创建数据库，执行 php artisan migrate:refresh
* 同步ES数据，执行 php artisan es:migrate
```
