<script src="__STATIC__/uploaify/jquery.uploadify.min.js" type="text/javascript"></script>
<link rel="stylesheet" type="text/css" href="__STATIC__/uploaify/uploadify.css">
<script>
  $(function(){
      var url="{:U('')}";
      var obj="{$obj}";
      if(obj.length>1){
      $("#"+obj).uploadify({
                'swf'      : '__STATIC__/uploaify/uploadify.swf',
		'uploader' : url,
            });
       }
     });
</script>