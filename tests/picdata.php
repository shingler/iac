<?php
if (count($argv) < 2) {
    echo sprintf("Usage: php picdata.php /path/to/file.jpg");
}
$filename = realpath($argv[1]);
if (!$filename) {
    echo sprintf("the path [%s] is not accessable.", $filename);
}
$output_name = basename($filename.".txt");
file_put_contents(dirname(__FILE__)."/".$output_name, base64_encode(file_get_contents($filename)));
echo sprintf("pic data is in [%s].".PHP_EOL, $output_name);