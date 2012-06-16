<?php

require_once PUREMVC.'patterns/proxy/Proxy.php';
require_once COMMON.'model/vo/VO.php';
require_once BASEDIR.'ApplicationFacade.php';

class TemplateProxy extends Proxy
{
	const NAME = "TemplateProxy";
	
	public function __construct()
	{
		parent::__construct( TemplateProxy::NAME, new VO() );
	}
	
	public function loadFile( $url )
	{
		$file = file_get_contents( $url ) or die("error loading file");
		return $file;
	}

	public function vo()
	{
		return $this->getData();
	}
}
?>
