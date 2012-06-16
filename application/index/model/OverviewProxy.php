<?php

require_once PUREMVC.'patterns/proxy/Proxy.php';
require_once COMMON.'model/vo/VO.php';
require_once BASEDIR.'ApplicationFacade.php';
require_once COMMON.'Session.php';

class OverviewProxy extends Proxy
{
	const NAME = "OverviewProxy";
	var $mysql;
	
	public function __construct()
	{
		parent::__construct( OverviewProxy::NAME, new VO() );
		$this->session = new Session();
		$this->mysql = new MySQL();
	}

	public function vo()
	{
		return $this->getData();
	}
}


?>
