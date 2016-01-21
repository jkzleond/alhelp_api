<?php
	if(false){
?>
<script type="text/javascript" src="__STATIC__/ueditor/ueditor.config.js"></script>
<script type="text/javascript" src="__STATIC__/ueditor/ueditor.all.js"></script>
<script type="text/javascript" charset="utf-8" src="__STATIC__/ueditor/lang/zh-cn/zh-cn.js"></script>
<script id="<?=$editor_id?>" type="text/plain" name="content" style="width:630px;height:200px;"><?=$content?></script>
<script type="text/javascript">
UE.getEditor('<?=$editor_id?>',{
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
	<?=$editor_id?>.ready(function(){
	    <?=$editor_id?>.execCommand('insertImage',{
			src:url
		});
	});
}
</script>
<?php
	}
?>

<!-- 配置文件 -->
<script type="text/javascript" src="__STATIC__/ueditor1/ueditor.config.js"></script>
<!-- 编辑器源码文件 -->
<script type="text/javascript" src="__STATIC__/ueditor1/ueditor.all.js"></script>
<!-- 语言包文件(建议手动加载语言包，避免在ie下，因为加载语言失败导致编辑器加载失败) -->
<script type="text/javascript" src="__STATIC__/ueditor1/lang/zh-cn/zh-cn.js"></script>

<script id="container" name="content" type="text/plain" style="height:300px"><?=$content?></script>
<script type="text/javascript">
	var editor = UE.getEditor('container')
</script>