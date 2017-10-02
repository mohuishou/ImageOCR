<?php

namespace Mohuishou\ImageOCR\Example;

require_once 'vendor/autoload.php';
$img_path=__DIR__."/img/inImgTemp.png";
$code_path="https://cas.baidu.com/?action=image";
$img=new \Mohuishou\ImageOCR\Example\OCR($code_path);

$img->draw()

//第一步灰度化
// $data=$img->grey();

// //第二步二值化
// $img->hashByBackground(ImageOCR::MAX_MODEL);

// \Mohuishou\ImageOCR\ImageTool::drawBrowser($img->getStandardData());
?>
<!--<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
<img src="img/inImgTemp.png" alt="">
</body>
</html>-->
