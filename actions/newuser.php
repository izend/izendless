<?php

/**
 *
 * @copyright  2010-2018 izend.org
 * @version    2
 * @link       http://www.izend.org
 */

function newuser($lang) {
	head('title', translate('newuser:title', $lang));
	head('description', false);
	head('keywords', false);
	head('robots', 'noindex');

	$banner = build('banner', $lang);

	$register = build('register', $lang);

	$content = view('newuser', $lang, compact('register'));

	$output = layout('standard', compact('banner', 'content'));

	return $output;
}

