[安装说明]
把ThinkPHP、Admin、HOME、Public目录直接上传（或拷贝到）服务器web目录下面
导入Admin目录下面的admin.sql数据库脚本到mysql数据库
（如果是mysql4.1以下版本，请修改admin.sql文件，把DEFAULT CHARSET=utf8去掉）
注意示例程序mysql保存数据采用utf-8编码，请注意mysql相应设置。
修改配置文件config.php设置好数据库访问信息
如果是Unix类环境，请保证下面目录可写（设置为777）
Admin和HOME目录下面的
Cache 
Conf
Temp
Logs

如果需要在后台自动创建项目和项目模块，请设置网站根目录为可写。

后台初始登录帐户名 admin 
密码 admin 
验证码0000 对应的字母