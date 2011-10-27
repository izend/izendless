<?php

/**
 *
 * @copyright  2010-2011 izend.org
 * @version    9
 * @link       http://www.izend.org
 */

require_once 'userhasrole.php';
require_once 'models/thread.inc';

function story($lang, $arglist=false) {
	$story=$page=false;

	if (is_array($arglist)) {
		if (isset($arglist[0])) {
			$story=$arglist[0];
		}
		if (isset($arglist[1])) {
			$page=$arglist[1];
		}
	}

	if (!$story) {
		return run('error/notfound', $lang);
	}

	$story_id = thread_id($story);
	if (!$story_id) {
		return run('error/notfound', $lang);
	}

	$page_id = thread_node_id($story_id, $page);
	if (!$page_id) {
		return run('error/notfound', $lang);
	}

	$r = thread_get($lang, $story_id);
	if (!$r) {
		return run('error/notfound', $lang);
	}
	extract($r); /* thread_type thread_name thread_title thread_abstract thread_cloud thread_nocloud thread_nosearch thread_nocomment thread_nomorecomment */

	if ($thread_type != 'story') {
		return run('error/notfound', $lang);
	}

	$story_name = $thread_name;
	$story_title = $thread_title;
	$story_abstract = $thread_abstract;
	$story_cloud = $thread_cloud;
	$story_nocloud = $thread_nocloud;
	$story_nosearch = $thread_nosearch;

	$r = thread_get_node($lang, $story_id, $page_id);
	if (!$r) {
		return run('error/notfound', $lang);
	}
	extract($r); /* node_number node_ignored node_name node_title node_abstract node_cloud node_nocomment node_nomorecomment node_ilike node_tweet node_plusone */

	if ($node_ignored) {
		return run('error/notfound', $lang);
	}

	$page_name=$node_name;
	$page_title=$node_title;
	$page_abstract=$node_abstract;
	$page_cloud=$node_cloud;
	$page_number=$node_number;

	if ($story_title) {
		head('title', $story_title);
	}
	if ($page_abstract) {
		head('description', $page_abstract);
	}
	else if ($story_abstract) {
		head('description', $story_abstract);
	}
	if ($page_cloud) {
		head('keywords', $page_cloud);
	}
	else if ($story_cloud) {
		head('keywords', $story_cloud);
	}

	$page_contents = build('nodecontent', $lang, $page_id);

	$page_comment=false;
	if (!($thread_nocomment or $node_nocomment)) {
		$moderate=user_has_role('moderator');
		$nomore=(!$page_contents or $thread_nomorecomment or $node_nomorecomment) ? true : false;
		$page_url = url('story', $lang) . '/'. $story_name . '/' . $page_name;
		$page_comment = build('nodecomment', $lang, $page_id, $page_url, $nomore, $moderate);
	}

	$besocial=$sharebar=false;
	if ($page_contents or $page_comment) {
		$ilike=false;
		$tweetit=false;
		$plusone=false;
		$besocial=build('besocial', $lang, compact('ilike', 'tweetit', 'plusone'));
		$ilike=$node_ilike;
		$tweetit=$node_tweet;
		$plusone=$node_plusone;
		$sharebar=build('sharebar', $lang, compact('ilike', 'tweetit', 'plusone'));
	}

	$content = view('storycontent', false, compact('page_id', 'page_title', 'page_contents', 'page_comment', 'page_number', 'besocial'));

	$search=false;
	if (!$story_nosearch) {
		$search_text='';
		$search_url= url('search', $lang, $story_name);
		$suggest_url= url('suggest', $lang, $story_name);
		$search=view('searchinput', $lang, compact('search_url', 'search_text', 'suggest_url'));
	}

	$cloud=false;
	if (!$story_nocloud) {
		$cloud = build('cloud', $lang, $story_id, false, 50, true, true);
	}

	$summary=array();
	$r = thread_get_contents($lang, $story_id);
	if ($r) {
		$story_url = url('story', $lang) . '/'. $story_name;
		foreach ($r as $c) {
			extract($c);	/* node_id node_name node_title node_number */
			$summary_page_id = $node_id;
			$summary_page_title = $node_title;
			$summary_page_url=$story_url . '/' . $node_name;
			$summary[] = compact('summary_page_id', 'summary_page_title', 'summary_page_url');
		}
	}

	$title=false;
	if ($story_title) {
		$headline_text=$story_title;
		$headline_url=false;
		$headline=compact('headline_text', 'headline_url');
		$title = view('headline', false, $headline);
	}

	$sidebar = view('sidebar', false, compact('search', 'cloud', 'title', 'summary'));

	$search=!$story_nosearch ? compact('search_url', 'search_text', 'suggest_url') : false;
	$edit=user_has_role('writer') ? url('storyedit', $_SESSION['user']['locale']) . '/'. $story_id . '/' . $page_id . '?' . 'clang=' . $lang : false;
	$validate=url('story', $lang) . '/' . $story_name . '/' . $page_name;
	$banner = build('banner', $lang, compact('headline', 'edit', 'validate', 'search'));

	$output = layout('standard', compact('sharebar', 'banner', 'sidebar', 'content'));

	return $output;
}
