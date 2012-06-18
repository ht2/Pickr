<?php

require_once PUREMVC.'patterns/proxy/Proxy.php';
require_once COMMON.'model/vo/VO.php';
require_once BASEDIR.'ApplicationFacade.php';
require_once COMMON.'Session.php';

class TablesProxy extends Proxy
{
	const NAME = "TablesProxy";
	var $mysql;
    
    var $user = false;
    
	public function __construct()
	{
		parent::__construct( TablesProxy::NAME );
		$this->session = new Session();
		$this->mysql = new MySQL();
	}
    
    public function viewFilms( $films ){
        $headers = array( 'Title', 'Runtime', 'Genres', 'Year', 'Director', 'Lead Actor', 'IMDB ID', 'Added by' );
        
        
        $rows = array();
        foreach( $films as $f ){
            $link = easylink( $f->title, 'films/view/'.$f->f_id );
            $genres = $this->facade->retrieveProxy(FilmsProxy::NAME)->genreList( $f->genres );
            $added_by = $f->fname . " " . substr($f->lname, 0, 1);
            $actors = explode( ',', $f->actors );
            $lead_actor = ( sizeof($actors)>0) ? trim( $actors[0]) : "-";
            $external_link = easylink( $f->imdbID, 'http://www.imdb.com/title/'.$f->imdbID, 'Go to the IMDB page for this film', '', 'target="_blank"' );
            
            $row = array( $link, $f->runtime, $genres, $f->year, $f->director, $lead_actor, $external_link, $added_by );
            $rows[] = $row;
        }
        
        return $this->facade->retrieveProxy(TemplateProxy::NAME)->createSortableTable( $headers, $rows, 'filmsTable');
    }
    
    
	
}

?>