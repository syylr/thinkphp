示例环境要求：
PHP5 Mysql4.0以上 开启GD库和支持PATHINFO

示例运行步骤：
1、导入examples.sql到你的mysql数据库
2、修改config.php配置文件中的数据库连接信息
3、运行示例根目录下面的index.html文件

注意事项：
1、如果你在导入数据库文件之前运行了示例中心，并且本地的数据库账号不是root和空密码的话，需要清空每个示例下面的Runtime目录。
2、如果你的环境不支持PATHINFO的话，把config.php文件中的
'URL_MODEL'=>1 改成'URL_MODEL'=>3 然后清空每个示例下面的Runtime目录即可。