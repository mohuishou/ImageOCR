<?php
/**
 * Created by PhpStorm.
 * User: mohuishou<1@lailin.xyz>
 * Date: 2017/3/20
 * Time: 20:33
 */

namespace Mohuishou\ImageOCR;
interface iOCR{
    /**
     * 验证码识别，返回识别得到的数字
     * @author mohuishou<1@lailin.xyz>
     * @return mixed
     */
    public function ocr();

    /**
     * 识别库的建立
     * @author mohuishou<1@lailin.xyz>
     * @return mixed
     */
    public function study();
}