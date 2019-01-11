# EXAMPLE

## install

```
composer install
```

## 说明

`study.php` 用于建立基本的模型库，数据保存在`db/db.db`当中
`index.php` 直接查看识别的效果
`OCR.php` 基础的使用例子

## FAQ

如果遇到下面这个问题，请确认所安装的 PHP 有没有带 freetype

```
Call to undefined function imagettfbbox()

// for mac
brew install php --with-freetype
```
