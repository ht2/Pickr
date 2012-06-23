<?php
class FilmsCommand extends ExtendedSimpleCommand
{
	public function execute( INotification $notification )
	{	
		parent::execute( $notification);
		
		$this->module = "films";
        
        $this->addInclude('validate');
        $this->inits .= $this->facade->retrieveProxy(IncludesProxy::NAME )->includeJS('/view/templates/films/js/rating.js');
        
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
        $film = $this->facade->retrieveProxy(FilmsProxy::NAME )->getFilm( $this->id );
        if( !$film ) $this->redirect ('/films');
        
        $this->content = $this->loadTemplate('films/view_film.html');
        
        $this->addPostTokens(array(
            '{FILM_INFO}'   =>  $this->loadTemplate('films/film_info.html')
        ));
        
        
        $film_tokens = $this->facade->retrieveProxy(FilmsProxy::NAME )->tokens( $film );        
        $vote = $this->facade->retrieveProxy( FilmsProxy::NAME)->getVote( $this->session->user_id, $film->f_id );                
        $film_tokens['{VOTE_INFO}'] = $this->facade->retrieveProxy( FilmsProxy::NAME)->getVoteWidget( $vote, $film );
        
        $this->addPostTokens( $film_tokens );
    }

    public function allFilms() {
        $this->content = $this->loadTemplate('films/allfilms.html');
        
        $films = $this->facade->retrieveProxy(FilmsProxy::NAME )->allFilms();
        
        $this->addPostTokens(array(
            '{ALL_FILMS}'   => $this->facade->retrieveProxy( TablesProxy::NAME )->viewFilms( $films, $this->session->user_id )
        ));
    }
}

?>
