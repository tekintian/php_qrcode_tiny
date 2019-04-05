<?php
namespace tekintian\qrcode_tiny;
/**
 * @Author: tekintian
 * @Date:   2019-04-05 14:59:59
 * @Last Modified 2019-04-05
 */

class QRrsblock {
	public $dataLength;
	public $data = array();
	public $eccLength;
	public $ecc = array();

	public function __construct($dl, $data, $el, &$ecc, QRrsItem $rs) {
		$rs->encode_rs_char($data, $ecc);

		$this->dataLength = $dl;
		$this->data = $data;
		$this->eccLength = $el;
		$this->ecc = $ecc;
	}
};
