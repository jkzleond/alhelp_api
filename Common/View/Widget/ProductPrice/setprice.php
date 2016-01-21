<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>酒店价格设置弹出窗</title>
<!--公共样式-->
<link rel="stylesheet" type="text/css" href="__CSS__/common.css">
<!--公共js-->
<script type="text/javascript" src="__JS__/jquery-1.7.2.js"></script>
<script type="text/javascript" src="__JS__/myFrame.js"></script>
<script type="text/javascript" src="__JS__/setprice.js"></script>
<!--本页样式-->
<link rel="stylesheet" type="text/css" href="__CSS__/product_pub.css"/>

<!--本页js-->
<!--日历-->
<script type="text/javascript" src="__JS__/plugin/My97DatePicker/WdatePicker.js"></script>
<style>
	html,body{width:1000px; background:white;}
</style>
</head>

<body>
    <div class="dialog_wrap">
        <div class="container_dia cf">
            <table class="table_roomPrice left" style="width:500px;">
                <tbody>
                	<tr>
                        <td width="90" class="right_text">历史最高价：</td>
                        <td width="410">
                            <label>房间：<span class="c_ff6600">121</span>&nbsp;UID</label>
                            <label>加床：<span class="c_ff6600">100</span>&nbsp;UID</label>
                        </td>
                    </tr>
                     <tr>
                        <td width="90" class="right_text" style="vertical-align:top;">优惠：</td>
                        <td>
                            <label>房间：<font class="c_ff6600">11</font>&nbsp;USD</label>
                            <label>加床：<font class="c_ff6600">5</font>&nbsp;USD</label>
                            <span class="room_tip">（此优惠价格将在前台产品页面显示，优惠=历史最高成交价-您输入的价格)</span>
                        </td>
                    </tr>
                    <tr>
                        <td width="90" class="right_text">价格：</td>
                        <td>
                            <label>房间：<input type="text" placeholder="设定单价" value="" size="7" class="input_text_2 " autocomplete="off" style="padding:4px;"></label>
                            <label>加床：<input type="text" placeholder="设定单价" value="" size="7" class="input_text_2 " autocomplete="off" style="padding:4px;"></label>
                            <select class="select_common">
                                <option>USD</option>
                                <option>UED</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td>可销售数量：</td>
                        <td>
                            <label>房间：<input type="text" class="input_text_2 " value="" size="4" style="padding:4px;"></label>
                            <span>已售<font class="c_0fdecf">351</font>&nbsp;&nbsp;</span>
                            <label>加床：<input type="text" class="input_text_2 " value="" size="4" style="padding:4px;"></label>
                            <span>已售<font class="c_0fdecf">351</font></span>
                        </td>
                    </tr>
                    <tr>
                        <td style="vertical-align:top;" width="90" class="right_text">起止时间：</td>
                        <td>
                        	<!--这里的id和js有关，不要轻易改动-->
                            <p class="cf"><span class="left">房间：</span>
                                <input type="text" style="padding:4px;" size="10"  class="input_text_2 left jq_date_1" id="d4311" > - 
                                <input type="text" style="padding:4px;" size="10" class="input_text_2 jq_date_2" id="d4312">
                            </p>
                            <p class="cf" style="margin-top:10px;"><span class="left">加床：</span>
                                <input type="text" style="padding:4px;" size="10" class="input_text_2 left jq_date_3" id="d4313"> - 
                                <input type="text" style="padding:4px;" size="10" class="input_text_2 jq_date_4" id="d4314">
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td style="vertical-align:top;" class="right_text">产品类型：</td>
                        <td class="js_product_style">
                            <p>
                                <label>
                                    <input type="radio" name="trade_type" checked="checked" class="js_tuangou">团购交易
                                </label>
                                团购最低数量：<input type="text" class="input_text_2" style="padding:2px 4px;width:50px;text-align:center;" value="10" />
                            </p>
                            <p style="margin-top:10px;">
                                <label>
                                    <input type="radio" name="trade_type" class="js_yijia"/>议价交易
                                </label>
                                <font class="room_tip m_top5">（选择议价交易，需要跟买家达成一致价格。）</font>
                            </p>
                            <p style="margin-top:10px;">
                                <label>
                                    <input type="radio" name="trade_type" class="js_qita"/>其他
                                     <font class="room_tip m_top5">(如果您的产品既不属于团购，也不可进行议价，请选择此项）</font>
                                </label>
                            </p>
                        </td>
                    </tr>
                </tbody>
            </table>
            <!--右边表格-->
            <table class="table_roomPrice left" style="width:460px;">
                <tbody>
                    <tr>
                        <td width="90" valign="top" class="right_text">付款模式：</td>
                        <td class="js_pay_style">
                            <p>
                                <label>
                                    <input type="radio" name="trade_type_pay_1" class="js_danbao" checked="checked">担保交易
                                </label>
                               <span class="room_tip">订单使用 <select class="select_common js_danbao_option" style="padding:0 5px;">
                                    <option value="10">10</option>
                                    <option value="15">15</option>
                                    <option value="30">30</option>
                                    <option value="60">60</option>
                                    <option value="90">90</option>
                                    <option value="180">180</option>
                                </select> 天后，如无操作将自动结算</span>
                            </p>
                            <p class="m_top10">
                                <label>
                                    <input type="radio" name="trade_type_pay_1" class="js_jishi">即时付款
                                </label>
                               <span class="room_tip">(买家付款后钱会立即在您的微银帐户显示)</span>
                            </p>
                             <p class="m_top10">
                                <label>
                                    <input type="radio" name="trade_type_pay_1" class="js_yuyue" disabled="disabled">网上预约
                                </label>
                               <span class="room_tip">(买家到达后再付款)</span>
                               <label class="room_tip d_none js_cacel" ><input type="checkbox"/>买家可免费取消订单</label>
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td style="vertical-align:top;" class="right_text">
                             供应商确认：
                        </td>
                        <td>
                            <label>
                                <input type="checkbox" name="order_confirm" class="js_confirm">需要您确认订单
                            </label>
                            <span class="room_tip">从订单产生时间起，如果您不确认订单，24小时内将会自动取消</span>
                        </td>
                    </tr>
                    <tr>
                        <td valign="top" class="right_text">其他条件：</td>
                        <td class="js_other_condition">
                            <p>
                                <span><label><input type="radio" name="cacel" checked="checked" />订单不可取消</label></span>
                                <span>
                                    <label><input type="radio" name="cacel" />订单可取消</label>
                                    <label class="font_1">使用前 <input type="text" size="4" class="input_text_2" style="padding:4px;" /> 天可取消</label>
                                </span>
                            </p>
                            <p class="m_top10">
                                <span><label><input type="radio" name="tuikuan" checked="checked" />不可申请退款</label></span>
                                <span>
                                    <label><input type="radio" name="tuikuan" />可申请退款</label>
                                    <label class="font_1"><input type="text" size="4" class="input_text_2" style="padding:4px;" /> 天内可申请</label>
                                </span>
                                
                            </p>
                             <p class="m_top10">
                                <span>
                                    <label><input type="radio" name="edit"  checked="checked" />订单不可修改</label>
                                </span>
                                <span><label><input type="radio" name="edit"/>订单可修改</label></span>
                            </p>
                            <p class="m_top10">
                                <span>
                                    <label><input type="radio" name="price"  checked="checked"/>非特价</label>
                                </span>
                                <span>
                                    <label><input type="radio" name="price"/>特价促销</label>
                                    <label class="font_1">促销名称：<input type="text" class="input_text_2" size="10"/></label>
                                </span>
                            </p>
                        </td>
                        <td style="display:none;">
                            <div class="trade_pro">
                                <label>
                                    <input type="radio" name="trade_type" >特价促销
                                </label>
                                <label>
                                    <input type="checkbox" name="trade_type_child[]" >不可退款
                                </label>
                                <label>
                                    <input type="checkbox" name="trade_type_child[]" >不可修改
                                </label>
                            </div>
                            <div class="trade_ref" style="margin-top:5px;">
                                <label>
                                    <input type="radio" name="trade_type" >即时付款
                                </label>
                                <label>
                                    <input type="checkbox" name="trade_type_child[]" >不可撤销
                                </label>
                                <label>
                                    <input type="checkbox" name="trade_type_child[]" >可修改订单
                                </label>
                            </div>
                            <div style="line-height:23px;padding:0 10px;background:#FFF0C4;color:#DB8131;">
                                订单使用5天后将会自动结算
                            </div>
                        </td>
                    </tr>
                    
                    <tr>
                        <td valign="top" width="80" class="right_text">附加条件：</td>
                        <td>
                            <p><label class="f_12"><input type="checkbox"/>是否需要上传证书执照？</label></p>
                            <p><label class="f_12"><input type="checkbox"/>是否需要客人提供酒店地址，联系方式。以上资料通过关联订单的，站内联系方式，提交供应商以备落实订单。</label></p> 
                        </td>
                    </tr>
                    
                </tbody>
            </table>
        </div>
        <ul class="conpon_style js_conpon_style">
            <li class="conpon_cur js_conpon_use"><label><input type="checkbox" class="js_use_checkbox"/>抵扣券使用</label></li>
            <li class="js_conpon_send"><label><input type="checkbox" class="js_send_checkbox"/>抵扣券发放(店铺)</label></li>
            <li class="js_conpon_send"><label><input type="checkbox" class="js_send_checkbox"/>抵扣券发放(平台通用)</label></li>
        </ul>
        <div class="js_table">
            <table class="table_roomPrice coupons js_coupons center_text">
                <caption></caption>
                <thead>
                    <tr>
                        <th align="center" width="40">使用</th>
                        <th width="100" align="center">抵扣次数</th>
                        <th style="border-right:#999 solid 1px;">使用规则</th>
                        <th align="center" width="40">使用</th>
                        <th width="100">抵扣次数</th>
                        <th>使用规则</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><input type="checkbox" name="room_biaoqian" value="10"></td>
                        <td>
                            <input type="text" class="input_text_2" style="width:60px;padding:2px 4px;text-align:center;" value="10" /> 
                        </td>
                        <td style="border-right:#999 solid 1px;">
                            满 <input type="text" class="input_text_2" style="padding:2px 4px;width:80px;text-align:center;" value="10" /> 元可以抵扣
                            <input type="text" class="input_text_2" style="padding:2px 4px;width:80px;text-align:center;" value="10" /> 元
                        </td>
        
                        <td align="center"><input type="checkbox" name="room_biaoqian" value="20"></td>
                        <td>
                            <input type="text" class="input_text_2" style="width:60px;padding:2px 4px;text-align:center;" value="10" /> 
                        </td>
                        <td>
                            满 <input type="text" class="input_text_2" style="padding:2px 4px;width:80px;text-align:center;" value="10" /> 元可以抵扣
                            <input type="text" class="input_text_2" style="padding:2px 4px;width:80px;text-align:center;" value="10" /> 元
                        </td>
                    </tr>
                    <tr>
                        <td><input type="checkbox" name="room_biaoqian" value="10"></td>
                        <td>
                            <input type="text" class="input_text_2" style="width:60px;padding:2px 4px;text-align:center;" value="10" /> 
                        </td>
                        <td style="border-right:#999 solid 1px;">
                            满 <input type="text" class="input_text_2" style="padding:2px 4px;width:80px;text-align:center;" value="10" /> 元可以抵扣
                            <input type="text" class="input_text_2" style="padding:2px 4px;width:80px;text-align:center;" value="10" /> 元
                        </td>
        
                        <td align="center"><input type="checkbox" name="room_biaoqian" value="20"></td>
                        <td>
                            <input type="text" class="input_text_2" style="width:60px;padding:2px 4px;text-align:center;" value="10" /> 
                        </td>
                        <td>
                            满 <input type="text" class="input_text_2" style="padding:2px 4px;width:80px;text-align:center;" value="10" /> 元可以抵扣
                            <input type="text" class="input_text_2" style="padding:2px 4px;width:80px;text-align:center;" value="10" /> 元
                        </td>
                    </tr>
                </tbody>
            </table>
            <table class="table_roomPrice coupons js_coupons d_none">
                <caption></caption>
                <thead>
                    <tr>
                        <th align="center" width="40">发放</th>
                        <th width="100">类型</th>
                        <th width="70" align="center">发放数量</th>
                        <th width="170">发放规则</th>
                        <th style="border-right:#999 solid 1px;">有效期</th></th>
        
                        <th align="center" width="40">发放</th>
                        <th width="100">类型</th>
                        <th width="70" align="center">发放数量</th>
                        <th width="170">发放规则</th>
                        <th>有效期</th>
                    </tr>
                </thead>

                <tbody>
                    <tr>
                        <td align="center"><input type="checkbox" name="room_biaoqian" value="10"></td>
                        <td><input type="text" class="input_text_2" style="width:60px;padding:2px 4px;text-align:center;"/> 元券</td>
                        <td>
                            <input type="text" class="input_text_2" style="width:70px;padding:2px 4px;text-align:center;"/> 
                        </td>
                        <td>
                            满 <input type="text" class="input_text_2" style="padding:2px 4px;width:50px;text-align:center;"/> 元发放抵扣券
                        </td>
                        <td style="border-right:#999 solid 1px;">
                            <input type="text" class="input_text_2" style="width:75px;padding:2px 4px;text-align:center;"/> 
                        </td>
        
                        <td align="center"><input type="checkbox" name="room_biaoqian" value="20"></td>
                        <td><input type="text" class="input_text_2" style="width:60px;padding:2px 4px;text-align:center;"/> 元券</td>
                        <td>
                            <input type="text" class="input_text_2" style="width:70px;padding:2px 4px;text-align:center;"/> 
                        </td>
                        <td>
                            满 <input type="text" class="input_text_2" style="padding:2px 4px;width:50px;text-align:center;"/> 元发放抵扣券
                        </td>
                        <td>
                            <input type="text" class="input_text_2" style="width:75px;padding:2px 4px;text-align:center;"/> 
                        </td>
                    </tr>
                    <tr>
                        <td align="center"><input type="checkbox" name="room_biaoqian" value="30"></td>
                        <td><input type="text" class="input_text_2" style="width:60px;padding:2px 4px;text-align:center;"/> 元券</td>
                        <td>
                            <input type="text" class="input_text_2" style="width:70px;padding:2px 4px;text-align:center;"/> 
                        </td>
                        <td>
                            满 <input type="text" class="input_text_2" style="padding:2px 4px;width:50px;text-align:center;"/> 元发放抵扣券
                        </td>
                        <td style="border-right:#999 solid 1px;">
                            <input type="text" class="input_text_2" style="width:75px;padding:2px 4px;text-align:center;"/> 
                        </td>
        
                        <td align="center"><input type="checkbox" name="room_biaoqian" value="40"></td>
                        <td><input type="text" class="input_text_2" style="width:60px;padding:2px 4px;text-align:center;"/> 元券</td>
                        <td>
                            <input type="text" class="input_text_2" style="width:70px;padding:2px 4px;text-align:center;"/> 
                        </td>
                        <td>
                            满 <input type="text" class="input_text_2" style="padding:2px 4px;width:50px;text-align:center;"/> 元发放抵扣券
                        </td>
                        <td>
                            <input type="text" class="input_text_2" style="width:75px;padding:2px 4px;text-align:center;"/> 
                        </td>
                    </tr>
                </tbody>
            </table>
            <table class="table_roomPrice coupons js_coupons d_none">
                <caption></caption>
                <thead>
                    <tr>
                        <th align="center" width="40">使用</th>
                        <th width="100">类型</th>
                        <th width="100" align="center">发放数量</th>
                        <th style="border-right:#999 solid 1px;">发放规则</th>
        
                        <th align="center" width="40">发放</th>
                        <th width="100">类型</th>
                        <th width="100" align="center">发放数量</th>
                        <th>发放规则</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td align="center"><input type="checkbox" name="room_biaoqian" value="10"></td>
                        <td>平台10元券</td>
                        <td>
                            <input type="text" class="input_text_2" style="width:100px;padding:2px 4px;text-align:center;" value="10" /> 
                        </td>
                        <td style="border-right:#999 solid 1px;">
                            满 <input type="text" class="input_text_2" style="padding:2px 4px;width:50px;text-align:center;" value="10" /> 元可以发放
                        </td>
        
                        <td align="center"><input type="checkbox" name="room_biaoqian" value="20"></td>
                        <td>平台20元券</td>
                        <td>
                            <input type="text" class="input_text_2" style="width:100px;padding:2px 4px;text-align:center;" value="10" /> 
                        </td>
                        <td>
                            满 <input type="text" class="input_text_2" style="padding:2px 4px;width:50px;text-align:center;" value="10" /> 元可以使用发放
                        </td>
                    </tr>
                    <tr>
                        <td align="center"><input type="checkbox" name="room_biaoqian" value="30"></td>
                        <td>平台30元券</td>
                        <td>
                            <input type="text" class="input_text_2" style="width:100px;padding:2px 4px;text-align:center;" value="10" /> 
                        </td>
                        <td style="border-right:#999 solid 1px;">
                            满 <input type="text" class="input_text_2" style="padding:2px 4px;width:50px;text-align:center;" value="10" /> 元可以使用发放
                        </td>
        
                        <td align="center"><input type="checkbox" name="room_biaoqian" value="40"></td>
                        <td>平台40元券</td>
                        <td>
                            <input type="text" class="input_text_2" style="width:100px;padding:2px 4px;text-align:center;" value="10" /> 
                        </td>
                        <td>
                            满 <input type="text" class="input_text_2" style="padding:2px 4px;width:50px;text-align:center;" value="10" /> 元可以使用发放
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div style="padding:10px;">
            <input type="submit" style="padding:6px 25px;margin-right:10px;" class="btn btn_green" value="确定">
            <!-- 产品的属性，是，客户购买后，不需要付款，单生成订单，供应商看到后确认，供应商确认以后，客户订单状态改为付款，付款完成后，订单确定，其他按标准流程走，但平台对这个确认时间应该有个时限不超过24小时 -->
        </div>
    </div>
</body>
</html>