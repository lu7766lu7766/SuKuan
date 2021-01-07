<?php

class Verification
{
    private $code;
    private $codelen;

    public function __construct($codelen)
    {
        //產生驗證碼
        $charset = "abcdefghkmnprstuvwxyzABCDEFGHKMNPRSTUVWXYZ23456789"; //驗證碼元素
        $_len = strlen($charset) - 1;
        $code = "";            //驗證碼
        for ($i = 0; $i < $codelen; $i++)
        {
            $code .= $charset[mt_rand(0, $_len)];
        }
        $this->code = $code;
        $this->codelen = $codelen;
    }

    public function getCode()
    {
        return $this->code;
    }

    public function draw($width = 130, $height = 50, $fontsize = 20)
    {
        $font = config("comm_dir") . "/elephant.ttf";

        //繪製背景
        $img = imagecreatetruecolor($width, $height);
        $color = imagecolorallocate($img, mt_rand(157, 255), mt_rand(157, 255), mt_rand(157, 255));
        imagefilledrectangle($img, 0, $height, $width, 0, $color);

        //繪製干擾
        for ($i = 0; $i < 6; $i++)
        {
            $color = imagecolorallocate($img, mt_rand(0, 156), mt_rand(0, 156), mt_rand(0, 156));
            imageline($img, mt_rand(0, $width), mt_rand(0, $height), mt_rand(0, $width), mt_rand(0, $height), $color);
        }
        for ($i = 0; $i < 100; $i++)
        {
            $color = imagecolorallocate($img, mt_rand(200, 255), mt_rand(200, 255), mt_rand(200, 255));
            imagestring($img, mt_rand(1, 5), mt_rand(0, $width), mt_rand(0, $height), "*", $color);
        }

        //繪製文字
        $_x = $width / $this->codelen;
        for ($i = 0; $i < $this->codelen; $i++)
        {
            $fontcolor = imagecolorallocate($img, mt_rand(0, 156), mt_rand(0, 156), mt_rand(0, 156));
            imagettftext($img, $fontsize, mt_rand(-30, 30), $_x * $i + mt_rand(1, 5), $height / 1.4, $fontcolor, $font, $this->code[$i]);
        }

        //輸出
        header('Content-Type: image/png');
        //ob_start();
        imagepng($img);
        //$this->img = base64_encode(ob_get_clean());
        imagedestroy($img);

        return $this;
    }
}

?>