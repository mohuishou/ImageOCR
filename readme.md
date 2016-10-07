# ImageOCR

> php验证码识别


### 安装
```
composer require mohuishou/image-ocr
```

### 初始化
1. 检查db文件夹是否存在且有写入权限
2. 使用前请先运行study.php，为识别建立特征库，特征库越多，识别率越高，运行时间越久
3. 使用前请先根据个人需要更改下面常量的值

```php
    //标准化的图像的宽高信息，可调
    const HASH_W = 10;
    const HASH_H = 10;

    //图像字符串的个数
    const CHAR_NUM=4;
```

### 运行

```php
//注意，这里的图片，如果是url验证码的话，请先将其保存下来，因为一般url每一次get的时候图像会被重新生成
$image=new \ImageOCR\Image("./img/inImgTemp.png"); 
$a=$image->find();
```

### debug

```php
$image->draw(); //将会把接收到的图像，去除干扰之后，点阵画的输出
```

### CHANGELOG
#### 0.1 [2016-10-7]
1. 默认模板保存方式由数据库改为文件，保存路径为./db/db.json
2. 使用composer安装
