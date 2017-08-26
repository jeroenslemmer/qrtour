<?php



function censorNameOK($name){
	$patterns = [
		'lul',
		'kut',
		'neuk'
	];

	$clean = true;

	foreach ($patterns as $pattern){
		$clean &= !(preg_match('/' . $pattern . '/', $name, $matches));
	}
	return $clean;
}