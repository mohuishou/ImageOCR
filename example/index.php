<?php

namespace Mohuishou\ImageOCR\Example;

require_once 'vendor/autoload.php';
$img_path=__DIR__."/img/inImgTemp.png";
$code_path="http://www.169ol.com/Stream/Code/getCode";
$ocr=new OCR($code_path);
echo $ocr->ocr();
$ocr->draw();
$ocr->save($img_path);
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
<img src="img/inImgTemp.png" alt="">
</body>
</html>
