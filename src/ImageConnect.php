<?php

namespace Mohuishou\ImageOCR;

/**
 * 连通域去噪&图像分割
 *
 * @author mohuishou<1@lailn.xyz>
 */
class ImageConnect
{

    /**
     * TAG 初始值
     */
    const TAG=10;

    /**
     * 字符区域的最小值
     * @var integer
     */
    const MIN=40;

    private $_len_w;
    private $_len_h;

    /**
     * 二值化图像数组
     * @var array
     */
    private $_hash_data=[];

    /**
     *  保存标记的大小
     */
    private $_tags=[];

    /**
     * 需要继续探索的种子
     * @var array
     */
    private $_seeds=[];

    /**
     * 初始化
     * @param  array     $hash_data 二值化图像数组
     * @author mohushou<1@lailin.xyz>
     */
    public function __construct($hash_data)
    {
        $this->_hash_data=$hash_data;
        $this->_len_h=count($hash_data);
        $this->_len_w=count($hash_data[0]);
        $this->addTag();
    }

    /**
     * 字符区块分割
     * @author mohuishou<1@lailin.xyz>
     * @return array
     */
    public function split(){
        $strs=[];
        $str_data=[];

        //找到需要保存的字符区块
        foreach ($this->_tags as $key => $value) {
            if ($value>self::MIN) {
                $strs[]=$key;
            }
        }

        //遍历找到需要的字符区块
        foreach ($strs as $k=>$v){
            $str_data[$k]=[];
            foreach ($this->_hash_data as $i=>$value){
                foreach ($value as $j=>$val) {
                    if ($val == $v){
                        $str_data[$k][$i][$j]=1;
                    }else{
                        $str_data[$k][$i][$j]=0;
                    }
                }
            }
        }

        //去除零行零列
        foreach ($str_data as $k=>$v){

            //去除零列
            $str_data[$k]=ImageTool::removeZeroColumn($v);

            //去除零行
            $str_data[$k]=ImageTool::removeZero($str_data[$k]);

            //重建索引
            $str_data[$k]=array_values($str_data[$k]);
        }

        return $str_data;
    }

    /**
     * 去除噪点
     * @return array $hash_data 去噪之后的数组
     * @author mohushou<1@lailin.xyz>
     */
    public function removeHotSpots(){
        $remove=[];
        foreach ($this->_tags as $key => $value) {
            if ($value<self::MIN) {
                $remove[]=$key;
            }
        }
        foreach ($this->_hash_data as $i=>$value) {
            foreach ($value as $j=>$val) {
                if (in_array($val, $remove)) {
                    $this->_hash_data[$i][$j]=0;
                }else {
                    if ($val>self::TAG) {
                        $this->_hash_data[$i][$j]=1;
                    }
                }
            }
        }
        return $this->_hash_data;
    }

    /**
     * 添加标记
     *
     * @return void
     * @author mohuishou<1@lailn.xyz>
     */
    public function addTag()
    {
        $data=&$this->_hash_data;
        // foreach ($data as $key => $value) {
        //     echo count($value)."\n";
        // }
        // return;
        $tag=self::TAG;
        for ($i=0; $i < $this->_len_h; $i++) {
            for ($j=0; $j < $this->_len_w; $j++) {
                if ($data[$i][$j]>0&&$data[$i][$j]<self::TAG) {
                    $tag++;
                    $data[$i][$j]=$tag;
                    $this->_tags[$tag]=1;
                    $this->connectPoint($i, $j,$tag);
                    while (!empty($this->_seeds)){
                        list($x,$y)=array_shift($this->_seeds);
                        $this->connectPoint($x, $y,$tag);
                    }
                }
            }
        }
    }

    /**
     * 查找连通的点
     * @param  int $i 横坐标
     * @param  int $j 纵坐标
     * @return void
     */
    public function connectPoint($i, $j,$tag)
    {
        if($i == 0 || $j == 0 || $i == ($this->_len_h - 1) || $j == ($this->_len_w - 1)) return true;
        $count=0;
        for ($m=-1; $m < 2; $m++) {
            for ($n=-1; $n < 2; $n++) {
                if ($this->_hash_data[$i+$m][$j+$n]>0&&$this->_hash_data[$i+$m][$j+$n]<self::TAG){
                    $count++;
                }
            }
        }
        if ($count<2) {
            return;
        }
        for ($m=-1; $m < 2; $m++) {
            for ($n=-1; $n < 2; $n++) {
                if ($this->_hash_data[$i+$m][$j+$n]>0&&$this->_hash_data[$i+$m][$j+$n]<self::TAG) {
                    $this->_hash_data[$i+$m][$j+$n]=$tag;
                    $this->_tags[$tag]++;
                    array_push($this->_seeds,[$i+$m,$j+$n]);
                }
            }
        }
    }
}
