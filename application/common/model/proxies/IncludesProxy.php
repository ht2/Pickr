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
				$output .= br('<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>');
				$output .= br('<script type="text/javascript" src="/view/packages/jquery.ui/js/jquery-ui-1.8.21.custom.min.js"></script>');
				$output .= $this->includeCSS( 'jquery-ui-1.8.21.custom.css', '/view/packages/jquery.ui/css/smoothness/' );
				$output .= br('<script type="text/javascript" src="/view/javascript/commonJQuery.js"></script>');	;	
			break;
			
            case 'validate':
				$output .= $this->includeJS( '/view/packages/jquery-validation-1.9.0/jquery.validate.min.js' );
			break;
        
			case "datatables":
				$output .= $this->includeJS( '/view/packages/DataTables-1.9.1/media/js/jquery.dataTables.min.js' );
				$output .= $this->includeCSS( 'jquery.dataTables_themeroller.css', '/view/packages/DataTables-1.9.1/media/css/' );
				$output .= $this->includeJS( '/view/packages/DataTables-1.9.1/media/js/dataTables.init.js' );
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
