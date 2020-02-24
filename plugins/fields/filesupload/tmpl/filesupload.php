<?php

defined('_JEXEC') or die;

$filesDir = $field->value;
$jsonFiles = json_decode($filesDir);
error_reporting(0);
$fileNames = array_diff(scandir($jsonFiles->{'uploadDirPath'}), array('..', '.', 'index.html'));
$fileLinks = array();
if ($fileNames) {
    foreach ($fileNames as $name) {
        $fileLinks[] = '<a href="' . $jsonFiles->{'relDirPath'} . $name . '">'. $name .'</a>';
    }
} else {
    $fileLinks = array();
}

$i = 0;
if ($fileLinks) {
    foreach ($fileLinks as $link) {
        echo '<table><div id="file' . $i . '">' . $link . '</div></td></tr></table>';
        $i++;
    }
}