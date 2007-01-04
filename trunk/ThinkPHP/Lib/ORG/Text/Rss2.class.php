<?php
/*————————————————

使用方法：
将下面的数组，带入相应的函数。

1、频道主体
$channel = array(
 title => '频道名称(必备)',
 link => '频道的URL(必备)',
 description => '频道的描述(必备)',
 language => '频道文章所用语言(可选)',
 webmaster => '负责频道技术事务的网站管理员email(可选)',
 managingEditor => '责任编辑的email(可选)',
 pubDate => '频道内容发布日期，格式遵循RFC822格式（年份可为2位或4位）(可选)',
 lastBuildDate => '频道内容最后的修改日期(Sat, 07 Sep 2002 09:42:31 GMT)(可选)',
 skipDays => '提示新闻聚合器，那些天它可以跳过。(可选)',
 copyright => '频道内容的版权说明(可选)',
 ttl => '有效期，4BXO]n((YR提I用以指明该频道可被缓存的最长时间(可选)',
);

2、频道图片
$image = array(
 url => '图片的链接(必备)',
 title => '图片的标题，用于http的alt属性(必备)',
 link => '网站的url(实际中常以频道的url代替)(必备)',
 width => '图片的宽度(象素为单位)最大144,默认88(可选)',
 height => '图片的高度(象素为单位)最大400，默认31(可选)',
 description => '用于link的title属性(可选)' 
   );

3、频道项
$item = array(
 title => '项(item)的标题(必备)',
 description => '项的大纲(必备)',
 link => '项的URL(必备)',
 comments => '该项的评论(comments)页的URL(可选)',
 guid => '1项的唯一标志符串(可选)',
 author => '该项作者的email(可选)',
 enclosure => '描述该附带的媒体对象(可选)',
 category => '包含该项的一个或几个分类(catogory)(可选)',
 pubdate => '项的发布时间(可选)',
 source_url => '该项来自的RSS道(可选)',
 source_name => '该项来自的RSS道(可选)'
);
————————————————*/

class rss2
{
 var $channel_pre="";
 var $str_image="";
 var $str_item="";
 var $channel_end="";
 /*构造函数*/
 function rss2($channel,$encoding="utf-8")
  {
   $this->channel($channel,$encoding);
  }
 /*生成频道主体*/
 function channel($channel,$encoding="utf-8")
  {
  $this->channel_pre.="<?xml version=\"1.0\" encoding=\"$encoding\"?>\n";
  $this->channel_pre.= "<rss version=\"2.0\">\n";

  $this->channel_pre.= " <channel>\n";

  $this->channel_pre.= "  <title>".$channel['title']."</title>\n";//频道名称(必备)
  $this->channel_pre.= "  <link>".$channel['link']."</link>\n";//频道的URL(必备)
  $this->channel_pre.= "  <description><![CDATA[".$channel['description']."]]></description>\n";//频道的描述(必备)
  $this->channel_pre.= "  <generator>ThinkPHP RSS Generator </generator>\n";//创建此文档的程序(可选)

  if(isset($channel['language']))$this->channel_pre.= "  <language>".$channel['language']."</language>\n";//频道文章所用语言(可选)
  if(isset($channel['webmaster']))$this->channel_pre.= "  <webMaster>".$channel['webmaster']."</webMaster>\n";//负责频道技术事务的网站管理员email(可选)
  if(isset($channel['managingeditor']))$this->channel_pre.= "  <managingEditor>".$channel['managingeditor']."</managingEditor>\n";//责任编辑的email(可选)
  if(isset($channel['pubdate']))$this->channel_pre.= "  <pubDate>".date('r',$channel['pubdate'])."</pubDate>\n";//频道内容发布日期， 
  if(isset($channel['lastbuilddate']))$this->channel_pre.= "  <lastBuildDate>".date('r',$channel['lastbuilddate'])."</lastBuildDate>\n";//频道内容最后的修改日期(Sat, 07 Sep 2002 09:42:31 GMT)(可选)
  if(isset($channel['skipdays']))$this->channel_pre.= "  <skipDays>".$channel['skipdays']."</skipDays>\n";//提示新闻聚合器，那些天它可以跳过。(可选)
  if(isset($channel['copyright']))$this->channel_pre.= "  <copyright>".$channel['copyright']."</copyright>\n";//频道内容的版权说明(可选)
  if(isset($channel['ttl']))$this->channel_pre.= "  <ttl>".$channel['ttl']."</ttl>\n";//有效期，用以指明该频道可被缓存的最长时间(可选) 

  $this->channel_end.= " </channel>\n";
  $this->channel_end.= "</rss>\n";
 }
 /*生成频道图片*/
 function image($image)
 {
  if(isset($this->str_image))unset($this->str_image);
  $this->str_image.= "  <image>\n";
  if(isset($image['url']))$this->str_image.= "   <url>".$image['url']."</url>\n";//图片的url(必备)
  if(isset($image['title']))$this->str_image.= "   <title>".$image['title']."</title>\n";//图片的标题，用于http的alt属性(必备)
  if(isset($image['link']))$this->str_image.= "   <link>".$image['link']."</link>\n";//网站的url(实际中常以频道的url代替)(必备)
  if(isset($image['width']))$this->str_image.= "   <width>".$image['width']."</width>\n";//图片的宽度(象素为单位)最大144,默认88(可选)
  if(isset($image['height']))$this->str_image.= "   <height>".$image['height']."</height>\n";//图片的高度(象素为单位)最大400， 
  if(isset($image['description']))$this->str_image.= "   <description><![CDATA[".$image['description']."]]></description>\n";//用于link的title属性(可选)
  $this->str_image.= "  </image>\n";
 }
 /*频道项*/
 function item($item)
 {
  $this->str_item.="  <item>\n";
  $this->str_item.="   <title>".$item['title']."</title>\n";//项(item)的标题(必备)
  $this->str_item.="   <description><![CDATA[".$item['description']."]]></description>\n";//项的大纲(必备)
  $this->str_item.="   <link>".$item['link']."</link>\n";//项的URL(必备) 

  if(isset($item['comments']))$this->str_item.="   <comments>".$item['comments']."</comments>\n";//该项的评论(comments)页的URL(可选)
  if(isset($item['guid']))$this->str_item.="   <guid>".$item['guid']."</guid>\n";//1项的唯一标志符串(可选)
  if(isset($item['author']))$this->str_item.="   <author>".$item['author']."</author>\n";//该项作者的email(可选)
  if(isset($item['enclosure']))$this->str_item.="   <enclosure>".$item['enclosure']."</enclosure>\n";//描述该附带的媒体对象(可选)
  if(isset($item['category']))$this->str_item.="   <category>".$item['category']."</category>\n";//包含该项的一个或几个分类(catogory)(可选)
  if(isset($item['pubdate']))$this->str_item.="   <pubDate>".date('r',$item['pubdate'])."</pubDate>\n";//项的发布时间(可选)
  if(isset($item['source_url']))$this->str_item.="   <source url=\"".$item['source_url']."\">".$item['source_name']."</source>\n";//该项来自的RSS道(可选)
  $this->str_item.="  </item>\n";
 }
 /*输出xml*/
 function generate()
 {
  if(isset($this->channel_pre)&&isset($this->channel_end)&&isset($this->str_item))
  {
   header('Content-type: text/xml; charset=utf-8');
   echo $this->channel_pre;
   echo $this->str_image;
   echo $this->str_item;
   exit($this->channel_end);
  }
 }
}
?>