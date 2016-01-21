<tr>
    <td class="right_text" width="100">
        上传主图：
        <span class="chicun">（尺寸:400*400）</span>
    </td>
    <td>
        <span class="js_image_list suolue left m_right10 cover">
            <img src="__IMG__/eq_suolue.png" width="100" height="100">
            <b class="zz_add js_div">设置主图</b>
            <input type="hidden" name="cover" class="cover_form" value="<?=$info['cover']?>" />
        </span>
    </td>
</tr>
<tr>
    <td class="right_text" width="100">
        上传缩略图片：
        <span class="chicun">（尺寸:310*210）</span>
    </td>
    <td>
        <ul class="load_list load_hotel_list cf">
            
            <li>
                <span class="xc_delete js_xc_delete"></span>
                <span class="imgWrap"><img src="__IMG__/eq/eq_hotel3.jpg"></span>
                <input type="text" class="txt_hotel" value="图片说明" autocomplete="off">
            </li>
            <li>
                <span class="xc_delete js_xc_delete"></span>
                <span class="imgWrap"><img src="__IMG__/eq/eq_hotel3.jpg"></span>
                <input type="text" class="txt_hotel" value="图片说明" autocomplete="off">
            </li>
            <li>
                <span class="xc_delete js_xc_delete"></span>
                <span class="imgWrap"><img src="__IMG__/eq/eq_hotel3.jpg"></span>
                <input type="text" class="txt_hotel" value="图片说明" autocomplete="off">
            </li>
            <li>
                <span class="xc_delete js_xc_delete"></span>
                <span class="imgWrap"><img src="__IMG__/eq/eq_hotel3.jpg"></span>
                <input type="text" class="txt_hotel" value="图片说明" autocomplete="off">
            </li>

        </ul>
        <input type="button" class="btn_fukian" value="上传潜水图片">
    </td>
</tr>
 <tr>
    <td class="right_text" width="100">
        上传图片：
        <span class="chicun">（尺寸:400*400）</span>
    </td>
    <td>
        <div class="cf">
            <div class="left center_text">
                <span class="suolue js_image_list image_1">
                    <img src="__IMG__/eq_suolue.png" width="100" height="100">
                    <b class="zz_add js_div">设置主图</b> 
                    <input type="hidden" name="image_1" value="<?=$info['image_1']?>" class="form_image1">
                </span>
                <input type="button" class="btn_fukian js_div" value="上传图片">
            </div>
            <div style="width:480px; padding-right:2px;" class="right textarea_wrap">
                <textarea placeholder="发布您想显示的公告" rows="5" id="jq_container_canyin"></textarea>
            </div>
        </div>
    </td>
</tr>