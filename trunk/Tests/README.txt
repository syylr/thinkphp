使用单元测试工具
================

[环境设置]

将PHP脚本路径加入PATH环境变量。

[目录设置]

Tests目录下的Docs和Temp目录必须可写。

[测试配置]

Confiugre.php中定义了默认的用于测试的数据库帐号，为了方便起见建议不要修改，可以
建立测试帐号和数据库帐号。
默认数据库名、帐号、密码都是test，端口使用默认。

[运行测试]

进入Tests目录，运行test (Windows)或./test (Linux/Unix)，Docs目录下会产生测试
日志log.txt、log.xml和测试文档test.html、test.txt。

[生成报告]

安装xdebug扩展，修改PHP配置文件，把zend debugger或zend optimizer注释掉，添加一行：
zend_extension_ts="D:\PHP\ext\php_xdebug.dll"

运行report或./report，将在Docs/Report目录下生成代码覆盖率报告。

[清理目录]

运行clean或./clean清理Docs测试日志和报告。

[目录结构]

Docs           测试文档生成目录
Temp           测试所需临时目录
ThinkPHP       测试用例目录
AllTests.php   主测试文件
build.xml      ant构建文件
clean          Shell脚本，执行Clean.php
clean.bat      批处理脚本，执行Clean.php
Clean.php      PHP脚本，清理Docs目录
config.xml     用于生成代码覆盖率报告的XML配置文件
Configure.php  测试配置文件
README.txt     单元测试说明
report         Shell脚本，执行测试并生成报告
report.bat     批处理脚本，执行测试并生成报告
test           Shell脚本，执行测试
test.bat       批处理脚本，执行测试

[自动化测试]

ThinkPHP/Vendor/PHPUnit 为PHPUnit目录。
ThinkPHP/Tools/phpunit.php 为PHPUnit命令行工具，可以自定义参数运行。

php "../ThinkPHP/Tools/phpunit.php" [参数]

自动生成测试类
php "../ThinkPHP/Tools/phpunit.php" --skeleton [类名] [源文件]

注意：源文件中如果引用其他类，要先包含引用类，必须确保该文件能单独运行而不报错。

使用Apache Ant进行测试。
ant         执行测试
ant report  生成测试报告
ant clean   清理测试日志和文档
