<?php
/**
 * Created by PhpStorm.
 * User: mohuishou<1@lailin.xyz>
 * Date: 2017/3/19
 * Time: 16:28
 */

namespace Mohuishou\ImageOCR;

/**
 * Class ImageTool
 * 一些工具函数
 * @author mohuishou<1@lailin.xyz>
 * @package Mohuishou\ImageOCR
 */
class ImageTool{

    /**
     * 去除零行
     * @author mohuishou<1@lailin.xyz>
     * @param array $data
     * @return array $data 去掉零行之后的二值化数组
     */
    static public function removeZero($data){
        foreach ($data as $k=>$v) {
            if (implode("", $v) == 0) unset($data[$k]);
        }
        return $data;
    }

    /**
     * 去除零列
     * @param array $hash_data
     * @author mohuishou<1@lailin.xyz>
     * @return array
     */
    static public function removeZeroColumn($hash_data){
        $data=[];
        for ($i=0;$i<count($hash_data[0]);$i++){
            $column=array_column($hash_data,$i);
            if(implode("",$column)!=0){
                $data[]=$column;
            }
        }
        $res_data=[];
        for ($i=0;$i<count($data[0]);$i++){
            $column=array_column($data,$i);
            $res_data[]=$column;
        }
        return $res_data;
    }

    /**
     * 使用点阵的形式在浏览器上画出二值图
     * @param null $data
     * @author mohuishou<1@lailin.xyz>
     */
    static public function drawBrowser($data){
        foreach ($data as $v){
            foreach ($v as $val){
                if($val){
                    echo "<span style='color: #333;'>&#x2022;</span>";
                }else{
                    echo "<span style='color: #eee;'>&#x2022;</span>";
                }
            }
            echo "<br />";
        }
    }


    static public function transposeAndRemoveZero($hash_data){
        $data=[];
        for ($i=0;$i<count(current($hash_data));$i++){
            $column=array_column($hash_data,$i);
            if(implode("",$column)!=0){
                $data[]=$column;
            }
        }
        return $data;
    }

    /**
     * 二维数组转置
     * @param array $hash_data 二值化数组
     * @author mohuishou<1@lailin.xyz>
     * @return array $data 转置之后的数组
     */
    static public function hashTranspose($hash_data){
        $data=[];
        for ($i=0;$i<count($hash_data[0]);$i++){
            $column=array_column($hash_data,$i);
            $data[]=$column;
        }
        return $data;
    }


    /**
     * 黑白图像转二值化数组
     * @param resource $img 图像资源句柄
     * @author mohuishou<1@lailin.xyz>
     * @return array $hash_data 二值化数组
     */
    static public function img2hash($img){

        //二值化数组初始化
        $hash_data=[];

        //根据资源句柄获取整个图像的高与宽
        $img_w=imagesx($img);
        $img_h=imagesy($img);

        //图像二值化
        for($i = 0; $i <$img_h; $i++) {
            for ($j = 0; $j <$img_w; $j++) {
                $rgb = imagecolorat($img,$j,$i);
                if($rgb==0){
                    $hash_data[$i][$j]=1;
                }else{
                    $hash_data[$i][$j]=0;
                }
            }
        }

        return $hash_data;
    }

    /**
     * 二值化数组转图像
     * @param array $hash_data 二值化数组
     * @param int $padding 边距
     * @author mohuishou<1@lailin.xyz>
     * @return resource 图像的资源句柄
     */
    static public function hash2img($hash_data,$padding=0){

        //计算图片的宽度与高度
        $img_w=count($hash_data[0])+2*$padding;
        $img_h=count($hash_data)+2*$padding;

        //图像初始化
        $img = imagecreatetruecolor($img_w,$img_h);//创建一幅真彩色图像
        $white=imagecolorallocate($img, 255, 255, 255);//白色
        $black=imagecolorallocate($img, 0, 0, 0);//黑色

        //背景填充为白色
        imagefill($img, 0,0, $white);

        //进行画图
        foreach ($hash_data as $k=>$v){
            foreach ($v as $key=> $val){
                if ($val){
                    imagesetpixel($img, $key+$padding,$k+$padding, $black);
                }
            }
        }

        return $img;
    }
}