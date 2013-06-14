<?php
require_once "../inc/common.inc";

$fn = "placenames.txt";
list($province_codes, $province_names) = get_placenames();

header("Content-type:text/plain; charset=utf-8");

$content = file_get_contents($fn);
$lines = explode("\n", $content);

$current_prov = "";
$province = array();
$secondary = array();
foreach ($lines as $line) {
	//remove all line breakers
	$line = str_replace(array("\n", "\r"), array("", ""), $line);
	//skip blank lines
	if ($line == "")
		continue;

	if ($line[0] != "\t") {
		//province line
		$current_prov = $line;
		array_push($province, $line);
	}
	else {
		//secondary line
		$sec = substr($line, $tabpos+1);
		$secondary[$sec] = $province_names[$current_prov];
	}
}

echo "\$provinces =\n";
var_export($province);
echo ";\n";

echo "\$secondary_names =\n";
var_export($secondary);
echo ";\n";
?>
