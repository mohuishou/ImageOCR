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
     * 最大灰度值
     * @var int
     * @author mohuishou<1@lailin.xyz>
     */
    protected $max_grey=0;

    /**
     * 最小灰度值
     * @var int
     * @author mohuishou<1@lailin.xyz>
     */
    protected $min_grey=0;

    /**
     * 标准化图像的宽度
     * @var int
     * @author mohuishou<1@lailin.xyz>
     */
    protected $standard_width=10;

    /**
     * 标准化图像的高度
     * @var int
     * @author mohuishou<1@lailin.xyz>
     */
    protected $standard_height=10;

    /**
     * 对象初始化，需要分割得到标准化数组
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
}