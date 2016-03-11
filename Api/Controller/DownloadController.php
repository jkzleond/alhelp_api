<?php
/**
 * Created by PhpStorm.
 * User: jkzleond
 * Date: 16-3-9
 * Time: 上午11:10
 */

namespace Api\Controller;


class DownloadController extends ApiBaseController
{
    public function download() {
        $attachment_id = I('get.aid');
        if (!$attachment_id) {
            $this->error(1001);
        }
    }
}