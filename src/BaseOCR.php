<?php
/**
 * Created by PhpStorm.
 * User: mohuishou<1@lailin.xyz>
 * Date: 2017/3/20
 * Time: 0:35
 */

namespace Mohuishou\ImageOCR;

/**
 * 基础识别类
 * 请先根据验证码类型，选择合适的预处理方式
 * Class BaseOCR
 * @author mohuishou<1@lailin.xyz>
 * @package Mohuishou\ImageOCR
 */
abstract class BaseOCR{

    /**
     * 标准化字符
     * @var array
     * @author mohuishou<1@lailin.xyz>
     */
    protected $standard_data=[];

    /**
     * @var Image
     * @author mohuishou<1@lailin.xyz>
     */
    protected $image;

    /**
     * 对象初始化，需要分割得到标准化数组
     * 在构造函数内，需要设置 $this->standard_data & $this->image
     * 详细实现可以参见example文件夹内的函数类
     * BaseOCR constructor.
     * @param Image $image
     */
    abstract public function __construct(Image $image);

    /**
     * 验证码识别，返回识别得到的数字
     * @author mohuishou<1@lailin.xyz>
     * @return mixed
     */
    abstract public function ocr();

    /**
     * 识别库的建立
     * @author mohuishou<1@lailin.xyz>
     * @return mixed
     */
    abstract public function study();


    /**
     * 获取标准化的二值化数组
     * @author mohuishou<1@lailin.xyz>
     * @return array
     */
    public function getHashData(){
        return $this->standard_data;
    }

    /**
     * @param $max_grey
     * @author mohuishou<1@lailin.xyz>
     * @return mixed
     */
    public function setMaxGrey($max_grey){
        return $this->image->max_grey=$max_grey;
    }

    /**
     * @param $min_grey
     * @author mohuishou<1@lailin.xyz>
     * @return mixed
     */
    public function setMinGrey($min_grey){
        return $this->image->min_grey=$min_grey;
    }

    /**
     * @param $standard_width
     * @author mohuishou<1@lailin.xyz>
     * @return mixed
     */
    public function setStandardWidth($standard_width){
        return $this->image->standard_width=$standard_width;
    }

    /**
     * @param $standard_height
     * @author mohuishou<1@lailin.xyz>
     * @return mixed
     */
    public function setStandardHeight($standard_height){
        return $this->image->standard_height=$standard_height;
    }
}

