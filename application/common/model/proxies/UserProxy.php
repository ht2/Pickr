<?php

require_once PUREMVC.'patterns/proxy/Proxy.php';
require_once COMMON.'model/vo/VO.php';
require_once BASEDIR.'ApplicationFacade.php';
require_once COMMON.'Session.php';

class UserProxy extends Proxy
{
	const NAME = "UserProxy";
	var $mysql;
    
    var $user = false;
    
	public function __construct()
	{
		parent::__construct( UserProxy::NAME, new VO() );
		$this->session = new Session();
		$this->mysql = new MySQL();
	}
    
    public function setUser($user){
        $this->user = $user;
    }
    
    
    public function getUser( $user_id ){
        $user_id = intval($user_id);
        $this->mysql->select("users", "user_id=$user_id");
        return $this->mysql->singleResult();
    }
    
    public function clearUser(){
        $this->user = false;
    }
    
    public function tokens()
    {	
        if( $this->user === false ) return array();
        
        return array(
            '{U_EMAIL_LINK}'	=> easylink( '{U_EMAIL}', "mailto:{U_EMAIL}" ),
            '{U_NAME}'          => '{U_FNAME} {U_LNAME}',
            '{U_ID}'            => $this->user->user_id,
            '{U_FNAME}'         => $this->user->fname,
            '{U_LNAME}'         => $this->user->lname,
            '{U_EMAIL}'         => $this->user->email
        );
    }
	
}

?>