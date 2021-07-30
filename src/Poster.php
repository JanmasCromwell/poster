<?php
//+---------------------------------------------------------------------------------------------------------------------
//| 人生是荒芜的旅行，冷暖自知，苦乐在心
//+---------------------------------------------------------------------------------------------------------------------
//| Author:Janmas <janmas@126.com>
//+---------------------------------------------------------------------------------------------------------------------
//|
//+---------------------------------------------------------------------------------------------------------------------
namespace janmas;

class Poster
{

    /**
     * 画布
     * @var
     */
    protected $canvas;

    /**
     * 底片
     * @var mixed|null
     */
    protected $negative = null;

    /**
     * 底片信息
     * @var array|false|null
     */
    protected $negativeInfo = null;

    /**
     * 字体信息
     * @var string
     */
    public $ttfPath = '';

    public function __construct($path = null)
    {
        if (!is_null($path)) {
            if (!is_file($path)) throw new \Exception('文件' . $path . '不存在');
            $this->negative = $path;
            $this->negativeInfo = getimagesize($this->negative);
            $this->negativeInfo['ext'] = image_type_to_extension($this->negativeInfo[2], false);
        }
    }

    /**
     * 创建画布
     * @param null $width
     * @param null $height
     * @return $this
     * @throws \Exception
     */
    public function create($width = null, $height = null)
    {
        if (is_null($this->negative)) {
            if (is_null($width) || is_null($height)) {
                throw new \Exception('请传入宽高');
            }
            $this->negativeInfo = [
                $width, $height
            ];
            $this->canvas = imagecreatetruecolor($width, $height);
        } else {
            $func = 'imagecreatefrom' . $this->negativeInfo['ext'];
            $this->canvas = $func($this->negative);
        }
        return $this;
    }

    /**
     * 设置背景颜色
     * @param int $x
     * @param int $y
     * @param array $color
     */
    public function setBackgroundColor($x = 0, $y = 0, $color = [])
    {
        if (is_array($color)) {
            $color = imagecolorallocate($this->canvas, array_shift($color) ?? 0, array_shift($color) ?? 0, array_shift($color) ?? 0);
        }
        imagefill($this->canvas, $x, $y, $color);
        return $this;
    }

    /**
     * @param string $path 需要载入的图片路径
     * @param int $src_w 原图要载入的宽度
     * @param int $src_h 原图要载入的高度
     * @param int $src_x 设定载入图片要载入的区域x坐标
     * @param int $src_y 设定载入图片要载入的区域y坐标
     * @param int $tar_w 设定载入的原图的宽度
     * @param int $tar_h 设定载入的原图的高度
     * @param int $dst_x 设定需要载入的图片在新图中的x坐标
     * @param int $dst_y 设定需要载入的图片在新图中的y坐标
     * @return $this
     */
    public function setPicture($path, $tar_w = -1, $tar_h = -1, $dst_x = 0, $dst_y = 0, $src_w = -1, $src_h = -1, $src_x = 0, $src_y = 0)
    {
        if (!is_file($path)) {
            throw new \Exception('文件' . $path . '不存在');
        }
        $picSize = getimagesize($path);
        $picHandleFunc = 'imagecreatefrom' . image_type_to_extension($picSize[2], false);
        $picGdResource = $picHandleFunc($path);
        $src_w = $src_w < 0 ? $picSize[0] : $src_w;
        $src_h = $src_h < 0 ? $picSize[1] : $src_h;

        $tar_w = $tar_w < 0 ? $this->negativeInfo[0] : $tar_w;
        $tar_h = $tar_h < 0 ? $this->negativeInfo[1] : $tar_h;

        imagecopyresampled($this->canvas, $picGdResource, $dst_x, $dst_y, $src_x, $src_y, $tar_w, $tar_h, $src_w, $src_h);
        imagedestroy($picGdResource);
        return $this;
    }

    /**
     * @param string $string 文字
     * @param int $areaWidth 文字区域大小
     * @param int $fontSize 字体大小
     * @param int $rowHeight 行高
     * @param int $maxRow 几行
     * @param int $angle 扭转
     * @param int $x x轴偏移
     * @param int $y y轴偏移
     * @param array $color 颜色
     * @return $this
     */
    public function setText($string = '', $areaWidth = 100, $fontSize = 12, $rowHeight = 12, $maxRow = 2, $angle = 0, $x = 0, $y = 0, $color = [])
    {
        if (empty($this->ttfPath)) {
            throw new \Exception('请先设置字体');
        }
        if (is_array($color) && !empty($color)) {
            $fontColor = imagecolorallocate($this->canvas, array_shift($color) ?? 0, array_shift($color) ?? 0, array_shift($color) ?? 0);
        } else {
            $fontColor = imagecolorallocate($this->canvas, 0, 0, 0);
        }
        $this->textalign($this->canvas, $string, $areaWidth, $x, $y, $fontSize, $this->ttfPath, $fontColor, $rowHeight, $maxRow);
        return $this;
    }

    public function setTtf($path)
    {
        $info = pathinfo($path);
        $this->ttfPath = $_SERVER['DOCUMENT_ROOT'] . '/' . $info['dirname'] . '/' . $info['basename'];
        return $this;
    }

    /**
     * 返回文件
     * @param $filename
     * @param int $quality
     */
    public function output($filename = null, $quality = 100)
    {
        $ext = explode('.', $filename);
        $ext = array_pop($ext);
        $this->checkDir(dirname($filename));
        if (method_exists($this, $ext)) {
            $this->$ext($filename, $quality);
        }
    }

    /**
     * 直接输出到浏览器
     * @param string $ext
     */
    public function stream($ext = 'png')
    {
        header('Content-Type:image/' . $ext != 'jpg' ?:'jpeg');
        if (method_exists($this, $ext)) {
            $this->$ext(null, 5);
        }
        exit;
    }

    public function getCanvas(){
        return $this->canvas;
    }

    public function getTtf(){
        return $this->ttfPath;
    }
    /**
     * 文字自动换行（摘自互联网）
     * @param $card 画板
     * @param $str 要换行的文字
     * @param $width 文字显示的宽度，到达这个宽度自动换行
     * @param $x 基础 x坐标
     * @param $y 基础Y坐标
     * @param $fontsize 文字大小
     * @param $fontfile 字体
     * @param $color array 字体颜色
     * @param $rowheight 行间距
     * @param $maxrow 最多多少行
     */
    private function textalign($card, $str, $width, $x, $y, $fontsize, $fontfile, $color, $rowheight, $maxrow)
    {
        $tempstr = '';
        $row = 0;
        $length = mb_strlen($str, 'utf8');
        for ($i = 0; $i < $length; $i++) {
            if ($row >= $maxrow) {
                break;
            }
            $tempstr = $tempstr . mb_substr($str, $i, 1, 'utf-8');//当前的文字
            $nextstr = $tempstr . mb_substr($str, $i + 1, 1, 'utf-8');//下一个字符串
            $nexttemp = imagettfbbox($fontsize, 0, $fontfile, $nextstr);//用来测量每个字的大小
            $nextsize = ($nexttemp[2] - $nexttemp[0]);
            if ($nextsize > $width - 10) {//大于整体宽度限制 直接换行 这一行写入画布
                $row = $row + 1;
                imagettftext($card, $fontsize, 0, $x, $y, $color, $fontfile, $tempstr);
                $y = $y + $fontsize + $rowheight;
                $tempstr = '';
            } else if ($i + 1 == mb_strlen($str, 'utf8') && $nextsize < $width - 10) {
                imagettftext($card, $fontsize, 0, $x, $y, $color, $fontfile, $tempstr);
            }
        }
        return true;
    }

    private function jpg($filename = null, $quality)
    {
        imagejpeg($this->canvas,$filename,$quality);
    }

    private function jpeg($filename = null, $quality)
    {
        imagejpeg($this->canvas,$filename,$quality);
    }

    private function png($filename = null, $quality)
    {
        $white = imagecolorallocate($this->canvas, 255, 255, 255);
        imagefill($this->canvas, 0, 0, $white);//把画布染成白色
        imagecolortransparent($this->canvas, $white);
        imagepng($this->canvas, $filename, ceil($quality/10));
    }

    private function gif($filename = null, $quality)
    {
        imagegif($this->canvas, $filename);
    }

    private function checkDir($dirname)
    {
        if (!is_dir($dirname) && !mkdir($dirname, 0777, true) && !is_dir($dirname) ) {
            throw new \RuntimeException(sprintf('Directory "%s" was not created', $dirname));
        }
    }

    public function __destruct()
    {
        imagedestroy($this->canvas);
    }
}
