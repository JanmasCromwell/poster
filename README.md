### 合成海报

#### Poster类解析

```
不传path则创建空白画布
传path则以path文件为画布

$poster = new Post(
    $path //图片路径
);
```

##### create

- 创建画布

```
$poster->create(
	$width=0,//画布宽
	$height=0//画布高
	);
```

##### setPicture

- 添加图片

```
$poster->setPicture(
	$path,//图片路径
	$tar_w=0,//原图要载入的宽度 -1为跟随画布大小
	$tar_h=0,//原图要载入的高度 -1为跟随画布大小
	$dst_x=0,//设定需要载入的图片在新图中的x坐标
	$dst_y=0,//设定需要载入的图片在新图中的y坐标
	$src_w=-1,//原图要载入的宽度 -1为原图大小
	$src_h=-1,//原图要载入的高度-1为原图大小
	$src_x=0,//设定载入图片要载入的区域x坐标
	$src_y=0//设定载入图片要载入的区域y坐标
	);
```

| 参数名 | 类型   | 描述                              |
| ------ | ------ | --------------------------------- |
| $path  | string | 图片路径                          |
| $tar_w | int    | 原图要载入的宽度                  |
| $tar_h | int    | 原图要载入的高度                  |
| $dst_x | int    | 设定需要载入的图片在新图中的x坐标 |
| $dst_y | int    | 设定需要载入的图片在新图中的y坐标 |
| $src_w | int    | 原图要载入的宽度 -1为原图大小     |
| $src_h | int    | 原图要载入的高度-1为原图大小      |
| $src_x | int    | 设定载入图片要载入的区域x坐标     |
| $src_y | int    | 设定载入图片要载入的区域y坐标     |
|        |        |                                   |


##### setTtf

- 设置字体

```
$poster->setTtf($path);
```





##### setText

- <font color="red">在添加文字前一定要先设置字体</font>
- 添加文字

```
$post->setText(
	$string='',//字符串
	$areaWidth=100,//字符区域大小
	$fontSize=12,//字体大小
	$rowHeight=12,//行高
	$maxRow=2,//最多几行
	$angle=0,//
	$x=0,//x轴偏移
	$y=0,//y轴偏移
	$color=[]//数组 [0=>255,1=>255,2=>255] 0是红色通道 1是绿色通道 2是蓝色通道 
    );
```
| 参数名 | 类型   | 描述                              |
| ------ | ------ | --------------------------------- |
| $string  | string | 字符串                          |
| $areaWidth | int    | 字符区域大小                  |
| $fontSize | int    | 字体大小                  |
| $rowHeight | int    | 行高 |
| $maxRow | int    | 最多几行 |
| $angle | int    | 原图要载入的宽度 -1为原图大小     |
| $x | int    | x轴偏移      |
| $y | int    | y轴偏移     |
|   color     |  array      | 数组 [0=>255,1=>255,2=>255] 0是红色通道 1是绿色通道 2是蓝色通道 |





##### output

- 输出资源到文件

```
$poster->output(
	$filename,//文件路径（包含文件名）
	$quality=5//图片质量
	);
```

##### stream

- 输出资源到浏览器

```
$poster->stream(
	$ext='png' //图片后缀
	);
```


##### eg：

```
  $poster = new Poster();
  $poster->create(614,870);//创建画布
  $poster->setBackgroundColor(0,0,0xFFFFFF);//设置画布背景颜色
  $poster->setPicture('./../goods.png',614,583,-1,-1);//添加图片
  $poster->setPicture('./../b.png',177,177,-1,-1,614-177-65,583+65/2);
  $poster->setTtf('./ttf/ziti.ttf');//设置字体
  $poster->setText('长按识别二维码',177,18,12,1,0,644-177-65-65/2,583+177+65,[0,0,0]);//添加文字（添加文字前必须要先设置字体）
  $poster->output('./test/a.png');//输出文件（现在仅能输出到文本暂不支持输出到浏览器）
```

##### <font color="red">注：仅支持jpg、png、jpeg、gif后缀类型</font>
