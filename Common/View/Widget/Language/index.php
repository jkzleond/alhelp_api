<select name="language_id" class="select_common" id="language">
	<option value="" location=''>请选择语言</option>
<?php
	foreach($result as $row){
?>
<option value="<?=$row['id']?>" location='<?=U($action,array_merge($parameter,array('language_id'=>$row['id'])))?>'><?=$row['title']?></option>
<?php
	}
?>
</select>
<script>
	$(function(){
		$('#language').val('<?=$language_id?>');
		
        $('#language').change(function(){
            var location=$(this).find("option:selected").attr("location");
            if(location){
                window.location =location;
            }
        })
    })
    
</script>