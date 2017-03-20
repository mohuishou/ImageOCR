<?php
/**
 * Created by mohuishou<1@lailin.xyz>.
 * User: mohuishou<1@lailin.xyz>
 * Date: 2016/5/1 0001
 * Time: 20:44
 */
require_once 'vendor/autoload.php';
$db=new \Mohuishou\ImageOCR\StorageFile();
//$a=$db->get();
//print_r($a);
if(isset($_POST['send'])&&$_POST['send']=="send"){
    $image=new \Mohuishou\ImageOCR\Image("./img/inImgTemp.png");
    $code=$_POST['code'];
    $code_arr=str_split($code);

    for($i=0;$i<$image::CHAR_NUM;$i++){
        $hash_img_data=implode("",$image->splitImage($i));
        $db->add($code_arr[$i],$hash_img_data);
    }

    echo "<script>location.href='./study.php?t=".time()."'</script>";

}else{
    $image=new \Mohuishou\ImageOCR\Image("http://www.169ol.com/Stream/Code/getCode");
    imagepng($image->_in_img,"./img/inImgTemp.png");
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