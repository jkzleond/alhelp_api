<div class="fn-clear selectes">
    <style>
      select{margin: 5px 0;}
    </style>
    专业类型：
    <select name="profes_type" id="profes_type"> 
        <?php foreach($profession_type as $key=>$val){ ?>
        <option value="{$key}" <?php if($type==$key){ echo "selected";  } ?> >{$val}</option>
        <?php } ?>
		 <option value="{$key+1}" <?php if($type==$key+1){ echo "selected";  } ?> >其他</option>
    </select><br />
    <span id="showpro" >
    
    </span>
</div> 
<!--<script src="__STATIC__/city.js"></script>
<script src="__STATIC__/college.js"></script>
<script src="__STATIC__/major.js"></script>
<script src="__STATIC__/university.js"></script>-->
<script>  
 function getschool(val,type){
        result=null;
        if(!!val && val>0){
            var data={id:val,type:type};
            $.ajax({
               type:'post',
               url:"{:U('Home/Common/get_next_school')}",
               data: data,
               dataType:'json',
               async: false, 
               success:function(data){
                  result=data.info;
               }
            });
        }
        return result;
}
function getcity(data,obj,val){
            $.each(data,function(n,om){
              var  html="<option value='"+om.id+"'";
               if(val==om.id){
                   html+='selected';
               }
               html+=">"+om.title+"</option>";
               obj.append(html);
            });
    }
    function getnext(data,obj,pid,val){
            if(!!data && data.length>0){
            $.each(data,function(n,om){
                path = om.path.split('.');
              if($.inArray(pid,path)!=-1){
              var  html="<option value='"+om.id+"'  data_code='"+om.code+"' ";
               if(val==om.id){
                   html+='selected';
               }
               html+=">"+om.title+"</option>";
               obj.append(html);
              }
            });
           }
    }
    function get_other_next(data,obj,val){
            $.each(data,function(n,om){
              var  html="<option value='"+n+"'";
               if(val==n){
                   html+='selected';
               }
               html+=">"+om+"</option>";
               obj.append(html);
            });
    }

        var type="{$type}";
          html = "地 区: <select name='city' id='city' >";
          html+="<option value='0'>--请选择--</option>";
          html+=" </select>";
          html+="大 学: <select name='university' id='university' style='max-width:150px' >";
          html+="<option value='0'>--请选择--</option>";
          html+="</select>";
          html+="学 院: <select name='college' id='college' style='max-width:150px'>";
          html+="<option value='0'>--请选择--</option>";
          html+="</select><br />";
        
          
       var html1= '专 业: <select name="major" id="major"  style="max-width:150px">';
           html1+='<option value="0" >--请选择--</option>';
           html1+='</select>'; 
           
        var html3= '专 业: <select name="major_subject"  id="major_subject"  style="max-width:150px">';
           html3+='<option value="0" >--请选择--</option>';
           html3+='</select>';  
         var html2= '分 类: <select name="major_classify" id="major_classify"  style="max-width:150px">';
           html2+='<option value="0" >--请选择--</option>';
           html2+='</select>'; 		   
          json_city = {$_city};
//          json_university = {$_university};
//          json_college = {$_college};
//          json_major = {$_major}; 
        
        
        if(type==1){
            //先获取城市
			$('#showpro').html(html);
			   getcity(json_city,$('#city'),"{$city}");
			   var city="{$city}";
			   var university="{$university}";
			   var college = "{$college}";
			    var major="{$major}";
				if(city>0){
				   var  json_university =getschool(city,2);
				   getnext(json_university,$('#university'),"{$city}","{$university}");
				}
				if(university>0){
					 var  json_college =getschool(university,3);
					getnext(json_college,$('#college'),"{$university}","{$college}");
				}
				if(college>0){
					var  json_major =getschool(college,4);
					getnext(json_major,$('#major'),"{$college}","{$major}");
				}
                          
//			  getnext(json_college,$('#college'),"{$university}","{$college}");
//			  getnext(json_major,$('#major'),"{$college}","{$major}");
			  $('#city').on('change',$(this),function(){
			   var val = $(this).val();
				$('#university').html("<option value='0'>--请选择--</option>");
				$('#college').html("<option value='0'>--请选择--</option>");
				$('#major').html("<option value='0'>--请选择--</option>");
				$('#major_code').val('');
                                var json_university = getschool(val,2);
				getnext(json_university,$('#university'),val); 
			  });
			  $('#university').on('change',$(this),function(){
			   var val = $(this).val();
				$('#college').html("<option value='0'>--请选择--</option>");
				$('#major').html("<option value='0'>--请选择--</option>");
				$('#major_code').val('');
                                var json_college = getschool(val,3);
				getnext(json_college,$('#college'),val); 
			  });
			  $('#college').on('change',$(this),function(){
			   var val = $(this).val();
			   $('#major').html("<option value='0'>--请选择--</option>");
			   $('#major_code').val('');
                           var json_major = getschool(val,4);
				getnext(json_major,$('#major'),val); 
			  });
			  $('#major').on('change',$(this),function(){
				 var code = $(this).find('option:selected').attr('data_code');
				 $('#major_code').val(code);
			  });
      }else if(type==2){
           $('#showpro').html(html2+html3); 
          json_list_two={$_list_two};
         // get_other_next(json_list_two,$('#major'),"{$major}");
		 // function get_other_next(data,obj,val){
		 /* $.each(json_list_two,function(n,om){
              var  html="<option value='"+n+"'";
               if(val==n){
                   html+='selected';
               }
               html+=">"+om+"</option>";
               obj.append(html);
            });*/
		
			 $.each(json_list_two,function(n,om){
			
                var  html="<option value='"+n+"'";
				if(n=="{$major_c['cid']}"){
				    html+='selected';
				}
                html+=">"+om[0]['title']+"</option>";
                $('#major_classify').append(html);
				
				array=eval(json_list_two);
				
			    
            });
			    array=eval(json_list_two);
			    i="{$major_c['cid']}";
				 
				$('#major_subject').empty();
				$('#major_subject').append('<option value="0" >--请选择--</option>');
                $.each(array[i],function(n,om){
					var  html="<option value='"+om['id']+"'";
					if(om['id']=="{$major}"){
				        html+='selected';
				    }
					html+=">"+om['cname']+"</option>";
					$('#major_subject').append(html);
				});
      }else if(type==3){
          $('#showpro').html(html1); 
          json_list_three={$_list_three};
          get_other_next(json_list_three,$('#major'),"{$major}"); 
      }
      
      $('#profes_type').change(function(){
          var val = $(this).val();
          if(val==1){
             $('#showpro').html(html);
             getcity(json_city,$('#city'),"{$city}");
             getnext(json_university,$('#university'),"{$city}","{$university}");
             getnext(json_college,$('#college'),"{$university}","{$college}");
             getnext(json_major,$('#major'),"{$college}","{$major}");

             $('#city').on('change',$(this),function(){ 
                var val = $(this).val();
                var  json_university =getschool(val,2);
                $('#university').html("<option value='0'>--请选择--</option>");
                $('#college').html("<option value='0'>--请选择--</option>");
                $('#major').html("<option value='0'>--请选择--</option>");
                $('#major_code').val('');
                getnext(json_university,$('#university'),val); 
            });
            $('#university').on('change',$(this),function(){
                var val = $(this).val();
                var  json_college =getschool(val,3);
                $('#college').html("<option value='0'>--请选择--</option>");
                $('#major').html("<option value='0'>--请选择--</option>");
                $('#major_code').val('');
                getnext(json_college,$('#college'),val); 
            });
            $('#college').on('change',$(this),function(){
                var val = $(this).val();
                var  json_major =getschool(val,4);
                $('#major').html("<option value='0'>--请选择--</option>");
                $('#major_code').val('');
                 getnext(json_major,$('#major'),val); 
             });
           $('#major').on('change',$(this),function(){
             var code = $(this).find('option:selected').attr('data_code');
             $('#major_code').val(code);
          });
          }else if(val==2){
             $('#showpro').html(html2+html3); 
             json_list_two={$_list_two};
          //   get_other_next(json_list_two,$('#major'),"{$major}");
		    $('#major_classify').on('change',$(this),function(){
			    array=eval(json_list_two);
				
			    i=parseInt($(this).val());
				 
				$('#major_subject').empty();
				 $('#major_subject').append('<option value="0" >--请选择--</option>');
                 $.each(array[i],function(n,om){
			   
				   var  html="<option value='"+om['id']+"'";
				   html+=">"+om['cname']+"</option>";
				   $('#major_subject').append(html);
				});
               
            });
		    $.each(json_list_two,function(n,om){
			   
               var  html="<option value='"+n+"'";
               html+=">"+om[0]['title']+"</option>";
               $('#major_classify').append(html);
            });
          }else if(val==3){
             $('#showpro').html(html1); 
             json_list_three={$_list_three};
             get_other_next(json_list_three,$('#major'),"{$major}"); 
          }else if(val==4){
             $('#showpro').html(''); 
            
          }
      });

</script>