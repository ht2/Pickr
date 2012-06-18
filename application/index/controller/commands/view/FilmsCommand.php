<?php
class FilmsCommand extends ExtendedSimpleCommand
{
	public function execute( INotification $notification )
	{	
		parent::execute( $notification);
		
		$this->module = "films";
        
        switch( $this->command ){
            default:
            case "all":
                $this->allFilms();
                break;
            case "view":
                $this->viewFilm();
                break;
        }
		
		$this->buildPage();
	}

    public function viewFilm() {
        $this->content = "View film";
    }

    public function allFilms() {
        $this->content = "All films";
    }
}

?>
