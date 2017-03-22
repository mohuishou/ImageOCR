<?php
/**
 * Created by PhpStorm.
 * User: mohuishou<1@lailin.xyz>
 * Date: 2017/3/19
 * Time: 23:32
 */

namespace Mohuishou\ImageOCR\Example;
use Mohuishou\ImageOCR\Image;
use Mohuishou\ImageOCR\ImageOCR;
use Mohuishou\ImageOCR\ImageTool;

/**
 * 图像识别
 * Class ImageOCR
 * @author mohuishou<1@lailin.xyz>
 * @package Mohuishou\ImageOCR
 */
class OCR {

    protected $image_ocr;

    protected $standard_data;

    protected $hash_data=[];

    public function __construct($path)
    {
        $image=new Image($path);
        $this->image_ocr=new ImageOCR($image);

        //初始化
        $this->image_ocr->setMaxGrey(90);
        $this->image_ocr->setMinGrey(10);
        $this->image_ocr->setStandardWidth(13);
        $this->image_ocr->setStandardHeight(20);
    }

    protected function standard(){
        //避免重复调用
        if (!empty($this->standard_data)){
            return $this->standard_data;
        }

        try{
            //第一步灰度化
            $this->image_ocr->grey();

            //第二步二值化
            $this->image_ocr->hashByBackground(ImageOCR::MAX_MODEL);

            //下一步会用到连通域分割法，先初始化
            $this->image_ocr->setImageConnect();

            //第三部除噪&第四步分割
            $this->image_ocr->splitByConnect();

            //第五步标准化
            return $this->image_ocr->standard();
        }catch (\Exception $e){
            echo $e->getMessage();
        }
        return false;
    }

    public function draw(){
        foreach ($this->standard() as $v){
            ImageTool::drawBrowser($v);
            echo "<br />";
        }
    }

    public function save($path){
        $this->image_ocr->saveImage($path);
    }

    /**
     * 返回识别结果
     * @author mohuishou<1@lailin.xyz>
     */
    public function ocr(){
        $res = [];
        $hash_data=$this->getHash();
        $db=new StorageFile();
        $samples=$db->get();
        foreach ($hash_data as $k=>$v){
            $res[]=$this->compare($v,$samples);
        }
        return implode("",$res);
    }

    /**
     * 和特征值库进行对比
     * @author mohuishou<1@lailin.xyz>
     * @param array $hash 待识别的二值化图像字符串
     * @param array $samples 特征值库的数组
     * @return string $code 返回识别的字符
     */
    public function compare($hash,$samples) {
        $code=0;
        $s = 0;
        foreach ($samples as $k=>$v){
            foreach ($v as $val){
                $samples_hash_data=$val;
                $c = count( array_intersect_assoc ($samples_hash_data, $hash) );
                if($c>$s){
                    $s=$c;
                    $code=$k;
                }
                if($s>0.99*count($samples_hash_data)){
                    return $k;
                }
            }
        }
        return $code;
    }

    public function getHash(){
        if (!empty($this->hash_data)){
            return $this->hash_data;
        }
        $standard=$this->standard();
        foreach ($standard as $k=>$v){
            $this->hash_data[$k]=[];
            foreach ($v as $value){
                $this->hash_data[$k]=array_merge($this->hash_data[$k],$value);
            }
        }
        return $this->hash_data;
    }


    public function study($code){
        $hash_data=$this->getHash();
        $standard_data=$this->standard();
        $code_arr=str_split($code);
        if(count($code_arr)!=count($standard_data)){
            echo "错误！您输入的验证码位数与识别的位数不符，请检查您的验证码！<br />";
            echo "您输入的字符串为：$code <br />";
            echo "标准化数组为：<br />";
            $this->draw();
            exit(0);
        }
        $db=new StorageFile();
        foreach ($code_arr as $k=>$v){
            $db->add($v,$hash_data[$k]);
        }
    }
}