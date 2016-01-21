<?php
namespace Common\Widget;
use Think\Controller;
/**
 * Description of ImageUploadWidget
 * 图片上传插件
 * @author Auser
 */
class ImageUploadWidget extends Controller{
    
    
    /*
       * $param 参数 
       * 
       * 模板中调用方法 {:W('Common/ImageUpload/uploadImg')} 
       */
    public function uploadImg($obj_id,$dir){
        $data['obj']=$obj_id;
        $this->assign($data);
        $this->display(T('Common@Widget/ImageUpload/uploadImg'));
    }
}
