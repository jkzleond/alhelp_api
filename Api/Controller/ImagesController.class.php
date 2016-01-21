<?php
// 本类由系统自动生成，仅供测试用途
namespace Api\Controller;
use Think\Controller;
use Think\Upload;
class ImagesController extends ApiBaseController
{
	protected $mimes = array( 
		'image/jpg', 
		'image/jpeg', 
		'image/png', 
		'image/pjpeg', 
		'image/gif', 
		'image/bmp', 
		'image/x-png' 
	);
	protected $ext = array( 
		'jpg', 
		'gif', 
		'png', 
		'jpeg' 
	);
	public function index()
	{
		uploadImg();
		phpinfo();
	}
	/**
	 * 
	 */
	public function upload_image_post()
	{
		$this->check_token();
	
		$uid = $this->uid;
		$upload = new Upload(); // 实例化上传类
		$upload->maxSize = 2 * 1024 * 1024; // 设置附件上传大小
		$upload->mimes = $this->mimes;
		$upload->exts = $this->ext; // 设置附件上传类型
		$upload->rootPath = GetImageRoot(); // 设置附件上传根目录
		$upload->savePath = date('Y') . '/' . date('m') . '/' . date('d') . '/'; // 设置附件上传（子）目录
		// 上传文件
		$infos = $upload->upload();
		if (!$infos)
		{ // 上传错误提示错误信息
			$this->errorMsg('1400', $upload->getError());
		}
		else
		{
			foreach ($infos as &$info)
			{
				$id = $this->savePic($uid, $info);
				$info['id'] = $id;
				$info['status'] = 1;
				if ($id === false)
				{
					$info['status'] = 0;
					$info['id'] = null;
				}
			}
			// 上传成功
			$this->success($infos);
		}
		
		$this->error(1417);
	}
	
	private function savePic($uid, $info)
	{
		$model = M("attachments");
		$values["member_id"] = $uid;
		$values["table"] = null;
		$values["table_id"] = null;
		$values["add_time"] = date("Y-m-d H:i:s");
		$values["sha1"] = $info['sha1'];
		$values['path'] = info['savepath'] . $info['savename'];
		$values['name'] = info['name'];
		$values['size'] = info['size'];
		$values['status'] = 1;
		return $model->add($values);
	}
	
	public function image_resize_post()
	{
		$file = $this->get_request_data("url");
		
		$ch = curl_init($file);
		// 跟踪301跳转
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		// 返回结果
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		
		$dataBlock = curl_exec($ch);
		curl_close($ch);
		
		if (!$dataBlock)
		{
			$this->error(1418);
		}
		
		list($width, $height) = getimagesize('data:image/jpeg;base64,' . base64_encode($dataBlock)); // 获取原图尺寸
		

		if (!$width || !$height)
		{
			$this->error(1418);
		}
		
		header("Content-type: image/jpeg");
		
		$percent = $height / $width; // 图片高宽比
		

		$temp_percent = intval(round($percent * 100));
		
		// 原比例缩放尺寸
		if ($temp_percent <= 115 && $temp_percent >= 85)
		{
			$newheight = $newwidth = 128;
		}
		else
		{
			if ($width > $height)
			{
				$newwidth = 128;
				$newheight = $newwidth * $percent;
			}
			else
			{
				$newheight = 128;
				$newwidth = $newheight / $percent;
			}
		}
		
		$src_im = imagecreatefromstring($dataBlock);
		
		// 创建一个白色画布
		$dst_im = imagecreatetruecolor($newwidth, $newheight);
		$white = imagecolorallocate($dst_im, 255, 255, 255);
		imagefill($dst_im, 0, 0, $white);
		
		// 重采样拷贝部分图像并调整大小
		imagecopyresampled($dst_im, $src_im, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
		imagejpeg($dst_im); // 输出压缩后的图片
		imagedestroy($dst_im);
		imagedestroy($src_im);
	}
}
