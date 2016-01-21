<select name="currency_code" id="currency" class="select_common">
	<option value="">请选择币种</option>
<?php
	foreach($result as $row){
?>
<option value="<?=$row['code']?>"><?=$row['code']?></option>
<?php
	}
?>
</select>
<script>
	$(function(){
		$('#currency').val('<?=$currency?>');
		
    })
    
</script>