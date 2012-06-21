<?php
class HomeCommand extends ExtendedSimpleCommand
{
	public function execute( INotification $notification )
	{	
		parent::execute( $notification);		
		$this->module = "home";        
        $this->addInclude('validate');
        $this->inits .= $this->facade->retrieveProxy(IncludesProxy::NAME )->includeJS('view/templates/films/js/rating.js');
        
        $this->content = $this->loadTemplate('home/yourhome.html');
		$this->buildPage();
	}
}
?>