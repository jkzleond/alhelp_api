<?php
// 本类由系统自动生成，仅供测试用途
namespace Home\Controller;
use Think\Controller;
class EditorController extends Controller
{
	//图片上传提交地址
	public function imageUp()
	{
		
		$title = htmlspecialchars ( $_POST ['pictitle'], ENT_QUOTES );
		$path = htmlspecialchars ( $_POST ['dir'], ENT_QUOTES );
		$save_path = C ( 'UE_IMG_SAVE_PATH' );
		
		$cur_uid = session ( 'u_id' );
		if ($cur_uid == '')
		{
			//当没有获取到用户ID时，使用默认
			$save_path = array (
				'Platform' => '平台' 
			);
		}
		
		//获取存储目录
		if (isset ( $_GET ['fetch'] ))
		{
			header ( 'Content-Type:text/javascript' );
			echo 'updateSavePath(' . json_encode ( $save_path ) . ');';
			return;
		}
		//获取上传设置
		$config = C ( 'UE_EDITOR_IMG_UPLOAD' );
		if (empty ( $path ))
		{
			$path = array_keys ( $save_path [0] );
		}
		
		//上传目录验证
		if (! array_key_exists ( $path, $save_path ))
		{
			//非法上传目录
			echo '{"state":"\u975e\u6cd5\u4e0a\u4f20\u76ee\u5f55"}';
			return;
		}
		
		//设置上传路径
		//$_SERVER['DOCUMENT_ROOT']
		if ($cur_uid != '')
		{
			$root_path = $config ['savePath'] . 'UserImg/' . $cur_uid . '/' . $path . '/';
		} else
		{
			$root_path = $config ['savePath'] . $path . '/Image/';
		}
		$config ['savePath'] = $root_path;
		
		$up = new \Ueditor\UEUploader ( 'upfile', $config ); //实例化上传类
		//上传文件
		/**
		 * 得到上传文件所对应的各个参数,数组结构
		 * array(
		 * "originalName" => "",   //原始文件名
		 * "name" => "",           //新文件名
		 * "url" => "",            //返回的地址
		 * "size" => "",           //文件大小
		 * "type" => "" ,          //文件类型
		 * "state" => ""           //上传状态，上传成功时必须返回"SUCCESS"
		 * )
		 */
		$info = $up->UE_getFileInfo ();
		$url = ltrim ( $info ['url'], '.' );
		/**
		 * 向浏览器返回数据json数据
		 * {
		 * 'url'      :'a.jpg',   //保存后的文件路径
		 * 'title'    :'hello',   //文件描述，对图片来说在前端会添加到title属性上
		 * 'original' :'b.jpg',   //原始文件名
		 * 'state'    :'SUCCESS'  //上传状态，成功时返回SUCCESS,其他任何值将原样返回至图片上传框中
		 * }
		 */
		echo "{'url':'" . $url . "','title':'" . $title . "','original':'" . $info ["name"] . "','state':'" . $info ["state"] . "'}";
	}
	
	//涂鸦上传地址
	public function scrawlUp()
	{
		//获取上传设置
		$config = C ( 'UE_EDITOR_IMG_UPLOAD' );
		
		//临时文件目录
		$tmpPath = $config ['savePath'] . "ue_tmp/";
		
		//获取当前上传的类型
		$action = htmlspecialchars ( $_GET ["action"] );
		if ($action == "tmpImg")
		{ // 背景上传
			//背景保存在临时目录中
			$config ["savePath"] = $tmpPath;
			$up = new \Ueditor\UEUploader ( 'upfile', $config );
			$info = $up->UE_getFileInfo ();
			$url = ltrim ( $info ['url'], '.' );
			/**
			 * 返回数据，调用父页面的ue_callback回调
			 */
			echo "<script>parent.ue_callback('" . $url . "','" . $info ["state"] . "')</script>";
		} else
		{
			//涂鸦上传，上传方式采用了base64编码模式，所以第三个参数设置为true
			$cur_uid = session ( 'u_id' );
			$path = 'ue_scrawl';
			if ($cur_uid != '')
			{
				$root_path = $config ['savePath'] . 'UserImg/' . $cur_uid . '/' . $path . '/';
			} else
			{
				$root_path = $config ['savePath'] . 'Platform/Image/' . $path . '/';
			}
			$config ["savePath"] = $root_path;
			$up = new \Ueditor\UEUploader ( "content", $config, true );
			//上传成功后删除临时目录
			if (file_exists ( $tmpPath ))
			{
				$this->delDir ( $tmpPath );
			}
			$info = $up->UE_getFileInfo ();
			$url = ltrim ( $info ['url'], '.' );
			echo "{'url':'" . $url . "',state:'" . $info ["state"] . "'}";
		}
	}
	
	//附件上传提交地址
	public function fileUp()
	{
		//获取上传设置
		$config = C ( 'UE_EDITOR_FILE_UPLOAD' );
		$save_path = C ( 'UE_FILE_SAVE_PATH' );
		
		$cur_uid = session ( 'u_id' );
		//设置上传路径
		if ($cur_uid != '')
		{
			$root_path = $config ['savePath'] . 'UserFile/' . $cur_uid . '/';
		} else
		{
			$root_path = $config ['savePath'] . 'Platform/File/';
		}
		$config ['savePath'] = $root_path;
		
		//生成上传实例对象并完成上传
		$up = new \Ueditor\UEUploader ( "upfile", $config );
		
		/**
		 * 得到上传文件所对应的各个参数,数组结构
		 * array(
		 * "originalName" => "",   //原始文件名
		 * "name" => "",           //新文件名
		 * "url" => "",            //返回的地址
		 * "size" => "",           //文件大小
		 * "type" => "" ,          //文件类型
		 * "state" => ""           //上传状态，上传成功时必须返回"SUCCESS"
		 * )
		 */
		$info = $up->UE_getFileInfo ();
		$url = ltrim ( $info ['url'], '.' );
		
		/**
		 * 向浏览器返回数据json数据
		 * {
		 * 'url'      :'a.rar',        //保存后的文件路径
		 * 'fileType' :'.rar',         //文件描述，对图片来说在前端会添加到title属性上
		 * 'original' :'编辑器.jpg',   //原始文件名
		 * 'state'    :'SUCCESS'       //上传状态，成功时返回SUCCESS,其他任何值将原样返回至图片上传框中
		 * }
		 */
		echo '{"url":"' . $url . '","fileType":"' . $info ["type"] . '","original":"' . $info ["originalName"] . '","state":"' . $info ["state"] . '"}';
	}
	
	//处理远程图片抓取的地址
	public function getRemoteImage()
	{
		//获取上传设置
		$config = C ( 'UE_EDITOR_IMG_UPLOAD' );
		
		$cur_uid = session ( 'u_id' );
		//设置上传路径
		if ($cur_uid != '')
		{
			$root_path = $config ['savePath'] . 'UserImg/' . $cur_uid . '/';
		} else
		{
			$root_path = $config ['savePath'] . 'Platform/Image/';
		}
		$config ['savePath'] = $root_path;
		
		$uri = htmlspecialchars ( $_POST ['upfile'] );
		$uri = str_replace ( "&amp;", "&", $uri );
		$this->getRemoteImages ( $uri, $config );
	}
	
	//图片在线管理的处理地址
	public function imageManager()
	{
		/*
		 * $dir 默认遍历循环目录
		 */
		//获取Config配置文件里的相关信息
		$ue_path = C ( "UE_EDITOR_IMG_UPLOAD" );
		$path = $ue_path ['savePath'];
		$cur_uid = session ( 'u_id' );
		if ($cur_uid != '')
		{
			$path .= 'UserImg/' . $cur_uid . '/';
		} else
		{
			$path .= 'Platform/Image/';
		}
		
		if ($_POST ['parameters'] != '')
		{
			$path = urldecode ( $_POST ['parameters'] ) . '/';
		}
		//需要遍历的目录列表，最好使用缩略图地址，否则当网速慢时可能会造成严重的延时
		$action = htmlspecialchars ( $_POST ["action"] );
		//if ( $action == "get" ){
		$tmp = $this->listDirFiles ( $path );
		$jsonstr = json_encode ( $tmp );
		echo $jsonstr;
	
		//}
	}
	
	/*
	 * 遍历获取目录下的目录及文件
	 * @param $path
	 * @param array $files
	 * @return array
	 */
	private function listDirFiles($path, &$files = array())
	{
		//$path=$_SERVER['DOCUMENT_ROOT'].$path;
		// 申明文件夹数组与文件数组
		$aFolders = array ();
		$aFiles = array ();
		
		/*
		 * 则该函数返回一个目录流，否则返回 false 以及一个 error
		 * 可以通过在函数名前加上 "@" 来隐藏 error 的输出。
		 * 用法opendir(path,context)
		 */
		$ocfolder = opendir ( $path );
		
		// readdir() 函数返回由 opendir() 打开的目录句柄中的条目
		// 语法:readdir(dir_stream)
		while ( $sFile = readdir ( $ocfolder ) )
		{
			if ($sFile != '.' && $sFile != '..')
			{
				// 判断是否为目录，是返回ture否就返回false;
				if (is_dir ( $path . $sFile ))
				{
					$aFolders [] = iconv ( "gb2312", "UTF-8", $sFile ) . '|' . $path;
				} else
				{
					$aFiles [] = iconv ( "gb2312", "UTF-8", $sFile ) . '|' . $path;
					;
				}
			}
		}
		$data ['folders'] = array ();
		$data ['files'] = array ();
		// 对目录进行自然排序
		//对数组自然排序 natcasesort(array)
		natcasesort ( $aFolders );
		foreach ( $aFolders as $sFolder )
		{
			$data ['folders'] [] = $sFolder;
		}
		
		// 对文件进行自然排序
		// 自然1-9,a-z排序natcasesort(array);
		natcasesort ( $aFiles );
		foreach ( $aFiles as $sFiles )
		{
			$data ['files'] [] = $sFiles;
		}
		$returnArr = array (
			$data ['folders'], 
			$data ['files'] 
		);
		return $returnArr;
	}
	
	/**
	 * 删除整个目录
	 * @param $dir
	 * @return bool
	 */
	private function delDir($dir)
	{
		//先删除目录下的所有文件：
		$dh = opendir ( $dir );
		while ( $file = readdir ( $dh ) )
		{
			if ($file != "." && $file != "..")
			{
				$fullpath = $dir . "/" . $file;
				if (! is_dir ( $fullpath ))
				{
					unlink ( $fullpath );
				} else
				{
					$this->delDir ( $fullpath );
				}
			}
		}
		closedir ( $dh );
		//删除当前文件夹：
		return rmdir ( $dir );
	}
	
	/**
	 * 远程抓取
	 * @param $uri
	 * @param $config
	 */
	private function getRemoteImages($uri, $config)
	{
		//忽略抓取时间限制
		set_time_limit ( 0 );
		//ue_separate_ue  ue用于传递数据分割符号
		$imgUrls = explode ( "ue_separate_ue", $uri );
		$tmpNames = array ();
		foreach ( $imgUrls as $imgUrl )
		{
			//http开头验证
			if (strpos ( $imgUrl, "http" ) !== 0)
			{
				array_push ( $tmpNames, "error" );
				continue;
			}
			//获取请求头
			$heads = get_headers ( $imgUrl );
			//死链检测
			if (! (stristr ( $heads [0], "200" ) && stristr ( $heads [0], "OK" )))
			{
				array_push ( $tmpNames, "error" );
				continue;
			}
			
			//格式验证(扩展名验证和Content-Type验证)
			$fileType = strtolower ( strrchr ( $imgUrl, '.' ) );
			if (! in_array ( $fileType, $config ['allowFiles'] ) || stristr ( $heads ['Content-Type'], "image" ))
			{
				array_push ( $tmpNames, "error" );
				continue;
			}
			
			//打开输出缓冲区并获取远程图片
			ob_start ();
			$context = stream_context_create ( array (
				'http' => array (
					'follow_location' => false  // don't follow redirects
				) 
			) );
			//请确保php.ini中的fopen wrappers已经激活
			readfile ( $imgUrl, false, $context );
			$img = ob_get_contents ();
			ob_end_clean ();
			
			//大小验证
			$uriSize = strlen ( $img ); //得到图片大小
			$allowSize = 1024 * $config ['maxSize'];
			if ($uriSize > $allowSize)
			{
				array_push ( $tmpNames, "error" );
				continue;
			}
			//创建保存位置
			$savePath = $config ['savePath'];
			if (! file_exists ( $savePath ))
			{
				mkdir ( "$savePath", 0777 );
			}
			//写入文件
			$tmpName = $savePath . rand ( 1, 10000 ) . time () . strrchr ( $imgUrl, '.' );
			try
			{
				$fp2 = @fopen ( $tmpName, "a" );
				fwrite ( $fp2, $img );
				fclose ( $fp2 );
				array_push ( $tmpNames, $tmpName );
			} catch ( Exception $e )
			{
				array_push ( $tmpNames, "error" );
			}
		}
		/**
		 * 返回数据格式
		 * {
		 * 'url'   : '新地址一ue_separate_ue新地址二ue_separate_ue新地址三',
		 * 'srcUrl': '原始地址一ue_separate_ue原始地址二ue_separate_ue原始地址三'，
		 * 'tip'   : '状态提示'
		 * }
		 */
		$url = ltrim ( implode ( "ue_separate_ue", $tmpNames ), '.' );
		echo "{'url':'" . $url . "','tip':'远程图片抓取成功！','srcUrl':'" . $uri . "'}";
	}
	
	//删除文件
	public function delFile()
	{
		$file_path = '.' . $_POST ['parameters'];
		if (file_exists ( $file_path ))
		{
			unlink ( $file_path );
		}
		$this->ajaxReturn ();
	}
}
