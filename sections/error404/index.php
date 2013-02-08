<?php

	$core->setTitle($config->title);
	$core->setMetaDescription(utf8_encode($config->description));
	$core->setMetaKeywords(utf8_encode($config->keywords));
	$core->setStyle($config->domain."sections/error404/styles/full-error.css");
	
?>