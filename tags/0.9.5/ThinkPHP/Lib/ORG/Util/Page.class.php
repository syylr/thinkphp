<?php 
// +----------------------------------------------------------------------+
// | ThinkPHP                                                             |
// +----------------------------------------------------------------------+
// | Copyright (c) 2006 liu21st.com All rights reserved.                  |
// +----------------------------------------------------------------------+
// | Licensed under the Apache License, Version 2.0 (the 'License');      |
// | you may not use this file except in compliance with the License.     |
// | You may obtain a copy of the License at                              |
// | http://www.apache.org/licenses/LICENSE-2.0                           |
// | Unless required by applicable law or agreed to in writing, software  |
// | distributed under the License is distributed on an 'AS IS' BASIS,    |
// | WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or      |
// | implied. See the License for the specific language governing         |
// | permissions and limitations under the License.                       |
// +----------------------------------------------------------------------+
// | Author: liu21st <liu21st@gmail.com>                                  |
// +----------------------------------------------------------------------+
// $Id: Page.class.php 11 2007-01-04 03:57:34Z liu21st $

/**
 +------------------------------------------------------------------------------
 * 分页显示类
 +------------------------------------------------------------------------------
 * @author    liu21st <liu21st@gmail.com>
 * @version   $Id: Page.class.php 11 2007-01-04 03:57:34Z liu21st $
 +------------------------------------------------------------------------------
 */
class Page extends Base
{//类定义开始

    /**
     +----------------------------------------------------------
     * 分页起始行数
     +----------------------------------------------------------
     * @var integer
     * @access protected
     +----------------------------------------------------------
     */
    var $firstRow	;

    /**
     +----------------------------------------------------------
     * 列表每页显示行数
     +----------------------------------------------------------
     * @var integer
     * @access protected
     +----------------------------------------------------------
     */
    var $listRows	;

    /**
     +----------------------------------------------------------
     * 页数跳转时要带的参数
     +----------------------------------------------------------
     * @var integer
     * @access protected
     +----------------------------------------------------------
     */
    var $parameter  ;

    /**
     +----------------------------------------------------------
     * 分页总页面数
     +----------------------------------------------------------
     * @var integer
     * @access protected
     +----------------------------------------------------------
     */
    var $totalPages  ;

    /**
     +----------------------------------------------------------
     * 总行数
     +----------------------------------------------------------
     * @var integer
     * @access protected
     +----------------------------------------------------------
     */
    var $totalRows  ;

    /**
     +----------------------------------------------------------
     * 当前页数
     +----------------------------------------------------------
     * @var integer
     * @access protected
     +----------------------------------------------------------
     */
    var $nowPage    ;

    /**
     +----------------------------------------------------------
     * 分页的栏的总页数
     +----------------------------------------------------------
     * @var integer
     * @access protected
     +----------------------------------------------------------
     */
    var $coolPages   ;

    /**
     +----------------------------------------------------------
     * 分页栏每页显示的页数
     +----------------------------------------------------------
     * @var integer
     * @access protected
     +----------------------------------------------------------
     */
    var $rollPage   ;
    
    /**
     +----------------------------------------------------------
     * 分页记录名称
     +----------------------------------------------------------
     * @var integer
     * @access protected
     +----------------------------------------------------------
     */
    var $recordName   =	'条记录';

    /**
     +----------------------------------------------------------
     * 分页记录名称
     +----------------------------------------------------------
     * @var integer
     * @access protected
     +----------------------------------------------------------
     */
    var $show   =	array('上一页','下一页','第一页','最后一页');

    /**
     +----------------------------------------------------------
     * 架构函数
     * 
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @param array $totalRows  总的记录数
     * @param array $firstRow  起始记录位置
     * @param array $listRows  每页显示记录数
     * @param array $parameter  分页跳转的参数
     +----------------------------------------------------------
     */
    function __construct($totalRows,$listRows='',$parameter='')
    {    
        $this->totalRows = $totalRows;
        $this->parameter = $parameter;
        $this->rollPage = PAGE_NUMBERS;
        $this->listRows = !empty($listRows)?$listRows:LIST_NUMBERS;
        $this->totalPages = ceil($this->totalRows/$this->listRows);     //总页数
        $this->coolPages  = ceil($this->totalPages/$this->rollPage);
        $this->nowPage  = !empty($_GET[VAR_PAGE])?$_GET[VAR_PAGE]:1;

        if(!empty($this->totalPages) && $this->nowPage>$this->totalPages) {
            $this->nowPage = $this->totalPages;
        }
        $this->firstRow = $this->listRows*($this->nowPage-1);
    }


    /**
     +----------------------------------------------------------
     * 分页显示
     * 用于在页面显示的分页栏的输出
     +----------------------------------------------------------
     * @access public 
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
     */
    function show($isArray=false){

        if(0 == $this->totalRows) return;
        $nowCoolPage      = ceil($this->nowPage/$this->rollPage);
		$url  =  $_SERVER['PHP_SELF'].(strpos($_SERVER['PHP_SELF'],'?')?'':"?").$this->parameter;

        //上下翻页字符串
        $upRow   = $this->nowPage-1;
        $downRow = $this->nowPage+1;
        if ($upRow>0){
            $upPage="[<a href='".$url."&".VAR_PAGE."=$upRow'>上一页</a>]";
        }else{
            $upPage="";
        }

        if ($downRow <= $this->totalPages){
            $downPage="[<a href='".$url."&".VAR_PAGE."=$downRow'>下一页</a>]";
        }else{
            $downPage="";
        }
        // << < > >>
        if($nowCoolPage == 1){
            $theFirst = "";
            $prePage = "";
        }else{
            $preRow =  $this->nowPage-$this->rollPage;
            $prePage = "[<a href='".$url."&".VAR_PAGE."=$preRow' >上".$this->rollPage."页</a>]";
            $theFirst = "[<a href='".$url."&".VAR_PAGE."=1' >第一页</a>]";
        }
        if($nowCoolPage == $this->coolPages){
            $nextPage = "";
            $theEnd="";
        }else{
            $nextRow = $this->nowPage+$this->rollPage;
            $theEndRow = $this->totalPages;
            $nextPage = "[<a href='".$url."&".VAR_PAGE."=$nextRow' >下".$this->rollPage."页</a>]";
            $theEnd = "[<a href='".$url."&".VAR_PAGE."=$theEndRow' >最后一页</a>]";
        }
        // 1 2 3 4 5
        $linkPage = "";
        for($i=1;$i<=$this->rollPage;$i++){
            $page=($nowCoolPage-1)*$this->rollPage+$i;
            if($page!=$this->nowPage){
                if($page<=$this->totalPages){
                    $linkPage .= "&nbsp;<a href='".$url."&".VAR_PAGE."=$page'>&nbsp;".$page."&nbsp;</a>";
                }else{
                    break;
                }
            }else{
                if($this->totalPages != 1){
                    $linkPage .= " [".$page."]";
                }
            }
        }
        $pageStr = $this->totalRows.' '.$this->recordName.' '.$upPage.' '.$downPage.' 共'.$this->totalPages.'页 '.$theFirst.' '.$prePage.' '.$linkPage.' '.$nextPage.' '.$theEnd; 
        if($isArray) {
            $pageArray['totalRows'] =   $this->totalRows;
            $pageArray['upPage']    =   $url.'&'.VAR_PAGE."=$upRow";
            $pageArray['downPage']  =   $url.'&'.VAR_PAGE."=$downRow";
            $pageArray['totalPages']=   $this->totalPages;
            $pageArray['firstPage'] =   $url.'&'.VAR_PAGE."=1";
            $pageArray['endPage']   =   $url.'&'.VAR_PAGE."=$theEndRow";
            $pageArray['nextPages'] =   $url.'&'.VAR_PAGE."=$nextRow";
            $pageArray['prePages']  =   $url.'&'.VAR_PAGE."=$preRow";
            $pageArray['linkPages'] =   $linkPage;
			$pageArray['nowPage'] =   $this->nowPage;
        	return $pageArray;
        }
        return $pageStr;
    }

}//类定义结束
?>