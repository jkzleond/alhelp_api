<?php
/**
 * Created by PhpStorm.
 * User: jkzleond
 * Date: 16-1-9
 * Time: 下午1:08
 */

namespace Common\Behavior;
use \Think\Behavior;

class ErrorHandlerRegisterBehavior extends Behavior
{
    public function run(&$params)
    {
        //set_error_handler(array($this, 'errHandler'));
        //set_exception_handler(array($this, 'exceptionHandler'));
    }

    public function errHandler($err_no, $err_str, $err_file, $err_line, $err_context)
    {
        $err_name = '';
        switch($err_no)
        {
            case E_STRICT:
                $err_name = 'E_STRICT';
                break;
        }
        printf("%s: %s in %s on %s line \n", $err_name, $err_str, $err_file, $err_line);
    }

    public function exceptionHandler($e)
    {
        echo $e->getMessage();
    }
}