<?php

require_once PUREMVC.'patterns/proxy/Proxy.php';
require_once COMMON.'model/vo/VO.php';
require_once BASEDIR.'ApplicationFacade.php';
require_once COMMON.'Session.php';

class FilmsProxy extends Proxy
{
	const NAME = "FilmsProxy";
	var $mysql;
    
    var $user = false;
    
	public function __construct()
	{
		parent::__construct( FilmsProxy::NAME );
		$this->session = new Session();
		$this->mysql = new MySQL();
		$this->template = new Template();
	}
    
    public function allFilms(){
        $this->mysql->select("films_norm");
        return $this->mysql->results();
    }
    
    public function yourFilms($user_id){
        $user_id = intval($user_id);
        $query = "
            SELECT f.*, v.rating
            FROM votes v
            JOIN films_norm f ON v.f_id = v.f_id AND v.active=1
            WHERE v.user_id = $user_id
        ";
        $this->mysql->query( $query );
        return $this->mysql->results();
    }
    
    public function usersFilms( $user_id ){        
        $query = "SELECT ";        
        $this->mysql->query($query);
    }
    
    public function tokens( $film ){        
                
        $genres = $this->genreList( $film->genres );
        
        return array(
            '{F_IMDB_ID}'   =>  $film->imdbID,
            '{F_TITLE}'     =>  $film->title,
            '{F_YEAR}'      =>  $film->year,
            '{F_RUNTIME}'   =>  $film->runtime,
            '{F_GENRES}'    =>  $genres,
            '{F_DIRECTOR}'  =>  $film->director,
            '{F_ACTORS}'    =>  $film->actors,
            '{F_IMAGE}'     =>  $film->image,
        );
    }
    
    public function genreList( $genres ){
        
        $genres = explode(',', $genres );
        foreach( $genres as &$g ){
            $g = easylink( $g, "/genres/$g" );
        }
        
        return implode( ', ', $genres );
    }
    
    public function checkFilm( $imdb_data ){
        $film = $this->facade->retrieveProxy( FilmsProxy::NAME)->getFilm( $imdb_data->imdbID );
        if( !$film ){
            $this->insertFilm( $imdb_data, $this->session->user_id );
            return $this->checkFilm( $imdb_data );
        } else {
            return $film;
        }
    }
    
    public function getFilm( $imdbID ){
        $imdbID = $this->mysql->safe($imdbID);
        $this->mysql->select("films_norm", "imdbID='$imdbID'");
        return $this->mysql->singleResult();
    }
    
    public function getAllFilms(){
        $this->mysql->select( "films_norm" );
        return $this->mysql->results();
    }
    
    public function insertFilm( $imdb_data, $user_id ){
        
        $f_id = $this->mysql->insert('films', array(
            'user_id'   =>  $user_id,
            'imdbID'    =>  $imdb_data->imdbID,
            'title'     =>  $imdb_data->Title,
            'year'      =>  $imdb_data->Year,
            'runtime'   =>  $imdb_data->Runtime,
            'director'  =>  $imdb_data->Director,
            'plot'      =>  $imdb_data->Plot,
            'image'     =>  $imdb_data->Poster,
            'actors'    =>  $imdb_data->Actors,
        ));
        
        $genres = explode( ',', $imdb_data->Genre );
        foreach( $genres as $g ){
            $g = trim($g);
            if( strlen($g)>0){
                $genre = $this->checkGenre($g);
                $this->addFilmGenre($f_id, $genre->g_id);
            }
        }
        
        return $f_id;
    }
    
    public function checkGenre( $genre_name ){
        $genre = $this->getGenreByName($genre_name);
        if( !$genre ){
                $this->insertGenre($genre_name);
                return $this->checkGenre($genre_name);
        } else {
            return $genre;
        }
    }
    
    public function getGenreByName( $genre_name ){
        $genre_name = $this->mysql->safe($genre_name);
        $this->mysql->select( "genres", "name='$genre_name'");
        return $this->mysql->singleResult();
    }
    
    public function addFilmGenre( $f_id, $g_id ){
        $this->mysql->insert( "film_genres", array("f_id"=>$f_id, "g_id"=>$g_id));
    }


    public function insertGenre( $genre_name ){
        return $this->mysql->insert("genres", array('name'=>$genre_name) );
    }
    
    public function getVote( $user_id, $f_id ){
        $user_id = intval($user_id);
        $f_id = intval($f_id);
        
        $this->mysql->select( "votes", "user_id=$user_id AND f_id=$f_id");
        return $this->mysql->singleResult();
    }
    
    public function getVoteWidget( $vote, $film ){
        $vote_cont = TemplateProxy::loadFile('view/templates/films/rate_widget.html');
        
        $rating = ( !$vote ) ? 0 :  $vote->rating;
        
        return $this->template->tokenize( array('{F_IMDB_ID}'=>$film->imdbID, '{RATING}'=>$rating), $vote_cont );
    }
    
    public function pushVote( $imdbID, $user_id, $rating ){
        $film = $this->getFilm($imdbID);
        if( !$film ) return false;
        
        $f_id = $film->f_id;
        $user_id    = intval($user_id);
        $rating     = intval($rating);
        
        $this->mysql->query("DELETE FROM votes WHERE user_id=$user_id AND f_id=$f_id");
        $v_id = $this->mysql->insert(
            "votes", 
            array( 
                'f_id'      =>  $f_id,
                'user_id'   =>  $user_id,
                'rating'    =>  $rating
            )
        );
        
        $v_id = intval($v_id);
        if( $v_id == 0 ) return false;
        
        $this->mysql->select( "votes", "v_id=$v_id");
        $vote = $this->mysql->singleResult();
        return $this->getVoteWidget( $vote, $film );
    }
    
    
	
}

?>