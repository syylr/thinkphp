// v3 site behaviors, Mike Foster, Cross-Browser.com

// Frame breaker

if (top.location != document.location) top.location = document.location;

// Page Object

function xPage(sRoot, bLeft, bRight, bVTb)
{
  this.siteRoot = sRoot;
  this.left = bLeft;
  this.right = bRight;
  this.onLoad = function()
  {
    if (!this.downgrade) {
      if (bLeft) this.xcl = new xCollapsible('leftColumn', 1);
      if (bRight) this.xcr = new xCollapsible('rightColumn', 1);
      if (bVTb) vtbInit();
      eqCol();
      var e = document.getElementById('footerTopLink');
      if (e) e.onclick = topBtnOnClick;
    }
  }
  this.onUnload = function()
  {
    if (this.xcl) this.xcl.onUnload();
    if (this.xcr) this.xcr.onUnload();
  }
  // Constructor
  this.downgrade = true;
  if (document.getElementById || document.all) { // need to enhance downgrade detection
    this.downgrade = false;
  }
}

function eqCol()
{
  var lc = xGetElementById('leftColumn');
  var lch = xHeight(lc);
  var rc = xGetElementById('rightColumn');
  rc = xFirstChild(rc); // 'rightContent'
  if (lch > xHeight(rc)) {
    xHeight(rc, lch);
  }
}

// Vertical Toolbar

function xNewEle(p, n, cls, h, clk, movr, mout)
{
  var e = document.createElement('DIV');
  if (e && p) {
    e.id = cls + n;
    e.className = cls;
    if (h) e.innerHTML = h;
    p.appendChild(e);
    if (clk) e.onclick = clk;
    if (movr) e.onmouseover = movr;
    if (mout) e.onmouseout = mout;
  }
  return e;
}

function vtbInit(bLeft, bRight)
{
  var rc = xGetElementById('rightColumn');
  // vertical toolbar (button container)
  var tb = xNewEle(document.body, 1, 'xToolbar');
  tb.floatOffset = xPageY('leftColumn'); //10;
  // topofpg button
  var b = xNewEle(tb, 1, 'xButton', '^', topBtnOnClick, btnOnMouseover, btnOnMouseout);
  b.setAttribute('title', 'Top of Page');
  // sidebar button
  b = xNewEle(tb, 2, 'xButton', null, clpsBtnOnClick, btnOnMouseover, btnOnMouseout);
  b.clpsColor = '#ffc';
  b.clpsEle = rc;
  b.collapsed = true;
  b.onclick();
  // popup menu button
  b = xNewEle(tb, 3, 'xButton', 'm', pumBtnOnClick, btnOnMouseover, btnOnMouseout);
  b.setAttribute('title', 'Menu');
  pumInit(); // create popup menu
  // initial position and slide
  vtbWinOnResize(1);
  vtbWinOnScroll();
  // vtb event listeners
  xAddEventListener(window, 'resize', vtbWinOnResize, false);
  xAddEventListener(window, 'scroll', vtbWinOnScroll, false);
}

function clpsBtnOnClick()
{
  clpsBtnDoClick(this, this.collapsed);
}
function clpsBtnDoClick(thisEle, bShow)
{
  var d, t, w, h;
  var lc = xGetElementById('leftColumn');
  var rc = xGetElementById('rightColumn');
  if (bShow) { // show
    d = 'block';
    t = 'Hide';
    w = '70';
    h = '&gt;';
  }
  else {                // hide
    d = 'none';
    t = 'Show';
    w = '94';
    h = '&lt;';
  }
  t += ' Side Panel';
  rc.style.display = d;
  lc.style.width = w + '%';
  thisEle.innerHTML = h;
  thisEle.setAttribute('title', t);
  thisEle.collapsed = !bShow;
}

function topBtnOnClick()
{
  if (window.scrollTo) window.scrollTo(0,0);
  else if (window.scroll) window.scroll(0,0);
  else location.href = '#topofpg'; // this causes opera to issue the window.onunload event :-(
  return false;
}

function btnOnMouseover()
{
  this.className = 'xButtonHover';
}

function btnOnMouseout()
{
  this.className = 'xButton';
}

function vtbWinOnResize(init)
{
  var tb = xGetElementById('xToolbar1');
  var lc = xGetElementById('leftColumn');
  xMoveTo(tb, xClientWidth() - xWidth(tb) - 2, xPageY(lc));
  if (init==1) xShow(tb);
  else vtbWinOnScroll();
  eqCol();
}

function vtbWinOnScroll()
{
  var tb = xGetElementById('xToolbar1');
  xSlideTo(tb, xPageX(tb), xScrollTop() + tb.floatOffset, 800);
}

function pumInit()
{
  var i;
  var aH = xGetElementsByTagName('H3', xGetElementById('leftColumn'));
  var pm = xNewEle(document.body, 1, 'xPopupMenu');
  xHide(pm);
  xWidth(pm, 150);
  xZIndex(pm, 100);
  
  var s = "<div class='mnuBox'>";
  s += "<h4>Page Menu</h4>";
//  s += "<p><a href='#topofpg'>Top</a></p>"
  for (i = 0; i < aH.length; ++i) {
    aH[i].id = 'lnk' + i;
    s += "<p><a href='#"+'lnk'+i+"'>" + aH[i].innerHTML + "</a></p>"; 
  }
  s += '</div>';



  if (pg.left || pg.right) {
    s += "<div class='mnuOpt'>";
    s += '<h4>Options</h4>';
    s += "<p><a href=\"javascript:" + (pg.left?"pg.xcl.displayAll(false);":"") + (pg.right?"pg.xcr.displayAll(false)":"") + "\">Hide</a> / ";
    s += "<a href=\"javascript:" + (pg.left?"pg.xcl.displayAll(true)":"") + (pg.right?";pg.xcr.displayAll(true)":"") + "\">Show</a> All Sections</p>";
    s += '</div>';
  }
  
  pm.innerHTML = s;
}

function pumBtnOnClick()
{
  var pm = xGetElementById('xPopupMenu1');
  xMoveTo(pm, xClientWidth() - xWidth(pm) - 6, xPageY(this));
  xShow(pm);
  xAddEventListener(document, 'mousemove', pumDocOnMousemove, false);
}
function pumDocOnMousemove(ev)
{
  var e = new xEvent(ev);
  var pm = xGetElementById('xPopupMenu1');
  if (!xHasPoint(pm, e.pageX, e.pageY, -10)) {
    xHide(pm);
    xRemoveEventListener(document, 'mousemove', pumDocOnMousemove, false);
  }
}




