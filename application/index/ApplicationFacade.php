<?php
require_once PUREMVC.'patterns/facade/Facade.php';
require_once COMMON.'controller/command/CommonInitialiseCommand.php';
require_once BASEDIR.'controller/commands/application/StateCommand.php';

foreach( glob(BASEDIR.'controller/commands/view/*.php') as $filename ) require $filename;

class ApplicationFacade extends Facade
{
	// Global commands
	const INITIALISE 	= "application/initialise";
	const TEMPLATE		= "application/template";
	const TOKENIZE		= "application/tokenize";
	const RENDER		= "application/render";
	const RENDER_JSON   = "application/render/json";
	const STATE		 	= "application/state";
	
	// View commands
	const VIEW_HOME     = "view/home";
	const VIEW_AJAX     = "view/ajax";
	const VIEW_LOGIN	= "view/login";
	const VIEW_FILMS	= "view/films";

	static public function getInstance()
	{
		if (parent::$instance == null) parent::$instance = new ApplicationFacade();
		return parent::$instance;
	}
	
	protected function initializeController()
	{
		parent::initializeController();
		
		// Global commands
		$this->registerCommand( ApplicationFacade::INITIALISE, 'CommonInitialiseCommand' );
		$this->registerCommand( ApplicationFacade::STATE,      'StateCommand' );		
		
		// View commands
		$this->registerCommand( ApplicationFacade::VIEW_HOME, 'HomeCommand' );
		$this->registerCommand( ApplicationFacade::VIEW_AJAX, 'AjaxCommand' );
		$this->registerCommand( ApplicationFacade::VIEW_LOGIN, 'LoginCommand' );
		$this->registerCommand( ApplicationFacade::VIEW_FILMS, 'FilmsCommand' );
	}	
	
	public function initialise()
	{
		$this->sendNotification( ApplicationFacade::INITIALISE );
		$this->removeCommand( ApplicationFacade::INITIALISE );
	}
}

?>