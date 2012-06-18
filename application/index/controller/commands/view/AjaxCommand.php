<?php
class AjaxCommand extends ExtendedSimpleCommand
{
	public function execute( INotification $notification )
	{	
		parent::execute( $notification);
		
		$this->module = "home";
        
        switch( $this->command ){
            default:
                $this->json['valid'] = $this->session->valid();
                break;
                
            case "check_name":
                $this->checkName();
                break;
                
            case "check_id":
                $this->checkID();
                break;
        }
		
		$this->printJSON();
	}

    public function checkName() {
        
        
        $film_name = urlencode($this->checkPost('film_name'));
        if( strlen($film_name)>0 ){
            $film_data = json_decode( file_get_contents( "http://www.imdbapi.com/?t=$film_name") );
            $this->handleFilmData($film_data);
        } else {
            $this->json['error'] = "No film name given";
        }
    }

    public function checkID() {
        $film_id = $this->checkPost('imdb_id');        
        $film_data = json_decode( file_get_contents( "http://www.imdbapi.com/?i=$film_id") );        
        $this->handleFilmData($film_data);
    }
    
    public function handleFilmData( $film_data ){
        if( $film_data->Response === "False"  ){    
            $this->json['error'] = para("Error retrieving your film. Please try again (".$film_data->Error.")", "error");
        } else {
            $film = $this->facade->retrieveProxy( FilmsProxy::NAME)->checkFilm( $film_data );
            
            if( $film ){
                $tokens =  $this->facade->retrieveProxy( FilmsProxy::NAME)->tokens( $film );
                $tokens['{VOTE_INFO}'] = para("Vote info will go here.");
                $html = $this->loadTemplate('films/film_info.html');            
                $html = $this->template->tokenize($tokens, $html);
                
                $html = $this->loadTemplate('home/add_another.html') . $html;
                
                $this->json['html'] = $html;
                
                $films = $this->facade->retrieveProxy( FilmsProxy::NAME)->allFilms();
                $this->json['all_films'] = $this->facade->retrieveProxy( TablesProxy::NAME)->viewFilms( $films );
            } else {
                $this->json['error'] = "There was an error finding your film. Please try again.";
            }
        }
    }
}

?>
