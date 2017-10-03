<?php

namespace Mohuishou\ImageOCR\Example;
use Minho\Captcha\CaptchaBuilder;

require_once 'vendor/autoload.php';

$captch = new CaptchaBuilder();

$captch->initialize([
    'width' => 150,     // 宽度
    'height' => 50,     // 高度
    'line' => false,    // 直线
    'curve' => false,    // 曲线
    'noise' => 0,       // 噪点背景
    'fonts' => ["./fonts/num.ttf"]       // 字体
]);

$captch->create();
$img_path=__DIR__."/img/1.png";
$captch->save($img_path,1);

$img=new \Mohuishou\ImageOCR\Example\OCR($img_path);

echo "识别结果" . $img->ocr();
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
<img src="img/1.png" alt="">
</body>
</html>
