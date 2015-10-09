<?php
namespace bookin\composer\web;


use Composer\IO\NullIO;
use \Yii;

class WebIO extends NullIO
{
    public function isDebug(){
        return YII_ENV_DEV;
    }

}