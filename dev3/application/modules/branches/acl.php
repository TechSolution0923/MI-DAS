<?php
	$class = $this->router->fetch_class();
	$method = $this->router->fetch_method();
	$module = $this->router->fetch_module();
	echo $module." >> ".$class." >> ".$module;exit;
?>