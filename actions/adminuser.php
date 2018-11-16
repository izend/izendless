<?php

/**
 *
 * @copyright  2011-2018 izend.org
 * @version    4
 * @link       http://www.izend.org
 */

require_once 'userhasrole.php';
require_once 'models/user.inc';

function adminuser($lang, $arglist=false) {
	global $with_toolbar;

	if (!user_has_role('administrator')) {
		return run('error/unauthorized', $lang);
	}

	$user_id=false;

	if (is_array($arglist)) {
		if (isset($arglist[0])) {
			$user_id=$arglist[0];
		}
	}

	if (!$user_id) {
		return run('error/notfound', $lang);
	}

	$user_id = user_id($user_id);
	if (!$user_id) {
		return run('error/notfound', $lang);
	}

	$useredit = build('useredit', $lang, $user_id);

	if ($useredit === false) {
		return redirect('admin', $lang);
	}

	head('title', translate('admin:title', $lang));
	head('description', false);
	head('keywords', false);
	head('robots', 'noindex');

	$admin=true;
	$banner = build('banner', $lang, $with_toolbar ? false : compact('admin'));
	$toolbar = $with_toolbar ? build('toolbar', $lang, compact('admin')) : false;

	$content = view('adminuser', $lang, compact('useredit'));

	$output = layout('standard', compact('lang', 'toolbar', 'banner', 'content'));

	return $output;
}

