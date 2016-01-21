<div class="fn-clear selectes">
    <style>
      select{margin: 5px 0;}
    </style>
    专业类型：
    <select name="profes_type" id="profes_type"> 
	<?php
    foreach($profession_type as $key=>$val)
	{
		?>
        <option value="{$key}" <?php if($type==$key){ echo "selected";  } ?> >{$val}</option>
		<?php 
	}
		?>
    </select><br />
    <span class="profes_type_select" id="profes_type_1" style="display:none" >
    地 区: <select name='city' id='city' >
<option value='0'>--请选择--</option></select>
大 学: <select name='university' id='university' style='max-width:150px' >
<option value='0'>--请选择--</option>
</select>
学 院: <select name='college' id='college' style='max-width:150px'>
<option value='0'>--请选择--</option>
</select><br />
<span id='major_subject_c'>
专 业:  <select  name='major' id='major' style='max-width:150px'>
<option value='0'>--请选择--</option>
</select></span>
<input type='hidden' name='major_code' value='{$major_code}' id='major_code' />
    </span>
    
    <span  class="profes_type_select" id="profes_type_2">
    分 类: <select name="major_classify" id="major_classify" style="max-width:150px">
    <option value="0">--请选择--</option>
    </select>
    
    专 业: <select name="major_subject" id="major_subject" style="max-width:150px">
    <option value="0">--请选择--</option></select>
    
    </span>


<span class="profes_type_select" id="profes_type_3" style="display:none" >
专 业: <select name="open_class_major_subject" id="open_class_major_subject"  style="max-width:150px">';
<option value="0" >--请选择--</option>
</select>
</span>


<span class="profes_type_select" id="profes_type_4" style="display:none">
学科门类: <select name='subject_1' id='subject_1' >
<option value='0'>--请选择--</option>
</select>
一级学科: <select name='subject_2' id='subject_2' style='max-width:150px' >
<option value='0'>--请选择--</option>
</select>
二级学科: <select name='subject_3' id='subject_3' style='max-width:150px'>
<option value='0'>--请选择--</option>
</select>

    </span>
    
</div>
<script src="/Home/JsData/city.js"></script>
<script src="/Home/JsData/university.js"></script>

<!-- 统考 -->
<script src="/Home/JsData/unified_classify.js"></script>
<script src="/Home/JsData/unified.js"></script>


<!-- 公共课 -->
<script src="/Home/JsData/open_class.js"></script>

<!-- 专业圈 -->
<script src="/Home/JsData/subject_array.js"></script>
<script src="/Home/JsData/degree_course_array.js"></script>
<script src="/Home/JsData/subject_profession_array.js"></script>
<script>  
function major_classify_change()
{
	var pid = $("#major_classify").val();
	var array = unified_array;
	console.log(array);
	$('#major_subject').html("<option value='0'>--请选择--</option>");
	for(var id in array)
	{
		if(pid == array[id].cid)
		{
			$("#major_subject").append("<option value='"+id+"'>"+array[id].cname+"</option>");  //添加一项option
		}
	}  
}

function profes_type_change()
{
	$(".profes_type_select").css("display","none");
	var val = $("#profes_type").val();
	console.log("profes_type_change " + val);
	if(val==1)
	{
		$("#profes_type_1").show();
		var array = city_array;
		console.log(array);
		$('#city').html("<option value='0'>--请选择--</option>");
		for(var id in array)
		{
			$("#city").append("<option value='"+id+"'>"+array[id].title+"</option>");  //添加一项option
        }   
	}
	else if(val==2)
	{
		$("#profes_type_2").show();
		var array = unified_classify_array;
		console.log(array);
		$('#major_classify').html("<option value='0'>--请选择--</option>");
		for(var id in array)
		{
			$("#major_classify").append("<option value='"+id+"'>"+array[id].title+"</option>");  //添加一项option
        }   
	}
	else if(val==3)
	{
		$("#profes_type_3").show();
		var array = open_class_array;
		console.log(array);
		$('#open_class_major_subject').html("<option value='0'>--请选择--</option>");
		for(var id in array)
		{
			$("#open_class_major_subject").append("<option value='"+id+"'>"+array[id].title+"</option>");  //添加一项option
        }  
		
		
	}else if(val==4)
	{
		$("#profes_type_4").show();
		var array = subject_array;
		console.log(array);
		$('#subject_1').html("<option value='0'>--请选择--</option>");
		for(var id in array)
		{
			$("#subject_1").append("<option value='"+id+"'>"+array[id]+"</option>");  //添加一项option
        }  
	}
}
function subject_1_change()
{
	var array = degree_course_array;
	var pid = $("#subject_1").val();
	
	if(1 == pid.length)
	{
		pid = "0" + pid;
	}
	
	console.log("subject_1: " + pid);
	$('#subject_2').html("<option value='0'>--请选择--</option>");
	for(var id in array)
	{
		if(pid == array[id].category)
		{
			$("#subject_2").append("<option value='"+array[id].code+"'>"+array[id].title+"</option>");  //添加一项option
		}
	} 
}
function subject_2_change()
{
	var array = subject_profession_array;

	var pid = $("#subject_2").val();
	
	
	console.log("subject_2: " + pid);
	$('#subject_3').html("<option value='0'>--请选择--</option>");
	for(var id in array)
	{
		if(!array[id].code)
		{
			continue;
		}
		if(pid == array[id].code.substr(0,4).toLowerCase())
		{
			$("#subject_3").append("<option value='"+id+"'>"+array[id].title+"</option>");  //添加一项option
		}
	} 
}
function subject_3_change()
{
	
}
function city_change()
{
	var array = university_array;
	var pid = $("#city").val();
	$('#university').html("<option value='0'>--请选择--</option>");
	$('#college').html("<option value='0'>--请选择--</option>");
	$('#major').html("<option value='0'>--请选择--</option>");
	for(var id in array)
	{
		if(pid == array[id].pid)
		{
			$("#university").append("<option value='"+id+"'>"+array[id].title+"</option>");  //添加一项option
		}
	}   
}

function university_change()
{
	
	try
	{
		var pid = $("#university").val();
		var array = get_college(pid);
		$('#college').html("<option value='0'>--请选择--</option>");
		$('#major').html("<option value='0'>--请选择--</option>");
		for(var id in array)
		{
			if(pid == array[id].pid)
			{
				$("#college").append("<option value='"+id+"'>"+array[id].title+"</option>");  //添加一项option
			}
		}
	}
	catch(e)
	{
		<?php
		if(isdebug())
		{
			?>
			alert($);
			alert("university_change catch" + e.name  +   " :  "   +  e.message);
			<?php
		}
		?>
	}
}

function college_change()
{
	
	try
	{
		var pid = $("#college").val();
		
		var array = get_profession(pid);

		console.log(array);
		$('#major').html("<option value='0'>--请选择--</option>");
		for(var id in array)
		{
			if(pid == array[id].pid)
			{
				$("#major").append("<option value='"+id+"'>"+array[id].title+"</option>");  //添加一项option
			}
		} 
	
		 
	}
	catch(e)
	{
	}
	
}

var type="{$type}";//专业类型

$(document).ready(function()
{
	
	
	$('#profes_type').on('change',$(this),function()
	{
		profes_type_change();
	});
	
	$('#city').on('change',$(this),function()
	{
		city_change();
	});
	$('#university').on('change',$(this),function()
	{
		university_change();
	});
	
	$('#college').on('change',$(this),function()
	{
		college_change();
	});
	
	$('#major_classify').on('change',$(this),function()
	{
		major_classify_change();
	});
	
	
	$('#subject_1').on('change',$(this),function()
	{
		subject_1_change();
	});
	
	$('#subject_2').on('change',$(this),function()
	{
		subject_2_change();
	});
	
	$('#subject_3').on('change',$(this),function()
	{
		subject_3_change();
	});
	
	profes_type_change();
	
	if(1 == type)
	{
		$("#city").val({$city});
		city_change();
		
		$("#university").val({$university});
		university_change();
		
		$("#college").val({$college});
		college_change();
		
		$("#major").val({$major});
	}
	else if(4 == type)
	{
		$("#subject_1").val("{$city}");
		subject_1_change();
		$("#subject_2").val("{$university}");
		subject_2_change();
		$("#subject_3").val("{$college}");
		subject_3_change();
	}
	
	
	
	
	
});

</script>