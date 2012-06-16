<?php

require_once PUREMVC.'patterns/proxy/Proxy.php';
require_once COMMON.'model/vo/VO.php';
require_once BASEDIR.'ApplicationFacade.php';

class IncludesProxy extends Proxy
{
	const NAME = "IncludesProxy";
	
	public function __construct()
	{
		parent::__construct( IncludesProxy::NAME, new VO() );
	}
	
	public function includeCSS( $file, $location='/view/css/' )
	{
		return br('<link href="'.$location.$file.'" rel="stylesheet" type="text/css" />');
	}
	public function includeJS( $file_location )
	{
		return br('<script type="text/javascript" src="'.$file_location.'"></script>');
	}
	
	public function includes( $type=NULL )
	{
		$output = "";
		switch( $type )
		{
			case 'jquery':
				$output .=  br('<script type="text/javascript" src="/view/packages/jquery/jquery-1.6.1.min.js"></script>');
				$output .= br('<script type="text/javascript" src="/view/javascript/commonJQuery.js"></script>');	
			break;
			
			case "tiptip":
				$output .= $this->includeCSS( 'tipTip.css', '/view/packages/tiptip/' );
				$output .= $this->includeJS( '/view/packages/tiptip/jquery.tipTip.minified.js' );
			break;
			
			case "nyroModal":
				$output .= $this->includeCSS( 'nyroModal.css', '/view/packages/nyroModal/styles/' );
				$output .= $this->includeJS( '/view/packages/nyroModal/js/jquery.nyroModal.custom.min.js' );
			break;
				
		}
		return $output;
	}

	public function vo()
	{
		return $this->getData();
	}
}
?>
