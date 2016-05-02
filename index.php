<?php
/**
 * Created by mohuishou<1@lailin.xyz>.
 * User: mohuishou<1@lailin.xyz>
 * Date: 2016/5/1 0001
 * Time: 20:44
 */
//require_once 'Image.class.php';
//
////$image=new \ImageOCR\Image("./img/inImgTemp.png");
//$image=new \ImageOCR\Image("http://www.169ol.com/Mall/Code/getCode");
////$image->splitImage(2, __DIR__ . "/gray0.jpg");
////$image->imageHash();
//$a=$image->find();
//$code=implode("",$a);
//echo "验证码：$code \n";
$url="http://www.169ol.com/Mall/User/loginValiCode";
$data="Verification=5193";
//$data="Verification=".$code;
$cookie="PHPSESSID=fur79gcoalfv3fidofnn29a6apuo10ou;";
$ch = curl_init ();
curl_setopt ( $ch, CURLOPT_URL, $url );
curl_setopt ( $ch, CURLOPT_POST, 1 );
curl_setopt ( $ch, CURLOPT_HEADER, 0 );
curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
curl_setopt ( $ch, CURLOPT_POSTFIELDS, $data );
curl_setopt($ch,CURLOPT_COOKIE, $cookie);
$rs = curl_exec ( $ch );
curl_close ( $ch );
echo $rs;