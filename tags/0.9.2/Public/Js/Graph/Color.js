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
 * @version    $Id: Color.js 73 2006-11-08 10:08:01Z fcs $
 +------------------------------------------------------------------------------
 */
/*使用说明
+--------------------------------------------------------
对象方法列表
//** 用平常方法声明一个对象
window.document.write("------------------以下是用声明的对象的使用方法----------------<br>");
var NColor = new Color();
NColor.r = 255;
NColor.g = 255;
NColor.b = 255;
window.document.write("得到十六进制的表示法：" + NColor.getHec() + "<br>");
window.document.write("得到RGB的表示法：" + NColor.getRGB() + "<br>");

window.document.write("------------------以下是用Create新建对象的使用方法----------------<br>");
NColor.g = 0;
NColor.b = 0;
NColor = Color.Create(NColor);

window.document.write("参数为Color对象得到十六进制的表示法：" + NColor.getHec() + "<br>");
window.document.write("参数为Color对象得到RGB的表示法：" + NColor.getRGB() + "<br>");

NColor = Color.Create("#EFEFEF");
window.document.write("参数为十六进制字符串#EFEFEF得到十六进制的表示法：" + NColor.getHec() + "<br>");
window.document.write("参数为十六进制字符串#EFEFEF得到RGB的表示法：" + NColor.getRGB() + "<br>");

NColor = Color.Create("rgb(123,123,123)");
window.document.write("参数为RGB字符串rgb(123,123,123)得到十六进制的表示法：" + NColor.getHec() + "<br>");
window.document.write("参数为RGB字符串rgb(123,123,123)#EFEFEF得到RGB的表示法：" + NColor.getRGB() + "<br>");

NColor = Color.Create(200,200,200);
window.document.write("参数为数值200,200,200得到十六进制的表示法：" + NColor.getHec() + "<br>");
window.document.write("参数为数值200,200,200得到RGB的表示法：" + NColor.getRGB() + "<br>");
+--------------------------------------------------------
*/
function parseColor(vValue) { 
    var oColorParser = document.createElement("body"); 
    oColorParser.bgColor = vValue; 
    return oColorParser.bgColor; 
}

//+--------------------------------------------------------------------
//
//       Function:  Color
//
//      Arguments:  无
//
//         Return:  无
//
//    Description:  一个实现网页颜色各种功能模块的类
//
//+--------------------------------------------------------------------
function Color()
{
  this.r = 0; //**  红色，范围为0至125
  this.g = 0; //**  绿色，范围为0至125
  this.b = 0; //**  蓝色，范围为0至125
  
  //+--------------------------------------------------------------------
  //
  //       Function:  getHec
  //
  //      Arguments:  没有
  //
  //         Return:  本对象的十六进制表示法，比如#CECECE
  //
  //    Description:  通过调用Color.getHec来返回本身的十六进制表示法
  //
  //+--------------------------------------------------------------------
  this.getHec = function()
  {
    return Color.getHec(this);
  }
  
  //+--------------------------------------------------------------------
  //
  //       Function:  getRGB
  //
  //      Arguments:  没有
  //
  //         Return:  本对象的RGB表示法，比如rgb(255,255,255)
  //
  //    Description:  通过调用Color.getRGB返回本身的RGB表示法
  //
  //+--------------------------------------------------------------------
  this.getRGB = function()
  {
    return Color.getRGB(this);
  }
  
  //+--------------------------------------------------------------------
  //
  //       Function:  toString
  //
  //      Arguments:  没有
  //
  //         Return:  返回本身的十六进制表示法
  //
  //    Description:  重写对象的toString方法
  //
  //+--------------------------------------------------------------------
  this.toString = function()
  {
    return this.getHec();
  }
}

//+--------------------------------------------------------------------
//
//       Function:  getColor
//
//      Arguments:  红、绿、蓝的十进制数值(范围为0-255)
//
//         Return:  相应的Color对象，如果R,G,B不能构成一个合法Color对象
//                  将会返回一个默认的Color对象，它的RGB为rgb(0,0,0)
//
//    Description:  通过传入的红、绿、蓝十进制数值返回相应的Color对象
//
//+--------------------------------------------------------------------
Color.getColor = function(R,G,B)
{
  R = parseInt(R);
  G = parseInt(G);
  B = parseInt(B);
  
  //**  注意下边的R != R，只有R为NaN的时候才成立，因为NaN是唯一不等于自身的常数
  //**  下一行判断R、G、B是否能够构成一个合法的Color对象
  if((R != R) || (G != G) || (B != B) || R < 0 || R > 255 || G < 0 || G > 255 || B < 0 || B > 255)
  {
    return null; //** 如果不能够构成返回null
  }
  else
  {
      //** 如果能够构成返回相应的Color对象
    var NewColor = new Color;
    NewColor.r = R;
    NewColor.g = G;
    NewColor.b = B;
    return NewColor;
  }
}

//+--------------------------------------------------------------------
//
//       Function:  getHec
//
//      Arguments:  一个Color对象
//
//         Return:  参数对象的十六进制表示法，如果传入参数不合法，返回
//                  默认的Color对象，RGB为rgb(0,0,0)
//
//    Description:  能过传入一个Color对象返回十六进制表示法
//
//+--------------------------------------------------------------------
Color.getHec = function(PColor)
{
    //** 判断参数PColor是否为类Color的一个实例
  if(!(PColor instanceof Color))
  {
    return "#000000"; //** 如果PColor不是Color类的实例，返回默认十六进制表示法#000000
  }
  //** 如果PColor是Color类的实例，生成一个临时的Color实例tmpColor
  var tmpColor = Color.getColor(PColor.r,PColor.g,PColor.b);
  if(tmpColor == null)
  {
    return "#000000";//** 当PColor本身的r、g、b不合法，返回默认十六进制表示法#000000
  }
  else
  {
      //** 如果PColor合法，返回PColor的十六进制表示法
    //** 因为每一个色的表示有两位，就算值为1也必须以01表示，因此当该色的数值小于10给数
    //** 值前边添加一个0，否则调用Color.getHecStr得到十六进制的字符串
    var Red = PColor.r < 10 ? "0" + PColor.r : Color.getHecStr(PColor.r);    
    var Green = PColor.g < 10 ? "0" + PColor.g : Color.getHecStr(PColor.g);
    var Blue = PColor.b < 10 ? "0" + PColor.b : Color.getHecStr(PColor.b);
    return "#" + Red + Green + Blue;
  }
}

//+--------------------------------------------------------------------
//
//       Function:  getRGB
//
//      Arguments:  一个Color对象
//
//         Return:  PColor参数的RGB表示法，比如rgb(0,0,0)
//
//    Description:  通过传入一个Color对象返回该对象的RGB表示法，如果对
//                  象不合法，返回默认RGB表示法rgb(0,0,0)
//
//+--------------------------------------------------------------------
Color.getRGB = function(PColor)
{
  //** 判断参数PColor是否为类Color的一个实例
  if(!(PColor instanceof Color))
  {
    return "rgb(0,0,0)";//** 如果PColor不是Color类的实例，返回默认RGB表示法rgb(0,0,0)
  }
  var tmpColor = Color.getColor(PColor.r,PColor.g,PColor.b);
  if(tmpColor == null)
  {
    //** 当得到的临时Color对象为null时修改PColor的值为默认值0
    PColor.r = 0;
    PColor.g = 0;
    PColor.b = 0;
  }
  return "rgb(" + PColor.r + "," + PColor.g + "," + PColor.b + ")";
}

//+--------------------------------------------------------------------
//
//       Function:  Create
//
//      Arguments:  1. 一个Color类的对象
//                  2. 十六进制表示法
//                  3. R、G、B十进制数值
//
//         Return:  1. 一个Color类的对象，返回跟参数对象相同的Color对象
//                  2. 十六进制表示法,比如#000000，返回相应的Color对象
//                  3. R、G、B十进制数值，返回相应的Color对象
//
//    Description:  新建一个Color对象，参数是不指明的，然后从arguments
//                  数组来判断参数的类型，关于arguments可以参考《Windows
//                  脚本技术》
//
//+--------------------------------------------------------------------
Color.Create = function()
{ 
    //** 得到arguments对象
  var Arg = arguments;
  
  //** 判断参数的个数，必须为1或者为3，否则返回默认Color对象(rgb为rgb(0,0,0))
  if(Arg.length != 1 && Arg.length != 3)
  {
    return new Color;
  }
  //** 当参数个数为0，则有可能参数是1和2的情况(查看函数说明Arguments)
  if(Arg.length == 1)
  {
    var Param = Arg[0];
    //** 当参数Param为Color对象，则为1情况
    if(Param instanceof Color)
    {
      var tmpColor = Color.getColor(Param.r,Param.g,Param.b);
      if(tmpColor == null)
      {
        return new Color;
      }
      return tmpColor;
    }
    //** 当参数为字符串，则有可能为2情况
    if(typeof(Param) == "string")
    {
        //** 利用正则表达式判断是否为十进制表示法
      var re = /^#[0-9A-Fa-f]{6}$/;
      if(re.test(Param))
      {
        var NewColor = new Color;
        NewColor.r = parseInt(Param.substr(1,2),16);
        NewColor.g = parseInt(Param.substr(3,2),16);
        NewColor.b = parseInt(Param.substr(5,2),16);
        return NewColor;
      }
      
      //** 利用正则表达式判断是否为RGB表示法
      re = /^rgb\((\d{1,3}),(\d{1,3}),(\d{1,3})\)$/g;
      if(re.test(Param))
      {
        var R = Param.replace(re,"$1");
        var G = Param.replace(re,"$2");
        var B = Param.replace(re,"$3");
        var tmpColor = Color.getColor(R,G,B);
        if(tmpColor == null)
        {
          return new Color;
        }
        return tmpColor;
      }
    }
    //** 如果参数没有一个合法，返回默认Color对象
    return new Color;
  }
  else
  {
      //** 得到三种颜色的值
    var R = Arg[0];
    var G = Arg[1];
    var B = Arg[2];
    //** 利用正则表达式判断是否为三个参数全部为数值，如果不是返回默认Color对象
    if(!(/^\d{1,3}$/.test(R) && /^\d{1,3}$/.test(G) || /^\d{1,3}$/.test(B)))
    {
      return  new Color;
    }
    //** 如果三种颜色为数值，得到该数值相应的Color对象
    var tmpColor = Color.getColor(R,G,B); //** 如果得到的临时对象为null返回默认Color对象
    if(tmpColor == null)
    {
      return new Color;
    }
    //** 返回Color对象
    return tmpColor;
  }
}

//+--------------------------------------------------------------------
//
//       Function:  getHecStr
//
//      Arguments:  十进制数值
//
//         Return:  十六进制数值字符串
//
//    Description:  把传入的十进制数值转换成十六进制
//
//+--------------------------------------------------------------------
Color.getHecStr = function(Num)
{
  //+--------------------------------------------------------------------
  //
  //       Function:  getHec
  //
  //      Arguments:  十进制数值(范围在0-15之间)
  //
  //         Return:  十六进制数值字符串，如果范围不对返回原数值的字符串
  //
  //    Description:  把传入的十进制数值转换成十六进制，此函数为内部函数，
  //                  只能在Color.getHecStr里调用。
  //
  //+--------------------------------------------------------------------
  function getHec(Num)
  {
    
    if(Num < 10 || Num > 15)
    {
      return Num.toString();
    }
    switch(Num)
    {
      case 10: return "A";
      case 11: return "B";
      case 12: return "C";
      case 13: return "D";
      case 14: return "E";
      case 15: return "F";
    }
  }
  Num = parseInt(Num);
  if(Num != Num || Num < 0)
  {
    return "";
  }
  var NumStr = "";
  //** 进行十进制向十六进制的转换
  while(Num > 15)
  {
    NumStr = getHec(Num % 16) + NumStr;
    Num = (Num - Num % 16) / 16;
  }
  NumStr = getHec(Num) + NumStr;
  return NumStr;
}