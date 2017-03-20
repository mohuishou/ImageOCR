<?php

namespace Mohuishou\ImageOCR;

require_once 'vendor/autoload.php';
$image=new Image("http://www.169ol.com/Stream/Code/getCode");
imagepng($image->_in_img,"./img/inImgTemp.png");
// $image=new Image("./img/inImgTemp.png");
$image_ocr=new ImageOCR($image);
$data=$image_ocr->getHashData();
foreach ($data as $v){
    ImageTool::drawBrowser($v);
}
// $a=$image->find();
// $code=implode("",$a);
// echo "验证码：$code \n";
