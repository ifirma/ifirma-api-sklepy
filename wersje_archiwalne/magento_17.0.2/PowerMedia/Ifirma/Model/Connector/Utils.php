<?php

namespace ifirma;

/**
 * Description of Utils
 *
 * @author bbojanowicz
 */
class Utils {

	/**
	 * 
	 * @param int $percent
	 * @return float
	 */
	public static function percentToFloat($percent) {
		return empty($percent) ? 0.0 : $percent / 100;
	}

	/**
	 * 
	 * @param string $hex
	 * @return string
	 */
	public static function hexToStr($hex) {
		$string = '';
		for ($i = 0; $i < strlen($hex) - 1; $i+=2) {
			$string .= chr(hexdec($hex[$i] . $hex[$i + 1]));
		}
		return $string;
	}

	/**
	 * 
	 * @param string $key
	 * @param string $data
	 * @return string
	 */
	public static function hmac($key, $data){
		$blocksize = 64;
		$hashfunc = 'sha1';
		if (strlen($key) > $blocksize)
			$key = pack('H*', $hashfunc($key));
		$key = str_pad($key, $blocksize, chr(0x00));
		$ipad = str_repeat(chr(0x36), $blocksize);
		$opad = str_repeat(chr(0x5c), $blocksize);
		$hmac = pack('H*', $hashfunc(($key ^ $opad) . pack('H*', $hashfunc(($key ^ $ipad) . $data))));
		return bin2hex($hmac);
	}
}

