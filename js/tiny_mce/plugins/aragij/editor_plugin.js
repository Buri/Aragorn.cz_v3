(function(){tinymce.create('tinymce.plugins.aragijPlugin',{init:function(ed,url){ed.addCommand('mcearagij',function(){ed.windowManager.open({file:url+'/dialog.htm',width:320+parseInt(ed.getLang('aragij.delta_width',0)),height:150+parseInt(ed.getLang('aragij.delta_height',0)),inline:1},{plugin_url:url,some_custom_arg:'custom arg'})});ed.addButton('aragij',{title:'Vložit šablonu deníku',cmd:'mcearagij',image:url+'/img/journal.gif'});ed.onNodeChange.add(function(ed,cm,n){cm.setActive('aragij',n.nodeName=='IMG')})},createControl:function(n,cm){return null},getInfo:function(){return{longname:'aragij plugin',author:'Some author',authorurl:'http://tinymce.moxiecode.com',infourl:'http://wiki.moxiecode.com/index.php/TinyMCE:Plugins/aragij',version:"1.0"}}});tinymce.PluginManager.add('aragij',tinymce.plugins.aragijPlugin)})();
