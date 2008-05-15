<?php 
return array(
	'Category'=>array('Blog','category','id'),
	'Blog@'=>array(
		array('/^\/(\d+)$/','Blog','read','id'),
		array('/^\/(\d+)\/(\d+)/','Blog','archive','year,month'),
		),
);
?>