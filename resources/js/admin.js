// Generated by CoffeeScript 1.6.1
(function(){(function(e){return e.fn.cnp_media_uploader=function(t){var n,r,i,s;r={title:"Upload/Select Image",button:"Select Image",type:"image",multiple:!1,select:function(e){}};s=e.extend(r,t||{});n=e(this);i=void 0;return n.on("click",function(e){e.preventDefault();i!=null&&i.close();i=wp.media.frames.customHeader=wp.media({title:s.title,library:{type:s.type},button:{text:s.button},multiple:!1});i.on("select",function(){return s.select(i.state().get("selection").map(function(e){return e.toJSON()}))});return i.open()})}})(jQuery)}).call(this);