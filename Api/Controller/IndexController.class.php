<?php
// 本类由系统自动生成，仅供测试用途
namespace Api\Controller;
use Think\Controller;
class IndexController extends ApiBaseController
{
	public function index()
	{
		uploadImg();
		phpinfo();
	}
	
	// 修改密码
	public function set_pwd_put()
	{
		$this->check_token();
	}
	
	// 冻结用户
	public function disable_user_put()
	{
		// echo
		// 'ii';
		// dump($this->uid);
		// $this_user
		// =
		// D('User');
		// $data
		// =
		// array('userInfo'
		// =>
		// $this_user->detail($this->uid));
		// $this->success($data);
	}
	public function upload_image_post()
	{
		$this->check_token();
		$array = uploadImg();
		if (!empty($array))
		{
			$result = array();
			$result['sha1'] = $array[0]['sha1'];
			$result['path'] = $array[0]['path'];
			$result['status'] = $array[0]['status'];
			$this->success($result);
		}
		
		$this->error(1417);
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
	public function getPubMedDistanceDate_get()
	{
		$date = M("app_setting")->getFieldByItem("ky_date", "value");
		$date -= time();

		$ret = ceil($date / 86400);
		
		if ($ret == 0)
		{
			$this->success(array( 
				"day" => 0 
			));
		}
		else
		{
			$this->success(array( 
				"day" => $ret 
			));
		}
	}
}
