<?php
/**
 * Created by mohuishou<1@lailin.xyz>.
 * User: mohuishou<1@lailin.xyz>
 * Date: 2016/5/1 0001
 * Time: 20:44
 */
namespace Mohuishou\ImageOCR\Example;

use Minho\Captcha\CaptchaBuilder;

require_once 'vendor/autoload.php';

$img_path=__DIR__."/img/1.png";

if (isset($_POST['send'])&&$_POST['send']=="send") {
    $ocr=new OCR($img_path);
    $ocr->study($_POST['code']);
    echo "<script>location.href='./study.php?t=".time()."'</script>";
    $ocr=null;
} else {
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
    
    $captch->save($img_path, 1);
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
        <img src="img/1.png">
        <input type="text" name="code">
        <input name="send" type="submit" value="send" />
    </form>
</body>
</html>