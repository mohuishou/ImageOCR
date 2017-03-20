<?php
/**
 * Created by PhpStorm.
 * User: mohuishou<1@lailin.xyz>
 * Date: 2017/3/20
 * Time: 0:35
 */

namespace Mohuishou\ImageOCR;

class ImageOCR{

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

    protected $grey_data;

    protected $hash_data;

    /**
     * 对象初始化，需要分割得到标准化数组
     * 在构造函数内，需要设置 $this->standard_data & $this->image
     * BaseOCR constructor.
     * @param Image $image
     */
    public function __construct(Image $image){
        $this->image=$image;
    }

    /**
     * 图片灰度化
     * @author mohuishou<1@lailin.xyz>
     * @return array
     */
    public function grey(){
        return $this->grey_data=$this->image->rgb2grey();
    }

    /**
     * 二值化，固定阈值
     * @param null $grey_data
     * @param null $max_gray
     * @param null $min_grey
     * @author mohuishou<1@lailin.xyz>
     * @return array
     */
    public function hash($grey_data=null,$max_gray=null,$min_grey=null){
        $grey_data==null && $grey_data=$this->grey_data;
        return $this->hash_data=$this->image->imageHash($grey_data,$max_gray,$min_grey);
    }

    /**
     * 二值化，背景动态阈值法
     * @param int $model
     * @param null $grey_data
     * @param null $max_gray
     * @param null $min_grey
     * @author mohuishou<1@lailin.xyz>
     * @return array
     * @throws \Exception
     */
    public function hashByBackground($model=0,$grey_data=null,$max_gray=null,$min_grey=null){
        $bg_grey=$this->image->getBgGrey($grey_data);
        switch ($model){
            case 0:
                $max_gray=$bg_grey;
                break;
            case 1:
                $min_grey=$bg_grey;
                break;
            case 2:
                $max_gray=$min_grey=$bg_grey;
                break;
            default:
                throw new \Exception("请选择正确模式！");
        }
        return $this->hash($grey_data,$max_gray,$min_grey);
    }

    
    public function removeSpots(){

    }

    public function removeSpotsByConnect(){

    }

    public function split(){

    }

    public function splitByConnect(){

    }

    public function splitByWater(){

    }

    /**
     * 设置image对象
     * @param Image $image
     * @author mohuishou<1@lailin.xyz>
     */
    public function setImage(Image $image){
        $this->image=$image;
    }

    /**
     * 获取标准化的二值化数组
     * @author mohuishou<1@lailin.xyz>
     * @return array
     */
    public function getStandardData(){
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

