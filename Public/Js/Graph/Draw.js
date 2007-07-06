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
 * @version    $Id: Draw.js 73 2006-11-08 10:08:01Z fcs $
 +------------------------------------------------------------------------------
 */
function (){
	
	function line(x1,y1,x2,y2,color)
	{
		var tmp
		if(x1>=x2)
		{
			tmp=x1;
			x1=x2;
			x2=tmp;
			tmp=y1;
			y1=y2;
			y2=tmp;
		}
		for(var i=x1;i<=x2;i++)
		{
			x = i;
			y = (y2 - y1) / (x2 - x1) * (x - x1) + y1;
			a(x,y,color);
		}
	}

	function a(x,y,color)
	{document.write("<img border='0' style='position: absolute; left: "+(x+20)+"; top: "+(y+20)+";background-color: "+color+"' src='px.gif' width=1 height=1>")}

	function randomColor(){
	  return "#"+Math.floor(Math.random()*0xffffff).toString(16);
	}

	//<img src="javascript:GetNO(GetRnd(12))" >
	function drawNumber(num){
	var NumArray=[
		["0","0","0","3c","66","66","66","66","66","66","66","66","3c","0","0","0"],
		["0","0","0","30","38","30","30","30","30","30","30","30","30","0","0","0"],
		["0","0","0","3c","66","60","60","30","18","c","6","6","7e","0","0","0"],
		["0","0","0","3c","66","60","60","38","60","60","60","66","3c","0","0","0"],
		["0","0","0","30","30","38","38","34","34","32","7e","30","78","0","0","0"],
		["0","0","0","7e","6","6","6","3e","60","60","60","66","3c","0","0","0"],
		["0","0","0","38","c","6","6","3e","66","66","66","66","3c","0","0","0"],
		["0","0","0","7e","66","60","60","30","30","18","18","c","c","0","0","0"],
		["0","0","0","3c","66","66","66","3c","66","66","66","66","3c","0","0","0"],
		["0","0","0","3c","66","66","66","66","7c","60","60","30","1c","0","0","0"]
		];
	var str=[];
	num=String(num).split("");
	var nlen=NumArray[0].length;
	var ulen=num.length;
	for(var i=0;i<nlen;i++)for(var j=0;j<ulen;j++)str[str.length]=("0x"+NumArray[num[j]][i]);
	var str1="#define counter_width "+j*8;
	var str2="#define counter_height 16";
	return str1+"\n"+str2+"\n"+"static unsigned char counter_bits[]={"+str+"}";
	}

	/************* 画点 **************
  x,y     点所在的屏幕坐标（像素）
  color   颜色（字符串值）
  size    大小（像素）
**********************************/
function drawDot(x,y,color,size){
  document.write("<table border='0' cellspacing=0 cellpadding=0><tr><td style='position: absolute; left: "+(x)+"; top: "+(y)+";background-color: "+color+"' width="+size+" height="+size+"></td></tr></table>")
}

/************* 画直线 **************
  x1,y1   起点所在的屏幕坐标（像素）
  x2,y2   终点所在的屏幕坐标（像素）
  color   颜色（字符串值）
  size    大小（像素）
  style   样式
          =0    实线
          =1    虚线
          =2    虚实线
**********************************/
function drawLine(x1,y1,x2,y2,color,size,style){
  var i;
  var r=Math.floor(Math.sqrt((x2-x1)*(x2-x1)+(y2-y1)*(y2-y1)));
  var theta=Math.atan((x2-x1)/(y2-y1));
  if(((y2-y1)<0&&(x2-x1)>0)||((y2-y1)<0&&(x2-x1)<0))
    theta=Math.PI+theta;
  var dx=Math.sin(theta);//alert(dx)
  var dy=Math.cos(theta);
  for(i=0;i<r;i++){
    switch(style){
      case 0:
        drawDot(x1+i*dx,y1+i*dy,color,size);
        break;
      case 1:
        i+=size*2;
        drawDot(x1+i*dx,y1+i*dy,color,size);
        break;
      case 2:
        if(Math.floor(i/4/size)%2==0){
          drawDot(x1+i*dx,y1+i*dy,color,size);
        }
        else{
            i+=size*2;
            drawDot(x1+i*dx,y1+i*dy,color,size);
        }
        break;
      default:
        drawDot(x1+i*dx,y1+i*dy,color,size);
        break;
    }
  }
}

/************* 画实心矩形 **************
  x1,y1   起点（矩形左上角）所在的屏幕坐标（像素）
  x2,y2   终点（矩形右下角）所在的屏幕坐标（像素）
  color   颜色（字符串值）
**********************************/
function drawFilledRect(x1,y1,x2,y2,color){
  document.write("<table border='0' cellspacing=0 cellpadding=0><tr><td style='position: absolute; left: "+(x1)+"; top: "+(y1)+";background-color: "+color+"' width="+(x2-x1)+" height="+(y2-y1)+"></td></tr></table>")
}

/************* 画矩形 **************
  x1,y1   起点（矩形左上角）所在的屏幕坐标（像素）
  x2,y2   终点（矩形右下角）所在的屏幕坐标（像素）
  color   颜色（字符串值）
  size    大小（像素）
  style   样式
          =0    实线
          =1    虚线
          =2    虚实线
**********************************/
function drawRect(x1,y1,x2,y2,color,size,style){
  drawLine(x1,y1,x2,y1,color,size,style);
  drawLine(x1,y2,x2,y2,color,size,style);
  drawLine(x1,y1,x1,y2,color,size,style);
  drawLine(x2,y1,x2,y2,color,size,style);
}

/************* 画椭圆 **************
  x,y         中心所在的屏幕坐标（像素）
  a,b         长轴和短轴的长度（像素）
  color       颜色（字符串值）
  size        大小（像素）
  precision   边缘精细度
**********************************/
function drawOval(x,y,a,b,color,size,precision){
  var i;
  var iMax=2*Math.PI;
  var step=2*Math.PI/(precision*Math.sqrt(a*b)*4.5);
  for(i=0;i<iMax;i+=step){
    drawDot(x+a*Math.cos(i),y+b*Math.sin(i),color,size);
  }
}

/************* 画多边形 **************
  x,y     中心所在的屏幕坐标（像素）
  r       多边形外接圆半径（像素）
  n       多边形的边数
  color   颜色（字符串值）
  size    大小（像素）
  style   样式
          =0    实线
          =1    虚线
          =2    虚实线
**********************************/
function drawPoly(x,y,r,n,color,size,style){
  var i;
  var theta=Math.PI;
  var x1=x,y1=y-r,x2,y2;
  for(i=0;i<n;i++){
    theta-=(2*Math.PI/n);
    x2=x+r*Math.sin(theta);
    y2=y+r*Math.cos(theta);
    drawLine(x1,y1,x2,y2,color,size,style);
    x1=x2;
    y1=y2;//alert(x1+" "+y1)
  }
}
}