<?php

/**
 *
 * @copyright  2010-2011 izend.org
 * @version    3
 * @link       http://www.izend.org
 */

require_once 'userhasrole.php';
require_once 'models/node.inc';

function node($lang, $arglist=false) {
	global $system_languages;

	if (!user_has_role('writer')) {
		return run('error/unauthorized', $lang);
	}

	$slang=false;
	if (isset($_GET['slang'])) {
		$slang = $_GET['slang'];
	}
	else {
		$slang=$lang;
	}
	if (!in_array($slang, $system_languages)) {
		return run('error/notfound', $lang);
	}

	$node=false;

	if (is_array($arglist)) {
		if (isset($arglist[0])) {
			$node=$arglist[0];
		}
	}

	if (!$node) {
		return run('error/notfound', $lang);
	}

	$node_id = node_id($node);
	if (!$node_id) {
		return run('error/notfound', $lang);
	}

	$r = node_get($lang, $node_id);
	if (!$r) {
		return run('error/notfound', $lang);
	}
	extract($r); /* node_number node_ignored node_name node_title node_abstract node_cloud node_nocomment node_nomorecomment node_ilike node_tweet node_plusone */

	$node_comment=!$node_nocomment;
	$node_morecomment=!$node_nomorecomment;

	head('title', $node_id);
	head('description', $node_abstract);
	head('keywords', $node_cloud);
	head('robots', 'noindex, nofollow');

	$edit=user_has_role('writer') ? url('editnode', $_SESSION['user']['locale']) . '/'. $node_id . '?' . 'clang=' . $lang : false;
	$validate=url('node', $lang) . '/' . $node_id;
	$banner = build('banner', $lang, compact('edit', 'validate'));

	$node_contents = build('nodecontent', $lang, $node_id);

	$content = view('node', $slang, compact('node_id', 'node_name', 'node_title', 'node_abstract', 'node_cloud', 'node_created', 'node_modified', 'node_comment', 'node_morecomment', 'node_ilike', 'node_tweet', 'node_plusone', 'node_contents'));

	$output = layout('standard', compact('banner', 'content'));

	return $output;
}

