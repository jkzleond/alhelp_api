<script>
var continent_json=<?=$continent_json?>;
var country_json=<?=$country_json?>;
var city_json=<?=$city_json?>;
var location_id_json=<?=$location_id?>;
</script>

<?php
	/* 使用示例 */
	if(false){
?>
<div>
	<select class="continent">
		<option value=''>请选择洲</option>
	</select>
	<select class="country">
		<option value=''>请选择国家</option>
	</select>
	<select class="city">
		<option value=''>请选择城市</option>
	</select>
</div>
<script>
	for(var p in continent_json){  
		var html='<option value="'+p+'">'+p+'</option>';
		$('.continent').append(html);
	}

	$('.continent').change(function(){
		$(this).parent().find('.country option:gt(0)').remove();
		$(this).parent().find('.city option:gt(0)').remove();

		var val=$(this).val();
		var countrys=country_json[val];
		for(var p in countrys){  
			var val=countrys[p];
			var html='<option value="'+val+'">'+val+'</option>';
			$(this).parent().find('.country').append(html);
		}
	});

	$('.country').change(function(){
		$(this).parent().find('.city option:gt(0)').remove();

		var val=$(this).val();
		var citys=city_json[val];
		for(var p in citys){  
			var val=citys[p];
			var html='<option value="'+val+'">'+val+'</option>';
			$(this).parent().find('.city').append(html);
		}
	});
</script>
<?php
	}
?>
