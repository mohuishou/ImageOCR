<?php
/**
 * Created by mohuishou<1@lailin.xyz>.
 * User: mohuishou<1@lailin.xyz>
 * Date: 2016/5/1 0001
 * Time: 20:44
 */
namespace Mohuishou\ImageOCR\Example;

require_once 'vendor/autoload.php';
$img_path=__DIR__."/img/inImgTemp.png";

if(isset($_POST['send'])&&$_POST['send']=="send"){
    $ocr=new OCR($img_path);
    $ocr->study($_POST['code']);
    echo "<script>location.href='./study.php?t=".time()."'</script>";
    $ocr=null;
}else{
    $ocr = new OCR("http://www.169ol.com/Stream/Code/getCode");
    $ocr->save($img_path);
    $ocr=null;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Study</title>
</head>
<body>
    <form action="" method="post">
        <img src="img/inImgTemp.png">
        <input type="text" name="code">
        <input name="send" type="submit" value="send" />
    </form>
</body>
</html>