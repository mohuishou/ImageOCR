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

    /**
     * 最大灰度值
     * @var int
     * @author mohuishou<1@lailin.xyz>
     */
    protected $max_grey=null;

    /**
     * 最小灰度值
     * @var int
     * @author mohuishou<1@lailin.xyz>
     */
    protected $min_grey=null;

    /**
     * 灰度化图像
     * @var array
     * @author mohuishou<1@lailin.xyz>\
     */
    protected $grey_data;

    /**
     * 二值化图像
     * @var array
     * @author mohuishou<1@lailin.xyz>
     */
    protected $hash_data;

    /**
     * @var ImageConnect
     * @author mohuishou<1@lailin.xyz>
     */
    protected $image_connect;

    /**
     * 背景除噪的三种模式
     */
    const MAX_MODEL=1;
    const MIN_MODEL=0;
    const BG_MODEL=2;

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
     * 保存输入的图片
     * @param string $path 需要保存的图片路径
     * @author mohuishou<1@lailin.xyz>
     */
    public function saveImage($path){
        imagepng($this->image->in_img,$path);
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
     * @param null|int $max_grey 最大灰度阈值
     * @param null|int $min_grey 最小灰度阈值
     * @author mohuishou<1@lailin.xyz>
     */
    public function hash($max_grey=null,$min_grey=null){
        $max_grey==null && $max_grey=$this->max_grey;
        $min_grey==null && $min_grey=$this->min_grey;
        $this->hash_data=$this->image->imageHash($this->grey_data,$max_grey,$min_grey);
    }

    /**
     * 二值化，背景动态阈值法
     * @param int $model 0:最大灰度值模式，1：最小灰度值模式，2：唯一模式
     * @param null|int $max_grey 最大灰度阈值
     * @param null|int $min_grey 最小灰度阈值
     * @author mohuishou<1@lailin.xyz>
     * @return array $hash_data 二值化图像数组
     * @throws \Exception
     */
    public function hashByBackground($model=self::MAX_MODEL,$max_grey=null,$min_grey=null){
        $bg_grey=$this->image->getBgGrey($this->grey_data);
        switch ($model){
            case 0:
                $min_grey=$bg_grey;
                break;
            case 1:
                $max_grey=$bg_grey;
                break;
            case 2:
                $max_grey=$min_grey=$bg_grey;
                break;
            default:
                throw new \Exception("请选择正确模式！");
        }
        return $this->hash($max_grey,$min_grey);
    }


    /**
     * 孤立点除噪法
     * @author mohuishou<1@lailin.xyz>
     * @throws \Exception
     */
    public function removeSpots(){
        $this->checkHashData();
        $this->hash_data=$this->image->removeHotSpots($this->hash_data);
    }

    /**
     * 连通域去噪
     * @author mohuishou<1@lailin.xyz>
     */
    public function removeSpotsByConnect(){
        $this->checkImageConnect();
        $this->hash_data=$this->image_connect->removeHotSpots();
    }

    /**
     * 连通域分割
     * @author mohuishou<1@lailin.xyz>
     */
    public function splitByConnect(){
        $this->checkImageConnect();
        $this->standard_data=$this->image_connect->split();
    }

    /**
     * 滴水算法分割
     * @author mohuishou<1@lailin.xyz>
     */
    public function splitByWater(){

    }


    /**
     * 图像标准化
     * @return array 标准化的数组对象，从左到右的顺序
     * @author mohuishou<1@lailin.xyz>
     * @throws \Exception
     */
    public function standard(){
        if ($this->standard_data==null)
            throw new \Exception("请先获取分割之后的图片",5);
        $data=[];
        foreach ($this->standard_data as $item) {
            $data[]=$this->image->standard($item);
        }
        return $this->standard_data=$data;
    }

    /**
     * 检查ImageConnect是否初始化
     * @author mohuishou<1@lailin.xyz>
     * @throws \Exception
     */
    protected function checkImageConnect(){
        if (!($this->image_connect instanceof ImageConnect)){
            throw new \Exception("请先调用setImageConnect初始化ImageConnect类",4);
        }
    }

    /**
     * 检查图像是否已经二值化
     * @author mohuishou<1@lailin.xyz>
     * @throws \Exception
     */
    protected function checkHashData(){
        if ($this->hash_data==null)
            throw new \Exception("请先将图片二值化",4);
    }

    /**
     * 初始化ImageConnect
     * @author mohuishou<1@lailin.xyz>
     */
    public function setImageConnect(){
        $this->checkHashData();

        $this->image_connect=new ImageConnect($this->hash_data);
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
        return $this->max_grey=$max_grey;
    }

    /**
     * @param $min_grey
     * @author mohuishou<1@lailin.xyz>
     * @return mixed
     */
    public function setMinGrey($min_grey){
        return $this->min_grey=$min_grey;
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

