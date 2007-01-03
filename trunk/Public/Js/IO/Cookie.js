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
/*使用说明
+--------------------------------------------------------
浏览器能够通过Cookies保留有关数据。象Windows的注册表一样，用户不必知道Cookies的具体位置，浏览器能找到这些数据。第六代的浏览器，不管是IE还是NS都支持document.cookie属性。通过这个属性来读取或修改Cookies的值。不过Cookies的存储形式是非结构化的长字符串，需要经过相应的解析后才有意义。

Cookies的表达如下，除了name=value以外，其它均为可选：
name=value; 
expires=date;
domain=domainname
path=pathname;
secure;

例如：
User=HockeyDude; expires=Thu,01-Jan-70 00:00:01 GMT; domain=www.mydomain.com; path=/images; secure;
Pass=Gretzky; expires=Thu,01-Jan-70 00:00:01 GMT; domain=www.mydomain.com; path=/images; secure;

这么长的两个字符串只代表了两个Cookies。如果还要再加上电子信箱或其他信息就还得加长字符串。通常都是通过分解这样的字符串来取得各个变量或元素的。这实在是费时费力的事。

我使用面向对象设计(Object Oriented Design，OOD)的思路来编写Cookies处理函数，其特点如下：

便于增删子项。这是很重要的，有些浏览器限制Cookies的使用数量。
通过修改函数可以容易地修改时效数据。通常的做法很麻烦，要拷贝Cookies，删除原Cookies，修改并重写Cookies。
Cookies和它的子项存放在数组里。可以根据需要快速而有效地进行修改。这样也无须解析那长长的字符串。

Cookies对象的使用
以下是对象的公有方法：

方括号[]内是可选参数
//构造
Cookie([定界符，缺省为句点]) - 构造函数

//初始化
GetCookieCount() - 返回Cookies数量
Create(name, days) - 创建Cookies及其时效天数
Modify(name, days) - 修改Cookies的时效天数
Delete(name) - 删除Cookies及其子项
GetCount(name) - 返回Cookies的子项数量
AddItem(name,value) - 增加一个子项
GetItem(name,index) - 返回指定索引的子项
DelItem(name,index) - 删除指定的子项

//存取
Load() - 读取Cookies
Save() - 存储Cookies

+--------------------------------------------------------
*/

function Cookie(){//类定义

	//---------------------------------------------------
	//	属性声明
	//---------------------------------------------------
    this._Cookie=[];

	//---------------------------------------------------
	//	成员函数
	//---------------------------------------------------


    this.Load=function(){
        if(document.cookie.indexOf(";")!=-1){
            var _sp,_name,_tp,_tars,_tarslength;
            var _item=document.cookie.split("; ");
            var _itemlength=_item.length;
            while(_itemlength>0){
                _sp=_item[--_itemlength].split("=");
                _name=_sp[0];
                _tp=_sp[1].split(",");
                _tars=_tp.slice(1,_tp.length);
                this._Cookie[_name]=[];
                this._Cookie[_name]=_tars;
                this._Cookie[_name]["timeout"]=_tp[0];
                }
            return true;
            }
        return false;
        }


    this.Save=function(){
        var _str,_ars,_mars,_marslength,timeout,i,key;
        for(key in this._Cookie){
            if(!this._Cookie[key])return;
            _str=[];
            _mars=CookieClass._Cookie[key];
            _marslength=_mars.length;
            for(i=0;i<_marslength;i++)_str[_str.length]=escape(_mars[ i ]);
            document.cookie=key+"="+_mars["timeout"]+(_str.length>0?",":"")+_str+(_mars["timeout"]==0?"":";expires="+new Date(parseInt(_mars["timeout"])).toGMTString());
            }
        
        }


    this.GetCookieCount=function(){
        var _length=0,key;
        for(key in this._Cookie)_length++;
        return _length;
        }


    this.Create=function(name,days){
        days=days?days:0;
        if(!this._Cookie[name])this._Cookie[name]=[];
        this._Cookie[name]["timeout"]=days!=0?new Date().getTime()+parseInt(days)*86400000:0;
        }


    this.Modify=function(name,days){
        this.Create(name,days);
        }


    this.GetTime=function(name){
        return new Date(parseInt(this._Cookie[name]["timeout"]));
        }


    this.Delete=function(name){
        this.Create(name,0);
        }


    this.AddItem=function(name,value){
        this._Cookie[name][this._Cookie[name].length]=value;
        }


    this.DelItem=function(name,index){
        var _ttime=this._Cookie[name]["timeout"];
        this._Cookie[name]=this._Cookie[name].slice(0,index).concat(this._Cookie[name].slice(parseInt(index)+1,this._Cookie[name].length));
        this._Cookie[name]["timeout"]=_ttime;
        }


    this.GetCount=function(name){
        return this._Cookie[name].length;
        }


    this.GetItem=function(name,index){
        return this._Cookie[name][index];
        }
    }
/*
<script language="JScript">
var CookieClass=new Cookie();
if(!CookieClass.Load()){
    CookieClass.Create("Pass",10000);
    CookieClass.AddItem("Pass","Ps1");
    CookieClass.AddItem("Pass","Ps2");
    CookieClass.AddItem("Pass","Ps3");
    CookieClass.AddItem("Pass","Ps4");
    CookieClass.DelItem("Pass",0);
    CookieClass.Save();
    }
alert("Cookie过期时间:"+CookieClass.GetTime("Pass").toLocaleString());
alert(document.cookie);
alert(CookieClass.GetItem('Pass',0));
</script>
*/