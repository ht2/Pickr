<?php

require_once COMMON.'MySQL.php';
require_once COMMON.'Session.php';
require_once COMMON.'Utils.php';
require_once PUREMVC.'patterns/command/SimpleCommand.php';  
require_once COMMON.'controller/command/ExtendedSimpleCommand.php';  
require_once PUREMVC.'interfaces/INotification.php';

foreach( glob(COMMON.'model/proxies/*.php') as $filename ) require $filename;

require_once COMMON.'view/TemplateMediator.php';  
require_once COMMON.'view/Template.php'; 

class CommonInitialiseCommand extends SimpleCommand
{
	public function execute( INotification $notification )
	{	
		// Register Mediators / Proxies
		$this->facade->registerProxy( new LoginProxy() );
		$this->facade->registerProxy( new UserProxy() );
		$this->facade->registerProxy( new FilmsProxy() );
		$this->facade->registerProxy( new IncludesProxy() );
		$this->facade->registerProxy( new TemplateProxy() );
		$this->facade->registerMediator( new TemplateMediator( new Template() ) );
	
		// Get current state
		$this->facade->sendNotification( ApplicationFacade::STATE );	
	}
}

?>
