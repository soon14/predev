<?php
if(isset($_GET['xhprof'])){
	include_once(dirname(__FILE__) . "/lib/xhprof.php");
	serveradm_xhprof::begin();
	register_shutdown_function("serveradm_xhprof::end");
}
