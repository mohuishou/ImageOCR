<?php
/**
 * Created by PhpStorm.
 * User: mohuishou<1@lailin.xyz>
 * Date: 2017/3/22
 * Time: 14:32
 */

namespace Mohuishou\ImageOCR;

/**
 * 滴水算法
 * Class ImageWater
 * @author mohuishou<1@lailin.xyz>
 * @package Mohuishou\ImageOCR
 */
class ImageWater{

    protected $hash_data=[];

    protected $data=[];

    protected $min_points=[];

    protected $max_len;

    protected $min_len;

    protected $avg_len;


    public function __construct($hash_data)
    {
        $this->hash_data=$hash_data;
    }

    /**
     * @param $sp
     * @author mohuishou<1@lailin.xyz>
     */
    public function water($sp){

    }

    /**
     * 获取字符数目
     * @author mohuishou<1@lailin.xyz>
     * @return float
     */
    public function getStrNum()
    {
        return round(count($this->hash_data) / $this->avg_len);
    }

    /**
     * 获取分割点，也是滴水算法的起始滴落点
     * @author mohuishou<1@lailin.xyz>
     * @return array
     */
    public function getSplitPoints(){
        $str_num=$this->getStrNum();

        $this->setMinPoints();

        //初始化分割点
        $sp=[];
        for ($p=0;$p<$str_num-1;$p++){
            $sp[$p]=0;
        }

        //区块结束
        $end=count($this->data)-1;

        //循环查找分割点
        for ($p=1;$p<$str_num-1;$p++){
            foreach ($this->min_points as $i ){
                $len=abs($i-$sp[$p-1]);
                if ($len<=$this->max_len&&$len>=$this->min_len){
                    $len=abs($end-$i);
                    if ($len>=$this->min_len*($str_num-$p)&&$len<=$this->max_len*($str_num-$p)){
                        $sp[$p]=$i;
                    }
                }
            }
        }

        //分割点从$sp[1]开始
        unset($sp[0]);

        return $sp;
    }

    /**
     * 竖直投影统计
     * @author mohuishou<1@lailin.xyz>
     * @return array
     */
    public function projectionStatistics(){
        $data=[];
        //转置
        $hash_data=ImageTool::hashTranspose($this->hash_data);
        foreach ($hash_data as $v){
            //统计1的值
            $tmp=array_count_values($v);
            $data[]=$tmp[1];
        }
        $this->data=$data;
    }

    /**
     * 设置极小值点
     * @author mohuishou<1@lailin.xyz>
     */
    public function setMinPoints(){

        //先获取竖直投影统计
        $this->projectionStatistics();

        //一头一尾不需要计算
        for ($i=1;$i<count($this->data)-1;$i++){
            $res=$this->isMinPoint($i);
            if (is_array($res)){
                $this->min_points[]=$res;
                $i=$res[1];
            }else if (is_bool($res)){
                if ($res){
                    $this->min_points[]=$i;
                }
            }
        }
    }

    /**
     * 判断极小值点
     * @param $i
     * @author mohuishou<1@lailin.xyz>
     * @return array|bool
     */
    public function isMinPoint($i){
        if ($i==0){
            return false;
        }
        if ($this->H($i)>0&&$this->H($i-1)<0){
            return true;
        }
        if ($this->H($i-1)<0){
            if ($this->H($i)==0){
                $j=$i;
                while ($this->H($j)==0){
                    $j++;
                }
                if ($this->H($j)>0){
                    return [$i,$j];
                }
                return false;
            }
        }
        return false;
    }

    /**
     * @param $i
     * @author mohuishou<1@lailin.xyz>
     * @return mixed
     * @throws \Exception
     */
    protected function H($i){
        if ($i==count($this->data)-1){
            throw new \Exception("最后一点无需计算");
        }
        return $this->data[$i+1]-$this->data[$i];
    }


}