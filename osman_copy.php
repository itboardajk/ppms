<?php exit;
$file = 'https://my.hidrive.com/api/sharelink/download?id=DiIjAv57';
$newfile = $_SERVER['DOCUMENT_ROOT'] . '/episode47.mp4';

if ( copy($file, $newfile) ) {
    echo "Copy success!";
}else{
    echo "Copy failed.";
}