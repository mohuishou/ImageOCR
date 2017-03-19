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
}