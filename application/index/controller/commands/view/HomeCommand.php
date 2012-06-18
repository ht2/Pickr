<?php
class HomeCommand extends ExtendedSimpleCommand
{
	public function execute( INotification $notification )
	{	
		parent::execute( $notification);
		
		$this->module = "home";
        
        $this->addInclude('validate');
        
        switch( $this->command ){
            default:
                $this->yourHome();
                break;
        }
		
		$this->buildPage();
	}

    public function yourHome() {
        $this->content = $this->loadTemplate('home/yourhome.html');
        
        $films = $this->facade->retrieveProxy(FilmsProxy::NAME )->allFilms();
        
        $this->addPostTokens(array(
            '{ALL_FILMS}'   => $this->facade->retrieveProxy( TablesProxy::NAME )->viewFilms( $films )
        ));
        
        
    }
}

?>
