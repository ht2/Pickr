<?php
class HomeCommand extends ExtendedSimpleCommand
{
	public function execute( INotification $notification )
	{	
		parent::execute( $notification);
		
		$this->module = "home";
        
        switch( $this->command ){
            default:
                $this->yourHome();
                break;
        }
		
		$this->buildPage();
	}

    public function yourHome() {
        $this->content = $this->loadTemplate('home/yourhome.html');
    }
}

?>
