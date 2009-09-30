<?php
// 配置类型模型
class GroupModel extends CommonModel {
	public $_validate = array(
		array('name','require','名称必须'),
		);

	public $_auto		=	array(
        array('status',1,self::MODEL_INSERT,'string'),
		array('create_time','time',self::MODEL_INSERT,'function'),
		);
}
?>