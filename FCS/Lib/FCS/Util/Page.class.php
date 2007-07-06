<?php 
// +---------------------------------------------------------------------------+
// | FCS -- Fast,Compatible & Simple OOP PHP Framework                         |
// +---------------------------------------------------------------------------+
// | Copyright (c) 2005-2006 liu21st.com.  All rights reserved.                |
// | Website: http://www.fcs.org.cn/                                           |
// | Author : Liu21st 流年 <liu21st@gmail.com>                                 |
// +---------------------------------------------------------------------------+
// | This program is free software; you can redistribute it and/or modify it   |
// | under the terms of the GNU General Public License as published by the     |
// | Free Software Foundation; either version 2 of the License,  or (at your   |
// | option) any later version.                                                |
// |                                                                           |
// | This program is distributed in the hope that it will be useful,  but      |
// | WITHOUT ANY WARRANTY; without even the implied warranty of                |
// | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General |
// | Public License for more details.                                          |
// +---------------------------------------------------------------------------+

/**
 +------------------------------------------------------------------------------
 * FCS
 +------------------------------------------------------------------------------
 * @package    Util
 * @link       http://www.fcs.org.cn
 * @copyright  Copyright (c) 2005-2006 liu21st.com.  All rights reserved. 
 * @author     liu21st <liu21st@gmail.com>
 * @version    $Id$
 +------------------------------------------------------------------------------
 */

/**
 +------------------------------------------------------------------------------
 * 分页显示类
 +------------------------------------------------------------------------------
 * @package   Util
 * @author    liu21st <liu21st@gmail.com>
 * @version   0.8.0
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
    function __construct($totalRows,$firstRow=0,$listRows='',$parameter='')
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

        $this->parameter .= '&'.VAR_MODULE."=".MODULE_NAME;
        $this->parameter .= '&'.VAR_ACTION."=".ACTION_NAME;

        if(0 == $this->totalRows) return;

        $nowCoolPage      = ceil($this->nowPage/$this->rollPage);

		
        //上下翻页字符串
        $upRow   = $this->nowPage-1;
        $downRow = $this->nowPage+1;
        if ($upRow>0){
            $upPage="[<a href='?".VAR_PAGE."=$upRow&".$this->parameter."'>上一页</a>]";
        }else{
            $upPage="";
        }

        if ($downRow <= $this->totalPages){
            $downPage="[<a href='?".VAR_PAGE."=$downRow&".$this->parameter."'>下一页</a>]";
        }else{
            $downPage="";
        }
        // << < > >>
        if($nowCoolPage == 1){
            $theFirst = "";
            $prePage = "";
        }else{
            $preRow =  $this->nowPage-$this->rollPage;
            $prePage = "[<a href='?".VAR_PAGE."=$preRow&".$this->parameter."' >上".$this->rollPage."页</a>]";
            $theFirst = "[<a href='?".VAR_PAGE."=1&".$this->parameter."' >第一页</a>]";
        }
        if($nowCoolPage == $this->coolPages){
            $nextPage = "";
            $theEnd="";
        }else{
            $nextRow = $this->nowPage+$this->rollPage;
            $theEndRow = $this->totalPages;
            $nextPage = "[<a href='?".VAR_PAGE."=$nextRow&".$this->parameter."' >下".$this->rollPage."页</a>]";
            $theEnd = "[<a href='?".VAR_PAGE."=$theEndRow&".$this->parameter."' >最后一页</a>]";
        }
        // 1 2 3 4 5
        $linkPage = "";
        for($i=1;$i<=$this->rollPage;$i++){
            $page=($nowCoolPage-1)*$this->rollPage+$i;
            if($page!=$this->nowPage){
                if($page<=$this->totalPages){
                    $linkPage .= "&nbsp;<a href='?".VAR_PAGE."=$page&".$this->parameter."'>&nbsp;".$page."&nbsp;</a>";
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
            $pageArray['upPage']    =   '?'.VAR_PAGE."=$upRow&".$this->parameter;
            $pageArray['downPage']  =   '?'.VAR_PAGE."=$downRow&".$this->parameter;
            $pageArray['totalPages']=   $this->totalPages;
            $pageArray['firstPage'] =   '?'.VAR_PAGE."=1&".$this->parameter;
            $pageArray['endPage']   =   '?'.VAR_PAGE."=$theEndRow&".$this->parameter;
            $pageArray['nextPages'] =   '?'.VAR_PAGE."=$nextRow&".$this->parameter;
            $pageArray['prePages']  =   '?'.VAR_PAGE."=$preRow&".$this->parameter;
            $pageArray['linkPages'] =   $linkPage;
			$pageArray['nowPage'] =   $this->nowPage;
        	return $pageArray;
        }
        return $pageStr;
    }

}//类定义结束
?>