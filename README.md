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

## 截图

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
