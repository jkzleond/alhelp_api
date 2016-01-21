<extend name="Base/common"/>
<block name="style">
	<link rel="stylesheet" type="text/css" href="__CSS__/user_center.css"/>
	<style type="text/css">
	.main{background-color:#EEEEEF;padding:20px 0;}
	.msg{background-color:#fff;padding:140px 0;text-align:center;font-size:14px;color:#333;}
	.error,.success{font-family:微软雅黑,新宋体;font-size:16px;font-weight:bold;height:68px;line-height:68px;color:#BF0000;
	      background:url(__IMG__/bg_register_sucess.png) no-repeat;
		  width:380px;margin:0 auto;margin-bottom:10px;
			padding-left: 70px;
	}
	.error{
	      background-position:0 -108px;	
	}
	.success{
	      background-position:left top;		
	}
	.success{color:#c2be00}
	.jump{color:#666;}
	.jump span{color:#BF0000;}
	.jump a{color:#BF0000;}
	.jump a:hover{text-decoration:none;}
	.footer{margin:0;}
	</style>
</block>

<block name="body">
<div class="register_wrap">
	<div class="container_1210 p_relative msg">
		<div class="system-message">
			<present name="message">
			<p class="success"><?php echo($message); ?></p>
			<else/>
			<p class="error"><?php echo($error); ?></p>
			</present>
			<p class="jump">
				页面将在 <span id="wait"><?php echo($waitSecond); ?></span> 秒后自动 <a id="href" href="<?php echo($jumpUrl); ?>">跳 转</a>
			</p>
		</div>
		<script type="text/javascript">
		(function(){
		var wait = document.getElementById('wait'),href = document.getElementById('href').href;
		var interval = setInterval(function(){
			var time = --wait.innerHTML;
			if(time <= 0) {
				location.href = href;
				clearInterval(interval);
			};
		}, 1000);
		})();
		</script>
	</div>
</div>
</block>
