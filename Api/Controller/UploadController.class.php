<?php
// 本类由系统自动生成，仅供测试用途
namespace Api\Controller;

use Think\Controller;
use Think\Upload;

class UploadController extends ApiBaseController {

	public function images() {
		$this->check_token ();
		
		$uid =  $this->uid;
		$config = C ( 'PICTURE_UPLOAD' );
		$config ['rootPath'] = GetImageRoot ();
		$upload = new Upload ( $config );
		
		$infos = $upload->upload ();
		// var_dump($infos);
		if (! $infos) {
			$this->errorMsg ( '1400', $upload->getError () );
		} else {
			$tmp=array();
			foreach ( $infos as &$info ) {
				$id = $this->saveFileInfo ( $uid, $info );
				$info ['id'] = $id;
				$info ['status'] = 1;
				$info ['url'] = GetImage_new ( $info ['savepath'] . $info ['savename'], $info ['sha1'] );
				if ($id === false) {
					$info ['status'] = 0;
					$info ['id'] = null;
				}
				$tmp[]=$info;
			}
			$this->success ( $tmp );
		}
		
		$this->error ( 1417 );
	}

	/**
	 * 文件上传
	 */
	public function file_post() {
		$this->check_token ();
		$type = I('get.p', 'normal');
		$uid =  $this->uid;
		$config = C ( 'DOWNLOAD_UPLOAD' );
		$config['rootPath'] = $config['rootPath'].$type;
		$upload = new Upload ( $config );

		$infos = $upload->upload ();
		// var_dump($infos);
		if (! $infos) {
			$this->errorMsg ( '1400', $upload->getError () );
		} else {
			$tmp=array();
			foreach ( $infos as &$info ) {
				$id = $this->saveFileInfo ( $uid, $info );
				$info ['id'] = $id;
				$info ['status'] = 1;
				$file_path = null;
				//$file_path = $info ['savepath'].$info ['savename'];
				$filename = empty ( $file_path ) ? $info ['sha1'] . $info['ext'] : $file_path;
				$file_url = 'http://image.alhelp.net/attachments/'.$type.'/'.$filename;
				$info ['url'] = $file_url;
				if ($id === false) {
					$info ['status'] = 0;
					$info ['id'] = null;
				}
				$tmp[]=$info;
			}
			$this->success ( $tmp );
		}

		$this->error ( 1417 );
	}

	public function saveFileInfo($uid, $info) {
		$model = M ( "attachments" );
		$values ["member_id"] = $uid;
		$values ["table"] = null;
		$values ["table_id"] = null;
		$values ["add_time"] = date ( "Y-m-d H:i:s" );
		$values ["sha1"] = $info ['sha1'];
		$values ['path'] = $info ['savepath'] . $info ['savename'];
		$values ['name'] = $info ['name'];
		$values ['size'] = $info ['size'];
		$values ['status'] = 1;
		return $model->add ( $values );
	}
}
