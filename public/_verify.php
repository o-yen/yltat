<?php
if(($_GET["k"]??"")!=="p2026")die("no");
header("Content-Type:text/plain");
$f=dirname(__DIR__)."/".str_replace("..","",urldecode($_GET["f"]??""));
if(!file_exists($f)){echo "NOT_FOUND";exit;}
echo filesize($f)."|".md5_file($f);

