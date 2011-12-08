<?php
// +----------------------------------------------------------------------
// | TOPThink [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2010 http://topthink.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
// $Id$

// 默认行为集合定义文件
return array(
    // 系统默认会加载的行为集合包括
    'extends'    =>    THINK_PATH.'Common/tags.php', // 系统行为定义文件 [必须 支持数组直接定义或者文件名定义 ]
    'tags'         =>   CONFIG_PATH.'tags.php', // 项目应用行为定义文件 [支持数组直接定义或者文件名定义]
    'alias'         =>    CONFIG_PATH.'alias.php', // 项目别名定义文件 [支持数组直接定义或者文件名定义]
    'common'   =>    COMMON_PATH.'common.php', // 项目公共文件
    'app'          =>   CONFIG_PATH.'app.php', // 项目合并编译列表文件 [支持数组直接定义或者文件名定义]
    'config'       =>   array('LOAD_EXT_CONFIG'=>'routes'), // 集合配置文件  [支持数组直接定义或者文件名定义]（如有相同则覆盖项目配置文件中的配置）
);