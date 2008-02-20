<?php
function getStatus($status) 
{
	return $status=="1"?"启用":"禁用";
}

function getGroupName($id) 
{
    if($id==0) {
    	return '无上级组';
    }
	if(Session::is_set('groupName')) {
		$name	=	Session::get('groupName');
		return $name[$id];
	}
	$dao	=	D("Group");
	$list	=	$dao->findAll('','','id,name');
	$nameList	=	$list->getCol('id,name');
	$name	=	$nameList[$id];
	Session::set('groupName',$nameList);
    return $name;

}

?>