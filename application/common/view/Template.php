<?php

class Template
{

	private $html;
	public function __construct(){}
	
	public function tokenize( $tokens, $template )
	{
		if(sizeof($tokens)==0) return $template;
		foreach ($tokens as $token => $value) $template = str_replace( $token, $value, $template );
		return $template;
	}
	
	public function render()
	{
		echo $this->html;
		exit();
	}	
    
    public function renderJSON()
    {
        header('Cache-Control: no-cache, must-revalidate');
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
        header('Content-type: application/json');
        $this->render();
    }	
	
	public function __get($property) 
	{
		if( array_key_exists( $property, get_class_vars( __CLASS__ ) ) ) 
			return $this->$property;
		else 
			die("<p>(Getter) $property doesn't exist in ".__CLASS__."<br>");
	}
	
	public function __set($property, $value) 
	{
		if (array_key_exists( $property, get_class_vars(__CLASS__) ) ) 
			$this->$property = $value;
		else 
			die("<p>(Setter) $property doesn't exist in ".__CLASS__."<br>");
	}	
}

?>
