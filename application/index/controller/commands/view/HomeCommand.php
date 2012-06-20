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
        
        $your_films = $this->facade->retrieveProxy(FilmsProxy::NAME )->yourFilms( $this->session->user_id );
        
        if( sizeof($your_films)==0 ){
            $yf_html = para("You haven't voted on any films, head to the ".easylink('films', '/films')." page to browse and add to the library.");
        } else {
            $yf_html = "test";
        }
        
        $this->addPostTokens(array(
            '{YOUR_FILMS}'   => $yf_html
        ));
        
        
    }
}

?>
