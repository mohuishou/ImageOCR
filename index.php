<?php
/**
 * Created by mohuishou<1@lailin.xyz>.
 * User: mohuishou<1@lailin.xyz>
 * Date: 2016/5/1 0001
 * Time: 20:44
 */
require_once 'Image.class.php';
$image=new \ImageOCR\Image("./img/inImgTemp.png");
$a=$image->find();
$code=implode("",$a);
echo "验证码：$code \n";

