<?php

namespace tekintian\qrcode_tiny;

// use QRencode;
// use QRspec;
// use QRsplit;
// use QRtools;

define('QR_CACHEABLE', false); // use cache - more disk reads but less CPU power, masks and format templates are stored there
define('QR_CACHE_DIR', false); // used when QR_CACHEABLE === true
define('QR_LOG_DIR', false); // default error logs dir

define('QR_FIND_BEST_MASK', true); // if true, estimates best mask (spec. default, but extremally slow; set to false to significant performance boost but (propably) worst quality code
define('QR_FIND_FROM_RANDOM', 2); // if false, checks all masks available, otherwise value tells count of masks need to be checked, mask id are got randomly
define('QR_DEFAULT_MASK', 2); // when QR_FIND_BEST_MASK === false

define('QR_PNG_MAXIMUM_SIZE', 1024); // maximum allowed png image width (in pixels), tune to make sure GD and PHP can handle such big images

// Encoding modes
define('QR_MODE_NUL', -1);
define('QR_MODE_NUM', 0);
define('QR_MODE_AN', 1);
define('QR_MODE_8', 2);
define('QR_MODE_KANJI', 3);
define('QR_MODE_STRUCTURE', 4);

// Levels of error correction.

define('QR_ECLEVEL_L', 0);
define('QR_ECLEVEL_M', 1);
define('QR_ECLEVEL_Q', 2);
define('QR_ECLEVEL_H', 3);

// Supported output formats

define('QR_FORMAT_TEXT', 0);
define('QR_FORMAT_PNG', 1);

define('QRSPEC_VERSION_MAX', 40);
define('QRSPEC_WIDTH_MAX', 177);

define('QRCAP_WIDTH', 0);
define('QRCAP_WORDS', 1);
define('QRCAP_REMINDER', 2);
define('QRCAP_EC', 3);
/**
 * PHP qrcode 二维码工具类 精简版本
 * @Author: Tekin
 * @Date:   2019-04-05 11:53:34
 * @Last Modified 2019-04-05
 */
// phpqrcode lib库文件载入
// require_once __DIR__ . DIRECTORY_SEPARATOR . 'qrcode_tiny_lib.php';
/**
 * PHP qrcode 二维码工具类 精简版本
 */
class QRcode {

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
	/**
	 * [encodeMask description]
	 * @param  QRinput $input [description]
	 * @param  [type]  $mask  [description]
	 * @return [type]         [description]
	 */
	public function encodeMask(QRinput $input, $mask) {
		if ($input->getVersion() < 0 || $input->getVersion() > QRSPEC_VERSION_MAX) {
			throw new Exception('wrong version');
		}
		if ($input->getErrorCorrectionLevel() > QR_ECLEVEL_H) {
			throw new Exception('wrong level');
		}

		$raw = new QRrawcode($input);

		QRtools::markTime('after_raw');

		$version = $raw->version;
		$width = QRspec::getWidth($version);
		$frame = QRspec::newFrame($version);

		$filler = new FrameFiller($width, $frame);
		if (is_null($filler)) {
			return NULL;
		}

		// inteleaved data and ecc codes
		for ($i = 0; $i < $raw->dataLength + $raw->eccLength; $i++) {
			$code = $raw->getCode();
			$bit = 0x80;
			for ($j = 0; $j < 8; $j++) {
				$addr = $filler->next();
				$filler->setFrameAt($addr, 0x02 | (($bit & $code) != 0));
				$bit = $bit >> 1;
			}
		}

		QRtools::markTime('after_filler');

		unset($raw);

		// remainder bits
		$j = QRspec::getRemainder($version);
		for ($i = 0; $i < $j; $i++) {
			$addr = $filler->next();
			$filler->setFrameAt($addr, 0x02);
		}

		$frame = $filler->frame;
		unset($filler);

		// masking
		$maskObj = new QRmask();
		if ($mask < 0) {

			if (QR_FIND_BEST_MASK) {
				$masked = $maskObj->mask($width, $frame, $input->getErrorCorrectionLevel());
			} else {
				$masked = $maskObj->makeMask($width, $frame, (intval(QR_DEFAULT_MASK) % 8), $input->getErrorCorrectionLevel());
			}
		} else {
			$masked = $maskObj->makeMask($width, $frame, $mask, $input->getErrorCorrectionLevel());
		}

		if ($masked == NULL) {
			return NULL;
		}

		QRtools::markTime('after_mask');

		$this->version = $version;
		$this->width = $width;
		$this->data = $masked;

		return $this;
	}

	/**
	 * [encodeInput description]
	 * @param  QRinput $input [description]
	 * @return [type]         [description]
	 */
	public function encodeInput(QRinput $input) {
		return $this->encodeMask($input, -1);
	}

	/**
	 * [encodeString8bit description]
	 * @param  [type] $string  [description]
	 * @param  [type] $version [description]
	 * @param  [type] $level   [description]
	 * @return [type]          [description]
	 */
	public function encodeString8bit($string, $version, $level) {
		if (string == NULL) {
			throw new Exception('empty string!');
			return NULL;
		}

		$input = new QRinput($version, $level);
		if ($input == NULL) {
			return NULL;
		}

		$ret = $input->append($input, QR_MODE_8, strlen($string), str_split($string));
		if ($ret < 0) {
			unset($input);
			return NULL;
		}
		return $this->encodeInput($input);
	}

	/**
	 * [encodeString description]
	 * @param  [type] $string        [description]
	 * @param  [type] $version       [description]
	 * @param  [type] $level         [description]
	 * @param  [type] $hint          [description]
	 * @param  [type] $casesensitive [description]
	 * @return [type]                [description]
	 */
	public function encodeString($string, $version, $level, $hint, $casesensitive) {

		if ($hint != QR_MODE_8 && $hint != QR_MODE_KANJI) {
			throw new Exception('bad hint');
			return NULL;
		}

		$input = new QRinput($version, $level);
		if ($input == NULL) {
			return NULL;
		}

		$ret = QRsplit::splitStringToQRinput($string, $input, $hint, $casesensitive);
		if ($ret < 0) {
			return NULL;
		}

		return $this->encodeInput($input);
	}
}