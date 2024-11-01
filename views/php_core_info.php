<div class="wrap">
<div id="icon-tools" class="icon32"><br></div><h2>Общая информация о настройках сервера</h2>
<?php ob_start();
phpinfo(INFO_CONFIGURATION);
$pinfo = ob_get_contents();
ob_end_clean();
 
$pinfo = preg_replace( '%^.*<body>(.*)</body>.*$%ms','$1',$pinfo);
echo $pinfo;?>