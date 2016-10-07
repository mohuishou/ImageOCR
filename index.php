<?php
require_once 'vendor/autoload.php';
$image=new \Mohuishou\ImageOCR\Image("http://www.169ol.com/Mall/Code/getCode&1462104790492");
imagepng($image->_in_img,"./img/inImgTemp.png");
//$image=new \ImageOCR\Image("./img/inImgTemp.png");
$a=$image->find();
$code=implode("",$a);
echo "验证码：$code \n";