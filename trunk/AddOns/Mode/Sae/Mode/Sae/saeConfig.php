<?php
//sae下的固定配置,以下配置将会覆盖项目配置。
return array(
        'DB_TYPE'=> 'mysql',     // 数据库类型
	'DB_HOST'=> SAE_MYSQL_HOST_M.",".SAE_MYSQL_HOST_S, // 服务器地址
	'DB_NAME'=> SAE_MYSQL_DB,        // 数据库名
	'DB_USER'=> SAE_MYSQL_USER,    // 用户名
	'DB_PWD'=> SAE_MYSQL_PASS,         // 密码
	'DB_PORT'=> SAE_MYSQL_PORT,        // 端口
	'DB_RW_SEPARATE'=>true,
        'DB_DEPLOY_TYPE'=> 1, // 数据库部署方式:0 集中式(单一服务器),1 分布式(主从服务器)
        'DATA_CACHE_TYPE'=> 'Memcache',//S缓存类型为Memcache
        //以下为SAE专有配置，本地环境不不需要设置下列配置。
        'SAE_THINK_DOMAIN'=>'think',//ThinkPHP系统所需storage的domain名称。用于存储日志和静态缓存等。
        'SAE_SHOW_LOG_ERR'=>false //当日志写入失败时，是否需要报错。默认不报错，不强制用户建立系统storage
        );
