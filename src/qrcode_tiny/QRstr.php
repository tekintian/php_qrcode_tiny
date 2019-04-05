<?php
namespace tekintian\qrcode_tiny;
/**
 * @Author: tekintian
 * @Date:   2019-04-05 15:16:26
 * @Last Modified 2019-04-05
 */
class QRstr {
	public static function set(&$srctab, $x, $y, $repl, $replLen = false) {
		$srctab[$y] = substr_replace($srctab[$y], ($replLen !== false) ? substr($repl, 0, $replLen) : $repl, $x, ($replLen !== false) ? $replLen : strlen($repl));
	}
}