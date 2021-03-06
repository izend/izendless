<?php

/**
 *
 * @copyright  2010-2019 izend.org
 * @version    14
 * @link       http://www.izend.org
 */

require_once 'readarg.php';
require_once 'strtofname.php';
require_once 'models/node.inc';

function nodeeditor($lang, $clang, $node_id, $content_types) {
	$action='init';
	if (isset($_POST['node_edit'])) {
		$action='edit';
	}

	$node_name=$node_title=$node_abstract=$node_cloud=$node_image=$node_comment=$node_morecomment=$node_vote=$node_morevote=false;
	$node_ilike=$node_tweet=$node_linkedin=$node_pinit=$node_whatsapp=false;
	$node_visits=false;

	$node_nocomment=$node_nomorecomment=$node_novote=$node_nomorevote=true;

	switch($action) {
		case 'init':
		case 'reset':
			$r = node_get($clang, $node_id, false);
			if ($r) {
				extract($r);
			}
			$node_comment=!$node_nocomment;
			$node_morecomment=!$node_nomorecomment;
			$node_vote=!$node_novote;
			$node_morevote=!$node_nomorevote;

			break;
		case 'edit':
			if (isset($_POST['node_title'])) {
				$node_title=readarg($_POST['node_title']);
			}
			if (isset($_POST['node_name'])) {
				$node_name=strtofname(readarg($_POST['node_name']));
			}
			if (empty($node_name) and !empty($node_title)) {
				$node_name=strtofname($node_title);
			}
			if (isset($_POST['node_abstract'])) {
				$node_abstract=readarg($_POST['node_abstract']);
			}
			if (isset($_POST['node_cloud'])) {
				$node_cloud=readarg($_POST['node_cloud'], true, false);	// trim but DON'T strip!
				preg_match_all('/(\S+)/', $node_cloud, $r);
				$node_cloud=implode(' ', array_unique($r[0]));
			}
			if (isset($_POST['node_image'])) {
				$node_image=readarg($_POST['node_image']);
			}
			if (isset($_POST['node_visits'])) {
				$node_visits=readarg($_POST['node_visits']) == 'on' ? true : false;
			}
			if (isset($_POST['node_comment'])) {
				$node_comment=readarg($_POST['node_comment']) == 'on' ? true : false;
				$node_nocomment=!$node_comment;
			}
			if (isset($_POST['node_morecomment'])) {
				$node_morecomment=readarg($_POST['node_morecomment']) == 'on' ? true : false;
				$node_nomorecomment=!$node_morecomment;
			}
			if (isset($_POST['node_vote'])) {
				$node_vote=readarg($_POST['node_vote']) == 'on' ? true : false;
				$node_novote=!$node_vote;
			}
			if (isset($_POST['node_morevote'])) {
				$node_morevote=readarg($_POST['node_morevote']) == 'on' ? true : false;
				$node_nomorevote=!$node_morevote;
			}
			if (isset($_POST['node_ilike'])) {
				$node_ilike=readarg($_POST['node_ilike'] == 'on' ? true : false);
			}
			if (isset($_POST['node_tweet'])) {
				$node_tweet=readarg($_POST['node_tweet'] == 'on' ? true : false);
			}
			if (isset($_POST['node_linkedin'])) {
				$node_linkedin=readarg($_POST['node_linkedin'] == 'on' ? true : false);
			}
			if (isset($_POST['node_pinit'])) {
				$node_pinit=readarg($_POST['node_pinit'] == 'on' ? true : false);
			}
			if (isset($_POST['node_whatsapp'])) {
				$node_whatsapp=readarg($_POST['node_whatsapp'] == 'on' ? true : false);
			}
			break;
		default:
			break;
	}

	$missing_node_name=false;
	$bad_node_name=false;

	switch($action) {
		case 'edit':
			if (empty($node_name)) {
				$missing_node_name = true;
			}
			else if (!preg_match('#^\w+(-\w+)*$#', $node_name)) {
				$bad_node_name = true;
			}
			break;
		default:
			break;
	}

	switch($action) {
		case 'edit':
			if ($missing_node_name or $bad_node_name) {
				break;
			}

			$r = node_set($clang, $node_id, $node_name, $node_title, $node_abstract, $node_cloud, $node_image, $node_visits, $node_nocomment, $node_nomorecomment, $node_novote, $node_nomorevote, $node_ilike, $node_tweet, $node_linkedin, $node_pinit, $node_whatsapp);

			if (!$r) {
				break;
			}

			if (!$node_comment) {
				$node_morecomment=false;
			}
			if (!$node_vote) {
				$node_morevote=false;
			}

			break;

		default:
			break;
	}

	$content_editor = build('nodecontenteditor', $lang, $clang, $node_id, $content_types);

	$inlanguages=view('inlanguages', false, compact('clang'));

	$errors = compact('missing_node_name', 'bad_node_name');

	$output = view('editing/nodeeditor', $lang, compact('clang', 'inlanguages', 'node_name', 'node_title', 'node_abstract', 'node_cloud', 'node_image', 'node_visits', 'node_comment', 'node_morecomment', 'node_vote', 'node_morevote', 'node_ilike', 'node_tweet', 'node_linkedin', 'node_pinit', 'node_whatsapp', 'content_editor', 'errors'));

	return $output;
}

