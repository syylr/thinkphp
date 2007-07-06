// +---------------------------------------------------------------------------+
// | FCS -- Fast,Compatible & Simple OOP PHP Framework                         |
// | FCS JS 基类库                                                             |
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
 * 下拉列表多选类
 +------------------------------------------------------------------------------
 * @package    Form
 * @link       http://www.fcs.org.cn
 * @copyright  Copyright (c) 2005-2006 liu21st.com.  All rights reserved. 
 * @author     liu21st <liu21st@gmail.com>
 * @version    $Id$
 +------------------------------------------------------------------------------
 */
function Excel(tableId){

	//系统常量定义
	var xlCenter=-4108;
	var xlLeft=1;
	var xlRight=-4152;
	var xlbottom=-4107;
	var xlPortrait=1;
	var xlPaperA4=9;
	var xlPaperA3=8;
	var xlLandscape=2;
	var xlContinuous=1;
	var xlDashDot = 5
	var xlEdgeLeft=7;
	var xlThin=2;
	var xlMedium=-4138;
	var xlAutomatic=-4105;
	var xlDiagonalDown=5;
	var xlDiagonalUp=6;
	var xlNone=-4142;
	var xlInsideHorizontal=12;
	var xlDownThenOver=1;
	var xlMaximized = -4137;
	var xlFontFST="仿宋_GB2312";
	var xlFontST="宋体";

	var oSheet;
	var oActiveSheet;
	var oApplication;


	//-------------------------------------------------------
	//	要打印显示的数据 二维数组 第一行是表头
	//--------------------------------------------------------
	var dataArray	=	new Array();

	//-------------------------------------------------------
	//	初始化Excel对象
	//-------------------------------------------------------
	try {
		oApplication    = new ActiveXObject ( "Excel.Application" );
		//oApplication.visible = true;    
		oApplication.DisplayAlerts = false;
		var xlBook = oApplication.Workbooks.Add;
		oActiveSheet = xlBook.Worksheets(1); 
		//隐藏应用程序和最小化
		oActiveSheet.Application.Visible  = false;
		oActiveSheet.Application.WindowState = 2;
     }
    catch(e) 
		{
			alert('初始化Excel对象出错：'+e.description);
             return ;
		}  
		
	this.tableId	=	tableId || '';
	this.tableObj	=	document.getElementById(this.tableId);
	//-------------------------------------------------------
	//	输出数据
	//-------------------------------------------------------	
	function showTableData()
	{
		var rowLength = this.tableObj.rows.length;
		var colLength = this.tableObj.rows[0].cells.length;
		for (i=0;i< rowLength;i++ )
		{
			for (j=0;j< colLength ;j++ )
			{
				oActiveSheet.Cells(i+1,j+1).formulaR1C1= this.tableObj.rows[i].cells[j].innerText;
			}
		}
		
	}
	//-------------------------------------------------------
	//	设置单元格格式
	//-------------------------------------------------------
	function setCellStyle()
	{
		oApplication.Selection.HorizontalAlignment = xlCenter;
		oApplication.Selection.VerticalAlignment = xlCenter;
		oApplication.Selection.Font.size=20;
		oApplication.Selection.Font.bold= true
	    oApplication.Selection.Font.name=xlFontFST;
	}

	function setAlign(type,align)
	{
		switch (align)
		{
		case 'L': align = xlLeft;break;
		case 'C': align = xlCenter;break;
		case 'R': align = xlRight;break;
		}
		oApplication.Selection[type] = align;
	}

	//-------------------------------------------------------
	//	选择单元格范围 'A1:F4'
	//	选择单元格范围 oActiveSheet.Cells(1,3),oActiveSheet.Cells(4,6)
	//-------------------------------------------------------
	function selectRange()
	{
		if (arguments.length==2)
		{
			oActiveSheet.Range( arguments[0] +":"+ arguments[1] ).Select(); 
		}else if (arguments.length==4)
		{
			oActiveSheet.Range(oActiveSheet.Cells(arguments[0],arguments[1]),oActiveSheet.Cells(arguments[2],arguments[3])).Select;
		}else {
			alert('范围格式不正确!，请使用A1,F4或者1,3,4,6格式');
		}
		
	}

	//-------------------------------------------------------
	//	设置自动换行
	//-------------------------------------------------------
	function autoFix()
	{
		//设置自动换行
		oApplication.Selection.WrapText=true;
	}

	//-------------------------------------------------------
	//	设置单元格数据
	//-------------------------------------------------------
	function setCellData(Col,Row,data)
	{
		oActiveSheet.Cells(Col,Row).formulaR1C1= data;
	}
	//-------------------------------------------------------
	//	合并单元格
	//-------------------------------------------------------
	function mergeCell()
	{
		 if (arguments.length==2)
		{
			selectRange(arguments[0],arguments[1]);
		}
		 else if (arguments.length==4)
		 {
			selectRange(arguments[0],arguments[1],arguments[2],arguments[3]);
		 }
		 oApplication.Selection.Merge();
	}
	//-------------------------------------------------------
	//	设置打印边框
	//	BorderType 1 打印每个单元格边框 0 只打印区域边框线
	//	LineStyle :xlContinuous
	//	Weight :xlMedium  xlThin
	//-------------------------------------------------------
	function setBorder(row1,col1,row2,col2,BorderType,LineStyle,Weight,Color){
    // 用于设置打印内容区域的边框
     oActiveSheet.Range(oActiveSheet.Cells(row1,col1),oActiveSheet.Cells(row2,col2)).Select;
     var selection=oApplication.Selection;
	 var MaxLength = BorderType?13:11;
	 for( i=7;i<MaxLength;i++){
		   selection.HorizontalAlignment = xlCenter
		   selection.VerticalAlignment = xlCenter
		   selection.Borders(i).LineStyle = LineStyle?LineStyle:xlContinuous               
		   selection.Borders(i).Weight = Weight?Weight:xlThin
		   selection.Borders(i).ColorIndex = Color?Color:xlAutomatic
         }
	}
	//-------------------------------------------------------
	//	设置打印参数
	//-------------------------------------------------------
	function setpage(){
     oActiveSheet.PageSetup.PrintArea = ""
     var page=oActiveSheet.PageSetup
          page.CenterFooter = "第 &P/&N 页"
          page.LeftMargin = oApplication.InchesToPoints(0.196850393700787)
          page.RightMargin = oApplication.InchesToPoints(0.196850393700787)
          page.TopMargin = oApplication.InchesToPoints(0.393700787401575)
          page.BottomMargin = oApplication.InchesToPoints(0.393700787401575)
          page.HeaderMargin = oApplication.InchesToPoints(0.393700787401575)
          page.FooterMargin = oApplication.InchesToPoints(0.393700787401575)
          page.PrintHeadings = false
          page.PrintGridlines = false
          page.CenterHorizontally = true
          page.CenterVertically = false
          page.Orientation = xlPortrait
          page.Draft = false
          page.PaperSize = xlPaperA4
          page.FirstPageNumber = xlAutomatic
          page.Order = xlDownThenOver
          page.BlackAndWhite = false
          page.Zoom = 100
	}
	//-------------------------------------------------------
	//	设置标题
	//-------------------------------------------------------
	function setTitle(title,Rang1,Rang2,font,size)
	{
		 //设置标题
		 oActiveSheet.Cells(1,1).formulaR1C1=title;
		 oActiveSheet.Range("A1:G2").Select();
		 oApplication.Selection.HorizontalAlignment = xlCenter;
		 oApplication.Selection.VerticalAlignment = xlCenter;
		 oApplication.Selection.Font.size=20;
		 oApplication.Selection.Font.name=xlFontFST;
		 oActiveSheet.Rows("1:1").RowHeight = 30;
		 oActiveSheet.Range("A1:G1").Select();
		 oApplication.Selection.Merge();
	}
	//-------------------------------------------------------
	//	设置某个范围列宽
	//-------------------------------------------------------
	function setColWidth( beginCol ,endCol ,width )
	{
		 if (!endCol) endCol = beginCol ;
		 oActiveSheet.Columns( beginCol + ":" + endCol ).ColumnWidth = width ;
	}
	//-------------------------------------------------------
	//	设置某个范围行高
	//-------------------------------------------------------
	function setRowHeight( beginRow, endRow , height )
	{
		 if (!endRow) endRow = beginRow ;
		oActiveSheet.Rows( beginRow + ":"+ endRow ).RowHeight = height ;
	}

	//-------------------------------------------------------
	//	设置打印格式
	//-------------------------------------------------------
	function setPageSetup()
	{
		oActiveSheet.PageSetup.PrintTitleRows = "$1:$3";
		oActiveSheet.PageSetup.PrintTitleColumns = "";
	}
/*
var excel  = new ActiveXObject("Excel.Application"); //创建AX对象excel
 excel.visible =true; //设置excel可见属性
 var xlBook = excel.Workbooks.Add; //获取workbook对象
 var sheet1 = xlBook.Worksheets(1);  //创建sheet1
 var sheet2 = xlBook.Worksheets(2);  //创建sheet2
 sheet1.Range(sheet1.Cells(1,1),sheet1.Cells(1,14)).mergecells=true; //合并单元格
 sheet1.Range(sheet1.Cells(1,1),sheet1.Cells(1,14)).value="员工月考核成绩"; //设置单元格内容
 sheet1.Range(sheet1.Cells(1,1),sheet1.Cells(1,14)).Interior.ColorIndex=6;//设置底色 
 sheet1.Range(sheet1.Cells(1,1),sheet1.Cells(1,14)).Font.ColorIndex=5;//设置字体色 
sheet1.Rows(1).RowHeight = 20; //设置列高
sheet1.Rows(1).Font.Size=16;  //设置文字大小
sheet1.Rows(1).Font.Name="宋体"; //设置字体
//设置每一列的标题
sheet1.Cells(2,1).Value="工程师考核项";
sheet1.Cells(2,2).Value="总分";
sheet1.Cells(2,3).Value="研发进度";
sheet1.Cells(2,4).Value="出勤率";
sheet1.Cells(2,5).Value="执行力";
sheet1.Cells(2,6).Value="责任心";
sheet1.Cells(2,7).Value="工作规范";
sheet1.Cells(2,8).Value="协作精神";
sheet1.Cells(2,9).Value="进取性";
sheet1.Cells(2,10).Value="工作合理性";
sheet1.Cells(2,11).Value="解决问题能力";
sheet1.Cells(2,12).Value="应变能力";
sheet1.Cells(2,13).Value="人际技能";
sheet1.Cells(2,14).Value="理解能力";*/
}