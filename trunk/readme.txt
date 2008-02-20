热烈祝贺ThinkPHP发布2周年！

本次2周年版本包含了全新的示例中心
[示例中心安装须知]
把ThinkPHP目录拷贝到web目录下面
导入Examples目录下面的examples.sql 文件到数据库
修改Examples/config.php 文件修改数据库连接信息
如果是Linux环境下面，请把Examples目录下面所有目录设置为可写，因为使用了目录结果自动生成功能。
执行 http://localhost/ThinkPHP/Examples/ 就可以进入示例中心
或者参考在线示例中心 http://exp.thinkphp.cn

2周年版本的更新
[ 1.0.2 版本 ] 2008-2-21
Model类增加toArray方法
修正因Log类的改动导致Trace错误信息无法正常显示的问题
数据查询返回的数据集由ArrayObject对象改成数组
修正Model类的create方法在定义字段映射的下面的bug
修正虚拟模型下面create方法的bug
Model类的facade方法增加数据表字段的检测
优化Image类的showAdvVerify方法
修正标签库的compare标签
Trace配置文件由原来的_trace.php更名为trace.php
项目调试配置文件由原来的_debug.php更名为debug.php
项目配置文件由原来的_config.php更名为config.php
路由定义文件由原来的_routes.php 更名为 routes.php
静态定义文件由原来的_htmls.php 更名为 htmls.php
Model类的create方法无需type参数，自动判断新增和编辑模型数据
增加第一次运行目录自动生成功能（只需要定义入口文件）
默认项目编译缓存目录为Temp目录

[ 1.0.1版本 ] 2008-2-2
修正Db类在Oracle下面的parseLimit方法判断
优化数据库驱动类的查询结果获取
Model 类增加字段的表达式插入和更新支持
完善了Db类的条件查询字段中带有空格的处理
Model类增加了delConnect方法用于删除动态增加的数据库连接
增强了分布式数据库的支持 可以设置是否需要读写分离
Model类增加智能切换功能 switchConnect方法可以自动识别是否是相同的数据库连接类型
增加了组件模块的URL分割定义配置 COMPONENT_DEPR 包括对操作链的设置采用相同的参数定义
修正Model类的count等统计方法会自动缓存的问题 DB类默认关闭查询缓存
修正RBAC组件的权限判断
修正组件模块方式下面的模板文件../Public的替换
修正Html标签库的list标签的actionlist属性的支持
修正PDO类在某些数据库下面的getAll方法的BUG
增加核心缓存文件的开关功能 在入口文件里面设置 CACHE_RUNTIME 为 false
修正了使用组件模块的时候模板文件中__URL__的解析问题
修正模板和语言的切换cookie
Model类增加addConnect和switchConnect方法 用于支持多数据库的连接
修正了语言包的缓存导致切换语言无效的问题
Db类增加多数据库连接的内置支持
修正Model类在某些数据库下面where条件表达式不支持where 1 的情况
Db类增加getLastSql方法用于获取最后一次查询的sql语句
完善Log类的操作以及优化错误日志的写入
修正model的数据库连接配置读取
在Ajax返回之前保存日志记录
完善compare标签
改进Vendor函数的baseUrl参数定义
改进项目语言包的定义 不同语言分成不同子目录
简化了query方法的数据库缓存
增加clearCache方法，用于清空项目相关缓存目录
增加firebird数据库驱动支持
修正pgsql驱动
修正~app.php 文件的编译缓存路径的问题 
增加编译缓存路径的设置 RUNTIME_PATH

