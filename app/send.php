<?php

require_once "config.php";
require_once "lib/Handler.php";

if($_SERVER['REQUEST_METHOD'] == 'POST')
{
	$handler = new Handler();
	$handler->setUtmTags(@parse_url($_SERVER["HTTP_REFERER"])['query']);
	$setFields = $handler->setFields($_POST);
	if($setFields === true)
	{
		$handler->setMsgContent();
		$statusMessage = $handler->sendMail();
	} else {
		$statusMessage = $setFields;
	}
}

require "templates/template.php";