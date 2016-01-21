<div class="cf js_position">
<?php
if($result){
    //如果存在的话就循环显示经纬度的数据
    foreach ($result as $row) {
?>
	<div class="left">
        经度：<input type="text" name="longitude[]" value="<?=$row['longitude']?>" class="input_text_2" size="15" autocomplete="off"> 
        纬度：<input type="text" name="altitude[]" value="<?=$row['altitude']?>" class="input_text_2" size="15" autocomplete="off">
        <input type="button" value="添加" class="btn btn_green" style="padding:10px 10px;"/>
    </div>
     <a href="#this" class="right js_delete_position" style="font-size:12px;">×删除</a>
<?php
    }
}else{
    //不存在就显示添加信息
?>
    <div class="left">
        经度：<input type="text" name="longitude[]" value="经度" class="input_text_2" size="15" autocomplete="off"> 
        纬度：<input type="text" name="altitude[]" value="纬度" class="input_text_2" size="15" autocomplete="off">
        <input type="button" value="添加" class="btn btn_green" style="padding:10px 10px;"/>
    </div>
<?php
}
?>
    <!--添加更多位置,克隆用-->
    <div class="cf js_position_div d_none" style="margin-top:5px;">
        <div class="left">
            经度：<input type="text" value="经度" class="input_text_2 longitude" size="15" autocomplete="off"> 
            纬度：<input type="text" value="纬度" class="input_text_2 altitude" size="15" autocomplete="off">
            <input type="button" value="添加" class="btn btn_green" style="padding:10px 10px;"/>
        </div>
        <a href="#this" class="right js_delete_position" style="display:none;font-size:12px;">×删除</a>
    </div>
    <a href="#this" class="right js_more_position" data-url="add_position">+添加更多位置</a>
</div>
<div class="map_product">
    <img src="__IMG__/map.png">
</div>