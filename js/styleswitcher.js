function setActiveStyleSheet(title) {
  var i, a, main;
  for(i=0; (a = document.getElementsByTagName("link")[i]); i++) {
    if(a.getAttribute("rel").indexOf("style") != -1 && a.getAttribute("title")) {
      a.disabled = true;
      if(a.getAttribute("title") == title) {
				a.disabled = false;
			}
    }
  }
}

function setStyleSheeter(title){
	var aX;
	//  setActiveStyleSheet(title);
	if (title.camelCase) {
		aX = document.body;
		aX.className = 'js '+title.camelCase().toLowerCase();
		aX.parentNode.id='ht'+title.camelCase().toLowerCase();
		Cookie.dispose('style');
		aX = new Cookie.write('style', title.camelCase().toLowerCase(),{'path':'/','domain':window.location.host,'duration':365});
	}
}

function getActiveStyleSheet() {
  return document.body.className.replace('js','').clean().toLowerCase();
}

function getPreferredStyleSheet() {
  var i, a;
  return document.body.className.replace('js','').clean().lowerCase();
  for(i=0; (a = document.getElementsByTagName("link")[i]); i++) {
    if(a.getAttribute("rel").indexOf("style") != -1
       && a.getAttribute("rel").indexOf("alt") == -1
       && a.getAttribute("title")
       ) return a.getAttribute("title");
  }
  return null;
}

function createCookie(name,value,days,v2) {
  if (days) {
    var date = new Date();
    date.setTime(date.getTime()+(days*24*60*60*1000));
    var expires = "; expires="+date.toGMTString();
  }
  else expires = "";
  document.cookie = name+"="+value+expires+"; path=/";
}

function readCookie(name) {
  var nameEQ = name + "=";
  var ca = document.cookie.split(';');
  for(var i=0;i < ca.length;i++) {
    var c = ca[i];
    while (c.charAt(0)==' ') c = c.substring(1,c.length);
    if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
  }
  return null;
}

window.onload = function(e) {
  var cookiee = Cookie.read("style");
  var title = cookiee ? cookiee : "gallery";
  setStyleSheeter(title);
}

window.onunload = function(e) {
	var a;
	Cookie.dispose('style');
	a = new Cookie.write('style', getActiveStyleSheet(),{'path':'/','domain':window.location.host,'duration':365});
}

//var cookiee = readCookie("style");
//var titleeX = cookiee ? cookiee : 'gallery';
