<?php

namespace tekintian;

use tekintian\qrcode_tiny\QRencode;

/**
 * PHP qrcode 二维码工具类 精简版本
 * @Author: Tekin
 * @Date:   2019-04-05 11:53:34
 * @Last Modified 2019-04-05
 */

class QrcodeTiny {

	public $version;
	public $width;
	public $data;
	/**
	 * png二维码图片生成
	 * @param  [type]  $text         [description]
	 * @param  boolean $outfile      [description]
	 * @param  [type]  $level        [description]
	 * @param  integer $size         [description]
	 * @param  integer $margin       [description]
	 * @param  boolean $saveandprint [description]
	 * @return [type]                [description]
	 */
	public static function png($text, $outfile = false, $level = QR_ECLEVEL_L, $size = 3, $margin = 4, $saveandprint = false) {
		$enc = QRencode::factory($level, $size, $margin);
		return $enc->encodePNG($text, $outfile, $saveandprint = false);
	}
	/**
	 * [jpg description]
	 * @param  [type]  $text         [description]
	 * @param  boolean $outfile      [description]
	 * @param  [type]  $level        [description]
	 * @param  integer $size         [description]
	 * @param  integer $margin       [description]
	 * @param  boolean $saveandprint [description]
	 * @return [type]                [description]
	 */
	public static function jpg($text, $outfile = false, $level = QR_ECLEVEL_L, $size = 3, $margin = 4, $saveandprint = false) {
		$enc = QRencode::factory($level, $size, $margin);
		return $enc->encodeJPG($text, $outfile, $saveandprint = false);
	}

	/**
	 * text二维码生成
	 * @param  [type]  $text    [description]
	 * @param  boolean $outfile [description]
	 * @param  [type]  $level   [description]
	 * @param  integer $size    [description]
	 * @param  integer $margin  [description]
	 * @return [type]           [description]
	 */
	public static function text($text, $outfile = false, $level = QR_ECLEVEL_L, $size = 3, $margin = 4) {
		$enc = QRencode::factory($level, $size, $margin);
		return $enc->encode($text, $outfile);
	}

	/**
	 * [raw description]
	 * @param  [type]  $text    [description]
	 * @param  boolean $outfile [description]
	 * @param  [type]  $level   [description]
	 * @param  integer $size    [description]
	 * @param  integer $margin  [description]
	 * @return [type]           [description]
	 */
	public static function raw($text, $outfile = false, $level = QR_ECLEVEL_L, $size = 3, $margin = 4) {
		$enc = QRencode::factory($level, $size, $margin);
		return $enc->encodeRAW($text, $outfile);
	}

}