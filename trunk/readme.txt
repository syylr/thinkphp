热烈祝贺ThinkPHP发布2周年！

本次2周年版本包含了全新的示例中心
[示例中心安装须知]
把ThinkPHP目录拷贝到web目录下面
导入Examples目录下面的examples.sql 文件到数据库
修改Examples/config.php 文件修改数据库连接信息
如果是Linux环境下面，请把Examples目录下面所有目录设置为可写，因为使用了目录结果自动生成功能。
执行 http://localhost/ThinkPHP/Examples/ 就可以进入示例中心
或者参考在线示例中心 http://exp.thinkphp.cn

[1.0.3 版本] 2008-3-16
增加空模块支持 如果指定的模块不存在会首先定位是否存在Empty模块
增加核心编译文件的去除空白和注释的开关，在入口文件定义 STRIP_RUNTIME_SPACE 常量为false 可以关闭去除空白和注释
Action缓存由原来的userCache成员属性控制改为项目参数 ACTION_CACHE_ON 控制，便于动态控制
增加数据库字段缓存的开关 惯例配置增加DB_FIELDS_CACHE 用以设置数据库字段是否缓存，默认进行缓存
修正xcache和sqlite缓存方式的读写次数记录
使用视图模型的时候，如果主键是id，不需要再定义getPk方法
修正多语言和多模板的cookie问题
入口文件免设置APP_NAME APP_PATH
完善Cookie类
修正模板检查的时候组件化的支持
模型类的查询操作支持连贯方法
去掉了一些废弃的惯例配置参数 包括：DATA_CACHE_ON 和 DATA_CACHE_MAX
惯例配置增加了DATA_CACHE_SUBDIR 参数控制文件缓存方式是否自动使用子目录哈希缓存
在项目根目录不存在的情况下自动创建
完善对跨库查询的支持
目录自动创建支持写入安全文件

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

