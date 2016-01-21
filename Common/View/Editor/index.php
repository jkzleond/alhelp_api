<script type="text/javascript" charset="utf-8" src="__STATIC__/ueditor/ueditor.config.js"></script>
<script type="text/javascript" charset="utf-8" src="__STATIC__/ueditor/ueditor.all.js"></script>
<script type="text/javascript" charset="utf-8" src="__STATIC__/ueditor/lang/zh-cn/zh-cn.js"></script>
<script id="editor" type="text/plain" name="content" style="width:1024px;height:500px;"><?=$info['content']?></script>
<script type="text/javascript">
var editor=UE.getEditor('[id]',{
	imageUrl:"<?=U('Common/Editor/imageUp')?>",//图片上传提交地址
	scrawlUrl:"<?=U('Common/Editor/scrawlUp')?>",//涂鸦上传地址
	fileUrl:"<?=U('Common/Editor/fileUp')?>",//附件上传提交地址
	catcherUrl:"<?=U('Common/Editor/getRemoteImage')?>",//处理远程图片抓取的地址
	imageManagerUrl:"<?=U('Common/Editor/imageManager')?>",//图片在线管理的处理地址
	snapscreenServerUrl:"<?=U('Common/Editor/imageUp')?>",//屏幕截图的server端保存程序
	wordImageUrl:"<?=U('Common/Editor/imageUp')?>",//word转存提交地址
	
	imageDelUrl:"<?=U('Common/Editor/delFile')?>",//图片删除处理地址
});

function insertImage(url){
	editor.ready(function(){
	    editor.execCommand('insertImage',{
			src:url
		});
	});
}
</script>
<div id="js_image_list"></div>