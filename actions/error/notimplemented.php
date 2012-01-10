<?php

/**
 *
 * @copyright  2010-2012 izend.org
 * @version    1
 * @link       http://www.izend.org
 */

function notimplemented($lang) {
	head('title', translate('http_not_implemented:title', $lang));
	head('robots', 'noindex, nofollow');

	$contact=true;
	$banner = build('banner', $lang, compact('contact'));

	$contact_page=url('contact', $lang);
	$content = view('error/notimplemented', $lang, compact('contact_page'));

	$output = layout('standard', compact('banner', 'content'));

	header('HTTP/1.1 501 Not Implemented');

	return $output;
}

