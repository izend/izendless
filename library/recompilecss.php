<?php

/**
 *
 * @copyright  2015 izend.org
 * @version    1
 * @link       http://www.izend.org
 */

require_once 'less.php/Less.php';

define('LESS_DIR', ROOT_DIR . DIRECTORY_SEPARATOR . 'less');
define('CSS_DIR', ROOT_DIR . DIRECTORY_SEPARATOR . 'css' . DIRECTORY_SEPARATOR . 'less');

function recompile_css() {
	global $base_path;

	$files=glob(LESS_DIR . DIRECTORY_SEPARATOR . '*.less');

	if (!$files) {
		return true;
	}

	$options = array('compress' => true);

	$variables = <<<_SEP_
@base-path: "$base_path";
_SEP_;

	foreach ($files as $f) {
		try {
			$parser	= new Less_Parser($options);
			$parser->parse($variables);
			$parser->parseFile($f, $base_path);
			$css = $parser->getCss();

			file_put_contents(CSS_DIR . DIRECTORY_SEPARATOR . basename($f, '.less') . '.css', $css);
		}
		catch(Exception $e) {
			die($e->getMessage());
		}
	}

	return true;
}
