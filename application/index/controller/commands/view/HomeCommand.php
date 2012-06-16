<?php
class HomeCommand extends ExtendedSimpleCommand
{
	public function execute( INotification $notification )
	{	
		parent::execute( $notification);
		
		$this->module = "home";
		
		$this->content = "Logged in ".easylink("(logout)", $this->logout_link );
		
		$this->buildPage();
		
	}
}

?>
