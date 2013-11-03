//MooTools, <http://mootools.net>, My Object Oriented (JavaScript) Tools. Copyright (c) 2006-2009 Valerio Proietti, <http://mad4milk.net>, MIT Style License.

var MooTools={version:"1.2.2",build:"f0491d62fbb7e906789aa3733d6a67d43e5af7c9"};var Native=function(k){k=k||{};var a=k.name;var i=k.legacy;var b=k.protect;
var c=k.implement;var h=k.generics;var f=k.initialize;var g=k.afterImplement||function(){};var d=f||i;h=h!==false;d.constructor=Native;d.$family={name:"native"};
if(i&&f){d.prototype=i.prototype;}d.prototype.constructor=d;if(a){var e=a.toLowerCase();d.prototype.$family={name:e};Native.typize(d,e);}var j=function(n,l,o,m){if(!b||m||!n.prototype[l]){n.prototype[l]=o;
}if(h){Native.genericize(n,l,b);}g.call(n,l,o);return n;};d.alias=function(n,l,o){if(typeof n=="string"){if((n=this.prototype[n])){return j(this,l,n,o);
}}for(var m in n){this.alias(m,n[m],l);}return this;};d.implement=function(m,l,o){if(typeof m=="string"){return j(this,m,l,o);}for(var n in m){j(this,n,m[n],l);
}return this;};if(c){d.implement(c);}return d;};Native.genericize=function(b,c,a){if((!a||!b[c])&&typeof b.prototype[c]=="function"){b[c]=function(){var d=Array.prototype.slice.call(arguments);
return b.prototype[c].apply(d.shift(),d);};}};Native.implement=function(d,c){for(var b=0,a=d.length;b<a;b++){d[b].implement(c);}};Native.typize=function(a,b){if(!a.type){a.type=function(c){return($type(c)===b);
};}};(function(){var a={Array:Array,Date:Date,Function:Function,Number:Number,RegExp:RegExp,String:String};for(var h in a){new Native({name:h,initialize:a[h],protect:true});
}var d={"boolean":Boolean,"native":Native,object:Object};for(var c in d){Native.typize(d[c],c);}var f={Array:["concat","indexOf","join","lastIndexOf","pop","push","reverse","shift","slice","sort","splice","toString","unshift","valueOf"],String:["charAt","charCodeAt","concat","indexOf","lastIndexOf","match","replace","search","slice","split","substr","substring","toLowerCase","toUpperCase","valueOf"]};
for(var e in f){for(var b=f[e].length;b--;){Native.genericize(window[e],f[e][b],true);}}})();var Hash=new Native({name:"Hash",initialize:function(a){if($type(a)=="hash"){a=$unlink(a.getClean());
}for(var b in a){this[b]=a[b];}return this;}});Hash.implement({forEach:function(b,c){for(var a in this){if(this.hasOwnProperty(a)){b.call(c,this[a],a,this);
}}},getClean:function(){var b={};for(var a in this){if(this.hasOwnProperty(a)){b[a]=this[a];}}return b;},getLength:function(){var b=0;for(var a in this){if(this.hasOwnProperty(a)){b++;
}}return b;}});Hash.alias("forEach","each");Array.implement({forEach:function(c,d){for(var b=0,a=this.length;b<a;b++){c.call(d,this[b],b,this);}}});Array.alias("forEach","each");
function $A(b){if(b.item){var a=b.length,c=new Array(a);while(a--){c[a]=b[a];}return c;}return Array.prototype.slice.call(b);}function $arguments(a){return function(){return arguments[a];
};}function $chk(a){return !!(a||a===0);}function $clear(a){clearTimeout(a);clearInterval(a);return null;}function $defined(a){return(a!=undefined);}function $each(c,b,d){var a=$type(c);
((a=="arguments"||a=="collection"||a=="array")?Array:Hash).each(c,b,d);}function $empty(){}function $extend(c,a){for(var b in (a||{})){c[b]=a[b];}return c;
}function $H(a){return new Hash(a);}function $lambda(a){return(typeof a=="function")?a:function(){return a;};}function $merge(){var a=Array.slice(arguments);
a.unshift({});return $mixin.apply(null,a);}function $mixin(e){for(var d=1,a=arguments.length;d<a;d++){var b=arguments[d];if($type(b)!="object"){continue;
}for(var c in b){var g=b[c],f=e[c];e[c]=(f&&$type(g)=="object"&&$type(f)=="object")?$mixin(f,g):$unlink(g);}}return e;}function $pick(){for(var b=0,a=arguments.length;
b<a;b++){if(arguments[b]!=undefined){return arguments[b];}}return null;}function $random(b,a){return Math.floor(Math.random()*(a-b+1)+b);}function $splat(b){var a=$type(b);
return(a)?((a!="array"&&a!="arguments")?[b]:b):[];}var $time=Date.now||function(){return +new Date;};function $try(){for(var b=0,a=arguments.length;b<a;
b++){try{return arguments[b]();}catch(c){}}return null;}function $type(a){if(a==undefined){return false;}if(a.$family){return(a.$family.name=="number"&&!isFinite(a))?false:a.$family.name;
}if(a.nodeName){switch(a.nodeType){case 1:return"element";case 3:return(/\S/).test(a.nodeValue)?"textnode":"whitespace";}}else{if(typeof a.length=="number"){if(a.callee){return"arguments";
}else{if(a.item){return"collection";}}}}return typeof a;}function $unlink(c){var b;switch($type(c)){case"object":b={};for(var e in c){b[e]=$unlink(c[e]);
}break;case"hash":b=new Hash(c);break;case"array":b=[];for(var d=0,a=c.length;d<a;d++){b[d]=$unlink(c[d]);}break;default:return c;}return b;}var Browser=$merge({Engine:{name:"unknown",version:0},Platform:{name:(window.orientation!=undefined)?"ipod":(navigator.platform.match(/mac|win|linux/i)||["other"])[0].toLowerCase()},Features:{xpath:!!(document.evaluate),air:!!(window.runtime),query:!!(document.querySelector)},Plugins:{},Engines:{presto:function(){return(!window.opera)?false:((arguments.callee.caller)?960:((document.getElementsByClassName)?950:925));
},trident:function(){return(!window.ActiveXObject)?false:((window.XMLHttpRequest)?5:4);},webkit:function(){return(navigator.taintEnabled)?false:((Browser.Features.xpath)?((Browser.Features.query)?525:420):419);
},gecko:function(){return(document.getBoxObjectFor==undefined)?false:((document.getElementsByClassName)?19:18);}}},Browser||{});Browser.Platform[Browser.Platform.name]=true;
Browser.detect=function(){for(var b in this.Engines){var a=this.Engines[b]();if(a){this.Engine={name:b,version:a};this.Engine[b]=this.Engine[b+a]=true;
break;}}return{name:b,version:a};};Browser.detect();Browser.Request=function(){return $try(function(){return new XMLHttpRequest();},function(){return new ActiveXObject("MSXML2.XMLHTTP");
});};Browser.Features.xhr=!!(Browser.Request());Browser.Plugins.Flash=(function(){var a=($try(function(){return navigator.plugins["Shockwave Flash"].description;
},function(){return new ActiveXObject("ShockwaveFlash.ShockwaveFlash").GetVariable("$version");})||"0 r0").match(/\d+/g);return{version:parseInt(a[0]||0+"."+a[1],10)||0,build:parseInt(a[2],10)||0};
})();function $exec(b){if(!b){return b;}if(window.execScript){window.execScript(b);}else{var a=document.createElement("script");a.setAttribute("type","text/javascript");
a[(Browser.Engine.webkit&&Browser.Engine.version<420)?"innerText":"text"]=b;document.head.appendChild(a);document.head.removeChild(a);}return b;}Native.UID=1;
var $uid=(Browser.Engine.trident)?function(a){return(a.uid||(a.uid=[Native.UID++]))[0];}:function(a){return a.uid||(a.uid=Native.UID++);};var Window=new Native({name:"Window",legacy:(Browser.Engine.trident)?null:window.Window,initialize:function(a){$uid(a);
if(!a.Element){a.Element=$empty;if(Browser.Engine.webkit){a.document.createElement("iframe");}a.Element.prototype=(Browser.Engine.webkit)?window["[[DOMElement.prototype]]"]:{};
}a.document.window=a;return $extend(a,Window.Prototype);},afterImplement:function(b,a){window[b]=Window.Prototype[b]=a;}});Window.Prototype={$family:{name:"window"}};
new Window(window);var Document=new Native({name:"Document",legacy:(Browser.Engine.trident)?null:window.Document,initialize:function(a){$uid(a);a.head=a.getElementsByTagName("head")[0];
a.html=a.getElementsByTagName("html")[0];if(Browser.Engine.trident&&Browser.Engine.version<=4){$try(function(){a.execCommand("BackgroundImageCache",false,true);
});}if(Browser.Engine.trident){a.window.attachEvent("onunload",function(){a.window.detachEvent("onunload",arguments.callee);a.head=a.html=a.window=null;
});}return $extend(a,Document.Prototype);},afterImplement:function(b,a){document[b]=Document.Prototype[b]=a;}});Document.Prototype={$family:{name:"document"}};
new Document(document);Array.implement({every:function(c,d){for(var b=0,a=this.length;b<a;b++){if(!c.call(d,this[b],b,this)){return false;}}return true;
},filter:function(d,e){var c=[];for(var b=0,a=this.length;b<a;b++){if(d.call(e,this[b],b,this)){c.push(this[b]);}}return c;},clean:function(){return this.filter($defined);
},indexOf:function(c,d){var a=this.length;for(var b=(d<0)?Math.max(0,a+d):d||0;b<a;b++){if(this[b]===c){return b;}}return -1;},map:function(d,e){var c=[];
for(var b=0,a=this.length;b<a;b++){c[b]=d.call(e,this[b],b,this);}return c;},some:function(c,d){for(var b=0,a=this.length;b<a;b++){if(c.call(d,this[b],b,this)){return true;
}}return false;},associate:function(c){var d={},b=Math.min(this.length,c.length);for(var a=0;a<b;a++){d[c[a]]=this[a];}return d;},link:function(c){var a={};
for(var e=0,b=this.length;e<b;e++){for(var d in c){if(c[d](this[e])){a[d]=this[e];delete c[d];break;}}}return a;},contains:function(a,b){return this.indexOf(a,b)!=-1;
},extend:function(c){for(var b=0,a=c.length;b<a;b++){this.push(c[b]);}return this;},getLast:function(){return(this.length)?this[this.length-1]:null;},getRandom:function(){return(this.length)?this[$random(0,this.length-1)]:null;
},include:function(a){if(!this.contains(a)){this.push(a);}return this;},combine:function(c){for(var b=0,a=c.length;b<a;b++){this.include(c[b]);}return this;
},erase:function(b){for(var a=this.length;a--;a){if(this[a]===b){this.splice(a,1);}}return this;},empty:function(){this.length=0;return this;},flatten:function(){var d=[];
for(var b=0,a=this.length;b<a;b++){var c=$type(this[b]);if(!c){continue;}d=d.concat((c=="array"||c=="collection"||c=="arguments")?Array.flatten(this[b]):this[b]);
}return d;},hexToRgb:function(b){if(this.length!=3){return null;}var a=this.map(function(c){if(c.length==1){c+=c;}return c.toInt(16);});return(b)?a:"rgb("+a+")";
},rgbToHex:function(d){if(this.length<3){return null;}if(this.length==4&&this[3]==0&&!d){return"transparent";}var b=[];for(var a=0;a<3;a++){var c=(this[a]-0).toString(16);
b.push((c.length==1)?"0"+c:c);}return(d)?b:"#"+b.join("");}});Function.implement({extend:function(a){for(var b in a){this[b]=a[b];}return this;},create:function(b){var a=this;
b=b||{};return function(d){var c=b.arguments;c=(c!=undefined)?$splat(c):Array.slice(arguments,(b.event)?1:0);if(b.event){c=[d||window.event].extend(c);
}var e=function(){return a.apply(b.bind||null,c);};if(b.delay){return setTimeout(e,b.delay);}if(b.periodical){return setInterval(e,b.periodical);}if(b.attempt){return $try(e);
}return e();};},run:function(a,b){return this.apply(b,$splat(a));},pass:function(a,b){return this.create({bind:b,arguments:a});},bind:function(b,a){return this.create({bind:b,arguments:a});
},bindWithEvent:function(b,a){return this.create({bind:b,arguments:a,event:true});},attempt:function(a,b){return this.create({bind:b,arguments:a,attempt:true})();
},delay:function(b,c,a){return this.create({bind:c,arguments:a,delay:b})();},periodical:function(c,b,a){return this.create({bind:b,arguments:a,periodical:c})();
}});Number.implement({limit:function(b,a){return Math.min(a,Math.max(b,this));},round:function(a){a=Math.pow(10,a||0);return Math.round(this*a)/a;},times:function(b,c){for(var a=0;
a<this;a++){b.call(c,a,this);}},toFloat:function(){return parseFloat(this);},toInt:function(a){return parseInt(this,a||10);}});Number.alias("times","each");
(function(b){var a={};b.each(function(c){if(!Number[c]){a[c]=function(){return Math[c].apply(null,[this].concat($A(arguments)));};}});Number.implement(a);
})(["abs","acos","asin","atan","atan2","ceil","cos","exp","floor","log","max","min","pow","sin","sqrt","tan"]);String.implement({test:function(a,b){return((typeof a=="string")?new RegExp(a,b):a).test(this);
},contains:function(a,b){return(b)?(b+this+b).indexOf(b+a+b)>-1:this.indexOf(a)>-1;},trim:function(){return this.replace(/^\s+|\s+$/g,"");},clean:function(){return this.replace(/\s+/g," ").trim();
},camelCase:function(){return this.replace(/-\D/g,function(a){return a.charAt(1).toUpperCase();});},hyphenate:function(){return this.replace(/[A-Z]/g,function(a){return("-"+a.charAt(0).toLowerCase());
});},capitalize:function(){return this.replace(/\b[a-z]/g,function(a){return a.toUpperCase();});},escapeRegExp:function(){return this.replace(/([-.*+?^${}()|[\]\/\\])/g,"\\$1");
},toInt:function(a){return parseInt(this,a||10);},toFloat:function(){return parseFloat(this);},hexToRgb:function(b){var a=this.match(/^#?(\w{1,2})(\w{1,2})(\w{1,2})$/);
return(a)?a.slice(1).hexToRgb(b):null;},rgbToHex:function(b){var a=this.match(/\d{1,3}/g);return(a)?a.rgbToHex(b):null;},stripScripts:function(b){var a="";
var c=this.replace(/<script[^>]*>([\s\S]*?)<\/script>/gi,function(){a+=arguments[1]+"\n";return"";});if(b===true){$exec(a);}else{if($type(b)=="function"){b(a,c);
}}return c;},substitute:function(a,b){return this.replace(b||(/\\?\{([^{}]+)\}/g),function(d,c){if(d.charAt(0)=="\\"){return d.slice(1);}return(a[c]!=undefined)?a[c]:"";
});}});Hash.implement({has:Object.prototype.hasOwnProperty,keyOf:function(b){for(var a in this){if(this.hasOwnProperty(a)&&this[a]===b){return a;}}return null;
},hasValue:function(a){return(Hash.keyOf(this,a)!==null);},extend:function(a){Hash.each(a,function(c,b){Hash.set(this,b,c);},this);return this;},combine:function(a){Hash.each(a,function(c,b){Hash.include(this,b,c);
},this);return this;},erase:function(a){if(this.hasOwnProperty(a)){delete this[a];}return this;},get:function(a){return(this.hasOwnProperty(a))?this[a]:null;
},set:function(a,b){if(!this[a]||this.hasOwnProperty(a)){this[a]=b;}return this;},empty:function(){Hash.each(this,function(b,a){delete this[a];},this);
return this;},include:function(a,b){if(this[a]==undefined){this[a]=b;}return this;},map:function(b,c){var a=new Hash;Hash.each(this,function(e,d){a.set(d,b.call(c,e,d,this));
},this);return a;},filter:function(b,c){var a=new Hash;Hash.each(this,function(e,d){if(b.call(c,e,d,this)){a.set(d,e);}},this);return a;},every:function(b,c){for(var a in this){if(this.hasOwnProperty(a)&&!b.call(c,this[a],a)){return false;
}}return true;},some:function(b,c){for(var a in this){if(this.hasOwnProperty(a)&&b.call(c,this[a],a)){return true;}}return false;},getKeys:function(){var a=[];
Hash.each(this,function(c,b){a.push(b);});return a;},getValues:function(){var a=[];Hash.each(this,function(b){a.push(b);});return a;},toQueryString:function(a){var b=[];
Hash.each(this,function(f,e){if(a){e=a+"["+e+"]";}var d;switch($type(f)){case"object":d=Hash.toQueryString(f,e);break;case"array":var c={};f.each(function(h,g){c[g]=h;
});d=Hash.toQueryString(c,e);break;default:d=e+"="+encodeURIComponent(f);}if(f!=undefined){b.push(d);}});return b.join("&");}});Hash.alias({keyOf:"indexOf",hasValue:"contains"});
var Event=new Native({name:"Event",initialize:function(a,f){f=f||window;var k=f.document;a=a||f.event;if(a.$extended){return a;}this.$extended=true;var j=a.type;
var g=a.target||a.srcElement;while(g&&g.nodeType==3){g=g.parentNode;}if(j.test(/key/)){var b=a.which||a.keyCode;var m=Event.Keys.keyOf(b);if(j=="keydown"){var d=b-111;
if(d>0&&d<13){m="f"+d;}}m=m||String.fromCharCode(b).toLowerCase();}else{if(j.match(/(click|mouse|menu)/i)){k=(!k.compatMode||k.compatMode=="CSS1Compat")?k.html:k.body;
var i={x:a.pageX||a.clientX+k.scrollLeft,y:a.pageY||a.clientY+k.scrollTop};var c={x:(a.pageX)?a.pageX-f.pageXOffset:a.clientX,y:(a.pageY)?a.pageY-f.pageYOffset:a.clientY};
if(j.match(/DOMMouseScroll|mousewheel/)){var h=(a.wheelDelta)?a.wheelDelta/120:-(a.detail||0)/3;}var e=(a.which==3)||(a.button==2);var l=null;if(j.match(/over|out/)){switch(j){case"mouseover":l=a.relatedTarget||a.fromElement;
break;case"mouseout":l=a.relatedTarget||a.toElement;}if(!(function(){while(l&&l.nodeType==3){l=l.parentNode;}return true;}).create({attempt:Browser.Engine.gecko})()){l=false;
}}}}return $extend(this,{event:a,type:j,page:i,client:c,rightClick:e,wheel:h,relatedTarget:l,target:g,code:b,key:m,shift:a.shiftKey,control:a.ctrlKey,alt:a.altKey,meta:a.metaKey});
}});Event.Keys=new Hash({enter:13,up:38,down:40,left:37,right:39,esc:27,space:32,backspace:8,tab:9,"delete":46});Event.implement({stop:function(){return this.stopPropagation().preventDefault();
},stopPropagation:function(){if(this.event.stopPropagation){this.event.stopPropagation();}else{this.event.cancelBubble=true;}return this;},preventDefault:function(){if(this.event.preventDefault){this.event.preventDefault();
}else{this.event.returnValue=false;}return this;}});function Class(b){if(b instanceof Function){b={initialize:b};}var a=function(){Object.reset(this);if(a._prototyping){return this;
}this._current=$empty;var c=(this.initialize)?this.initialize.apply(this,arguments):this;delete this._current;delete this.caller;return c;}.extend(this);
a.implement(b);a.constructor=Class;a.prototype.constructor=a;return a;}Function.prototype.protect=function(){this._protected=true;return this;};Object.reset=function(a,c){if(c==null){for(var e in a){Object.reset(a,e);
}return a;}delete a[c];switch($type(a[c])){case"object":var d=function(){};d.prototype=a[c];var b=new d;a[c]=Object.reset(b);break;case"array":a[c]=$unlink(a[c]);
break;}return a;};new Native({name:"Class",initialize:Class}).extend({instantiate:function(b){b._prototyping=true;var a=new b;delete b._prototyping;return a;
},wrap:function(a,b,c){if(c._origin){c=c._origin;}return function(){if(c._protected&&this._current==null){throw new Error('The method "'+b+'" cannot be called.');
}var e=this.caller,f=this._current;this.caller=f;this._current=arguments.callee;var d=c.apply(this,arguments);this._current=f;this.caller=e;return d;}.extend({_owner:a,_origin:c,_name:b});
}});Class.implement({implement:function(a,d){if($type(a)=="object"){for(var e in a){this.implement(e,a[e]);}return this;}var f=Class.Mutators[a];if(f){d=f.call(this,d);
if(d==null){return this;}}var c=this.prototype;switch($type(d)){case"function":if(d._hidden){return this;}c[a]=Class.wrap(this,a,d);break;case"object":var b=c[a];
if($type(b)=="object"){$mixin(b,d);}else{c[a]=$unlink(d);}break;case"array":c[a]=$unlink(d);break;default:c[a]=d;}return this;}});Class.Mutators={Extends:function(a){this.parent=a;
this.prototype=Class.instantiate(a);this.implement("parent",function(){var b=this.caller._name,c=this.caller._owner.parent.prototype[b];if(!c){throw new Error('The method "'+b+'" has no parent.');
}return c.apply(this,arguments);}.protect());},Implements:function(a){$splat(a).each(function(b){if(b instanceof Function){b=Class.instantiate(b);}this.implement(b);
},this);}};var Chain=new Class({$chain:[],chain:function(){this.$chain.extend(Array.flatten(arguments));return this;},callChain:function(){return(this.$chain.length)?this.$chain.shift().apply(this,arguments):false;
},clearChain:function(){this.$chain.empty();return this;}});var Events=new Class({$events:{},addEvent:function(c,b,a){c=Events.removeOn(c);if(b!=$empty){this.$events[c]=this.$events[c]||[];
this.$events[c].include(b);if(a){b.internal=true;}}return this;},addEvents:function(a){for(var b in a){this.addEvent(b,a[b]);}return this;},fireEvent:function(c,b,a){c=Events.removeOn(c);
if(!this.$events||!this.$events[c]){return this;}this.$events[c].each(function(d){d.create({bind:this,delay:a,"arguments":b})();},this);return this;},removeEvent:function(b,a){b=Events.removeOn(b);
if(!this.$events[b]){return this;}if(!a.internal){this.$events[b].erase(a);}return this;},removeEvents:function(c){var d;if($type(c)=="object"){for(d in c){this.removeEvent(d,c[d]);
}return this;}if(c){c=Events.removeOn(c);}for(d in this.$events){if(c&&c!=d){continue;}var b=this.$events[d];for(var a=b.length;a--;a){this.removeEvent(d,b[a]);
}}return this;}});Events.removeOn=function(a){return a.replace(/^on([A-Z])/,function(b,c){return c.toLowerCase();});};var Options=new Class({setOptions:function(){this.options=$merge.run([this.options].extend(arguments));
if(!this.addEvent){return this;}for(var a in this.options){if($type(this.options[a])!="function"||!(/^on[A-Z]/).test(a)){continue;}this.addEvent(a,this.options[a]);
delete this.options[a];}return this;}});var Element=new Native({name:"Element",legacy:window.Element,initialize:function(a,b){var c=Element.Constructors.get(a);
if(c){return c(b);}if(typeof a=="string"){return document.newElement(a,b);}return $(a).set(b);},afterImplement:function(a,b){Element.Prototype[a]=b;if(Array[a]){return;
}Elements.implement(a,function(){var c=[],g=true;for(var e=0,d=this.length;e<d;e++){var f=this[e][a].apply(this[e],arguments);c.push(f);if(g){g=($type(f)=="element");
}}return(g)?new Elements(c):c;});}});Element.Prototype={$family:{name:"element"}};Element.Constructors=new Hash;var IFrame=new Native({name:"IFrame",generics:false,initialize:function(){var e=Array.link(arguments,{properties:Object.type,iframe:$defined});
var c=e.properties||{};var b=$(e.iframe)||false;var d=c.onload||$empty;delete c.onload;c.id=c.name=$pick(c.id,c.name,b.id,b.name,"IFrame_"+$time());b=new Element(b||"iframe",c);
var a=function(){var f=$try(function(){return b.contentWindow.location.host;});if(f&&f==window.location.host){var g=new Window(b.contentWindow);new Document(b.contentWindow.document);
$extend(g.Element.prototype,Element.Prototype);}d.call(b.contentWindow,b.contentWindow.document);};(window.frames[c.id])?a():b.addListener("load",a);return b;
}});var Elements=new Native({initialize:function(f,b){b=$extend({ddup:true,cash:true},b);f=f||[];if(b.ddup||b.cash){var g={},e=[];for(var c=0,a=f.length;
c<a;c++){var d=$.element(f[c],!b.cash);if(b.ddup){if(g[d.uid]){continue;}g[d.uid]=true;}e.push(d);}f=e;}return(b.cash)?$extend(f,this):f;}});Elements.implement({filter:function(a,b){if(!a){return this;
}return new Elements(Array.filter(this,(typeof a=="string")?function(c){return c.match(a);}:a,b));}});Document.implement({newElement:function(a,b){if(Browser.Engine.trident&&b){["name","type","checked"].each(function(c){if(!b[c]){return;
}a+=" "+c+'="'+b[c]+'"';if(c!="checked"){delete b[c];}});a="<"+a+">";}return $.element(this.createElement(a)).set(b);},newTextNode:function(a){return this.createTextNode(a);
},getDocument:function(){return this;},getWindow:function(){return this.window;}});Window.implement({$:function(b,c){if(b&&b.$family&&b.uid){return b;}var a=$type(b);
return($[a])?$[a](b,c,this.document):null;},$$:function(a){if(arguments.length==1&&typeof a=="string"){return this.document.getElements(a);}var f=[];var c=Array.flatten(arguments);
for(var d=0,b=c.length;d<b;d++){var e=c[d];switch($type(e)){case"element":f.push(e);break;case"string":f.extend(this.document.getElements(e,true));}}return new Elements(f);
},getDocument:function(){return this.document;},getWindow:function(){return this;}});$.string=function(c,b,a){c=a.getElementById(c);return(c)?$.element(c,b):null;
};$.element=function(a,d){$uid(a);if(!d&&!a.$family&&!(/^object|embed$/i).test(a.tagName)){var b=Element.Prototype;for(var c in b){a[c]=b[c];}}return a;
};$.object=function(b,c,a){if(b.toElement){return $.element(b.toElement(a),c);}return null;};$.textnode=$.whitespace=$.window=$.document=$arguments(0);
Native.implement([Element,Document],{getElement:function(a,b){return $(this.getElements(a,true)[0]||null,b);},getElements:function(a,d){a=a.split(",");
var c=[];var b=(a.length>1);a.each(function(e){var f=this.getElementsByTagName(e.trim());(b)?c.extend(f):c=f;},this);return new Elements(c,{ddup:b,cash:!d});
}});(function(){var h={},f={};var i={input:"checked",option:"selected",textarea:(Browser.Engine.webkit&&Browser.Engine.version<420)?"innerHTML":"value"};
var c=function(l){return(f[l]||(f[l]={}));};var g=function(n,l){if(!n){return;}var m=n.uid;if(Browser.Engine.trident){if(n.clearAttributes){var q=l&&n.cloneNode(false);
n.clearAttributes();if(q){n.mergeAttributes(q);}}else{if(n.removeEvents){n.removeEvents();}}if((/object/i).test(n.tagName)){for(var o in n){if(typeof n[o]=="function"){n[o]=$empty;
}}Element.dispose(n);}}if(!m){return;}h[m]=f[m]=null;};var d=function(){Hash.each(h,g);if(Browser.Engine.trident){$A(document.getElementsByTagName("object")).each(g);
}if(window.CollectGarbage){CollectGarbage();}h=f=null;};var j=function(n,l,s,m,p,r){var o=n[s||l];var q=[];while(o){if(o.nodeType==1&&(!m||Element.match(o,m))){if(!p){return $(o,r);
}q.push(o);}o=o[l];}return(p)?new Elements(q,{ddup:false,cash:!r}):null;};var e={html:"innerHTML","class":"className","for":"htmlFor",text:(Browser.Engine.trident||(Browser.Engine.webkit&&Browser.Engine.version<420))?"innerText":"textContent"};
var b=["compact","nowrap","ismap","declare","noshade","checked","disabled","readonly","multiple","selected","noresize","defer"];var k=["value","accessKey","cellPadding","cellSpacing","colSpan","frameBorder","maxLength","readOnly","rowSpan","tabIndex","useMap"];
b=b.associate(b);Hash.extend(e,b);Hash.extend(e,k.associate(k.map(String.toLowerCase)));var a={before:function(m,l){if(l.parentNode){l.parentNode.insertBefore(m,l);
}},after:function(m,l){if(!l.parentNode){return;}var n=l.nextSibling;(n)?l.parentNode.insertBefore(m,n):l.parentNode.appendChild(m);},bottom:function(m,l){l.appendChild(m);
},top:function(m,l){var n=l.firstChild;(n)?l.insertBefore(m,n):l.appendChild(m);}};a.inside=a.bottom;Hash.each(a,function(l,m){m=m.capitalize();Element.implement("inject"+m,function(n){l(this,$(n,true));
return this;});Element.implement("grab"+m,function(n){l($(n,true),this);return this;});});Element.implement({set:function(o,m){switch($type(o)){case"object":for(var n in o){this.set(n,o[n]);
}break;case"string":var l=Element.Properties.get(o);(l&&l.set)?l.set.apply(this,Array.slice(arguments,1)):this.setProperty(o,m);}return this;},get:function(m){var l=Element.Properties.get(m);
return(l&&l.get)?l.get.apply(this,Array.slice(arguments,1)):this.getProperty(m);},erase:function(m){var l=Element.Properties.get(m);(l&&l.erase)?l.erase.apply(this):this.removeProperty(m);
return this;},setProperty:function(m,n){var l=e[m];if(n==undefined){return this.removeProperty(m);}if(l&&b[m]){n=!!n;}(l)?this[l]=n:this.setAttribute(m,""+n);
return this;},setProperties:function(l){for(var m in l){this.setProperty(m,l[m]);}return this;},getProperty:function(m){var l=e[m];var n=(l)?this[l]:this.getAttribute(m,2);
return(b[m])?!!n:(l)?n:n||null;},getProperties:function(){var l=$A(arguments);return l.map(this.getProperty,this).associate(l);},removeProperty:function(m){var l=e[m];
(l)?this[l]=(l&&b[m])?false:"":this.removeAttribute(m);return this;},removeProperties:function(){Array.each(arguments,this.removeProperty,this);return this;
},hasClass:function(l){return this.className.contains(l," ");},addClass:function(l){if(!this.hasClass(l)){this.className=(this.className+" "+l).clean();
}return this;},removeClass:function(l){this.className=this.className.replace(new RegExp("(^|\\s)"+l+"(?:\\s|$)"),"$1");return this;},toggleClass:function(l){return this.hasClass(l)?this.removeClass(l):this.addClass(l);
},adopt:function(){Array.flatten(arguments).each(function(l){l=$(l,true);if(l){this.appendChild(l);}},this);return this;},appendText:function(m,l){return this.grab(this.getDocument().newTextNode(m),l);
},grab:function(m,l){a[l||"bottom"]($(m,true),this);return this;},inject:function(m,l){a[l||"bottom"](this,$(m,true));return this;},replaces:function(l){l=$(l,true);
l.parentNode.replaceChild(this,l);return this;},wraps:function(m,l){m=$(m,true);return this.replaces(m).grab(m,l);},getPrevious:function(l,m){return j(this,"previousSibling",null,l,false,m);
},getAllPrevious:function(l,m){return j(this,"previousSibling",null,l,true,m);},getNext:function(l,m){return j(this,"nextSibling",null,l,false,m);},getAllNext:function(l,m){return j(this,"nextSibling",null,l,true,m);
},getFirst:function(l,m){return j(this,"nextSibling","firstChild",l,false,m);},getLast:function(l,m){return j(this,"previousSibling","lastChild",l,false,m);
},getParent:function(l,m){return j(this,"parentNode",null,l,false,m);},getParents:function(l,m){return j(this,"parentNode",null,l,true,m);},getSiblings:function(l,m){return this.getParent().getChildren(l,m).erase(this);
},getChildren:function(l,m){return j(this,"nextSibling","firstChild",l,true,m);},getWindow:function(){return this.ownerDocument.window;},getDocument:function(){return this.ownerDocument;
},getElementById:function(o,n){var m=this.ownerDocument.getElementById(o);if(!m){return null;}for(var l=m.parentNode;l!=this;l=l.parentNode){if(!l){return null;
}}return $.element(m,n);},getSelected:function(){return new Elements($A(this.options).filter(function(l){return l.selected;}));},getComputedStyle:function(m){if(this.currentStyle){return this.currentStyle[m.camelCase()];
}var l=this.getDocument().defaultView.getComputedStyle(this,null);return(l)?l.getPropertyValue([m.hyphenate()]):null;},toQueryString:function(){var l=[];
this.getElements("input, select, textarea",true).each(function(m){if(!m.name||m.disabled){return;}var n=(m.tagName.toLowerCase()=="select")?Element.getSelected(m).map(function(o){return o.value;
}):((m.type=="radio"||m.type=="checkbox")&&!m.checked)?null:m.value;$splat(n).each(function(o){if(typeof o!="undefined"){l.push(m.name+"="+encodeURIComponent(o));
}});});return l.join("&");},clone:function(o,l){o=o!==false;var r=this.cloneNode(o);var n=function(v,u){if(!l){v.removeAttribute("id");}if(Browser.Engine.trident){v.clearAttributes();
v.mergeAttributes(u);v.removeAttribute("uid");if(v.options){var w=v.options,s=u.options;for(var t=w.length;t--;){w[t].selected=s[t].selected;}}}var x=i[u.tagName.toLowerCase()];
if(x&&u[x]){v[x]=u[x];}};if(o){var p=r.getElementsByTagName("*"),q=this.getElementsByTagName("*");for(var m=p.length;m--;){n(p[m],q[m]);}}n(r,this);return $(r);
},destroy:function(){Element.empty(this);Element.dispose(this);g(this,true);return null;},empty:function(){$A(this.childNodes).each(function(l){Element.destroy(l);
});return this;},dispose:function(){return(this.parentNode)?this.parentNode.removeChild(this):this;},hasChild:function(l){l=$(l,true);if(!l){return false;
}if(Browser.Engine.webkit&&Browser.Engine.version<420){return $A(this.getElementsByTagName(l.tagName)).contains(l);}return(this.contains)?(this!=l&&this.contains(l)):!!(this.compareDocumentPosition(l)&16);
},match:function(l){return(!l||(l==this)||(Element.get(this,"tag")==l));}});Native.implement([Element,Window,Document],{addListener:function(o,n){if(o=="unload"){var l=n,m=this;
n=function(){m.removeListener("unload",n);l();};}else{h[this.uid]=this;}if(this.addEventListener){this.addEventListener(o,n,false);}else{this.attachEvent("on"+o,n);
}return this;},removeListener:function(m,l){if(this.removeEventListener){this.removeEventListener(m,l,false);}else{this.detachEvent("on"+m,l);}return this;
},retrieve:function(m,l){var o=c(this.uid),n=o[m];if(l!=undefined&&n==undefined){n=o[m]=l;}return $pick(n);},store:function(m,l){var n=c(this.uid);n[m]=l;
return this;},eliminate:function(l){var m=c(this.uid);delete m[l];return this;}});window.addListener("unload",d);})();Element.Properties=new Hash;Element.Properties.style={set:function(a){this.style.cssText=a;
},get:function(){return this.style.cssText;},erase:function(){this.style.cssText="";}};Element.Properties.tag={get:function(){return this.tagName.toLowerCase();
}};Element.Properties.html=(function(){var c=document.createElement("div");var a={table:[1,"<table>","</table>"],select:[1,"<select>","</select>"],tbody:[2,"<table><tbody>","</tbody></table>"],tr:[3,"<table><tbody><tr>","</tr></tbody></table>"]};
a.thead=a.tfoot=a.tbody;var b={set:function(){var e=Array.flatten(arguments).join("");var f=Browser.Engine.trident&&a[this.get("tag")];if(f){var g=c;g.innerHTML=f[1]+e+f[2];
for(var d=f[0];d--;){g=g.firstChild;}this.empty().adopt(g.childNodes);}else{this.innerHTML=e;}}};b.erase=b.set;return b;})();if(Browser.Engine.webkit&&Browser.Engine.version<420){Element.Properties.text={get:function(){if(this.innerText){return this.innerText;
}var a=this.ownerDocument.newElement("div",{html:this.innerHTML}).inject(this.ownerDocument.body);var b=a.innerText;a.destroy();return b;}};}Element.Properties.events={set:function(a){this.addEvents(a);
}};Native.implement([Element,Window,Document],{addEvent:function(e,g){var h=this.retrieve("events",{});h[e]=h[e]||{keys:[],values:[]};if(h[e].keys.contains(g)){return this;
}h[e].keys.push(g);var f=e,a=Element.Events.get(e),c=g,i=this;if(a){if(a.onAdd){a.onAdd.call(this,g);}if(a.condition){c=function(j){if(a.condition.call(this,j)){return g.call(this,j);
}return true;};}f=a.base||f;}var d=function(){return g.call(i);};var b=Element.NativeEvents[f];if(b){if(b==2){d=function(j){j=new Event(j,i.getWindow());
if(c.call(i,j)===false){j.stop();}};}this.addListener(f,d);}h[e].values.push(d);return this;},removeEvent:function(c,b){var a=this.retrieve("events");if(!a||!a[c]){return this;
}var f=a[c].keys.indexOf(b);if(f==-1){return this;}a[c].keys.splice(f,1);var e=a[c].values.splice(f,1)[0];var d=Element.Events.get(c);if(d){if(d.onRemove){d.onRemove.call(this,b);
}c=d.base||c;}return(Element.NativeEvents[c])?this.removeListener(c,e):this;},addEvents:function(a){for(var b in a){this.addEvent(b,a[b]);}return this;
},removeEvents:function(a){var c;if($type(a)=="object"){for(c in a){this.removeEvent(c,a[c]);}return this;}var b=this.retrieve("events");if(!b){return this;
}if(!a){for(c in b){this.removeEvents(c);}this.eliminate("events");}else{if(b[a]){while(b[a].keys[0]){this.removeEvent(a,b[a].keys[0]);}b[a]=null;}}return this;
},fireEvent:function(d,b,a){var c=this.retrieve("events");if(!c||!c[d]){return this;}c[d].keys.each(function(e){e.create({bind:this,delay:a,"arguments":b})();
},this);return this;},cloneEvents:function(d,a){d=$(d);var c=d.retrieve("events");if(!c){return this;}if(!a){for(var b in c){this.cloneEvents(d,b);}}else{if(c[a]){c[a].keys.each(function(e){this.addEvent(a,e);
},this);}}return this;}});Element.NativeEvents={click:2,dblclick:2,mouseup:2,mousedown:2,contextmenu:2,mousewheel:2,DOMMouseScroll:2,mouseover:2,mouseout:2,mousemove:2,selectstart:2,selectend:2,keydown:2,keypress:2,keyup:2,focus:2,blur:2,change:2,reset:2,select:2,submit:2,load:1,unload:1,beforeunload:2,resize:1,move:1,DOMContentLoaded:1,readystatechange:1,error:1,abort:1,scroll:1};
(function(){var a=function(b){var c=b.relatedTarget;if(c==undefined){return true;}if(c===false){return false;}return($type(this)!="document"&&c!=this&&c.prefix!="xul"&&!this.hasChild(c));
};Element.Events=new Hash({mouseenter:{base:"mouseover",condition:a},mouseleave:{base:"mouseout",condition:a},mousewheel:{base:(Browser.Engine.gecko)?"DOMMouseScroll":"mousewheel"}});
})();Element.Properties.styles={set:function(a){this.setStyles(a);}};Element.Properties.opacity={set:function(a,b){if(!b){if(a==0){if(this.style.visibility!="hidden"){this.style.visibility="hidden";
}}else{if(this.style.visibility!="visible"){this.style.visibility="visible";}}}if(!this.currentStyle||!this.currentStyle.hasLayout){this.style.zoom=1;}if(Browser.Engine.trident){this.style.filter=(a==1)?"":"alpha(opacity="+a*100+")";
}this.style.opacity=a;this.store("opacity",a);},get:function(){return this.retrieve("opacity",1);}};Element.implement({setOpacity:function(a){return this.set("opacity",a,true);
},getOpacity:function(){return this.get("opacity");},setStyle:function(b,a){switch(b){case"opacity":return this.set("opacity",parseFloat(a));case"float":b=(Browser.Engine.trident)?"styleFloat":"cssFloat";
}b=b.camelCase();if($type(a)!="string"){var c=(Element.Styles.get(b)||"@").split(" ");a=$splat(a).map(function(e,d){if(!c[d]){return"";}return($type(e)=="number")?c[d].replace("@",Math.round(e)):e;
}).join(" ");}else{if(a==String(Number(a))){a=Math.round(a);}}this.style[b]=a;return this;},getStyle:function(g){switch(g){case"opacity":return this.get("opacity");
case"float":g=(Browser.Engine.trident)?"styleFloat":"cssFloat";}g=g.camelCase();var a=this.style[g];if(!$chk(a)){a=[];for(var f in Element.ShortStyles){if(g!=f){continue;
}for(var e in Element.ShortStyles[f]){a.push(this.getStyle(e));}return a.join(" ");}a=this.getComputedStyle(g);}if(a){a=String(a);var c=a.match(/rgba?\([\d\s,]+\)/);
if(c){a=a.replace(c[0],c[0].rgbToHex());}}if(Browser.Engine.presto||(Browser.Engine.trident&&!$chk(parseInt(a,10)))){if(g.test(/^(height|width)$/)){var b=(g=="width")?["left","right"]:["top","bottom"],d=0;
b.each(function(h){d+=this.getStyle("border-"+h+"-width").toInt()+this.getStyle("padding-"+h).toInt();},this);return this["offset"+g.capitalize()]-d+"px";
}if((Browser.Engine.presto)&&String(a).test("px")){return a;}if(g.test(/(border(.+)Width|margin|padding)/)){return"0px";}}return a;},setStyles:function(b){for(var a in b){this.setStyle(a,b[a]);
}return this;},getStyles:function(){var a={};Array.each(arguments,function(b){a[b]=this.getStyle(b);},this);return a;}});Element.Styles=new Hash({left:"@px",top:"@px",bottom:"@px",right:"@px",width:"@px",height:"@px",maxWidth:"@px",maxHeight:"@px",minWidth:"@px",minHeight:"@px",backgroundColor:"rgb(@, @, @)",backgroundPosition:"@px @px",color:"rgb(@, @, @)",fontSize:"@px",letterSpacing:"@px",lineHeight:"@px",clip:"rect(@px @px @px @px)",margin:"@px @px @px @px",padding:"@px @px @px @px",border:"@px @ rgb(@, @, @) @px @ rgb(@, @, @) @px @ rgb(@, @, @)",borderWidth:"@px @px @px @px",borderStyle:"@ @ @ @",borderColor:"rgb(@, @, @) rgb(@, @, @) rgb(@, @, @) rgb(@, @, @)",zIndex:"@",zoom:"@",fontWeight:"@",textIndent:"@px",opacity:"@"});
Element.ShortStyles={margin:{},padding:{},border:{},borderWidth:{},borderStyle:{},borderColor:{}};["Top","Right","Bottom","Left"].each(function(g){var f=Element.ShortStyles;
var b=Element.Styles;["margin","padding"].each(function(h){var i=h+g;f[h][i]=b[i]="@px";});var e="border"+g;f.border[e]=b[e]="@px @ rgb(@, @, @)";var d=e+"Width",a=e+"Style",c=e+"Color";
f[e]={};f.borderWidth[d]=f[e][d]=b[d]="@px";f.borderStyle[a]=f[e][a]=b[a]="@";f.borderColor[c]=f[e][c]=b[c]="rgb(@, @, @)";});(function(){Element.implement({scrollTo:function(h,i){if(b(this)){this.getWindow().scrollTo(h,i);
}else{this.scrollLeft=h;this.scrollTop=i;}return this;},getSize:function(){if(b(this)){return this.getWindow().getSize();}return{x:this.offsetWidth,y:this.offsetHeight};
},getScrollSize:function(){if(b(this)){return this.getWindow().getScrollSize();}return{x:this.scrollWidth,y:this.scrollHeight};},getScroll:function(){if(b(this)){return this.getWindow().getScroll();
}return{x:this.scrollLeft,y:this.scrollTop};},getScrolls:function(){var i=this,h={x:0,y:0};while(i&&!b(i)){h.x+=i.scrollLeft;h.y+=i.scrollTop;i=i.parentNode;
}return h;},getOffsetParent:function(){var h=this;if(b(h)){return null;}if(!Browser.Engine.trident){return h.offsetParent;}while((h=h.parentNode)&&!b(h)){if(d(h,"position")!="static"){return h;
}}return null;},getOffsets:function(){if(Browser.Engine.trident){var l=this.getBoundingClientRect(),j=this.getDocument().documentElement;var m=d(this,"position")=="fixed";
return{x:l.left+((m)?0:j.scrollLeft)-j.clientLeft,y:l.top+((m)?0:j.scrollTop)-j.clientTop};}var i=this,h={x:0,y:0};if(b(this)){return h;}while(i&&!b(i)){h.x+=i.offsetLeft;
h.y+=i.offsetTop;if(Browser.Engine.gecko){if(!f(i)){h.x+=c(i);h.y+=g(i);}var k=i.parentNode;if(k&&d(k,"overflow")!="visible"){h.x+=c(k);h.y+=g(k);}}else{if(i!=this&&Browser.Engine.webkit){h.x+=c(i);
h.y+=g(i);}}i=i.offsetParent;}if(Browser.Engine.gecko&&!f(this)){h.x-=c(this);h.y-=g(this);}return h;},getPosition:function(k){if(b(this)){return{x:0,y:0};
}var l=this.getOffsets(),i=this.getScrolls();var h={x:l.x-i.x,y:l.y-i.y};var j=(k&&(k=$(k)))?k.getPosition():{x:0,y:0};return{x:h.x-j.x,y:h.y-j.y};},getCoordinates:function(j){if(b(this)){return this.getWindow().getCoordinates();
}var h=this.getPosition(j),i=this.getSize();var k={left:h.x,top:h.y,width:i.x,height:i.y};k.right=k.left+k.width;k.bottom=k.top+k.height;return k;},computePosition:function(h){return{left:h.x-e(this,"margin-left"),top:h.y-e(this,"margin-top")};
},position:function(h){return this.setStyles(this.computePosition(h));}});Native.implement([Document,Window],{getSize:function(){if(Browser.Engine.presto||Browser.Engine.webkit){var i=this.getWindow();
return{x:i.innerWidth,y:i.innerHeight};}var h=a(this);return{x:h.clientWidth,y:h.clientHeight};},getScroll:function(){var i=this.getWindow(),h=a(this);
return{x:i.pageXOffset||h.scrollLeft,y:i.pageYOffset||h.scrollTop};},getScrollSize:function(){var i=a(this),h=this.getSize();return{x:Math.max(i.scrollWidth,h.x),y:Math.max(i.scrollHeight,h.y)};
},getPosition:function(){return{x:0,y:0};},getCoordinates:function(){var h=this.getSize();return{top:0,left:0,bottom:h.y,right:h.x,height:h.y,width:h.x};
}});var d=Element.getComputedStyle;function e(h,i){return d(h,i).toInt()||0;}function f(h){return d(h,"-moz-box-sizing")=="border-box";}function g(h){return e(h,"border-top-width");
}function c(h){return e(h,"border-left-width");}function b(h){return(/^(?:body|html)$/i).test(h.tagName);}function a(h){var i=h.getDocument();return(!i.compatMode||i.compatMode=="CSS1Compat")?i.html:i.body;
}})();Native.implement([Window,Document,Element],{getHeight:function(){return this.getSize().y;},getWidth:function(){return this.getSize().x;},getScrollTop:function(){return this.getScroll().y;
},getScrollLeft:function(){return this.getScroll().x;},getScrollHeight:function(){return this.getScrollSize().y;},getScrollWidth:function(){return this.getScrollSize().x;
},getTop:function(){return this.getPosition().y;},getLeft:function(){return this.getPosition().x;}});Native.implement([Document,Element],{getElements:function(h,g){h=h.split(",");
var c,e={};for(var d=0,b=h.length;d<b;d++){var a=h[d],f=Selectors.Utils.search(this,a,e);if(d!=0&&f.item){f=$A(f);}c=(d==0)?f:(c.item)?$A(c).concat(f):c.concat(f);
}return new Elements(c,{ddup:(h.length>1),cash:!g});}});Element.implement({match:function(b){if(!b||(b==this)){return true;}var d=Selectors.Utils.parseTagAndID(b);
var a=d[0],e=d[1];if(!Selectors.Filters.byID(this,e)||!Selectors.Filters.byTag(this,a)){return false;}var c=Selectors.Utils.parseSelector(b);return(c)?Selectors.Utils.filter(this,c,{}):true;
}});var Selectors={Cache:{nth:{},parsed:{}}};Selectors.RegExps={id:(/#([\w-]+)/),tag:(/^(\w+|\*)/),quick:(/^(\w+|\*)$/),splitter:(/\s*([+>~\s])\s*([a-zA-Z#.*:\[])/g),combined:(/\.([\w-]+)|\[(\w+)(?:([!*^$~|]?=)(["']?)([^\4]*?)\4)?\]|:([\w-]+)(?:\(["']?(.*?)?["']?\)|$)/g)};
Selectors.Utils={chk:function(b,c){if(!c){return true;}var a=$uid(b);if(!c[a]){return c[a]=true;}return false;},parseNthArgument:function(h){if(Selectors.Cache.nth[h]){return Selectors.Cache.nth[h];
}var e=h.match(/^([+-]?\d*)?([a-z]+)?([+-]?\d*)?$/);if(!e){return false;}var g=parseInt(e[1],10);var d=(g||g===0)?g:1;var f=e[2]||false;var c=parseInt(e[3],10)||0;
if(d!=0){c--;while(c<1){c+=d;}while(c>=d){c-=d;}}else{d=c;f="index";}switch(f){case"n":e={a:d,b:c,special:"n"};break;case"odd":e={a:2,b:0,special:"n"};
break;case"even":e={a:2,b:1,special:"n"};break;case"first":e={a:0,special:"index"};break;case"last":e={special:"last-child"};break;case"only":e={special:"only-child"};
break;default:e={a:(d-1),special:"index"};}return Selectors.Cache.nth[h]=e;},parseSelector:function(e){if(Selectors.Cache.parsed[e]){return Selectors.Cache.parsed[e];
}var d,h={classes:[],pseudos:[],attributes:[]};while((d=Selectors.RegExps.combined.exec(e))){var i=d[1],g=d[2],f=d[3],b=d[5],c=d[6],j=d[7];if(i){h.classes.push(i);
}else{if(c){var a=Selectors.Pseudo.get(c);if(a){h.pseudos.push({parser:a,argument:j});}else{h.attributes.push({name:c,operator:"=",value:j});}}else{if(g){h.attributes.push({name:g,operator:f,value:b});
}}}}if(!h.classes.length){delete h.classes;}if(!h.attributes.length){delete h.attributes;}if(!h.pseudos.length){delete h.pseudos;}if(!h.classes&&!h.attributes&&!h.pseudos){h=null;
}return Selectors.Cache.parsed[e]=h;},parseTagAndID:function(b){var a=b.match(Selectors.RegExps.tag);var c=b.match(Selectors.RegExps.id);return[(a)?a[1]:"*",(c)?c[1]:false];
},filter:function(f,c,e){var d;if(c.classes){for(d=c.classes.length;d--;d){var g=c.classes[d];if(!Selectors.Filters.byClass(f,g)){return false;}}}if(c.attributes){for(d=c.attributes.length;
d--;d){var b=c.attributes[d];if(!Selectors.Filters.byAttribute(f,b.name,b.operator,b.value)){return false;}}}if(c.pseudos){for(d=c.pseudos.length;d--;d){var a=c.pseudos[d];
if(!Selectors.Filters.byPseudo(f,a.parser,a.argument,e)){return false;}}}return true;},getByTagAndID:function(b,a,d){if(d){var c=(b.getElementById)?b.getElementById(d,true):Element.getElementById(b,d,true);
return(c&&Selectors.Filters.byTag(c,a))?[c]:[];}else{return b.getElementsByTagName(a);}},search:function(o,h,t){var b=[];var c=h.trim().replace(Selectors.RegExps.splitter,function(k,j,i){b.push(j);
return":)"+i;}).split(":)");var p,e,A;for(var z=0,v=c.length;z<v;z++){var y=c[z];if(z==0&&Selectors.RegExps.quick.test(y)){p=o.getElementsByTagName(y);
continue;}var a=b[z-1];var q=Selectors.Utils.parseTagAndID(y);var B=q[0],r=q[1];if(z==0){p=Selectors.Utils.getByTagAndID(o,B,r);}else{var d={},g=[];for(var x=0,w=p.length;
x<w;x++){g=Selectors.Getters[a](g,p[x],B,r,d);}p=g;}var f=Selectors.Utils.parseSelector(y);if(f){e=[];for(var u=0,s=p.length;u<s;u++){A=p[u];if(Selectors.Utils.filter(A,f,t)){e.push(A);
}}p=e;}}return p;}};Selectors.Getters={" ":function(h,g,j,a,e){var d=Selectors.Utils.getByTagAndID(g,j,a);for(var c=0,b=d.length;c<b;c++){var f=d[c];if(Selectors.Utils.chk(f,e)){h.push(f);
}}return h;},">":function(h,g,j,a,f){var c=Selectors.Utils.getByTagAndID(g,j,a);for(var e=0,d=c.length;e<d;e++){var b=c[e];if(b.parentNode==g&&Selectors.Utils.chk(b,f)){h.push(b);
}}return h;},"+":function(c,b,a,e,d){while((b=b.nextSibling)){if(b.nodeType==1){if(Selectors.Utils.chk(b,d)&&Selectors.Filters.byTag(b,a)&&Selectors.Filters.byID(b,e)){c.push(b);
}break;}}return c;},"~":function(c,b,a,e,d){while((b=b.nextSibling)){if(b.nodeType==1){if(!Selectors.Utils.chk(b,d)){break;}if(Selectors.Filters.byTag(b,a)&&Selectors.Filters.byID(b,e)){c.push(b);
}}}return c;}};Selectors.Filters={byTag:function(b,a){return(a=="*"||(b.tagName&&b.tagName.toLowerCase()==a));},byID:function(a,b){return(!b||(a.id&&a.id==b));
},byClass:function(b,a){return(b.className&&b.className.contains(a," "));},byPseudo:function(a,d,c,b){return d.call(a,c,b);},byAttribute:function(c,d,b,e){var a=Element.prototype.getProperty.call(c,d);
if(!a){return(b=="!=");}if(!b||e==undefined){return true;}switch(b){case"=":return(a==e);case"*=":return(a.contains(e));case"^=":return(a.substr(0,e.length)==e);
case"$=":return(a.substr(a.length-e.length)==e);case"!=":return(a!=e);case"~=":return a.contains(e," ");case"|=":return a.contains(e,"-");}return false;
}};Selectors.Pseudo=new Hash({checked:function(){return this.checked;},empty:function(){return !(this.innerText||this.textContent||"").length;},not:function(a){return !Element.match(this,a);
},contains:function(a){return(this.innerText||this.textContent||"").contains(a);},"first-child":function(){return Selectors.Pseudo.index.call(this,0);},"last-child":function(){var a=this;
while((a=a.nextSibling)){if(a.nodeType==1){return false;}}return true;},"only-child":function(){var b=this;while((b=b.previousSibling)){if(b.nodeType==1){return false;
}}var a=this;while((a=a.nextSibling)){if(a.nodeType==1){return false;}}return true;},"nth-child":function(g,e){g=(g==undefined)?"n":g;var c=Selectors.Utils.parseNthArgument(g);
if(c.special!="n"){return Selectors.Pseudo[c.special].call(this,c.a,e);}var f=0;e.positions=e.positions||{};var d=$uid(this);if(!e.positions[d]){var b=this;
while((b=b.previousSibling)){if(b.nodeType!=1){continue;}f++;var a=e.positions[$uid(b)];if(a!=undefined){f=a+f;break;}}e.positions[d]=f;}return(e.positions[d]%c.a==c.b);
},index:function(a){var b=this,c=0;while((b=b.previousSibling)){if(b.nodeType==1&&++c>a){return false;}}return(c==a);},even:function(b,a){return Selectors.Pseudo["nth-child"].call(this,"2n+1",a);
},odd:function(b,a){return Selectors.Pseudo["nth-child"].call(this,"2n",a);},selected:function(){return this.selected;}});Element.Events.domready={onAdd:function(a){if(Browser.loaded){a.call(this);
}}};(function(){var b=function(){if(Browser.loaded){return;}Browser.loaded=true;window.fireEvent("domready");document.fireEvent("domready");};if(Browser.Engine.trident){var a=document.createElement("div");
(function(){($try(function(){a.doScroll("left");return $(a).inject(document.body).set("html","temp").dispose();}))?b():arguments.callee.delay(50);})();
}else{if(Browser.Engine.webkit&&Browser.Engine.version<525){(function(){(["loaded","complete"].contains(document.readyState))?b():arguments.callee.delay(50);
})();}else{window.addEvent("load",b);document.addEvent("DOMContentLoaded",b);}}})();var JSON=new Hash({$specialChars:{"\b":"\\b","\t":"\\t","\n":"\\n","\f":"\\f","\r":"\\r",'"':'\\"',"\\":"\\\\"},$replaceChars:function(a){return JSON.$specialChars[a]||"\\u00"+Math.floor(a.charCodeAt()/16).toString(16)+(a.charCodeAt()%16).toString(16);
},encode:function(b){switch($type(b)){case"string":return'"'+b.replace(/[\x00-\x1f\\"]/g,JSON.$replaceChars)+'"';case"array":return"["+String(b.map(JSON.encode).filter($defined))+"]";
case"object":case"hash":var a=[];Hash.each(b,function(e,d){var c=JSON.encode(e);if(c){a.push(JSON.encode(d)+":"+c);}});return"{"+a+"}";case"number":case"boolean":return String(b);
case false:return"null";}return null;},decode:function(string,secure){if($type(string)!="string"||!string.length){return null;}if(secure&&!(/^[,:{}\[\]0-9.\-+Eaeflnr-u \n\r\t]*$/).test(string.replace(/\\./g,"@").replace(/"[^"\\\n\r]*"/g,""))){return null;
}return eval("("+string+")");}});Native.implement([Hash,Array,String,Number],{toJSON:function(){return JSON.encode(this);}});var Cookie=new Class({Implements:Options,options:{path:false,domain:false,duration:false,secure:false,document:document},initialize:function(b,a){this.key=b;
this.setOptions(a);},write:function(b){b=encodeURIComponent(b);if(this.options.domain){b+="; domain="+this.options.domain;}if(this.options.path){b+="; path="+this.options.path;
}if(this.options.duration){var a=new Date();a.setTime(a.getTime()+this.options.duration*24*60*60*1000);b+="; expires="+a.toGMTString();}if(this.options.secure){b+="; secure";
}this.options.document.cookie=this.key+"="+b;return this;},read:function(){var a=this.options.document.cookie.match("(?:^|;)\\s*"+this.key.escapeRegExp()+"=([^;]*)");
return(a)?decodeURIComponent(a[1]):null;},dispose:function(){new Cookie(this.key,$merge(this.options,{duration:-1})).write("");return this;}});Cookie.write=function(b,c,a){return new Cookie(b,a).write(c);
};Cookie.read=function(a){return new Cookie(a).read();};Cookie.dispose=function(b,a){return new Cookie(b,a).dispose();};var Swiff=new Class({Implements:[Options],options:{id:null,height:1,width:1,container:null,properties:{},params:{quality:"high",allowScriptAccess:"always",wMode:"transparent",swLiveConnect:true},callBacks:{},vars:{}},toElement:function(){return this.object;
},initialize:function(l,m){this.instance="Swiff_"+$time();this.setOptions(m);m=this.options;var b=this.id=m.id||this.instance;var a=$(m.container);Swiff.CallBacks[this.instance]={};
var e=m.params,g=m.vars,f=m.callBacks;var h=$extend({height:m.height,width:m.width},m.properties);var k=this;for(var d in f){Swiff.CallBacks[this.instance][d]=(function(n){return function(){return n.apply(k.object,arguments);
};})(f[d]);g[d]="Swiff.CallBacks."+this.instance+"."+d;}e.flashVars=Hash.toQueryString(g);if(Browser.Engine.trident){h.classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000";
e.movie=l;}else{h.type="application/x-shockwave-flash";h.data=l;}var j='<object id="'+b+'"';for(var i in h){j+=" "+i+'="'+h[i]+'"';}j+=">";for(var c in e){if(e[c]){j+='<param name="'+c+'" value="'+e[c]+'" />';
}}j+="</object>";this.object=((a)?a.empty():new Element("div")).set("html",j).firstChild;},replaces:function(a){a=$(a,true);a.parentNode.replaceChild(this.toElement(),a);
return this;},inject:function(a){$(a,true).appendChild(this.toElement());return this;},remote:function(){return Swiff.remote.apply(Swiff,[this.toElement()].extend(arguments));
}});Swiff.CallBacks={};Swiff.remote=function(obj,fn){var rs=obj.CallFunction('<invoke name="'+fn+'" returntype="javascript">'+__flash__argumentsToXML(arguments,2)+"</invoke>");
return eval(rs);};var Fx=new Class({Implements:[Chain,Events,Options],options:{fps:50,unit:false,duration:500,link:"ignore"},initialize:function(a){this.subject=this.subject||this;
this.setOptions(a);this.options.duration=Fx.Durations[this.options.duration]||this.options.duration.toInt();var b=this.options.wait;if(b===false){this.options.link="cancel";
}},getTransition:function(){return function(a){return -(Math.cos(Math.PI*a)-1)/2;};},step:function(){var a=$time();if(a<this.time+this.options.duration){var b=this.transition((a-this.time)/this.options.duration);
this.set(this.compute(this.from,this.to,b));}else{this.set(this.compute(this.from,this.to,1));this.complete();}},set:function(a){return a;},compute:function(c,b,a){return Fx.compute(c,b,a);
},check:function(){if(!this.timer){return true;}switch(this.options.link){case"cancel":this.cancel();return true;case"chain":this.chain(this.caller.bind(this,arguments));
return false;}return false;},start:function(b,a){if(!this.check(b,a)){return this;}this.from=b;this.to=a;this.time=0;this.transition=this.getTransition();
this.startTimer();this.onStart();return this;},complete:function(){if(this.stopTimer()){this.onComplete();}return this;},cancel:function(){if(this.stopTimer()){this.onCancel();
}return this;},onStart:function(){this.fireEvent("start",this.subject);},onComplete:function(){this.fireEvent("complete",this.subject);if(!this.callChain()){this.fireEvent("chainComplete",this.subject);
}},onCancel:function(){this.fireEvent("cancel",this.subject).clearChain();},pause:function(){this.stopTimer();return this;},resume:function(){this.startTimer();
return this;},stopTimer:function(){if(!this.timer){return false;}this.time=$time()-this.time;this.timer=$clear(this.timer);return true;},startTimer:function(){if(this.timer){return false;
}this.time=$time()-this.time;this.timer=this.step.periodical(Math.round(1000/this.options.fps),this);return true;}});Fx.compute=function(c,b,a){return(b-c)*a+c;
};Fx.Durations={"short":250,normal:500,"long":1000};Fx.CSS=new Class({Extends:Fx,prepare:function(d,e,b){b=$splat(b);var c=b[1];if(!$chk(c)){b[1]=b[0];
b[0]=d.getStyle(e);}var a=b.map(this.parse);return{from:a[0],to:a[1]};},parse:function(a){a=$lambda(a)();a=(typeof a=="string")?a.split(" "):$splat(a);
return a.map(function(c){c=String(c);var b=false;Fx.CSS.Parsers.each(function(f,e){if(b){return;}var d=f.parse(c);if($chk(d)){b={value:d,parser:f};}});
b=b||{value:c,parser:Fx.CSS.Parsers.String};return b;});},compute:function(d,c,b){var a=[];(Math.min(d.length,c.length)).times(function(e){a.push({value:d[e].parser.compute(d[e].value,c[e].value,b),parser:d[e].parser});
});a.$family={name:"fx:css:value"};return a;},serve:function(c,b){if($type(c)!="fx:css:value"){c=this.parse(c);}var a=[];c.each(function(d){a=a.concat(d.parser.serve(d.value,b));
});return a;},render:function(a,d,c,b){a.setStyle(d,this.serve(c,b));},search:function(a){if(Fx.CSS.Cache[a]){return Fx.CSS.Cache[a];}var b={};Array.each(document.styleSheets,function(e,d){var c=e.href;
if(c&&c.contains("://")&&!c.contains(document.domain)){return;}var f=e.rules||e.cssRules;Array.each(f,function(j,g){if(!j.style){return;}var h=(j.selectorText)?j.selectorText.replace(/^\w+/,function(i){return i.toLowerCase();
}):null;if(!h||!h.test("^"+a+"$")){return;}Element.Styles.each(function(k,i){if(!j.style[i]||Element.ShortStyles[i]){return;}k=String(j.style[i]);b[i]=(k.test(/^rgb/))?k.rgbToHex():k;
});});});return Fx.CSS.Cache[a]=b;}});Fx.CSS.Cache={};Fx.CSS.Parsers=new Hash({Color:{parse:function(a){if(a.match(/^#[0-9a-f]{3,6}$/i)){return a.hexToRgb(true);
}return((a=a.match(/(\d+),\s*(\d+),\s*(\d+)/)))?[a[1],a[2],a[3]]:false;},compute:function(c,b,a){return c.map(function(e,d){return Math.round(Fx.compute(c[d],b[d],a));
});},serve:function(a){return a.map(Number);}},Number:{parse:parseFloat,compute:Fx.compute,serve:function(b,a){return(a)?b+a:b;}},String:{parse:$lambda(false),compute:$arguments(1),serve:$arguments(0)}});
Fx.Tween=new Class({Extends:Fx.CSS,initialize:function(b,a){this.element=this.subject=$(b);this.parent(a);},set:function(b,a){if(arguments.length==1){a=b;
b=this.property||this.options.property;}this.render(this.element,b,a,this.options.unit);return this;},start:function(c,e,d){if(!this.check(c,e,d)){return this;
}var b=Array.flatten(arguments);this.property=this.options.property||b.shift();var a=this.prepare(this.element,this.property,b);return this.parent(a.from,a.to);
}});Element.Properties.tween={set:function(a){var b=this.retrieve("tween");if(b){b.cancel();}return this.eliminate("tween").store("tween:options",$extend({link:"cancel"},a));
},get:function(a){if(a||!this.retrieve("tween")){if(a||!this.retrieve("tween:options")){this.set("tween",a);}this.store("tween",new Fx.Tween(this,this.retrieve("tween:options")));
}return this.retrieve("tween");}};Element.implement({tween:function(a,c,b){this.get("tween").start(arguments);return this;},fade:function(c){var e=this.get("tween"),d="opacity",a;
c=$pick(c,"toggle");switch(c){case"in":e.start(d,1);break;case"out":e.start(d,0);break;case"show":e.set(d,1);break;case"hide":e.set(d,0);break;case"toggle":var b=this.retrieve("fade:flag",this.get("opacity")==1);
e.start(d,(b)?0:1);this.store("fade:flag",!b);a=true;break;default:e.start(d,arguments);}if(!a){this.eliminate("fade:flag");}return this;},highlight:function(c,a){if(!a){a=this.retrieve("highlight:original",this.getStyle("background-color"));
a=(a=="transparent")?"#fff":a;}var b=this.get("tween");b.start("background-color",c||"#ffff88",a).chain(function(){this.setStyle("background-color",this.retrieve("highlight:original"));
b.callChain();}.bind(this));return this;}});Fx.Morph=new Class({Extends:Fx.CSS,initialize:function(b,a){this.element=this.subject=$(b);this.parent(a);},set:function(a){if(typeof a=="string"){a=this.search(a);
}for(var b in a){this.render(this.element,b,a[b],this.options.unit);}return this;},compute:function(e,d,c){var a={};for(var b in e){a[b]=this.parent(e[b],d[b],c);
}return a;},start:function(b){if(!this.check(b)){return this;}if(typeof b=="string"){b=this.search(b);}var e={},d={};for(var c in b){var a=this.prepare(this.element,c,b[c]);
e[c]=a.from;d[c]=a.to;}return this.parent(e,d);}});Element.Properties.morph={set:function(a){var b=this.retrieve("morph");if(b){b.cancel();}return this.eliminate("morph").store("morph:options",$extend({link:"cancel"},a));
},get:function(a){if(a||!this.retrieve("morph")){if(a||!this.retrieve("morph:options")){this.set("morph",a);}this.store("morph",new Fx.Morph(this,this.retrieve("morph:options")));
}return this.retrieve("morph");}};Element.implement({morph:function(a){this.get("morph").start(a);return this;}});Fx.implement({getTransition:function(){var a=this.options.transition||Fx.Transitions.Sine.easeInOut;
if(typeof a=="string"){var b=a.split(":");a=Fx.Transitions;a=a[b[0]]||a[b[0].capitalize()];if(b[1]){a=a["ease"+b[1].capitalize()+(b[2]?b[2].capitalize():"")];
}}return a;}});Fx.Transition=function(b,a){a=$splat(a);return $extend(b,{easeIn:function(c){return b(c,a);},easeOut:function(c){return 1-b(1-c,a);},easeInOut:function(c){return(c<=0.5)?b(2*c,a)/2:(2-b(2*(1-c),a))/2;
}});};Fx.Transitions=new Hash({linear:$arguments(0)});Fx.Transitions.extend=function(a){for(var b in a){Fx.Transitions[b]=new Fx.Transition(a[b]);}};Fx.Transitions.extend({Pow:function(b,a){return Math.pow(b,a[0]||6);
},Expo:function(a){return Math.pow(2,8*(a-1));},Circ:function(a){return 1-Math.sin(Math.acos(a));},Sine:function(a){return 1-Math.sin((1-a)*Math.PI/2);
},Back:function(b,a){a=a[0]||1.618;return Math.pow(b,2)*((a+1)*b-a);},Bounce:function(f){var e;for(var d=0,c=1;1;d+=c,c/=2){if(f>=(7-4*d)/11){e=c*c-Math.pow((11-6*d-11*f)/4,2);
break;}}return e;},Elastic:function(b,a){return Math.pow(2,10*--b)*Math.cos(20*b*Math.PI*(a[0]||1)/3);}});["Quad","Cubic","Quart","Quint"].each(function(b,a){Fx.Transitions[b]=new Fx.Transition(function(c){return Math.pow(c,[a+2]);
});});var Request=new Class({Implements:[Chain,Events,Options],options:{url:"",data:"",headers:{"X-Requested-With":"XMLHttpRequest",Accept:"text/javascript, text/html, application/xml, text/xml, */*"},async:true,format:false,method:"post",link:"ignore",isSuccess:null,emulation:true,urlEncoded:true,encoding:"utf-8",evalScripts:false,evalResponse:false,noCache:false},initialize:function(a){this.xhr=new Browser.Request();
this.setOptions(a);this.options.isSuccess=this.options.isSuccess||this.isSuccess;this.headers=new Hash(this.options.headers);},onStateChange:function(){if(this.xhr.readyState!=4||!this.running){return;
}this.running=false;this.status=0;$try(function(){this.status=this.xhr.status;}.bind(this));if(this.options.isSuccess.call(this,this.status)){this.response={text:this.xhr.responseText,xml:this.xhr.responseXML};
this.success(this.response.text,this.response.xml);}else{this.response={text:null,xml:null};this.failure();}this.xhr.onreadystatechange=$empty;},isSuccess:function(){return((this.status>=200)&&(this.status<300));
},processScripts:function(a){if(this.options.evalResponse||(/(ecma|java)script/).test(this.getHeader("Content-type"))){return $exec(a);}return a.stripScripts(this.options.evalScripts);
},success:function(b,a){this.onSuccess(this.processScripts(b),a);},onSuccess:function(){this.fireEvent("complete",arguments).fireEvent("success",arguments).callChain();
},failure:function(){this.onFailure();},onFailure:function(){this.fireEvent("complete").fireEvent("failure",this.xhr);},setHeader:function(a,b){this.headers.set(a,b);
return this;},getHeader:function(a){return $try(function(){return this.xhr.getResponseHeader(a);}.bind(this));},check:function(){if(!this.running){return true;
}switch(this.options.link){case"cancel":this.cancel();return true;case"chain":this.chain(this.caller.bind(this,arguments));return false;}return false;},send:function(j){if(!this.check(j)){return this;
}this.running=true;var h=$type(j);if(h=="string"||h=="element"){j={data:j};}var d=this.options;j=$extend({data:d.data,url:d.url,method:d.method},j);var f=j.data,b=j.url,a=j.method;
switch($type(f)){case"element":f=$(f).toQueryString();break;case"object":case"hash":f=Hash.toQueryString(f);}if(this.options.format){var i="format="+this.options.format;
f=(f)?i+"&"+f:i;}if(this.options.emulation&&["put","delete"].contains(a)){var g="_method="+a;f=(f)?g+"&"+f:g;a="post";}if(this.options.urlEncoded&&a=="post"){var c=(this.options.encoding)?"; charset="+this.options.encoding:"";
this.headers.set("Content-type","application/x-www-form-urlencoded"+c);}if(this.options.noCache){var e="noCache="+new Date().getTime();f=(f)?e+"&"+f:e;
}if(f&&a=="get"){b=b+(b.contains("?")?"&":"?")+f;f=null;}this.xhr.open(a.toUpperCase(),b,this.options.async);this.xhr.onreadystatechange=this.onStateChange.bind(this);
this.headers.each(function(l,k){try{this.xhr.setRequestHeader(k,l);}catch(m){this.fireEvent("exception",[k,l]);}},this);this.fireEvent("request");this.xhr.send(f);
if(!this.options.async){this.onStateChange();}return this;},cancel:function(){if(!this.running){return this;}this.running=false;this.xhr.abort();this.xhr.onreadystatechange=$empty;
this.xhr=new Browser.Request();this.fireEvent("cancel");return this;}});(function(){var a={};["get","post","put","delete","GET","POST","PUT","DELETE"].each(function(b){a[b]=function(){var c=Array.link(arguments,{url:String.type,data:$defined});
return this.send($extend(c,{method:b.toLowerCase()}));};});Request.implement(a);})();Request.HTML=new Class({Extends:Request,options:{update:false,append:false,evalScripts:true,filter:false},processHTML:function(c){var b=c.match(/<body[^>]*>([\s\S]*?)<\/body>/i);
c=(b)?b[1]:c;var a=new Element("div");return $try(function(){var d="<root>"+c+"</root>",g;if(Browser.Engine.trident){g=new ActiveXObject("Microsoft.XMLDOM");
g.async=false;g.loadXML(d);}else{g=new DOMParser().parseFromString(d,"text/xml");}d=g.getElementsByTagName("root")[0];if(!d){return null;}for(var f=0,e=d.childNodes.length;
f<e;f++){var h=Element.clone(d.childNodes[f],true,true);if(h){a.grab(h);}}return a;})||a.set("html",c);},success:function(d){var c=this.options,b=this.response;
b.html=d.stripScripts(function(e){b.javascript=e;});var a=this.processHTML(b.html);b.tree=a.childNodes;b.elements=a.getElements("*");if(c.filter){b.tree=b.elements.filter(c.filter);
}if(c.update){$(c.update).empty().set("html",b.html);}else{if(c.append){$(c.append).adopt(a.getChildren());}}if(c.evalScripts){$exec(b.javascript);}this.onSuccess(b.tree,b.elements,b.html,b.javascript);
}});Element.Properties.send={set:function(a){var b=this.retrieve("send");if(b){b.cancel();}return this.eliminate("send").store("send:options",$extend({data:this,link:"cancel",method:this.get("method")||"post",url:this.get("action")},a));
},get:function(a){if(a||!this.retrieve("send")){if(a||!this.retrieve("send:options")){this.set("send",a);}this.store("send",new Request(this.retrieve("send:options")));
}return this.retrieve("send");}};Element.Properties.load={set:function(a){var b=this.retrieve("load");if(b){b.cancel();}return this.eliminate("load").store("load:options",$extend({data:this,link:"cancel",update:this,method:"get"},a));
},get:function(a){if(a||!this.retrieve("load")){if(a||!this.retrieve("load:options")){this.set("load",a);}this.store("load",new Request.HTML(this.retrieve("load:options")));
}return this.retrieve("load");}};Element.implement({send:function(a){var b=this.get("send");b.send({data:this,url:a||b.options.url});return this;},load:function(){this.get("load").send(Array.link(arguments,{data:Object.type,url:String.type}));
return this;}});Request.JSON=new Class({Extends:Request,options:{secure:true},initialize:function(a){this.parent(a);this.headers.extend({Accept:"application/json","X-Request":"JSON"});
},success:function(a){this.response.json=JSON.decode(a,this.options.secure);this.onSuccess(this.response.json,a);}});

//MooTools More, <http://mootools.net/more>. Copyright (c) 2006-2008 Valerio Proietti, <http://mad4milk.net>, MIT Style License.
Fx.Slide=new Class({Extends:Fx,options:{mode:"vertical"},initialize:function(B,A){this.addEvent("complete",function(){this.open=(this.wrapper["offset"+this.layout.capitalize()]!=0);
if(this.open&&Browser.Engine.webkit419){this.element.dispose().inject(this.wrapper);}},true);this.element=this.subject=$(B);this.parent(A);var C=this.element.retrieve("wrapper");
this.wrapper=C||new Element("div",{styles:$extend(this.element.getStyles("margin","position"),{overflow:"hidden"})}).wraps(this.element);this.element.store("wrapper",this.wrapper).setStyle("margin",0);
this.now=[];this.open=true;},vertical:function(){this.margin="margin-top";this.layout="height";this.offset=this.element.offsetHeight;},horizontal:function(){this.margin="margin-left";
this.layout="width";this.offset=this.element.offsetWidth;},set:function(A){this.element.setStyle(this.margin,A[0]);this.wrapper.setStyle(this.layout,A[1]);
return this;},compute:function(E,D,C){var B=[];var A=2;A.times(function(F){B[F]=Fx.compute(E[F],D[F],C);});return B;},start:function(B,E){if(!this.check(arguments.callee,B,E)){return this;
}this[E||this.options.mode]();var D=this.element.getStyle(this.margin).toInt();var C=this.wrapper.getStyle(this.layout).toInt();var A=[[D,C],[0,this.offset]];
var G=[[D,C],[-this.offset,0]];var F;switch(B){case"in":F=A;break;case"out":F=G;break;case"toggle":F=(this.wrapper["offset"+this.layout.capitalize()]==0)?A:G;
}return this.parent(F[0],F[1]);},slideIn:function(A){return this.start("in",A);},slideOut:function(A){return this.start("out",A);},hide:function(A){this[A||this.options.mode]();
this.open=false;return this.set([-this.offset,0]);},show:function(A){this[A||this.options.mode]();this.open=true;return this.set([0,this.offset]);},toggle:function(A){return this.start("toggle",A);
}});Element.Properties.slide={set:function(B){var A=this.retrieve("slide");if(A){A.cancel();}return this.eliminate("slide").store("slide:options",$extend({link:"cancel"},B));
},get:function(A){if(A||!this.retrieve("slide")){if(A||!this.retrieve("slide:options")){this.set("slide",A);}this.store("slide",new Fx.Slide(this,this.retrieve("slide:options")));
}return this.retrieve("slide");}};Element.implement({slide:function(D,E){D=D||"toggle";var B=this.get("slide"),A;switch(D){case"hide":B.hide(E);break;case"show":B.show(E);
break;case"toggle":var C=this.retrieve("slide:flag",B.open);B[(C)?"slideOut":"slideIn"](E);this.store("slide:flag",!C);A=true;break;default:B.start(D,E);
}if(!A){this.eliminate("slide:flag");}return this;}});Fx.Scroll=new Class({Extends:Fx,options:{offset:{x:0,y:0},wheelStops:true},initialize:function(B,A){this.element=this.subject=$(B);
this.parent(A);var D=this.cancel.bind(this,false);if($type(this.element)!="element"){this.element=$(this.element.getDocument().body);}var C=this.element;
if(this.options.wheelStops){this.addEvent("start",function(){C.addEvent("mousewheel",D);},true);this.addEvent("complete",function(){C.removeEvent("mousewheel",D);
},true);}},set:function(){var A=Array.flatten(arguments);this.element.scrollTo(A[0],A[1]);},compute:function(E,D,C){var B=[];var A=2;A.times(function(F){B.push(Fx.compute(E[F],D[F],C));
});return B;},start:function(C,H){if(!this.check(arguments.callee,C,H)){return this;}var E=this.element.getSize(),F=this.element.getScrollSize();var B=this.element.getScroll(),D={x:C,y:H};
for(var G in D){var A=F[G]-E[G];if($chk(D[G])){D[G]=($type(D[G])=="number")?D[G].limit(0,A):A;}else{D[G]=B[G];}D[G]+=this.options.offset[G];}return this.parent([B.x,B.y],[D.x,D.y]);
},toTop:function(){return this.start(false,0);},toLeft:function(){return this.start(0,false);},toRight:function(){return this.start("right",false);},toBottom:function(){return this.start(false,"bottom");
},toElement:function(B){var A=$(B).getPosition(this.element);return this.start(A.x,A.y);}});Fx.Elements=new Class({Extends:Fx.CSS,initialize:function(B,A){this.elements=this.subject=$$(B);
this.parent(A);},compute:function(G,H,I){var C={};for(var D in G){var A=G[D],E=H[D],F=C[D]={};for(var B in A){F[B]=this.parent(A[B],E[B],I);}}return C;
},set:function(B){for(var C in B){var A=B[C];for(var D in A){this.render(this.elements[C],D,A[D],this.options.unit);}}return this;},start:function(C){if(!this.check(arguments.callee,C)){return this;
}var H={},I={};for(var D in C){var F=C[D],A=H[D]={},G=I[D]={};for(var B in F){var E=this.prepare(this.elements[D],B,F[B]);A[B]=E.from;G[B]=E.to;}}return this.parent(H,I);
}});var Drag=new Class({Implements:[Events,Options],options:{snap:6,unit:"px",grid:false,style:true,limit:false,handle:false,invert:false,preventDefault:false,modifiers:{x:"left",y:"top"}},initialize:function(){var B=Array.link(arguments,{options:Object.type,element:$defined});
this.element=$(B.element);this.document=this.element.getDocument();this.setOptions(B.options||{});var A=$type(this.options.handle);this.handles=(A=="array"||A=="collection")?$$(this.options.handle):$(this.options.handle)||this.element;
this.mouse={now:{},pos:{}};this.value={start:{},now:{}};this.selection=(Browser.Engine.trident)?"selectstart":"mousedown";this.bound={start:this.start.bind(this),check:this.check.bind(this),drag:this.drag.bind(this),stop:this.stop.bind(this),cancel:this.cancel.bind(this),eventStop:$lambda(false)};
this.attach();},attach:function(){this.handles.addEvent("mousedown",this.bound.start);return this;},detach:function(){this.handles.removeEvent("mousedown",this.bound.start);
return this;},start:function(C){if(this.options.preventDefault){C.preventDefault();}this.fireEvent("beforeStart",this.element);this.mouse.start=C.page;
var A=this.options.limit;this.limit={x:[],y:[]};for(var D in this.options.modifiers){if(!this.options.modifiers[D]){continue;}if(this.options.style){this.value.now[D]=this.element.getStyle(this.options.modifiers[D]).toInt();
}else{this.value.now[D]=this.element[this.options.modifiers[D]];}if(this.options.invert){this.value.now[D]*=-1;}this.mouse.pos[D]=C.page[D]-this.value.now[D];
if(A&&A[D]){for(var B=2;B--;B){if($chk(A[D][B])){this.limit[D][B]=$lambda(A[D][B])();}}}}if($type(this.options.grid)=="number"){this.options.grid={x:this.options.grid,y:this.options.grid};
}this.document.addEvents({mousemove:this.bound.check,mouseup:this.bound.cancel});this.document.addEvent(this.selection,this.bound.eventStop);},check:function(A){if(this.options.preventDefault){A.preventDefault();
}var B=Math.round(Math.sqrt(Math.pow(A.page.x-this.mouse.start.x,2)+Math.pow(A.page.y-this.mouse.start.y,2)));if(B>this.options.snap){this.cancel();this.document.addEvents({mousemove:this.bound.drag,mouseup:this.bound.stop});
this.fireEvent("start",this.element).fireEvent("snap",this.element);}},drag:function(A){if(this.options.preventDefault){A.preventDefault();}this.mouse.now=A.page;
for(var B in this.options.modifiers){if(!this.options.modifiers[B]){continue;}this.value.now[B]=this.mouse.now[B]-this.mouse.pos[B];if(this.options.invert){this.value.now[B]*=-1;
}if(this.options.limit&&this.limit[B]){if($chk(this.limit[B][1])&&(this.value.now[B]>this.limit[B][1])){this.value.now[B]=this.limit[B][1];}else{if($chk(this.limit[B][0])&&(this.value.now[B]<this.limit[B][0])){this.value.now[B]=this.limit[B][0];
}}}if(this.options.grid[B]){this.value.now[B]-=(this.value.now[B]%this.options.grid[B]);}if(this.options.style){this.element.setStyle(this.options.modifiers[B],this.value.now[B]+this.options.unit);
}else{this.element[this.options.modifiers[B]]=this.value.now[B];}}this.fireEvent("drag",this.element);},cancel:function(A){this.document.removeEvent("mousemove",this.bound.check);
this.document.removeEvent("mouseup",this.bound.cancel);if(A){this.document.removeEvent(this.selection,this.bound.eventStop);this.fireEvent("cancel",this.element);
}},stop:function(A){this.document.removeEvent(this.selection,this.bound.eventStop);this.document.removeEvent("mousemove",this.bound.drag);this.document.removeEvent("mouseup",this.bound.stop);
if(A){this.fireEvent("complete",this.element);}}});Element.implement({makeResizable:function(A){return new Drag(this,$merge({modifiers:{x:"width",y:"height"}},A));
}});Drag.Move=new Class({Extends:Drag,options:{droppables:[],container:false},initialize:function(C,B){this.parent(C,B);this.droppables=$$(this.options.droppables);
this.container=$(this.options.container);if(this.container&&$type(this.container)!="element"){this.container=$(this.container.getDocument().body);}C=this.element;
var D=C.getStyle("position");var A=(D!="static")?D:"absolute";if(C.getStyle("left")=="auto"||C.getStyle("top")=="auto"){C.position(C.getPosition(C.offsetParent));
}C.setStyle("position",A);this.addEvent("start",function(){this.checkDroppables();},true);},start:function(B){if(this.container){var D=this.element,J=this.container,E=J.getCoordinates(D.offsetParent),F={},A={};
["top","right","bottom","left"].each(function(K){F[K]=J.getStyle("padding-"+K).toInt();A[K]=D.getStyle("margin-"+K).toInt();},this);var C=D.offsetWidth+A.left+A.right,I=D.offsetHeight+A.top+A.bottom;
var H=[E.left+F.left,E.right-F.right-C];var G=[E.top+F.top,E.bottom-F.bottom-I];this.options.limit={x:H,y:G};}this.parent(B);},checkAgainst:function(B){B=B.getCoordinates();
var A=this.mouse.now;return(A.x>B.left&&A.x<B.right&&A.y<B.bottom&&A.y>B.top);},checkDroppables:function(){var A=this.droppables.filter(this.checkAgainst,this).getLast();
if(this.overed!=A){if(this.overed){this.fireEvent("leave",[this.element,this.overed]);}if(A){this.overed=A;this.fireEvent("enter",[this.element,A]);}else{this.overed=null;
}}},drag:function(A){this.parent(A);if(this.droppables.length){this.checkDroppables();}},stop:function(A){this.checkDroppables();this.fireEvent("drop",[this.element,this.overed]);
this.overed=null;return this.parent(A);}});Element.implement({makeDraggable:function(A){return new Drag.Move(this,A);}});Hash.Cookie=new Class({Extends:Cookie,options:{autoSave:true},initialize:function(B,A){this.parent(B,A);
this.load();},save:function(){var A=JSON.encode(this.hash);if(!A||A.length>4096){return false;}if(A=="{}"){this.dispose();}else{this.write(A);}return true;
},load:function(){this.hash=new Hash(JSON.decode(this.read(),true));return this;}});Hash.Cookie.implement((function(){var A={};Hash.each(Hash.prototype,function(C,B){A[B]=function(){var D=C.apply(this.hash,arguments);
if(this.options.autoSave){this.save();}return D;};});return A;})());var Color=new Native({initialize:function(B,C){if(arguments.length>=3){C="rgb";B=Array.slice(arguments,0,3);
}else{if(typeof B=="string"){if(B.match(/rgb/)){B=B.rgbToHex().hexToRgb(true);}else{if(B.match(/hsb/)){B=B.hsbToRgb();}else{B=B.hexToRgb(true);}}}}C=C||"rgb";
switch(C){case"hsb":var A=B;B=B.hsbToRgb();B.hsb=A;break;case"hex":B=B.hexToRgb(true);break;}B.rgb=B.slice(0,3);B.hsb=B.hsb||B.rgbToHsb();B.hex=B.rgbToHex();
return $extend(B,this);}});Color.implement({mix:function(){var A=Array.slice(arguments);var C=($type(A.getLast())=="number")?A.pop():50;var B=this.slice();
A.each(function(D){D=new Color(D);for(var E=0;E<3;E++){B[E]=Math.round((B[E]/100*(100-C))+(D[E]/100*C));}});return new Color(B,"rgb");},invert:function(){return new Color(this.map(function(A){return 255-A;
}));},setHue:function(A){return new Color([A,this.hsb[1],this.hsb[2]],"hsb");},setSaturation:function(A){return new Color([this.hsb[0],A,this.hsb[2]],"hsb");
},setBrightness:function(A){return new Color([this.hsb[0],this.hsb[1],A],"hsb");}});function $RGB(C,B,A){return new Color([C,B,A],"rgb");}function $HSB(C,B,A){return new Color([C,B,A],"hsb");
}function $HEX(A){return new Color(A,"hex");}Array.implement({rgbToHsb:function(){var B=this[0],C=this[1],J=this[2];var G,F,H;var I=Math.max(B,C,J),E=Math.min(B,C,J);
var K=I-E;H=I/255;F=(I!=0)?K/I:0;if(F==0){G=0;}else{var D=(I-B)/K;var A=(I-C)/K;var L=(I-J)/K;if(B==I){G=L-A;}else{if(C==I){G=2+D-L;}else{G=4+A-D;}}G/=6;
if(G<0){G++;}}return[Math.round(G*360),Math.round(F*100),Math.round(H*100)];},hsbToRgb:function(){var C=Math.round(this[2]/100*255);if(this[1]==0){return[C,C,C];
}else{var A=this[0]%360;var E=A%60;var F=Math.round((this[2]*(100-this[1]))/10000*255);var D=Math.round((this[2]*(6000-this[1]*E))/600000*255);var B=Math.round((this[2]*(6000-this[1]*(60-E)))/600000*255);
switch(Math.floor(A/60)){case 0:return[C,B,F];case 1:return[D,C,F];case 2:return[F,C,B];case 3:return[F,D,C];case 4:return[B,F,C];case 5:return[C,F,D];
}}return false;}});String.implement({rgbToHsb:function(){var A=this.match(/\d{1,3}/g);return(A)?hsb.rgbToHsb():null;},hsbToRgb:function(){var A=this.match(/\d{1,3}/g);
return(A)?A.hsbToRgb():null;}});var Group=new Class({initialize:function(){this.instances=Array.flatten(arguments);this.events={};this.checker={};},addEvent:function(B,A){this.checker[B]=this.checker[B]||{};
this.events[B]=this.events[B]||[];if(this.events[B].contains(A)){return false;}else{this.events[B].push(A);}this.instances.each(function(C,D){C.addEvent(B,this.check.bind(this,[B,C,D]));
},this);return this;},check:function(C,A,B){this.checker[C][B]=true;var D=this.instances.every(function(F,E){return this.checker[C][E]||false;},this);if(!D){return ;
}this.checker[C]={};this.events[C].each(function(E){E.call(this,this.instances,A);},this);}});var Asset=new Hash({javascript:function(F,D){D=$extend({onload:$empty,document:document,check:$lambda(true)},D);
var B=new Element("script",{src:F,type:"text/javascript"});var E=D.onload.bind(B),A=D.check,G=D.document;delete D.onload;delete D.check;delete D.document;
B.addEvents({load:E,readystatechange:function(){if(["loaded","complete"].contains(this.readyState)){E();}}}).setProperties(D);if(Browser.Engine.webkit419){var C=(function(){if(!$try(A)){return ;
}$clear(C);E();}).periodical(50);}return B.inject(G.head);},css:function(B,A){return new Element("link",$merge({rel:"stylesheet",media:"screen",type:"text/css",href:B},A)).inject(document.head);
},image:function(C,B){B=$merge({onload:$empty,onabort:$empty,onerror:$empty},B);var D=new Image();var A=$(D)||new Element("img");["load","abort","error"].each(function(E){var F="on"+E;
var G=B[F];delete B[F];D[F]=function(){if(!D){return ;}if(!A.parentNode){A.width=D.width;A.height=D.height;}D=D.onload=D.onabort=D.onerror=null;G.delay(1,A,A);
A.fireEvent(E,A,1);};});D.src=A.src=C;if(D&&D.complete){D.onload.delay(1);}return A.setProperties(B);},images:function(D,C){C=$merge({onComplete:$empty,onProgress:$empty},C);
if(!D.push){D=[D];}var A=[];var B=0;D.each(function(F){var E=new Asset.image(F,{onload:function(){C.onProgress.call(this,B,D.indexOf(F));B++;if(B==D.length){C.onComplete();
}}});A.push(E);});return new Elements(A);}});var Sortables=new Class({Implements:[Events,Options],options:{snap:4,opacity:1,clone:false,revert:false,handle:false,constrain:false},initialize:function(A,B){this.setOptions(B);
this.elements=[];this.lists=[];this.idle=true;this.addLists($$($(A)||A));if(!this.options.clone){this.options.revert=false;}if(this.options.revert){this.effect=new Fx.Morph(null,$merge({duration:250,link:"cancel"},this.options.revert));
}},attach:function(){this.addLists(this.lists);return this;},detach:function(){this.lists=this.removeLists(this.lists);return this;},addItems:function(){Array.flatten(arguments).each(function(A){this.elements.push(A);
var B=A.retrieve("sortables:start",this.start.bindWithEvent(this,A));(this.options.handle?A.getElement(this.options.handle)||A:A).addEvent("mousedown",B);
},this);return this;},addLists:function(){Array.flatten(arguments).each(function(A){this.lists.push(A);this.addItems(A.getChildren());},this);return this;
},removeItems:function(){var A=[];Array.flatten(arguments).each(function(B){A.push(B);this.elements.erase(B);var C=B.retrieve("sortables:start");(this.options.handle?B.getElement(this.options.handle)||B:B).removeEvent("mousedown",C);
},this);return $$(A);},removeLists:function(){var A=[];Array.flatten(arguments).each(function(B){A.push(B);this.lists.erase(B);this.removeItems(B.getChildren());
},this);return $$(A);},getClone:function(B,A){if(!this.options.clone){return new Element("div").inject(document.body);}if($type(this.options.clone)=="function"){return this.options.clone.call(this,B,A,this.list);
}return A.clone(true).setStyles({margin:"0px",position:"absolute",visibility:"hidden",width:A.getStyle("width")}).inject(this.list).position(A.getPosition(A.getOffsetParent()));
},getDroppables:function(){var A=this.list.getChildren();if(!this.options.constrain){A=this.lists.concat(A).erase(this.list);}return A.erase(this.clone).erase(this.element);
},insert:function(C,B){var A="inside";if(this.lists.contains(B)){this.list=B;this.drag.droppables=this.getDroppables();}else{A=this.element.getAllPrevious().contains(B)?"before":"after";
}this.element.inject(B,A);this.fireEvent("sort",[this.element,this.clone]);},start:function(B,A){if(!this.idle){return ;}this.idle=false;this.element=A;
this.opacity=A.get("opacity");this.list=A.getParent();this.clone=this.getClone(B,A);this.drag=new Drag.Move(this.clone,{snap:this.options.snap,container:this.options.constrain&&this.element.getParent(),droppables:this.getDroppables(),onSnap:function(){B.stop();
this.clone.setStyle("visibility","visible");this.element.set("opacity",this.options.opacity||0);this.fireEvent("start",[this.element,this.clone]);}.bind(this),onEnter:this.insert.bind(this),onCancel:this.reset.bind(this),onComplete:this.end.bind(this)});
this.clone.inject(this.element,"before");this.drag.start(B);},end:function(){this.drag.detach();this.element.set("opacity",this.opacity);if(this.effect){var A=this.element.getStyles("width","height");
var B=this.clone.computePosition(this.element.getPosition(this.clone.offsetParent));this.effect.element=this.clone;this.effect.start({top:B.top,left:B.left,width:A.width,height:A.height,opacity:0.25}).chain(this.reset.bind(this));
}else{this.reset();}},reset:function(){this.idle=true;this.clone.destroy();this.fireEvent("complete",this.element);},serialize:function(){var C=Array.link(arguments,{modifier:Function.type,index:$defined});
var B=this.lists.map(function(D){return D.getChildren().map(C.modifier||function(E){return E.get("id");},this);},this);var A=C.index;if(this.lists.length==1){A=0;
}return $chk(A)&&A>=0&&A<this.lists.length?B[A]:B;}});var Tips=new Class({Implements:[Events,Options],options:{onShow:function(A){A.setStyle("visibility","visible");
},onHide:function(A){A.setStyle("visibility","hidden");},showDelay:100,hideDelay:100,className:null,offsets:{x:16,y:16},fixed:false},initialize:function(){var C=Array.link(arguments,{options:Object.type,elements:$defined});
this.setOptions(C.options||null);this.tip=new Element("div").inject(document.body);if(this.options.className){this.tip.addClass(this.options.className);
}var B=new Element("div",{"class":"tip-top"}).inject(this.tip);this.container=new Element("div",{"class":"tip"}).inject(this.tip);var A=new Element("div",{"class":"tip-bottom"}).inject(this.tip);
this.tip.setStyles({position:"absolute",top:0,left:0,visibility:"hidden"});if(C.elements){this.attach(C.elements);}},attach:function(A){$$(A).each(function(D){var G=D.retrieve("tip:title",D.get("title"));
var F=D.retrieve("tip:text",D.get("rel")||D.get("href"));var E=D.retrieve("tip:enter",this.elementEnter.bindWithEvent(this,D));var C=D.retrieve("tip:leave",this.elementLeave.bindWithEvent(this,D));
D.addEvents({mouseenter:E,mouseleave:C});if(!this.options.fixed){var B=D.retrieve("tip:move",this.elementMove.bindWithEvent(this,D));D.addEvent("mousemove",B);
}D.store("tip:native",D.get("title"));D.erase("title");},this);return this;},detach:function(A){$$(A).each(function(C){C.removeEvent("mouseenter",C.retrieve("tip:enter")||$empty);
C.removeEvent("mouseleave",C.retrieve("tip:leave")||$empty);C.removeEvent("mousemove",C.retrieve("tip:move")||$empty);C.eliminate("tip:enter").eliminate("tip:leave").eliminate("tip:move");
var B=C.retrieve("tip:native");if(B){C.set("title",B);}});return this;},elementEnter:function(B,A){$A(this.container.childNodes).each(Element.dispose);
var D=A.retrieve("tip:title");if(D){this.titleElement=new Element("div",{"class":"tip-title"}).inject(this.container);this.fill(this.titleElement,D);}var C=A.retrieve("tip:text");
if(C){this.textElement=new Element("div",{"class":"tip-text"}).inject(this.container);this.fill(this.textElement,C);}this.timer=$clear(this.timer);this.timer=this.show.delay(this.options.showDelay,this);
this.position((!this.options.fixed)?B:{page:A.getPosition()});},elementLeave:function(A){$clear(this.timer);this.timer=this.hide.delay(this.options.hideDelay,this);
},elementMove:function(A){this.position(A);},position:function(D){var B=window.getSize(),A=window.getScroll();var E={x:this.tip.offsetWidth,y:this.tip.offsetHeight};
var C={x:"left",y:"top"};for(var F in C){var G=D.page[F]+this.options.offsets[F];if((G+E[F]-A[F])>B[F]){G=D.page[F]-this.options.offsets[F]-E[F];}this.tip.setStyle(C[F],G);
}},fill:function(A,B){(typeof B=="string")?A.set("html",B):A.adopt(B);},show:function(){this.fireEvent("show",this.tip);},hide:function(){this.fireEvent("hide",this.tip);
}});var SmoothScroll=new Class({Extends:Fx.Scroll,initialize:function(B,C){C=C||document;var E=C.getDocument(),D=C.getWindow();this.parent(E,B);this.links=(this.options.links)?$$(this.options.links):$$(E.links);
var A=D.location.href.match(/^[^#]*/)[0]+"#";this.links.each(function(G){if(G.href.indexOf(A)!=0){return ;}var F=G.href.substr(A.length);if(F&&$(F)){this.useLink(G,F);
}},this);if(!Browser.Engine.webkit419){this.addEvent("complete",function(){D.location.hash=this.anchor;},true);}},useLink:function(B,A){B.addEvent("click",function(C){this.anchor=A;
this.toElement(A);C.stop();}.bind(this));}});var Slider=new Class({Implements:[Events,Options],options:{onTick:function(A){if(this.options.snap){A=this.toPosition(this.step);
}this.knob.setStyle(this.property,A);},snap:false,offset:0,range:false,wheel:false,steps:100,mode:"horizontal"},initialize:function(E,A,D){this.setOptions(D);
this.element=$(E);this.knob=$(A);this.previousChange=this.previousEnd=this.step=-1;this.element.addEvent("mousedown",this.clickedElement.bind(this));if(this.options.wheel){this.element.addEvent("mousewheel",this.scrolledElement.bindWithEvent(this));
}var F,B={},C={x:false,y:false};switch(this.options.mode){case"vertical":this.axis="y";this.property="top";F="offsetHeight";break;case"horizontal":this.axis="x";
this.property="left";F="offsetWidth";}this.half=this.knob[F]/2;this.full=this.element[F]-this.knob[F]+(this.options.offset*2);this.min=$chk(this.options.range[0])?this.options.range[0]:0;
this.max=$chk(this.options.range[1])?this.options.range[1]:this.options.steps;this.range=this.max-this.min;this.steps=this.options.steps||this.full;this.stepSize=Math.abs(this.range)/this.steps;
this.stepWidth=this.stepSize*this.full/Math.abs(this.range);this.knob.setStyle("position","relative").setStyle(this.property,-this.options.offset);C[this.axis]=this.property;
B[this.axis]=[-this.options.offset,this.full-this.options.offset];this.drag=new Drag(this.knob,{snap:0,limit:B,modifiers:C,onDrag:this.draggedKnob.bind(this),onStart:this.draggedKnob.bind(this),onComplete:function(){this.draggedKnob();
this.end();}.bind(this)});if(this.options.snap){this.drag.options.grid=Math.ceil(this.stepWidth);this.drag.options.limit[this.axis][1]=this.full;}},set:function(A){if(!((this.range>0)^(A<this.min))){A=this.min;
}if(!((this.range>0)^(A>this.max))){A=this.max;}this.step=Math.round(A);this.checkStep();this.end();this.fireEvent("tick",this.toPosition(this.step));return this;
},clickedElement:function(C){var B=this.range<0?-1:1;var A=C.page[this.axis]-this.element.getPosition()[this.axis]-this.half;A=A.limit(-this.options.offset,this.full-this.options.offset);
this.step=Math.round(this.min+B*this.toStep(A));this.checkStep();this.end();this.fireEvent("tick",A);},scrolledElement:function(A){var B=(this.options.mode=="horizontal")?(A.wheel<0):(A.wheel>0);
this.set(B?this.step-this.stepSize:this.step+this.stepSize);A.stop();},draggedKnob:function(){var B=this.range<0?-1:1;var A=this.drag.value.now[this.axis];
A=A.limit(-this.options.offset,this.full-this.options.offset);this.step=Math.round(this.min+B*this.toStep(A));this.checkStep();},checkStep:function(){if(this.previousChange!=this.step){this.previousChange=this.step;
this.fireEvent("change",this.step);}},end:function(){if(this.previousEnd!==this.step){this.previousEnd=this.step;this.fireEvent("complete",this.step+"");
}},toStep:function(A){var B=(A+this.options.offset)*this.stepSize/this.full*this.steps;return this.options.steps?Math.round(B-=B%this.stepSize):B;},toPosition:function(A){return(this.full*Math.abs(this.min-A))/(this.steps*this.stepSize)-this.options.offset;
}});var Scroller=new Class({Implements:[Events,Options],options:{area:20,velocity:1,onChange:function(A,B){this.element.scrollTo(A,B);}},initialize:function(B,A){this.setOptions(A);
this.element=$(B);this.listener=($type(this.element)!="element")?$(this.element.getDocument().body):this.element;this.timer=null;this.coord=this.getCoords.bind(this);
},start:function(){this.listener.addEvent("mousemove",this.coord);},stop:function(){this.listener.removeEvent("mousemove",this.coord);this.timer=$clear(this.timer);
},getCoords:function(A){this.page=(this.listener.get("tag")=="body")?A.client:A.page;if(!this.timer){this.timer=this.scroll.periodical(50,this);}},scroll:function(){var B=this.element.getSize(),A=this.element.getScroll(),E=this.element.getPosition(),D={x:0,y:0};
for(var C in this.page){if(this.page[C]<(this.options.area+E[C])&&A[C]!=0){D[C]=(this.page[C]-this.options.area-E[C])*this.options.velocity;}else{if(this.page[C]+this.options.area>(B[C]+E[C])&&B[C]+B[C]!=A[C]){D[C]=(this.page[C]-B[C]+this.options.area-E[C])*this.options.velocity;
}}}if(D.y||D.x){this.fireEvent("change",[A.x+D.x,A.y+D.y]);}}});var Accordion=new Class({Extends:Fx.Elements,options:{display:0,show:false,height:true,width:false,opacity:true,fixedHeight:false,fixedWidth:false,wait:false,alwaysHide:false},initialize:function(){var C=Array.link(arguments,{container:Element.type,options:Object.type,togglers:$defined,elements:$defined});
this.parent(C.elements,C.options);this.togglers=$$(C.togglers);this.container=$(C.container);this.previous=-1;if(this.options.alwaysHide){this.options.wait=true;
}if($chk(this.options.show)){this.options.display=false;this.previous=this.options.show;}if(this.options.start){this.options.display=false;this.options.show=false;
}this.effects={};if(this.options.opacity){this.effects.opacity="fullOpacity";}if(this.options.width){this.effects.width=this.options.fixedWidth?"fullWidth":"offsetWidth";
}if(this.options.height){this.effects.height=this.options.fixedHeight?"fullHeight":"scrollHeight";}for(var B=0,A=this.togglers.length;B<A;B++){this.addSection(this.togglers[B],this.elements[B]);
}this.elements.each(function(E,D){if(this.options.show===D){this.fireEvent("active",[this.togglers[D],E]);}else{for(var F in this.effects){E.setStyle(F,0);
}}},this);if($chk(this.options.display)){this.display(this.options.display);}},addSection:function(E,C,G){E=$(E);C=$(C);var F=this.togglers.contains(E);
var B=this.togglers.length;this.togglers.include(E);this.elements.include(C);if(B&&(!F||G)){G=$pick(G,B-1);E.inject(this.togglers[G],"before");C.inject(E,"after");
}else{if(this.container&&!F){E.inject(this.container);C.inject(this.container);}}var A=this.togglers.indexOf(E);E.addEvent("click",this.display.bind(this,A));
if(this.options.height){C.setStyles({"padding-top":0,"border-top":"none","padding-bottom":0,"border-bottom":"none"});}if(this.options.width){C.setStyles({"padding-left":0,"border-left":"none","padding-right":0,"border-right":"none"});
}C.fullOpacity=1;if(this.options.fixedWidth){C.fullWidth=this.options.fixedWidth;}if(this.options.fixedHeight){C.fullHeight=this.options.fixedHeight;}C.setStyle("overflow","hidden");
if(!F){for(var D in this.effects){C.setStyle(D,0);}}return this;},display:function(A){A=($type(A)=="element")?this.elements.indexOf(A):A;if((this.timer&&this.options.wait)||(A===this.previous&&!this.options.alwaysHide)){return this;
}this.previous=A;var B={};this.elements.each(function(E,D){B[D]={};var C=(D!=A)||(this.options.alwaysHide&&(E.offsetHeight>0));this.fireEvent(C?"background":"active",[this.togglers[D],E]);
for(var F in this.effects){B[D][F]=C?0:E[this.effects[F]];}},this);return this.start(B);}});

function hide(obj){
	obj=$(obj);if(!obj){return;}
	if(obj.getStyle("display")=='none'){if(obj.hasClass("hide")){obj.removeClass("hide").setStyle("display","");}else{obj.setStyle("display",'').setStyle("visibility","visible");}
	}else if(obj.hasClass('hide')){obj.removeClass('hide');if(obj.getProperty("id")=="k" || obj.getProperty("id")=="zprava"){document.getElementById('km').focus();}
	}else{obj.addClass('hide');}
}

function check_mail(mail){
	re = /^[^.]+(\.[^.]+)*@([^.]+[.])+[a-z]{2,3}$/;
	return mail.search(re) == 0;
}
function conf(link){
	r=confirm("Jste si jist(a)?");
	if(r==true){
		window.location.href = link;
	}
}
function vloz_tag(znacka){var spravne=false;var kom=document.getElementById('km');switch(znacka){case "b":case "i":case "u":case "color1":case "color2":case "color3":start="{"+znacka+"}";end="{/}";spravne=true;break;}if(spravne){if(document.selection){kom.focus();sel=document.selection.createRange();sel.text=start+sel.text+end;kom.focus();}else if(kom.selectionStart||kom.selectionStart=='0'){var startPos=kom.selectionStart;var endPos=kom.selectionEnd;var cursorPos=endPos;if(startPos!=endPos){kom.value=kom.value.substring(0,startPos)+start+kom.value.substring(startPos,endPos)+end+kom.value.substring(endPos,kom.value.length);cursorPos+=end.length;}else{kom.value=kom.value.substring(0,startPos)+start+end+kom.value.substring(endPos,kom.value.length);}cursorPos+=start.length;kom.focus();kom.selectionStart=cursorPos;kom.selectionEnd=cursorPos;}else{kom.value+=start+end;kom.focus();}}}
function editor(type){switch (type){case 1:request = "{b}{/}";break;case 2:request = "{i}{/}";break;case 3:request = "{u}{/}";break;case 4:po = prompt("Zadejte odkaz včetně http://","");if (po.length > 0){if (po.indexOf('http://') == 0 || po.indexOf('ftp://') == 0 || po.indexOf('https://') == 0) request = "{link}"+po+"{/}";else {request = "{link}http://"+po+"{/}";}}break;case 5:po = prompt("Zadejte cestu k obrázku","");if (po.length > 0){request = "{link}"+po+"{/}";}break;}textContent = document.forms['txt']['mess'].value;textContent = textContent.concat(request);document.forms['txt']['mess'].value=textContent;po = "";request = "";}
function setCursor(el) {
end=el.value.length;
st=end; 
if(el.setSelectionRange) { 
el.focus(); 
el.setSelectionRange(st,end); 
} 
else { 
if(el.createTextRange) { 
range=el.createTextRange(); 
range.collapse(true); 
range.moveEnd('character',end); 
range.moveStart('character',st); 
range.select();
} 
} 
}
function react(re){
	re="{i}"+re+"{/}";
	document.getElementById('km').value=document.getElementById('km').value.concat(re+"\n");
	if($('k').hasClass('hide')){
		$('k').removeClass('hide');
	}
	else if (document.getElementById('k').style.display=='none') {
		document.getElementById('k').style.display="block";
	}
	document.getElementById('km').focus();
       setCursor(document.getElementById('km'));
	return false;
}
function throw_dices(t){if(t.nodeType==3){t=t.parentNode;}while(t.parentNode&&t.tagName.toUpperCase()!="A"){t=t.parentNode;}xK=prompt("Počet kostek (1 - 30)");if(xK>0&&!isNaN(xK)&&(xK>0&&xK<=30)){yK=prompt("Nejvyšší hodnota na kostce (minimální hod je napevno 1, maximální 10000)");if(yK>0&&!isNaN(yK)&&(yK>1&&yK<=10000)){if(t.href.indexOf("&")>0){a=t.href.split("&");t.href=a[0];}t.href+="&x="+xK+"&y="+yK;return true;}}return false;}
function send_xmlhttprequest(after, meth, url, content, heads) {
var xmlhttp=false;
/*@cc_on @*/
/*@if (@_jscript_version >= 5)
try {xmlhttp=new ActiveXObject("Msxml2.XMLHTTP");
}catch(e){
try{xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
}catch(E){xmlhttp = false;}
}
@end @*/
if(!xmlhttp&&typeof XMLHttpRequest!='undefined'){try{xmlhttp=new XMLHttpRequest();}catch(e){xmlhttp=false;}}if(!xmlhttp&&window.createRequest){try{xmlhttp=window.createRequest();}catch(e){xmlhttp=false;}}if(!xmlhttp){return false;}xmlhttp.open(meth,url);xmlhttp.onreadystatechange=function(){after(xmlhttp);};if(heads){for(var key in heads){xmlhttp.setRequestHeader(key,heads[key]);}}xmlhttp.setRequestHeader('Pragma','no-cache');xmlhttp.setRequestHeader("Cache-Control", "no-cache");xmlhttp.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');if(xmlhttp.overrideMimeType){xmlhttp.setRequestHeader('Connection','close');}if(content) {xmlhttp.send(content);}else{xmlhttp.send(null);}return true;}

function check_login(z){new Request({'url':'/ajaxing.php?do=logining&heads=1&zal='+z+'&rndtmr='+$time(),'method':'get','onComplete':function(txt,xml){login_checker(xml);},'onFailure': function(txt,xml){setTimeout('check_login(true);',30*1000);}}).send();return true;}
function cestinaSklonuj(pocet,kus,kusy,kusu){
	if (pocet > 4 || pocet == 0) return kusu;
	else if (pocet > 1) return kusy;
	else return kus;
}
function login_checker(oXml){
	var ajaxF=true;
	var txt=0;
	var xtx=oXml.getElementsByTagName('xtx');
	for(var i=0;i<xtx.length;i++){if(xtx[i].firstChild.data!="off"){txt=Math.abs(txt-xtx[i].firstChild.data);ajaxF=false;}else{ajaxF=true;break;}}
	if(!ajaxF){
		var ajaxiBar = $('ajaxi-bar');
		var theprofilepostlink = $('theprofilepostlink');
		if (theprofilepostlink.hasClass('doT')) {
			if (!ajaxiBar.retrieve('puvTitle')) {ajaxiBar.store('puvTitle',document.title);}
			document.title = ajaxiBar.retrieve('puvTitle');
		}

		ajaxiBar.getFirst().setStyle('width',(100-Math.round(100*txt/60/60))+'%').setProperty('title','Do automatického odhlášení zbývá: '+Math.round(Math.abs(txt-60*60)/60)+' minut').getParent().setProperty('title','Do automatického odhlášení zbývá: '+Math.round(Math.abs(txt-60*60)/60)+' minut').setStyle('display','block');
		var mn=oXml.getElementsByTagName('mn');
		if(mn){
			for(var i=0;i<mn.length;i++){
				$(mn[i].getAttribute("id")).set("html",mn[i].firstChild.data);
			}
		}
		var npost=oXml.getElementsByTagName('np');
		if(npost.length==1){
			if(npost[0].firstChild.data>0){
				theprofilepostlink.set('html','pošta ('+npost[0].firstChild.data+') <img src="/system/ruzne/neprecteno.gif" title="Máte nepřečtenou poštu" alt="Máte nepřečtenou poštu)" />');
				if (theprofilepostlink.hasClass('doT'))
					document.title = npost[0].firstChild.data+'p] ' + ajaxiBar.retrieve('puvTitle');
			}
			else{
				theprofilepostlink.set('html','pošta');
				if (theprofilepostlink.hasClass('doT'))
					document.title = '0p] '+ajaxiBar.retrieve('puvTitle');
			}
			if (theprofilepostlink.hasClass('doT')) {
				var tx = $('dropmenu4').getElements('span');
				if (tx)
					document.title = '['+tx.length+'z/' + document.title;
				else
					document.title = '[0z/' + document.title;
			}
		}
		
		aTx = setTimeout('check_login(true);',60*1000);
	}
	else{
		$('ajaxi-bar').setStyle('display','none');
	}
}
function login_timer_maker(){new Element("div",{'id':'ajaxi-bar','title':'Ukazatel poslední aktivity','styles':{'cursor':'help','display':'none'}}).adopt(new Element("a",{'href':'#','title':'Ukazatel poslední aktivity','styles':{'cursor':'help'}}).adopt(new Element("small",{'text':"neaktivita",'styles':{'cursor':'help','fontSize':'9px','textIndent':'2px'}}))).inject(document.body);check_login(false);return true;}

var submited_checkfornewX = false;

function checkfornewX(xmlhttp){
	if(xmlhttp.readyState==4){
		var frm=document.forms["form_for_new"];
		var status=0;
		try{status=xmlhttp.status;}catch(e){};
		if(status!=200||!xmlhttp.responseXML){frm.submit();}
		var elm=xmlhttp.responseXML.documentElement.firstChild;
		if(elm.getAttribute("t")=="ok"){
			if (submited_checkfornewX){
				alert('Formulář se již odesílá.');
			}
			else {
				submited_checkfornewX = true;
				frm.submit();
			}
		}
		else{
			if(document.getElementById("ajax_for_new_info")){
				var aaa=document.getElementById("ajax_for_new_info");
			}
			else{
				var aaa=document.createElement("div");
				aaa.id="ajax_for_new_info";
				var dc=frm.parentNode.previousSibling;
				while(dc.nodeType==3){
					dc=dc.previousSibling;
				}
				dc=dc.previousSibling;
				dc.parentNode.insertBefore(aaa,dc);
			}
			aaa.innerHTML="<p class='info' id='inf-x'><span class='war' title='Varování'></span>"+elm.firstChild.data+" <a href=\"javascript: hide('inf-x')\" class='permalink2' title='Zavřít'>Zavřít</a></p>";
			if(elm.hasAttribute("f")){
				document.forms["form_for_new"][elm.getAttribute("f")].focus();
			}
			aaa.firstChild.style.visibility="visible";
			aaa.firstChild.style.display="";
		}
	}
}

function checkForNew(w,f,a,b){
	var f=document.getElementById(f);
	f=f.value;
	var frm=document.forms["form_for_new"];
	adv=new Array();
	if(b){
		adv=a;
	}
	else{
		for(var aa=0;aa<a.length;aa++){
			frm[a[aa]].value=frm[a[aa]].value.trim();
			var t=frm[a[aa]];
			adv[aa]=a[aa]+':'+t.value.length;
			if((isNaN(t.value)&&t.value.length>=3)||t.value.length>0){
				continue;
			}
			while(t.parentNode&&t.tagName.toUpperCase()!="LABEL"){
				t=t.parentNode;
			}
			t=t.firstChild.innerHTML;
			if(t.indexOf(" (")>0){
				t=t.substr(0,t.indexOf(" ("));
			}
			if(frm[a[aa]].tagName.toLowerCase()=="select" && frm[a[aa]].options[frm[a[aa]].selectedIndex].value == ""){
				alert("Musíte vybrat jednu z možností pro '"+t+"'");
			}
			else if(frm[a[aa]].tagName.toLowerCase()=="input"&&frm[a[aa]].type.toLowerCase()=="file"&&frm[a[aa]].value.length<4){
				alert("Musíte vybrat soubor k odeslání.");
			}
			else{
				alert("Musíte vyplnit políčko '"+t+"'");
			}
			frm[a[aa]].focus();
			return false;
		}
		adv=adv.join(",");
	}
	if(!send_xmlhttprequest(checkfornewX,'POST','/ajaxing.php?do=checker&sekce='+w,'adv='+adv+'&co='+encodeURIComponent(f))){
		return true;
	}
	else{
		return false;
	}
}

var aTx;

window.addEvent("load",function(){
	if (document.body.className.indexOf("js") != -1 && Browser.Features.xhr) {
		aTx = setTimeout('login_timer_maker()', 5000);
		var frm = $('k');
		var profilePost = $('theprofilepostlink');
		if (profilePost && frm) {
			if (profilePost.getParent().getElement("a").get("text") === "apophis") {
				disModuleAjaxSend(frm);
			}
		}
	}
});

function makeStats(section, login){

aP = new Request({'url':"/ajaxing.php?do=stats&sec="+section+"&loginStats="+login, 'method': 'post', 'onComplete': function(txt, xml){ flushStats(txt, xml); } });
aP.setHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
aP.setHeader('Pragma','no-cache');
aP.setHeader("Cache-Control", "no-cache");
aP.send();

}

function flushStats(txt, xml){
 var rows = "";

 data = xml.getElementsByTagName('stats');
 dataC = data.length;
 for (i = 0; i < dataC; i++){

    autor = data[i].getElementsByTagName('autor');
    pocet = data[i].getElementsByTagName('pocet');
    prumer = data[i].getElementsByTagName('prumer');
    koef = data[i].getElementsByTagName('koef');
    rows = rows.concat("<tr><td>"+autor[0].firstChild.data+"</td><td>"+pocet[0].firstChild.data+"</td><td>"+prumer[0].firstChild.data+"</td><td class='jsStatsK'>"+koef[0].firstChild.data+"</td></tr>");

 }
msg = xml.getElementsByTagName('msg');

statsContent = "<table class='jsStats' id='jsStatsContent' width='100%' cellspacing='3'><thead><tr><th>Autor</th><th>Počet děl</th><th>Průměrné hodnocení</th><th>Koeficient</th></tr></thead><tbody>";
statsContent = statsContent.concat(rows);
statsContent = statsContent.concat("</tbody></table>");
statsContent = statsContent.concat(msg[0].firstChild.data);

$('jsStats').set('html',statsContent); 

}

var thePreviewSender = false;
var thePreviewPSender = false;
var prew;

function do_preview(id){
	var firstTable = $('dis-module-x').getElement('table');
	if (!firstTable) {
		prew = new Element('div').adopt(new Element('table',{'cellspacing':0,'class':'commtb'}).adopt(new Element("tbody").adopt(new Element('tr').adopt(new Element('td',{'colspan':2,'class':'c1'}))).adopt(new Element('tr').adopt(new Element('td',{'class':'c2',"html":"<a href='#'></a>"})).adopt(new Element('td',{'class':'c3','html':"<p class='c4'></p>"}))))).injectTop($('dis-module-x')).addClass('hide');
	}
	else var prew = firstTable.getParent().clone(true);
	if (!thePreviewSender) {
		thePreviewSender = new Request({'url':"/ajaxing.php?do=preview",
			'method':"post",
			'onRequest':function(){
				if(!document.getElementById('preview_table')){
					firstTable = $('dis-module-x').getElement('table').getParent().removeClass('hide');
					prew.getElements('tr').erase('id');
					if (prew.getElement('tr').getNext().getFirst().hasClass('cspt')){
						prew.getElement('tr').getNext().dispose();
					}
					prew.getElement('tr').getNext().getElement('td').getElement('a').dispose();
					prew.setProperty('id','preview_table').getElement('p').empty().removeClass("unr");

					if (!firstTable) {
						prew.injectTop($('dis-module-x'));
					}
					prew.injectBefore(firstTable);
				}
				$("preview_table").getElement('td').set('html','Náhled příspěvku: Načítání ...');
			},
			'onComplete':function(txt,xml){
				prvTbl = $("preview_table");
				prvTbl.getElement('td').set('html','Náhled příspěvku');
				prvTbl.getElement('p').set('html',txt);
			},
			'onFailure':function(){
				if (document.getElementById('preview_table')) {
					prvTbl = $("preview_table");
					prvTbl.getElement('td').set('html','Náhled příspěvku: Chyba');
					prvTbl.getElement('p').set('html',"Načítání náhledu selhalo!<br /><br />Patrně je chyba ve spojení se serverem Aragorn.cz");
				}
			}
		});
	}
	thePreviewSender.send({'data':{'txt':$(id).get('value')}});
}

function do_previewP(id){
	if (!thePreviewPSender) {
		thePreviewPSender = new Request({'url':"/ajaxing.php?do=preview",
			'method':"post",
			'onRequest':function(){
				if(!document.getElementById('preview_table')){
					new Element("div",{'html':"<table class='commtb' cellspacing='0' id='preview_table' cellpadding='0'><tr><td class='c1' colspan='2'></td></tr><tr><td class='c2'></td><td class='c3'><p class='c4'></p></td></tr></table>"}).injectAfter($('zprava'));
				}
				$("preview_table").getElement('td').set('html','Náhled zprávy: Načítání ...');
			},
			'onComplete':function(txt,xml){
				prevTable = $("preview_table");
				prevTable.getElement('td').set('html','Náhled zprávy');
				prevTable.getElement('p').set('html',txt);
			},
			'onFailure':function(){
				if (document.getElementById('preview_table')) {
					prevTable = $("preview_table");
					prevTable.getElement('td').set('html','Náhled zprávy: Chyba');
					prevTable.getElement('p').set('html',"Načítání náhledu zprávy selhalo!<br /><br />Patrně je chyba ve spojení se serverem Aragorn.cz");
				}
			}
		});
	}
	thePreviewPSender.send({'data':{'txt':$(id).get('value')}});
}

function comm_del(){
pole = [];
cn=0;
ids = $$("#dis-module-x input[type=checkbox]");
for(i=0,idsl = ids.length; i < idsl; i++){
	if (ids[i].checked == true && ids[i].value != "") {
		pole[cn] = ids[i].value;
		cn++;
	}
}
if (pole.length > 0){
	var konc = "ek";
	doVymaz = false;
	if (pole.length > 1) konc = "ky";
	if (pole.length > 4) konc = "ků";

	if (pole.length < 2) doVymaz = true;
	else if (confirm('Opravdu smazat '+pole.length+' příspěv'+konc+' ???')) doVymaz = true;

	if (doVymaz) {
		vymaz = pole.join(",");
		window.location.assign(urlPartDiskuze+(urlPartDiskuze.indexOf("?") == -1?'?':'&')+"akce=comm-delete&ids="+vymaz);
	}
	else {
		return false;
	}
}else{
	alert("Žádné vybrané položky ke smazání");
}

}

function rep(to){
$('zprava').removeClass('hide');
document.getElementById('to').value = to;
document.getElementById('km').focus();
return false;
}

function rep2(toP){
$('zprava').removeClass('hide');
var to = $('to');
to.setProperty('value',to.getProperty('value').clean());

if ($type(toP) != "string") {
	toP = $(toP).get('text');
}
if (to.getProperty('value') != ""){
	toP = ", "+toP;
	to.setProperty('value',to.getProperty('value')+toP);
}
else {
	to.setProperty('value',toP);
}
document.getElementById('km').focus();
return false;
}

function post_del(){
pole=[];ids=$$('#postolka-all input[type=checkbox]');idsl=ids.length;cn=0;
for(i=0;i<idsl;i++){
	if(ids[i].checked && ids[i].value != ""){
		pole[cn]=ids[i].value;
		cn++;
	}
}
if(pole.length>0){
	vymaz=pole.join(",");
	window.location.href = urlPartPosta+"/?akce=postolka-delete&ids="+vymaz;
}else{
	alert("Žádné vybrané zprávy ke smazání");
}
}

function PostolkaMaker() {

	var t='#postolka-all ';
	var tpr0 = $$(t+'.pr-0').setProperty('title','Nepřečteno').set('text',' ');
	var tpr1 = $$(t+'.pr-1').setProperty('title','Přečteno').set('text',' ');
	var tpr2 = $$(t+'.pr-2').setProperty('title','Smazáno').set('text',' ');
	var tpr3 = $$(t+'.pr-3').setProperty('title','Smazáno').set('text',' ');
	var tprX = $$(t+'.m-in-unr').setProperty('title','Počet nepřečtených příchozích zpráv');
	$$(t+'#dis-module-x .c2 span').addClass('post-status');

	var GiveThemTips = new Tips($$(tpr0,tpr1,tpr2,tpr3,tprX), {
		'className':'tool-tip',fixed:true,offsets:{x:15,y:25},showDelay:0,hideDelay:0
	});
	var TRs = $$('#vypis-out TR','#vypis-in TR').each(function(t){
		t.lastChild.className='pd';
		t.lastChild.previousSibling.className='pt';
		t.lastChild.previousSibling.previousSibling.className='po';
		t.firstChild.nextSibling.className='pp';
		t.addEvent('mouseout',function(){this.removeClass('postolka-hlight')});
		t.addEvent('mouseover',function(){this.addClass('postolka-hlight')});
		t.addEvent('click',	function(e){var t;if(!e)var e=window.event;if(e.target){t=e.target;}else if(e.srcElement){t=e.srcElement;}if(t.nodeType==3)t=t.parentNode;if(t.tagName.toLowerCase()=="input"||t.tagName.toLowerCase()=="a"){return;}while(t.parentNode&&t.tagName.toUpperCase()!= "TR"){t=t.parentNode;}var q=t.getElementsByTagName("INPUT");for(var qw=0;qw<q.length;qw++){q[qw].checked=!q[qw].checked;}});
	});
	
	if (!Browser.Engine.trident4 && Browser.Features.xhr) {

		var ajaxCommSubmiter = function(event){
			var t,prev,prevId,trgtDiv,qq;
			t = $(event.target);
			if (t.get('tag') == 'a') {
				prev = t.getPrevious();
				prevId = 'tmp_'+prev.getProperty('href').replace('/posta/in/','').replace('/posta/out/','');
				prevId = prevId.replace(/\/posta\/konverzace\/([^\?]{1,})\?/g,'').replace('p=','').replace(/\&index=([\w]{1,})$/g,'').replace(/\//g,'') + '_view';
				if (document.getElementById(prevId)){
					prevId = $(prevId);
					if (prevId.hasClass('opened')) {
						prevId.removeClass('opened').tween('height',0);
					}
					else {
						qq = prevId.removeClass('hide').getFirst().getHeight();prevId.addClass('opened').tween('height',qq);
					}
				}
				else {
					qq = prev;
					while(qq.get('tag') != 'tr' && qq.parentNode){
						qq = qq.getParent();
					}
					trgtDiv = new Element('tr',{
						styles:{
							'overflow':'hidden','height':'auto'
						}
					}).adopt(
						new Element('td',{
							colspan:5,styles:{
								'height':'auto','overflow':'hidden','padding':'0'
							}
						}).adopt(
							new Element('div',{
								'id':prevId,'styles':{'padding':'0','position':'relative','height':'0','overflow':'hidden'}
							}).set('tween',{
								'duration':500,'link':'cancel','onComplete':function(){
									if(this.element.offsetHeight<2){this.element.getParent().tween('height',0);this.element.addClass('hide');}
								},
								'onStart':function(){
									if(this.element.hasClass('hide')){this.element.removeClass('hide').getParent().tween('height',this.element.getFirst().getHeight());}
								}
							}).adopt(
								new Element('div',{
									'class':'postolkaPreviewText','styles':{'position':'absolute'}
								}).adopt(
									new Element('p',{'class':'c4','text':'Načítám...'})
								)
							)
						)
					).injectAfter(qq);
					trgtDiv = trgtDiv.getElement('div');
					qq = trgtDiv;
					trgtDiv = trgtDiv.getFirst();
					qq.tween('height',trgtDiv.getHeight())

					trgtDiv.set('load',{
						onSuccess:function(rTree,rEls,rHTML,rJS){
							this.set('html',rHTML);this.getParent().addClass('opened').tween('height',this.getHeight());
						}.bind(trgtDiv)
					});

					qq = prevId.replace('tmp_','').replace('_view','');
					trgtDiv.load('/ajaxing.php?do=postolka&num='+qq);
				}
			}
			event.stop();
			return false;
		};

		var q = new Element('a',{'class':'permalink2 nahled','href':'#','text':'Náhled','styles':{'fontSize':'80%','lineHeight':'150%','float':'right'}});
		q.addEvent('click',ajaxCommSubmiter.bindWithEvent(q));
		$$('#vypis-all a.r').each(function(el){
			q.clone(true).cloneEvents(q,'click').injectAfter(el);
		});
	}
}

function readIt(t){
	var q,e;
	e = $('unr_link_'+t) || false;
	if (e) {
		q = new Request({
			'url':'/ajaxing.php?do=postolka&read=1&num='+t,
			'method':'get',
			'onRequest':function(){
				this.removeEvent('click').addEvent('click',function(){return false;}).set('text','Zpracovávám...');
			}.bind(e.getElement('a')),
			'onSuccess':function(txt,xml){
				var a;
				txt = $('unr_link_'+txt) || false;
				if (txt) {
					txt.set('text','Status zprávy změněn na přečteno.').setStyle('overflow','hidden');
					a = txt;
					while(a.get('tag') != 'tr') {
						a = a.getParent();
					}
					a.getPrevious().getFirst().set({'class':'pr-1','title':'Přečteno'}).store('tip:title','Přečteno');
					txt.set('morph',{
						duration:2000,
						transition:'quad:in',
						onComplete:function(){
							var aa;
							aa = this.element.getParent().getParent();
							this.element.dispose();
							aa.tween('height',aa.getFirst().getHeight());
						}
					}).morph({'opacity':0,'height':0,'padding-top':0,'padding-bottom':0});
				}
			}
		});
		q.send();
	}
	return false;
}

function zaskrtnout(t){
	$$('#'+t+' input').each(function(el){el.checked=!el.checked;});
}

Element.Properties.disabled = {
 
    get: function(){
        return this.disabled;
    },
 
    set: function(value){
        this.disabled = !!value;
        this.setAttribute('disabled', !!value);
    }
 
};

function doAjaxSendForm(el,uri){
	el = $(el);
	el.getElements('input[type=submit]').setProperty('disabled',false);
	el.set('send',{
		'url':el.getProperty('action')+'&ajaxed=1',
		'onRequest':function(){
			form_ajax_loader.setStyles({'opacity':1,'top':0}).getElement("p").set("text",'Odesílám ...');
		},
		'onSuccess':function(txt,xml){
			if (txt == "off") {
				alert("Již nejste na serveru Aragorn.cz jako online uživatel.\n\nDoporučujeme otevřít nové okno, přihlásit se a odeslat tento příspěvek znovu.");
			}
			else if (txt == "--" || txt == "-") {
				alert('Neznámá chyba s odesláním příspěvku.');
				window.location.reload();
				return;
			}
			else if (txt == "right") {
				txt = "Nemáte práva na odeslání příspěvku.";
			}
			else if (txt == "text") {
				txt = "Příspěvek musí obsahovat nějaký text.";
			}
			else {
				txt = "Příspěvek odeslán.";
				this.reset();
			}
			var comms = $A(xml.getElementsByTagName('comm'));
			setTimeout(function(){
				form_ajax_loader.setStyle('top','100%');
			},2000);
			if (comms) {
				form_ajax_loader.getElement('p').set('html',txt);
				var disModule = $('dis-module-x').getElement('p');
				if (disModule && comms.length > 0) {
					if (document.getElementById('preview_table')){
						disModule = $('preview_table');
						if (disModule.getNext() && disModule.getNext().get('tag') == 'table' && disModule.getParent().get('tag') == 'div'){
							disModule = disModule.getParent();
						}
					}
					for (var a=0,cl=comms.length;a<cl;a++) {
						var elmnt = new Element("div",{'html':comms[a].firstChild.data}).inject(disModule,'after');
						disModule = elmnt;
					}
				}
			}
		}.bind(el),
		'onFailure':function(){
			alert('AJAX odeslání příspěvku selhalo na neznámé chybě.\n\nObnovuji stránku...');
			window.location.reload();
		}
	}).send();
	
}

var form_ajax_loader;

function disModuleAjaxSend(frm){
	var urlHere = window.location.pathname;
	if (urlHere.length > 1 && urlHere.indexOf("/",2)>0) {
		frm = frm.getElement('form');
		if (frm) {
			frm.setStyles({'position':'relative','top':0,'left':0,'overflow':'hidden'}).addEvent('submit',function(event){new Event(event).stop();doAjaxSendForm(this,urlHere);return false;});
			var sizes = frm.getSize();
			form_ajax_loader = new Element("div",{
				'class':'FormAjaxLoader t-a-c',
				'id':'form_ajax_loader',
				'styles':{
					'fontSize':'30px',
					'position':'absolute',
					'top':'100%','left':'0',
					'display':'block',
					'width':'100%',
					'height':'100%'
				}
			}).inject(frm,'bottom').adopt(new Element('div',{
				'styles':{
					'position':'absolute','top':0,'left':0,'height':'100%','width':'100%',
					'backgroundColor':'#000000','opacity':0.75
				}
			}),new Element('p',{'text':'Odesílám ...','class':'t-a-c','styles':{'color':'#ffffff','position':'absolute','padding':'0 0 32px 0','background':'url("/graphic/ajax-loader.gif") center bottom no-repeat','left':'0','width':'100%','top':'30px','lineHeight':'40px'}}));
		}
	}
}

function toplistMaker(){
	$('toplist_link_footer').adopt(new Element('img',{src:'http://toplist.cz/dot.asp?id=40769&wi='+escape(screen.width)+'&he='+escape(screen.height)+'&cd='+escape(window.screen.colorDepth)+'&t='+escape(document.title)+'&http='+escape(document.referrer)+'&_t='+$time(),width:1,height:1,border:0,alt:'TOPlist'}));
}

document.addEvent('domready', function(){
	$$('div.menu a').addEvent('click', function() {
		if (this.get('id') !== 'marked') {
			$('marked').erase('id');
			this.set('id', 'marked');
		}
	});
});


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


//Chrome Drop Down Menu- Author: Dynamic Drive (http://www.dynamicdrive.com)
//Last updated: Jan 1st, 06'

var cssdropdown={
disappeardelay:500,dropmenuobj:null,ie:document.all,firefox:document.getElementById&&!document.all,
getposOffset:function(what,offsettype){
var totaloffset=(offsettype=="left")?what.offsetLeft:what.offsetTop;
var parentEl=what.offsetParent;
while(parentEl!=null){
totaloffset=(offsettype=="left")?totaloffset+parentEl.offsetLeft:totaloffset+parentEl.offsetTop;
parentEl=parentEl.offsetParent;
}
return totaloffset;
},
showhide:function(obj,e,visible,hidden){
if(this.ie||this.firefox){this.dropmenuobj.style.left=this.dropmenuobj.style.top="-500px";}
if(e.type=="click"&&obj.visibility==hidden||e.type=="mouseover"){obj.visibility=visible;}
else if(e.type=="click"){obj.visibility=hidden;}
},
iecompattest:function(){
return (document.compatMode&&document.compatMode!="BackCompat")?document.documentElement:document.body;
},

clearbrowseredge:function(obj, whichedge){
var edgeoffset=0;
if(whichedge=="rightedge"){
var windowedge=this.ie&&!window.opera?this.iecompattest().scrollLeft+this.iecompattest().clientWidth-15:window.pageXOffset+window.innerWidth-15;
this.dropmenuobj.contentmeasure=this.dropmenuobj.offsetWidth;
if(windowedge-this.dropmenuobj.x<this.dropmenuobj.contentmeasure){edgeoffset=this.dropmenuobj.contentmeasure-obj.offsetWidth;}
}
else{
var topedge=((this.ie&&!window.opera)?this.iecompattest().scrollTop:window.pageYOffset);
var windowedge=(this.ie&&!window.opera)?this.iecompattest().scrollTop+this.iecompattest().clientHeight-15:window.pageYOffset+window.innerHeight-18;
this.dropmenuobj.contentmeasure=this.dropmenuobj.offsetHeight;
if(windowedge-this.dropmenuobj.y<this.dropmenuobj.contentmeasure){edgeoffset=this.dropmenuobj.contentmeasure+obj.offsetHeight;if((this.dropmenuobj.y-topedge)<this.dropmenuobj.contentmeasure){edgeoffset=this.dropmenuobj.y+obj.offsetHeight-topedge;}}
}
return edgeoffset;
},

dropit:function(obj,e,dropmenuID){
if(this.dropmenuobj!=null){this.dropmenuobj.style.visibility="hidden";}
this.clearhidemenu();
if(this.ie||this.firefox){
obj.onmouseout=function(){cssdropdown.delayhidemenu()};
this.dropmenuobj=document.getElementById(dropmenuID);
this.dropmenuobj.onmouseover=function(){cssdropdown.clearhidemenu()};
this.dropmenuobj.onmouseout=function(){cssdropdown.dynamichide(e)};
this.dropmenuobj.onclick=function(){cssdropdown.delayhidemenu()};
this.showhide(this.dropmenuobj.style, e,"visible","hidden");
this.dropmenuobj.x=this.getposOffset(obj,"left");
this.dropmenuobj.y=this.getposOffset(obj,"top");
this.dropmenuobj.style.left=this.dropmenuobj.x-this.clearbrowseredge(obj,"rightedge")+"px";
this.dropmenuobj.style.top=this.dropmenuobj.y-this.clearbrowseredge(obj,"bottomedge")+obj.offsetHeight+1+"px";
}
},

contains_firefox:function(a,b){
while(b.parentNode){if((b=b.parentNode)==a){return true;}}
return false;
},

dynamichide:function(e){
var evtobj=window.event?window.event:e;
if(this.ie&&!this.dropmenuobj.contains(evtobj.toElement)){this.delayhidemenu();}
else if(this.firefox&&e.currentTarget!=evtobj.relatedTarget&&!this.contains_firefox(evtobj.currentTarget,evtobj.relatedTarget)){this.delayhidemenu();}
},

delayhidemenu:function(){
this.delayhide=setTimeout("cssdropdown.dropmenuobj.style.visibility='hidden'",this.disappeardelay);
},

clearhidemenu:function(){if(this.delayhide!="undefined"){clearTimeout(this.delayhide);}}
}


/*
 * A JavaScript implementation of the RSA Data Security, Inc. MD5 Message
 * Digest Algorithm, as defined in RFC 1321.
 * Version 2.1 Copyright (C) Paul Johnston 1999 - 2002.
 * Other contributors: Greg Holt, Andrew Kepert, Ydnar, Lostinet
 * Distributed under the BSD License
 * See http://pajhome.org.uk/crypt/md5 for more info.
 */

var hexcase = 0;  /* hex output format. 0 - lowercase; 1 - uppercase        */
var b64pad  = ""; /* base-64 pad character. "=" for strict RFC compliance   */
var chrsz   = 8;  /* bits per input character. 8 - ASCII; 16 - Unicode      */

function hex_md5(s){ return binl2hex(core_md5(str2binl(s), s.length * chrsz));}
function b64_md5(s){ return binl2b64(core_md5(str2binl(s), s.length * chrsz));}
function str_md5(s){ return binl2str(core_md5(str2binl(s), s.length * chrsz));}
function hex_hmac_md5(key, data) { return binl2hex(core_hmac_md5(key, data)); }
function b64_hmac_md5(key, data) { return binl2b64(core_hmac_md5(key, data)); }
function str_hmac_md5(key, data) { return binl2str(core_hmac_md5(key, data)); }

function core_md5(x, len)
{
  x[len >> 5] |= 0x80 << ((len) % 32);
  x[(((len + 64) >>> 9) << 4) + 14] = len;

  var a =  1732584193;
  var b = -271733879;
  var c = -1732584194;
  var d =  271733878;

  for(var i = 0; i < x.length; i += 16)
  {
    var olda = a;
    var oldb = b;
    var oldc = c;
    var oldd = d;

    a = md5_ff(a, b, c, d, x[i+ 0], 7 , -680876936);
    d = md5_ff(d, a, b, c, x[i+ 1], 12, -389564586);
    c = md5_ff(c, d, a, b, x[i+ 2], 17,  606105819);
    b = md5_ff(b, c, d, a, x[i+ 3], 22, -1044525330);
    a = md5_ff(a, b, c, d, x[i+ 4], 7 , -176418897);
    d = md5_ff(d, a, b, c, x[i+ 5], 12,  1200080426);
    c = md5_ff(c, d, a, b, x[i+ 6], 17, -1473231341);
    b = md5_ff(b, c, d, a, x[i+ 7], 22, -45705983);
    a = md5_ff(a, b, c, d, x[i+ 8], 7 ,  1770035416);
    d = md5_ff(d, a, b, c, x[i+ 9], 12, -1958414417);
    c = md5_ff(c, d, a, b, x[i+10], 17, -42063);
    b = md5_ff(b, c, d, a, x[i+11], 22, -1990404162);
    a = md5_ff(a, b, c, d, x[i+12], 7 ,  1804603682);
    d = md5_ff(d, a, b, c, x[i+13], 12, -40341101);
    c = md5_ff(c, d, a, b, x[i+14], 17, -1502002290);
    b = md5_ff(b, c, d, a, x[i+15], 22,  1236535329);

    a = md5_gg(a, b, c, d, x[i+ 1], 5 , -165796510);
    d = md5_gg(d, a, b, c, x[i+ 6], 9 , -1069501632);
    c = md5_gg(c, d, a, b, x[i+11], 14,  643717713);
    b = md5_gg(b, c, d, a, x[i+ 0], 20, -373897302);
    a = md5_gg(a, b, c, d, x[i+ 5], 5 , -701558691);
    d = md5_gg(d, a, b, c, x[i+10], 9 ,  38016083);
    c = md5_gg(c, d, a, b, x[i+15], 14, -660478335);
    b = md5_gg(b, c, d, a, x[i+ 4], 20, -405537848);
    a = md5_gg(a, b, c, d, x[i+ 9], 5 ,  568446438);
    d = md5_gg(d, a, b, c, x[i+14], 9 , -1019803690);
    c = md5_gg(c, d, a, b, x[i+ 3], 14, -187363961);
    b = md5_gg(b, c, d, a, x[i+ 8], 20,  1163531501);
    a = md5_gg(a, b, c, d, x[i+13], 5 , -1444681467);
    d = md5_gg(d, a, b, c, x[i+ 2], 9 , -51403784);
    c = md5_gg(c, d, a, b, x[i+ 7], 14,  1735328473);
    b = md5_gg(b, c, d, a, x[i+12], 20, -1926607734);

    a = md5_hh(a, b, c, d, x[i+ 5], 4 , -378558);
    d = md5_hh(d, a, b, c, x[i+ 8], 11, -2022574463);
    c = md5_hh(c, d, a, b, x[i+11], 16,  1839030562);
    b = md5_hh(b, c, d, a, x[i+14], 23, -35309556);
    a = md5_hh(a, b, c, d, x[i+ 1], 4 , -1530992060);
    d = md5_hh(d, a, b, c, x[i+ 4], 11,  1272893353);
    c = md5_hh(c, d, a, b, x[i+ 7], 16, -155497632);
    b = md5_hh(b, c, d, a, x[i+10], 23, -1094730640);
    a = md5_hh(a, b, c, d, x[i+13], 4 ,  681279174);
    d = md5_hh(d, a, b, c, x[i+ 0], 11, -358537222);
    c = md5_hh(c, d, a, b, x[i+ 3], 16, -722521979);
    b = md5_hh(b, c, d, a, x[i+ 6], 23,  76029189);
    a = md5_hh(a, b, c, d, x[i+ 9], 4 , -640364487);
    d = md5_hh(d, a, b, c, x[i+12], 11, -421815835);
    c = md5_hh(c, d, a, b, x[i+15], 16,  530742520);
    b = md5_hh(b, c, d, a, x[i+ 2], 23, -995338651);

    a = md5_ii(a, b, c, d, x[i+ 0], 6 , -198630844);
    d = md5_ii(d, a, b, c, x[i+ 7], 10,  1126891415);
    c = md5_ii(c, d, a, b, x[i+14], 15, -1416354905);
    b = md5_ii(b, c, d, a, x[i+ 5], 21, -57434055);
    a = md5_ii(a, b, c, d, x[i+12], 6 ,  1700485571);
    d = md5_ii(d, a, b, c, x[i+ 3], 10, -1894986606);
    c = md5_ii(c, d, a, b, x[i+10], 15, -1051523);
    b = md5_ii(b, c, d, a, x[i+ 1], 21, -2054922799);
    a = md5_ii(a, b, c, d, x[i+ 8], 6 ,  1873313359);
    d = md5_ii(d, a, b, c, x[i+15], 10, -30611744);
    c = md5_ii(c, d, a, b, x[i+ 6], 15, -1560198380);
    b = md5_ii(b, c, d, a, x[i+13], 21,  1309151649);
    a = md5_ii(a, b, c, d, x[i+ 4], 6 , -145523070);
    d = md5_ii(d, a, b, c, x[i+11], 10, -1120210379);
    c = md5_ii(c, d, a, b, x[i+ 2], 15,  718787259);
    b = md5_ii(b, c, d, a, x[i+ 9], 21, -343485551);

    a = safe_add(a, olda);
    b = safe_add(b, oldb);
    c = safe_add(c, oldc);
    d = safe_add(d, oldd);
  }
  return Array(a, b, c, d);

}

function md5_cmn(q, a, b, x, s, t)
{
  return safe_add(bit_rol(safe_add(safe_add(a, q), safe_add(x, t)), s),b);
}
function md5_ff(a, b, c, d, x, s, t)
{
  return md5_cmn((b & c) | ((~b) & d), a, b, x, s, t);
}
function md5_gg(a, b, c, d, x, s, t)
{
  return md5_cmn((b & d) | (c & (~d)), a, b, x, s, t);
}
function md5_hh(a, b, c, d, x, s, t)
{
  return md5_cmn(b ^ c ^ d, a, b, x, s, t);
}
function md5_ii(a, b, c, d, x, s, t)
{
  return md5_cmn(c ^ (b | (~d)), a, b, x, s, t);
}

function core_hmac_md5(key, data)
{
  var bkey = str2binl(key);
  if(bkey.length > 16) bkey = core_md5(bkey, key.length * chrsz);

  var ipad = Array(16), opad = Array(16);
  for(var i = 0; i < 16; i++)
  {
    ipad[i] = bkey[i] ^ 0x36363636;
    opad[i] = bkey[i] ^ 0x5C5C5C5C;
  }

  var hash = core_md5(ipad.concat(str2binl(data)), 512 + data.length * chrsz);
  return core_md5(opad.concat(hash), 512 + 128);
}

function safe_add(x, y)
{
  var lsw = (x & 0xFFFF) + (y & 0xFFFF);
  var msw = (x >> 16) + (y >> 16) + (lsw >> 16);
  return (msw << 16) | (lsw & 0xFFFF);
}
function bit_rol(num, cnt)
{
  return (num << cnt) | (num >>> (32 - cnt));
}

function str2binl(str)
{
  var bin = Array();
  var mask = (1 << chrsz) - 1;
  for(var i = 0; i < str.length * chrsz; i += chrsz)
    bin[i>>5] |= (str.charCodeAt(i / chrsz) & mask) << (i%32);
  return bin;
}

function binl2str(bin)
{
  var str = "";
  var mask = (1 << chrsz) - 1;
  for(var i = 0; i < bin.length * 32; i += chrsz)
    str += String.fromCharCode((bin[i>>5] >>> (i % 32)) & mask);
  return str;
}

function binl2hex(binarray)
{
  var hex_tab = hexcase ? "0123456789ABCDEF" : "0123456789abcdef";
  var str = "";
  for(var i = 0; i < binarray.length * 4; i++)
  {
    str += hex_tab.charAt((binarray[i>>2] >> ((i%4)*8+4)) & 0xF) +
           hex_tab.charAt((binarray[i>>2] >> ((i%4)*8  )) & 0xF);
  }
  return str;
}

function binl2b64(binarray)
{
  var tab = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/";
  var str = "";
  for(var i = 0; i < binarray.length * 4; i += 3)
  {
    var triplet = (((binarray[i   >> 2] >> 8 * ( i   %4)) & 0xFF) << 16)
                | (((binarray[i+1 >> 2] >> 8 * ((i+1)%4)) & 0xFF) << 8 )
                |  ((binarray[i+2 >> 2] >> 8 * ((i+2)%4)) & 0xFF);
    for(var j = 0; j < 4; j++)
    {
      if(i * 8 + j * 6 > binarray.length * 32) str += b64pad;
      else str += tab.charAt((triplet >> 6*(3-j)) & 0x3F);
    }
  }
  return str;
}

function _to_utf8(s) {
  var c, d = "";
  for (var i = 0; i < s.length; i++) {
    c = s.charCodeAt(i);
    if (c <= 0x7f) {
      d += s.charAt(i);
    } else if (c >= 0x80 && c <= 0x7ff) {
      d += String.fromCharCode(((c >> 6) & 0x1f) | 0xc0);
      d += String.fromCharCode((c & 0x3f) | 0x80);
    } else {
      d += String.fromCharCode((c >> 12) | 0xe0);
      d += String.fromCharCode(((c >> 6) & 0x3f) | 0x80);
      d += String.fromCharCode((c & 0x3f) | 0x80);
    }
  }
  return d;
} 

var loginFormElement = null;

function getChallenge(t){
	loginFormElement = t;
	t = $(t);
	t.getElements('input').setProperty('readonly', 'readonly');
	t.getElement('input[type=submit]').addClass('ajaxBgLoader');
	new Asset.javascript('/ajaxing.php?challenge',{evalScript:true,onload:function(){md5form();}});
	return false;
}

function md5form(f) {
	if (!f) f = loginFormElement;
	f['password_hmac'].disabled = false;
	f['password_hmac'].value = hex_hmac_md5(hex_md5(_to_utf8(f['pass'].value)), f['challenge'].value);
	f['pass'].disabled = true;
	f.submit();
	f['pass'].disabled = false;
	f['password_hmac'].disabled = true;
	return false;
}


