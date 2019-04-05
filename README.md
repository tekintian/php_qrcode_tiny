# php qrcode tiny 二维码生成精简版
php生成带LOGO的二维码图片,支持png, jpg格式, 单文件轻巧版!

## 使用方法

~~~shell
# 切换至项目根目录后执行以下命令安装本工具
composer require "tekintian/php_qrcode_tiny"
~~~



## 生成二维码

~~~php
# autoload.php自动载入
require_once __DIR__ . 'vendor/autoload.php';

$qr_data = 'http://dev.yunnan.ws'; //二维码数据
$qr_name = 'myqr.png';
$qr_error_correction_level = 'L'; //纠错级别：L、M、Q、H
$qr_matrix_point_size = 10; //二维码矩阵点大小，单位：点， 1到10 数值越大生成的图片就越大

\tekintian\QRcodeTiny::png($qr_data, $qr_name, $qr_error_correction_level, $qr_matrix_point_size, 2); 


~~~





~~~php
# 生成带LOGO的二维码图片demo
# autoload.php自动载入
require_once __DIR__ . 'vendor/autoload.php';

$data = 'http://dev.yunnan.ws'; //二维码数据
$qr_error_correction_level = 'L'; //纠错级别：L、M、Q、H
$qr_matrix_point_size = 10; //二维码图片的大小，单位：点， 1到10
\tekintian\QRcodeTiny::png($data, 'myqr.png', $qr_error_correction_level, $qr_matrix_point_size, 2); //不带Logo二维码的文件名
//echo "二维码已生成" . "<br />";
$logo = 'logo.png'; //需要显示在二维码中的Logo图像
$QR = 'myqr.png';
if ($logo !== false) {
	$QR = imagecreatefromstring(file_get_contents($QR));
	$logo = imagecreatefromstring(file_get_contents($logo));
	$QR_width = imagesx($QR);
	$QR_height = imagesy($QR);
	$logo_width = imagesx($logo);
	$logo_height = imagesy($logo);
	$logo_qr_width = $QR_width / 5;
	$scale = $logo_width / $logo_qr_width;
	$logo_qr_height = $logo_height / $scale;
	$from_width = ($QR_width - $logo_qr_width) / 2;
	imagecopyresampled($QR, $logo, $from_width, $from_width, 0, 0, $logo_qr_width, $logo_qr_height, $logo_width, $logo_height);
}
//imagepng ( $QR, 'qr/qr.png' );//带Logo二维码的文件名

// 输出图像到浏览器
header('Content-Type: image/png');
imagepng($QR); //输出带Logo二维码图片到浏览器, More: https://www.php.net/manual/zh/function.imagepng.php

exit();
//qr end

~~~

