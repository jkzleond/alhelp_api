<!--日历-->
<!---有设置价格，就取出价格--->

<div class="js_calendar"></div>
 <div class="js_calendar_2"></div>
<script>

/**
*日历价格返回值
* json  内容返回值， clone_list  clone() 方法,克隆
**/
var jsonlist=''; //old
var product_id={$product_id};
function timeprice(price_id){ 
  $('.js_calendar').fullCalendar('refetchEvents'); //重新获取所有事件数据	
}


//价格日历绑定样式
/**
*  eventSources[{  events [{id:可以使用传递参数, start:来确定日历日期，title：日历每天下面内容  }]     }]
**/
$(".js_calendar").fullCalendar({
	theme:true,
	dayNamesShort:['周日','周一', '周二', '周三','周四', '周五', '周六'],	
	dayClick: function(date, allDay, jsEvent, view , calEvent) {
	//天点击
	showRoomSet();
},
eventClick: function(calEvent, jsEvent, view) {
	//事件点击
	//点击弹出房间设置框
	//参数在此 id,start...
	showRoomSet(calEvent.id);
},
eventMouseover: function(calEvent, jsEvent, view) {
	//事件鼠标移入
	// id 为 写入事件时的id
	var data_date =  calEvent.id; // 获取id，这个值是对应当天时间的data-date属性
	$("[data-date=" + data_date +"]").addClass("canlendHover");
},
eventMouseout: function(calEvent, jsEvent, view) {
	//事件鼠标移入
	// id 为 写入事件时的id
	var data_date =  calEvent.id; // 获取id，这个值是对应当天时间的data-date属性
	$("[data-date=" + data_date +"]").removeClass("canlendHover");
},
buttonText:{
	prev:     '上月',
	next:     '下月',
	today:    '今天'
},
header:{
	left:   '',
	center: 'title',
	right:  'prev,today,next'
},
editable: false,
events: '{:U("Seller/Product/get_price_json")}?product_id='+product_id
});


//秒杀日历重新绑定样式
function time_calendar_2_price(){ 
  $('.js_calendar_2').fullCalendar('refetchEvents'); //重新获取所有事件数据	
}

//秒杀日历绑定样式
$(".js_calendar_2").fullCalendar({
theme:true,
dayNamesShort:['周日','周一', '周二', '周三','周四', '周五', '周六'],
dayClick: function(date, allDay, jsEvent, view , calEvent) {
	var selDate =$.fullCalendar.formatDate(date,'yyyy-MM-dd');
	//天点击
	showKillSet(selDate);
},
eventClick: function(calEvent, jsEvent, view) {
	//事件点击
	//点击弹出房间设置框

	showKillSet(calEvent.id);
},
eventMouseover: function(calEvent, jsEvent, view) {
	//事件鼠标移入
	// id 为 写入事件时的id
	var data_date =  calEvent.id; // 获取id，这个值是对应当天时间的data-date属性
	$("[data-date=" + data_date +"]").addClass("canlendHover");
},
eventMouseout: function(calEvent, jsEvent, view) {
	//事件鼠标移入
	// id 为 写入事件时的id
	var data_date =  calEvent.id; // 获取id，这个值是对应当天时间的data-date属性
	$("[data-date=" + data_date +"]").removeClass("canlendHover");
},
buttonText:{
	prev:     '上月',
	next:     '下月',
	today:    '今天'
},
header:{
	left:   '',
	center: 'title',
	right:  'prev,today,next'
},
editable: false,
events: '{:U("Seller/Product/get_seckill_json")}?product_id='+product_id
}); 	
	//秒杀日历加载完后先隐藏
$(".js_calendar_2").hide();


//弹出设置窗口,弹出价格设置窗口
function showRoomSet(id){
	//var url = "/Seller/Product/diving_setprice/id/"+id;
	var url = '{:U("Seller/Product/diving_setprice")}?id='+id;
	
	
	//弹窗插件
	art.dialog.open(url,{
		title: '价格设置',
		fixed:true,
		lock:true,
		width:'1000px',
		height:'560px',
		padding:'0px'
	});
} 	


//弹出设置窗口,弹出价格设置窗口
function showKillSet(selDate){

var url = '{:U("Seller/Product/diving_seckill")}?date='+selDate;
	//var url = "diving_seckill?date="+selDate; 
	
	//弹窗插件
	art.dialog.open(url,{
		title: '秒杀价格设置',
		fixed:true,
		lock:true,
		width:'1000px',
		height:'510px',
		padding:'0px'
	});
}
</script>