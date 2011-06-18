<?php

/**
 *
 * @copyright  2010-2011 izend.org
 * @version    1
 * @link       http://www.izend.org
 */

require_once 'sendhttp.php';

function qrencode($s, $size=100, $quality='M') {
	$url = 'http://chart.googleapis.com/chart';
	$args = array(
		'cht'	=> 'qr',
		'chf'	=> 'bg,s,ffffff',
		'chs'	=> "${size}x${size}",
		'chld'	=> "${quality}|0",
		'chl'	=> $s,
	);

	$response=sendget($url, $args);

	if (!$response or $response[0] != 200) {
		return false;
	}

	return $response[2];
}

