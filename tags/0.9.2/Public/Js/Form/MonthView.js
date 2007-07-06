/*
jsMonthView 1.1
Author By DengKang(China) 2004-4-12
*/
function DefineMonthView(theTextObject){ //the month view construct function
	var theName = "jsMV"; //the name is only one on document
	this.Name = theName; //the month view name
	this.Source = theTextObject; //the month view act on theTextObject
	this.CenturyYear = 1940; //if input two digit year, and the year add 1900 less than this.CenturyYear, then year is the year add 2000 
	this.MinYear = 1970; //year list min value
	//return year between 1000 and 9999 and <= this.MaxYear
	this.MaxYear = 2029; //year list max value
	//return year between 1000 and 9999 and >= this.MinYear
	this.Width = 300; //the month view main body's width
	this.Height = 200; //the month view main body's height
	this.AllowNullDate = true; //if this attribute is false, then empty string is invalid date and spring this.WarningAction.
	this.AlwaysMakeHTML = false; //if this attribute is true, then every time show MonthView, the HTML code will rebuild
	this.OnlyInput = false; //do'nt show month view, only input and format date string.
	this.AutoHidden = true; //if this attribute is true, then use mouse select a date after, the month view auto hide;
	this.WarningAction = ""; //if input string is not a date and this is not empty, then execute this.
	this.DateFormat = "<yyyy>-<mm>-<dd>"; //the date format, must have year, month and day
	//<yy> or <yyyy> is year, <m> or <mm> is digital format month, <MMM> or <MMMMMM> is character format month, <d> or <dd> is day, other char unchanged
	//this function setting year, month and day sequence
	//for example:
	//  <yyyy>-<mm>-<dd> : 2002-04-01
	//  <yy>.<m>.<d> : 02.4.1
	//  <yyyy> Year <MMMMMM> Month <d> Day : 2002 Year April Month 1 Day
	//  <m>/<d>/<yy> : 4/1/02
	//  <MMM> <dd>, <yyyy> : Apr 01, 2002
	//  <MMMMMM> <d>,<yyyy> : April 1,2002
	//  <dd>-<MMM>-<yyyy> : 01-Apr-2002
	//  <dd>/<mm>/<yyyy> : 01/04/2002
	this.UnselectBgColor = "#FFFFFF"; //the month view default background color
	this.SelectedBgColor = "#808080"; //the selected date background color
	this.SelectedColor = "#FFFFFF"; //the selected date front color
	this.DayBdWidth = "2px"; //the day unit border width
	this.DayBdColor = this.UnselectBgColor; //the day unit border color,default is this.UnselectBgColor
	this.TodayBdColor = "#FF0000"; //denote today's date border color
	this.InvalidColor = "#808080"; //it is not current month day front color
	this.ValidColor = "#0000FF"; //it is current month day front color
	this.YearListStyle = "width:60px; font-size:12px; font-family:Verdana;"; //the year list's style
	this.MonthListStyle = "width:100px; font-size:12px; font-family:Verdana;"; //the month list's style
	this.MonthName = new Array(); //month name list, font is include this.MonthListStyle
	this.MonthName[0] = "January";
	this.MonthName[1] = "February";
	this.MonthName[2] = "March";
	this.MonthName[3] = "April";
	this.MonthName[4] = "May";
	this.MonthName[5] = "June";
	this.MonthName[6] = "July";
	this.MonthName[7] = "August";
	this.MonthName[8] = "September";
	this.MonthName[9] = "October";
	this.MonthName[10] = "November";
	this.MonthName[11] = "December";
	this.WeekListStyle = "font-size:16px; font-weight:bolder; font-family:Times new roman;"; //the week name's style
	this.TitleStyle = "text-align:center; vertical-align:bottom; cursor:default; color:#000000; background-color:" + this.UnselectBgColor + ";"; //the month view title area's style
	this.WeekName = new Array(); //week name list, font is include this.WeekListStyle
	this.WeekName[0] = "Sun";
	this.WeekName[1] = "Mon";
	this.WeekName[2] = "Tue";
	this.WeekName[3] = "Wed";
	this.WeekName[4] = "Thu";
	this.WeekName[5] = "Fri";
	this.WeekName[6] = "Sat";
	this.MonthGridStyle = "border-width:1px; border-style:solid; border-color:#000000;"; //the month view main body's default style
	this.HeaderStyle = "height:32px; background-color:buttonface;"; //the month view header area's style
	this.DayListStyle = "cursor:hand; font-size:12px; font-family:Verdana; text-align:center; vertical-align:middle;"; //the month view day area's style
	this.DayOverStyleName = new Array(); //a style name for mouse over day
	this.DayOverStyleValue = new Array(); //a style value for mouse over day with this.DayOverStyleName
	this.DayOverStyleName[0] = "textDecoration";
	this.DayOverStyleValue[0] = "underline";
	this.TodayListStyle = "font-size:12px; font-family:Verdana;"; //the today tip's style
	this.TodayListTitle = "Goto Today"; //the today tip's title
	this.FooterStyle = "text-align:left; vertical-align:middle; cursor:hand; color:#000000; background-color:" + this.UnselectBgColor + ";"; //the month footer area's style
	this.TodayTitle = "Today:"; //today tip string, font is include this.TodayListStyle
	this.MonthBtStyle = "font-family:Marlett; font-size:12px; width:20px; height:20px; "; //the change month button style
	this.PreviousMonthTitle = "Goto Previous Month";
	this.PreviousMonthText = "3"; //the go previous month button text
	//font is include this.MonthBtStyle
	this.NextMonthTitle = "Goto Next Month";
	this.NextMonthText = "4"; //the go next month button text
	//font is include this.MonthBtStyle
	this.LineBgStyle = "height:10px; background-color:" + this.UnselectBgColor + "; text-align:center; vertical-align:middle;"; //the month view title area and day area compart area background style
	this.LineStyle = "width:90%; height:1px; background-color:#000000;"; //the month view title area and day area compart area front style

	this.CheckIE = function( ){//check IE version. if greater than 5.0 return true, else return false;
		var version = "";
		var navAgent = navigator.userAgent;
		var navIndex = navAgent.indexOf("MSIE");
		if (navIndex == -1){
			return(false);
		}else{
			version = navAgent.substr(navIndex + 4, 4);
			if (isNaN(parseFloat(version))){
				return(false);
			}else{
				if (parseFloat(version) > 5.0){
					return(true);
				}else{
					return(false);
				}
			}
		}
	}
	this.GetoffsetLeft = function(theObject){ //return theObject's absolute offsetLeft
		var absLeft = 0;
		var thePosition = "";
		var tmpObject = theObject;
		while (tmpObject != null){
			thePosition = tmpObject.position;
			tmpObject.position = "static";
			absLeft += tmpObject.offsetLeft;
			tmpObject.position = thePosition;
			tmpObject = tmpObject.offsetParent;
		}
		return(absLeft);
	}
	this.GetoffsetTop = function(theObject){ //return theObj's absolute offsetTop
		var absTop = 0;
		var thePosition = "";
		var tmpObject = theObject;
		while (tmpObject != null){
			thePosition = tmpObject.position;
			tmpObject.position = "static";
			absTop += tmpObject.offsetTop;
			tmpObject.position = thePosition;
			tmpObject = tmpObject.offsetParent;
		}
		return(absTop);
	}
	this.GetFormatYear = function(theYear){//format theYear to 4 digit
		var tmpYear = parseInt(theYear,10);
		if (tmpYear < 100){
			tmpYear += 1900;
			if (tmpYear < this.CenturyYear){
				tmpYear += 100;
			}
		}
		if (tmpYear < this.MinYear){
			tmpYear = this.MinYear;
		}
		if (tmpYear > this.MaxYear){
			tmpYear = this.MaxYear;
		}
		return(tmpYear);
	}
	this.GetMonthDays = function(theYear, theMonth){ //get theYear and theMonth days number
		var theDays = new Array(31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);
		var theMonthDay = 0
		var tmpYear = this.GetFormatYear(theYear);
		theMonthDay = theDays[theMonth];
		if (theMonth == 1){ //theMonth is February
			if (((tmpYear % 4 == 0) && (tmpYear % 100 != 0)) || (tmpYear % 400 == 0)){
				theMonthDay++;
			}
		}
		return(theMonthDay);
	}
	this.SetDateFormat = function(theYear, theMonth, theDay){//format a date to this.DateFormat
		var theDate = this.DateFormat;
		var tmpYear = this.GetFormatYear(theYear);
		var tmpMonth = theMonth;
		if (tmpMonth < 0){
			tmpMonth = 0;
		}
		if (tmpMonth > 11){
			tmpMonth = 11;
		}
		var tmpDay = theDay;
		if (tmpDay < 1){
			tmpDay = 1;
		}else{
			tmpDay = this.GetMonthDays(tmpYear, tmpMonth);
			if (theDay < tmpDay){
				tmpDay = theDay;
			}
		}
		theDate = theDate.replace(/<yyyy>/g, tmpYear.toString());
		theDate = theDate.replace(/<yy>/g, tmpYear.toString().substr(2,2));
		theDate = theDate.replace(/<MMMMMM>/g, this.MonthName[tmpMonth]);
		theDate = theDate.replace(/<MMM>/g, this.MonthName[tmpMonth].substr(0,3));
		if (theMonth < 9){
			theDate = theDate.replace(/<mm>/g, "0" + (tmpMonth + 1).toString());
		}else{
			theDate = theDate.replace(/<mm>/g, (tmpMonth + 1).toString());
		}
		theDate = theDate.replace(/<m>/g, (tmpMonth + 1).toString());
		if (theDay < 10){
			theDate = theDate.replace(/<dd>/g, "0" + tmpDay.toString());
		}else{
			theDate = theDate.replace(/<dd>/g, tmpDay.toString());
		}
		theDate = theDate.replace(/<d>/g, tmpDay.toString());
		return(theDate);
	}
	this.GetTextDate = function(theString){ //convert a date string to a date, if the string is not a date, return empty
		var i = 0, j = 0, tmpChar = "", find_tag = "";
		var start_at = 0, end_at = 0, year_at = 0, month_at = 0, day_at = 0;
		var tmp_at = 0, one_at = 0, two_at = 0, one_days = 0, two_days = 0;
		var aryDate = new Array();
		var tmpYear = -1, tmpMonth = -1, tmpDay = -1;
		var tmpDate = theString.toLowerCase();
		var defDate = "";
		//convert string month to digital month
		tmpDate = tmpDate.replace(/(\D)0(\d)/g, "$1-$2");
		for (i=11; i>=9; i--){
			tmpDate = tmpDate.replace(this.MonthName[i].toLowerCase().substr(0,3), "-0" + (i+1).toString() + "-");
		}
		for (i=8; i>=0; i--){
			tmpDate = tmpDate.replace(this.MonthName[i].toLowerCase().substr(0,3), "-00" + (i+1).toString() + "-");
		}
		tmpDate = tmpDate.replace(/jan/g, "-001-");
		tmpDate = tmpDate.replace(/feb/g, "-002-");
		tmpDate = tmpDate.replace(/mar/g, "-003-");
		tmpDate = tmpDate.replace(/apr/g, "-004-");
		tmpDate = tmpDate.replace(/may/g, "-005-");
		tmpDate = tmpDate.replace(/jun/g, "-006-");
		tmpDate = tmpDate.replace(/jul/g, "-007-");
		tmpDate = tmpDate.replace(/aug/g, "-008-");
		tmpDate = tmpDate.replace(/sep/g, "-009-");
		tmpDate = tmpDate.replace(/oct/g, "-010-");
		tmpDate = tmpDate.replace(/nov/g, "-011-");
		tmpDate = tmpDate.replace(/dec/g, "-012-");
		//delete redundant chars
		for (i = 0; i < tmpDate.length; i++){
			tmpChar = tmpDate.charAt(i);
			if (((tmpChar < "0") || (tmpChar>"9")) && (tmpChar != "-")){
				tmpDate = tmpDate.replace(tmpChar, "-")
			}
		}
		while (tmpDate.indexOf("--") != -1){
			tmpDate = tmpDate.replace(/--/g, "-");
		}
		start_at = 0;
		end_at = tmpDate.length - 1;
		while (tmpDate.charAt(start_at) == "-"){
			start_at++;
		}
		while (tmpDate.charAt(end_at) == "-"){
			end_at--;
		}
		if (start_at < end_at+1){
			tmpDate = tmpDate.substring(start_at, end_at + 1);
		}else{
			tmpDate = "";
		}
		//get theString date format
		aryDate = tmpDate.split("-");
		if (aryDate.length != 3){
			return(defDate);
		}
		tmp_at = 0;
		for (i = 0; i < 3; i++){
			if (parseInt(aryDate[i], 10)==0){
				tmp_at++;
				year_at = i;
			}
		}
		if (tmp_at > 1){
			return(defDate);
		}
		if (tmp_at == 1){
			aryDate[year_at] = this.GetFormatYear(aryDate[year_at]).toString();
		}
		tmpDate = this.DateFormat;
		year_at = tmpDate.indexOf("<yyyy>");
		if (year_at == -1){
			year_at = tmpDate.indexOf("<yy>");
		}
		month_at = tmpDate.indexOf("<MMMMMM>");
		if (month_at == -1){
			month_at = tmpDate.indexOf("<MMM>");
		}
		if (month_at == -1){
			month_at = tmpDate.indexOf("<mm>");
		}
		if (month_at == -1){
			month_at = tmpDate.indexOf("<m>");
		}
		day_at = tmpDate.indexOf("<dd>");
		if (day_at == -1){
			day_at = tmpDate.indexOf("<d>");
		}
		//get month position
		find_tag = "000"; //start, the month position is null
		//find_tag	date_format
		//000	unknow, theString is not a date
		//001	day_year_month or year_day_month
		//010	day_month_year or year_month_day
		//100	month_day_year or month_year_dat
		for (i = 0; i < 3; i++){
			if (aryDate[i].length == 3){
				if ((aryDate[i] >= "001") && (aryDate[i] <= "012")){
					if (find_tag != "000"){
						return(defDate);
					}
					tmpMonth = parseInt(aryDate[i], 10) - 1;
					switch (i){
						case 0:
							find_tag = "100";
							one_at = parseInt(aryDate[1], 10);
							two_at = parseInt(aryDate[2], 10);
							break;
						case 1:
							find_tag = "010";
							one_at = parseInt(aryDate[0], 10);
							two_at = parseInt(aryDate[2], 10);
							break;
						case 2:
							find_tag = "001";
							one_at = parseInt(aryDate[0], 10);
							two_at = parseInt(aryDate[1], 10);
							break;
						default:;
					}
				}
			}
		}
		if (find_tag!="000"){
			one_days = this.GetMonthDays(two_at, tmpMonth);
			two_days = this.GetMonthDays(one_at, tmpMonth);
			if ((one_at > one_days) && (two_at > two_days)){
				return(defDate);
			}
			if ((one_at <= one_days) && (two_at > two_days)){
				tmpYear = this.GetFormatYear(two_at);
				tmpDay = one_at;
			}
			if ((one_at > one_days) && (two_at <= two_days)){
				tmpYear = this.GetFormatYear(one_at);
				tmpDay = two_at;
			}
			if ((one_at <= one_days) && (two_at <= two_days)){
				switch (find_tag){
					case "100": //default month,day,year
						tmpDay = one_at;
						tmpYear = this.GetFormatYear(two_at);
						if ((month_at > year_at) && (month_at > day_at)){
							if (day_at > year_at){
								tmpYear = this.GetFormatYear(one_at);
								tmpDay = two_at;
							}
						}
						break;
					case "010": //default day,month,year
						tmpDay = one_at;
						tmpYear = this.GetFormatYear(two_at);
						if (((month_at > year_at) && (month_at < day_at)) || ((month_at < year_at) && (month_at > day_at))){
							if (day_at > year_at){
								tmpYear = this.GetFormatYear(one_at);
								tmpDay = two_at;
							}
						}
						break;
					case "001": //default year,day,month
						tmpYear = this.GetFormatYear(one_at);
						tmpDay = two_at;	
						if ((month_at < year_at) && (month_at < day_at)){
							if (year_at > day_at){
								tmpDay = one_at;
								tmpYear = this.GetFormatYear(two_at);
							}
						}
						break;
					default: //default day,year
						tmpDay = one_at;
						tmpYear = this.GetFormatYear(two_at);
				}
			}
			return(new Date(tmpYear, tmpMonth, tmpDay));
		}
		find_tag = "000";
		for (i = 0; i < 3; i++){
			if (parseInt(aryDate[i], 10) > 31){
				if (find_tag!="000"){
					return(defDate);
				}
				tmpYear = this.GetFormatYear(aryDate[i]);
				switch (i){
					case 0:
						find_tag = "100";
						one_at = parseInt(aryDate[1], 10);
						two_at = parseInt(aryDate[2], 10);
						break;
					case 1:
						find_tag = "010";
						one_at = parseInt(aryDate[0], 10);
						two_at = parseInt(aryDate[2], 10);
						break;
					case 2:
						find_tag = "001";
						one_at = parseInt(aryDate[0], 10);
						two_at = parseInt(aryDate[1], 10);
						break;
					default:;
				}
			}
		}
		if (find_tag == "000"){
			if ((year_at > month_at) && (year_at > day_at)){
				find_tag = "001";
			}
			if ((year_at > month_at) && (year_at < day_at)){
				find_tag = "010";
			}
			if ((year_at < month_at) && (year_at > day_at)){
				find_tag = "010";
			}
			if ((year_at < month_at) && (year_at < day_at)){
				find_tag = "100";
			}
			switch (find_tag){
				case "100":
					tmpYear = parseInt(aryDate[0], 10);
					one_at = parseInt(aryDate[1], 10);
					two_at = parseInt(aryDate[2], 10);
					break;
				case "010":
					one_at = parseInt(aryDate[0], 10);
					tmpYear = parseInt(aryDate[1], 10);
					two_at = parseInt(aryDate[2], 10);
					break;
				case "001":
					one_at = parseInt(aryDate[0], 10);
					two_at = parseInt(aryDate[1], 10);
					tmpYear = parseInt(aryDate[2], 10);
					break;
				default:;
			}
			tmpYear = this.GetFormatYear(tmpYear);
		}
		if (find_tag == "000"){
			return(defDate);
		}else{
			if ((one_at > 12) && (two_at > 12)){
				return(defDate);
			}
			if ((one_at <= 12) && (two_at > 12)){
				if (two_at > this.GetMonthDays(tmpYear,one_at-1)){
					return(new Date(tmpYear, one_at-1, this.GetMonthDays(tmpYear,one_at-1)));
				}else{
					return(new Date(tmpYear, one_at-1, two_at));
				}
			}
			if ((one_at > 12) && (two_at <= 12)){
				if (one_at > this.GetMonthDays(tmpYear,two_at-1)){
					return(new Date(tmpYear, two_at-1, this.GetMonthDays(tmpYear,two_at-1)));
				}else{
					return(new Date(tmpYear, two_at-1, one_at));
				}
			}
			if ((one_at <= 12) && (two_at <= 12)){
				tmpMonth = one_at-1;
				tmpDay = two_at;
				if (month_at > day_at){
					tmpMonth = two_at-1;
					tmpDay = one_at;
				}
				return(new Date(tmpYear, tmpMonth, tmpDay));
			}
		}
	}
	this.CreateYearList = function(MinYear, MaxYear){ //create year list
		var theName = this.Name;
		var theYearObject = document.all.item(theName + "_YearList");
		if (theYearObject == null){
			return;
		}
		var theYear = 0;
		var theYearHTML = "<SELECT id=\"" + theName + "_YearList\" style=\"" + this.YearListStyle + "\" tabIndex=\"-1\"";
		theYearHTML += " onChange=\"document.jsMonthView.UpdateMonthGrid(this);\"";
		theYearHTML += " onBlur=\"document.jsMonthView.DeleteMonthGrid(false);\">";
		for (theYear = MaxYear; theYear >= MinYear; theYear--){
			theYearHTML += "<OPTION value=\"" + theYear.toString() + "\">" + theYear.toString() + "</OPTION>";
		}
		theYearHTML += "</SELECT>";
		theYearObject.outerHTML = theYearHTML;
	}
	this.CreateMonthList = function( ){ //create month list
		var theName = this.Name;
		var theMonthObject = document.all.item(theName + "_MonthList");
		if (theMonthObject == null){
			return;
		}
		var theMonth = 0;
		var theMonthHTML = "<SELECT id=\"" + theName + "_MonthList\" style=\"" + this.MonthListStyle + "\" tabIndex=\"-1\"";
		theMonthHTML += " onChange=\"document.jsMonthView.UpdateMonthGrid(this);\"";
		theMonthHTML += " onBlur=\"document.jsMonthView.DeleteMonthGrid(false);\">";
		for (theMonth = 0; theMonth < 12; theMonth++){
			theMonthHTML += "<OPTION value=\"" + theMonth.toString() + "\">" + this.MonthName[theMonth] + "</OPTION>";
		}
		theMonthHTML +="</SELECT>";
		theMonthObject.outerHTML = theMonthHTML;
	}
	this.OverDay = function(theDay){
		var i=0;
		for (i=0;i<this.DayOverStyleName.length;i++){
			eval("theDay.runtimeStyle." + this.DayOverStyleName[i] + " = \"" + this.DayOverStyleValue[i] + "\"");
		}
	}
	this.OutDay = function(theDay){
		var i=0;
		for (i=0;i<this.DayOverStyleName.length;i++){
			eval("theDay.runtimeStyle." + this.DayOverStyleName[i] + " = \"\"");
		}
	}
	this.SetDayList = function(theYear, theMonth, theDay, theTag){ //set the month view show a date
		var theName = this.Name;
		var theDayObject = document.all.item(theName + "_DayList");
		if (theDayObject == null){
			return;
		}
		theDayObject.value = theDay.toString();
		var theFirstDay = new Date(theYear, theMonth, 1);
		var theCurrentDate = new Date();
		var theWeek = theFirstDay.getDay();
		if (theWeek == 0){
			theWeek = 7;
		}
		var theLeftDay = 0;
		if (theMonth == 0){
			theLeftDay = 31;
		}else{
			theLeftDay = this.GetMonthDays(theYear, theMonth - 1);
		}
		var theRightDay = this.GetMonthDays(theYear, theMonth);
		var theCurrentDay = theLeftDay - theWeek + 1;
		var offsetMonth = -1; //the month is previous month
		var theColor = this.InvalidColor;
		var theBgColor = this.UnselectBgColor;
		var theBdColor = theBgColor;
		var WeekId = 0
		var DayId = 0;
		var theStyle = "";
		var theDayHTML = "<TABLE width=\"100%\" height=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">";
		theDayHTML += "     <TR style=\"" + this.TitleStyle + "\">";
		for (DayId = 0; DayId < 7; DayId++){
			theDayHTML += "     <TD width=\"10%\" style=\"" + this.WeekListStyle + "\">" + this.WeekName[DayId] + "</TD>";
		}
		theDayHTML += "     </TR>";
		theDayHTML += "     <TR>";
		theDayHTML += "       <TD colspan=\"7\" style=\"" + this.LineBgStyle + "\">";
		theDayHTML += "         <TABLE style=\"" + this.LineStyle + "\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">";
		theDayHTML += "           <TR><TD></TD></TR>";
		theDayHTML += "         </TABLE>";
		theDayHTML += "       </TD>";
		theDayHTML += "     </TR>";
		for (WeekId = 0; WeekId < 6; WeekId++){
			theDayHTML += "   <TR style=\"" + this.DayListStyle + "\">";
			for (DayId = 0; DayId < 7; DayId++){
				if ((theCurrentDay > theLeftDay) && (WeekId < 3)){
					offsetMonth++; //the month is current month;
					theCurrentDay = 1;
				}
				if ((theCurrentDay > theRightDay) && (WeekId > 3)){
					offsetMonth++; //the month is next month;
					theCurrentDay = 1;
				}
				switch (offsetMonth){
					case -1:
						theColor = this.InvalidColor;
						break;
					case 1:
						theColor = this.InvalidColor;
						break;
					case 0:
						theColor = this.ValidColor;
						break;
					default:;
				}
				theBgColor = this.UnselectBgColor;
				theBdColor = this.DayBdColor;
				if ((theCurrentDay == theDay) && (offsetMonth == 0) && (theTag == true)){
					theColor = this.SelectedColor;
					theBgColor = this.SelectedBgColor;
					theBdColor = theBgColor;
				}
				if ((theYear == theCurrentDate.getFullYear()) && (theMonth == theCurrentDate.getMonth()) && (theCurrentDay == theCurrentDate.getDate()) && (offsetMonth == 0)){
					theBdColor = this.TodayBdColor;
				}
				theStyle = "border:" + this.DayBdWidth + " solid " + theBdColor + "; color:" + theColor + "; background-color:" + theBgColor + ";";
				theDayHTML += "<TD style=\"" + theStyle + "\"";
				theDayHTML += " onMouseOver=\"document.jsMonthView.OverDay(this);\"";
				theDayHTML += " onMouseOut=\"document.jsMonthView.OutDay(this);\"";
				theDayHTML += " onMouseDown=\"document.jsMonthView.CreateMonthGrid(" + theYear.toString() + ", " + (theMonth + offsetMonth).toString() + ", " + theCurrentDay.toString() + ", true);\"";
				if (this.AutoHidden == true){
					theDayHTML += " onMouseUp=\"document.jsMonthView.DeleteMonthGrid(true);\"";
				}
				theDayHTML += ">" + theCurrentDay.toString()+ "</TD>";
				theCurrentDay++;
			}
			theDayHTML += "</TR>";
		}
		theDayHTML += "  <TR style=\"" + this.FooterStyle + "\" title=\"" + this.TodayListTitle + "\"";
		theDayHTML += " onMouseDown=\"document.jsMonthView.CreateMonthGrid(" + theCurrentDate.getFullYear().toString() + ", " + theCurrentDate.getMonth().toString() + ", " + theCurrentDate.getDate().toString() + ", true);\"";
		if (this.AutoHidden == true){
			theDayHTML += " onMouseUp=\"document.jsMonthView.DeleteMonthGrid(true);\"";
		}
		theDayHTML += ">";
		theStyle = "border:" + this.DayBdWidth + " solid " + this.TodayBdColor + "; " + this.TodayListStyle + ";";
		theDayHTML += "    <TD style=\"" + theStyle + "\">&nbsp;</TD>";
		theDayHTML += "    <TD colspan=\"6\" style=\"" + this.TodayListStyle + "\">&nbsp;" + this.TodayTitle + "&nbsp;" + this.SetDateFormat(theCurrentDate.getFullYear(), theCurrentDate.getMonth(), theCurrentDate.getDate()) + "</TD>";
		theDayHTML += "  </TR>";
		theDayHTML += "</TABLE>";
		var theMonthGrid = document.all.item(theName + "_MonthGrid");
		theMonthGrid.innerHTML = theDayHTML;
	}
	this.CreateMonthGrid = function(theYear, theMonth, theDay, theTag){ //refresh the month view to the date, main action is run this.setDayList() and set this.Source.value
		var theTextObject = this.Source;
		if (theTextObject == null){
			return;
		}
		var theName = this.Name;
		var theYearObject = document.all.item(theName + "_YearList");
		var theMonthObject = document.all.item(theName + "_MonthList");
		var tmpYear = theYear;
		var tmpMonth = theMonth;
		var tmpDay = 1;
		if (tmpMonth < 0){
			tmpYear--;
			tmpMonth = 11;
		}
		if (tmpMonth > 11){
			tmpYear++;
			tmpMonth = 0;
		}
		if (tmpYear < this.MinYear){
			tmpYear = this.MinYear;
		}
		if (tmpYear > this.MaxYear){
			tmpYear = this.MaxYear;
		}
		if (theDay < 1){
			tmpDay = 1;
		}else{
			tmpDay = this.GetMonthDays(tmpYear, tmpMonth);
			if (theDay < tmpDay){
				tmpDay = theDay;
			}
		}
		theYearObject.value = tmpYear;
		theMonthObject.value = tmpMonth;
		this.SetDayList(tmpYear, tmpMonth, tmpDay, theTag);
		if (theTag==true){
			theTextObject.value = this.SetDateFormat(tmpYear, tmpMonth, tmpDay);
			theTextObject.select();
		}
	}
	this.UpdateMonthGrid = function(theObject){ //run this.CreateMonthGrid() by theObject
		var theTextObject = this.Source;
		if (theTextObject == null){
			return;
		}
		var theName = this.Name;
		var theYearObject = document.all.item(theName + "_YearList");
		var theMonthObject = document.all.item(theName + "_MonthList");
		var theDayObject = document.all.item(theName + "_DayList");
		var tmpName = theObject.id.substr(theObject.id.lastIndexOf("_"));
		switch (tmpName){
			case "_goPreviousMonth": //go previous month button
				theObject.disabled = true;
				this.CreateMonthGrid(parseInt(theYearObject.value, 10), parseInt(theMonthObject.value, 10) - 1, parseInt(theDayObject.value, 10), true);
				theObject.disabled = false;
				break;
			case "_goNextMonth": //go next month button
				theObject.disabled = true;
				this.CreateMonthGrid(parseInt(theYearObject.value, 10), parseInt(theMonthObject.value, 10) + 1, parseInt(theDayObject.value, 10), true);
				theObject.disabled = false;
				break;
			case "_YearList": //year list
				this.CreateMonthGrid(parseInt(theYearObject.value, 10), parseInt(theMonthObject.value, 10), parseInt(theDayObject.value, 10), true);
				break;
			case "_MonthList": //month list
				this.CreateMonthGrid(parseInt(theYearObject.value, 10), parseInt(theMonthObject.value, 10), parseInt(theDayObject.value, 10), true);
				break;
			default:
				return;
		}
	}
	this.DeleteMonthGrid = function(theTag){ //check document focus, if blur this.Source then delete this
		var theName = this.Name;
		var theDivObject = document.all.item(theName + "_MonthView");
		if (theDivObject == null){
			return;
		}
		if (theTag == true){
			this.RevokeMonthGrid();
			return;
		}
		var tmpObject = document.activeElement;
		while (tmpObject != null){
			if (tmpObject == this.Source){
				return;
			}
			//if (tmpObject.id == theName + "_MonthView"){
			//	return;
			//}
			//if (tmpObject.id == theName + "_MonthGrid"){
			//	return;
			//}
			if (tmpObject.id == theName + "_goPreviousMonth"){
				return;
			}
			if (tmpObject.id == theName + "_goNextMonth"){
				return;
			}
			if (tmpObject.id == theName + "_YearList"){
				return;
			}
			if (tmpObject.id == theName + "_MonthList"){
				return;
			}	
			if (tmpObject.id == theName + "_DayList"){
				return;
			}
			tmpObject = tmpObject.parentElement;
		}
		if (tmpObject == null){ //delete the month view
			this.RevokeMonthGrid();
		}
	}
	this.RevokeMonthGrid = function( ){
		var theName = this.Name;
		var theDivObject = document.all.item(theName + "_MonthView");
		if (theDivObject == null){
			return;
		}
		//theDivObject.outerHTML = "";
		theDivObject.style.visibility = "hidden";
		if (this.Source != null){
			var theDate = new Date(this.GetTextDate(this.Source.value));
			if (isNaN(theDate)){
				var theTag = "";
				if (this.Source.value != ""){
					theTag += "1";
				}else{
					theTag += "0"
				}
				if (this.WarningAction != ""){
					theTag += "1";
				}else{
					theTag += "0";
				}
				switch (theTag){
					case "00":
						break;
					case "01":
						if (this.AllowNullDate == false){
							eval(this.WarningAction);
						}
						break;
					case "10":
						this.Source.value = "";
						break;
					case "11":
						this.Source.value = "";
						eval(this.WarningAction);
						break;
				}				
			}else{
				this.Source.value = this.SetDateFormat(theDate.getFullYear(), theDate.getMonth(), theDate.getDate());
			}
			this.Source = null;
		}
	}
	this.InitialMonthView = function( ){
		var theName = this.Name;
		var theTag = true;
		var theValue = this.Source.value;
		if (theValue.replace(/ /g,"")==""){
			theTag = false;
		}
		var theCurrentDate = new Date(this.GetTextDate(theValue));
		if (isNaN(theCurrentDate)){
			theCurrentDate = new Date();
			theTag = false;
		}
		var theIETag = this.CheckIE()
		var theDivHTML = "";
		var theDivObject = document.all.item(theName + "_MonthView");
		if ((theDivObject == null)||(this.AlwaysMakeHTML == true)){
			theDivHTML += "<DIV id=\"" + theName + "_MonthView\" onBlur=\"document.jsMonthView.DeleteMonthGrid(false);\">";
			theDivHTML += "  <TABLE width=\"" + this.Width.toString() + "\" height=\"" + this.Height.toString() + "\" style=\"" + this.MonthGridStyle + "\" cellpadding=\"0\" cellspacing=\"0\">";
			theDivHTML += "    <TR>";
			theDivHTML += "      <TD align=\"center\" valign=\"top\">";
			theDivHTML += "        <TABLE width=\"100%\" height=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">";
			theDivHTML += "          <TR align=\"center\" style=\"" + this.HeaderStyle + "\">";
			theDivHTML += "            <TD>";
			theDivHTML += "              <INPUT type=\"button\" tabIndex=\"-1\" style=\"" + this.MonthBtStyle + "\" id=\"" + theName + "_goPreviousMonth\" value=\"" + this.PreviousMonthText + "\" title=\"" + this.PreviousMonthTitle + "\"";
			theDivHTML += " onClick=\"document.jsMonthView.UpdateMonthGrid(this);\"";
			theDivHTML += " onBlur=\"document.jsMonthView.DeleteMonthGrid(false);\">";
			theDivHTML += "            </TD>";
			theDivHTML += "            <TD>";
			theDivHTML += "              <SELECT id=\"" + theName + "_MonthList\"></SELECT>";
			theDivHTML += "            </TD>";
			theDivHTML += "            <TD>";
			theDivHTML += "              <SELECT id=\"" + theName + "_YearList\"></SELECT>";
			theDivHTML += "              <INPUT type=\"hidden\" id=\"" + theName + "_DayList\" value=\"1\">";
			theDivHTML += "            </TD>";
			theDivHTML += "            <TD>";
			theDivHTML += "              <INPUT type=\"button\" tabIndex=\"-1\" style=\"" + this.MonthBtStyle + "\" id=\"" + theName + "_goNextMonth\" value=\"" + this.NextMonthText + "\" title=\"" + this.NextMonthTitle + "\"";
			theDivHTML += " onClick=\"document.jsMonthView.UpdateMonthGrid(this);\"";
			theDivHTML += " onBlur=\"document.jsMonthView.DeleteMonthGrid(false);\">";
			theDivHTML += "            </TD>";
			theDivHTML += "          </TR>";
			theDivHTML += "          <TR>";
			theDivHTML += "            <TD colspan=\"4\" bgcolor=\"" + this.UnselectBgColor + "\">";
			theDivHTML += "              <DIV id=\"" + theName + "_MonthGrid\">&nbsp;</DIV>";
			theDivHTML += "            </TD>";
			theDivHTML += "          </TR>";
			theDivHTML += "        </TABLE>";
			theDivHTML += "      </TD>";
			theDivHTML += "    </TR>";
			theDivHTML += "  </TABLE>";
			if (theIETag == true){
				theDivHTML += "  <IFRAME frameborder=\"no\" scrolling=\"no\" src=\"about:blank\" style=\"position:absolute; top:0px; left:0px; width:100%; height:100%; z-index:-1;\"></IFRAME>"; //keep out SELECT element
			}
			theDivHTML += "</DIV>";
			if (theDivObject != null){
				theDivObject.outerHTML = "";
			}
			document.body.insertAdjacentHTML("beforeEnd", theDivHTML);
			theDivObject = document.all.item(theName + "_MonthView");
			this.CreateYearList(this.MinYear, this.MaxYear);
			this.CreateMonthList();
		}
		theDivObject.style.position = "absolute";
		var tmpLeft = this.GetoffsetLeft(this.Source);		
		if (tmpLeft + this.Width > document.body.clientWidth){
			tmpLeft = tmpLeft + this.Source.offsetWidth - this.Width;
		}
		if (tmpLeft < 0){
			tmpLeft = 0;
		}
		theDivObject.style.posLeft = tmpLeft;
		theDivObject.style.posTop = this.GetoffsetTop(this.Source) + this.Source.offsetHeight;
		this.CreateMonthGrid(theCurrentDate.getFullYear(), theCurrentDate.getMonth(), theCurrentDate.getDate(), theTag);
		if (this.OnlyInput == false){
			theDivObject.style.visibility = "inherit";
		}else{
			theDivObject.style.display = "none";
		}
	}
	this.SchemeMonthView = function( ){//restore attribute to default
		return;
	}
}

function CreateMonthView(theTextObject, theScheme){ //the month view create interface, fire at element's onFocus event
  if ((theTextObject.readOnly == true)||(theTextObject.disabled == true)){
    return;
  }
  if (document.jsMonthView == null){
    document.jsMonthView = new DefineMonthView(theTextObject);
  }else{
    if (document.jsMonthView.Source == null){
      document.jsMonthView.Source = theTextObject;
	  document.jsMonthView.SchemeMonthView();
    }else{
	  return;
    }
  }
  //document.jsMonthView.DateFormat = "<yyyy>-<mm>-<dd>";
  if (theScheme != null){
    eval(theScheme);
  }
  document.jsMonthView.InitialMonthView();
  theTextObject.select();
}
function DeleteMonthView(theTextObject){ //the month view delete interface, fire at element's onBlur event
  if (document.jsMonthView == null){
    return;
  }  
  document.jsMonthView.DeleteMonthGrid(false);
  if ((document.jsMonthView.Source == null)&&(document.jsMonthView.AlwaysMakeHTML == true)){
    var theDivObject = document.all.item(document.jsMonthView.Name + "_MonthView");
	if (theDivObject != null){
	  theDivObject.outerHTML = "";
	}
    document.jsMonthView = null;
  }
}
