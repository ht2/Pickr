<?php
 
require_once PUREMVC.'patterns/mediator/Mediator.php';
require_once PUREMVC.'interfaces/INotification.php';
require_once BASEDIR.'ApplicationFacade.php';
require_once COMMON.'view/Template.php';

class TemplateMediator extends Mediator
{
	const NAME = "TemplateMediator";
	
	public function __construct( $view )
	{
		parent::__construct( TemplateMediator::NAME, $view );
	}
	
	public function listNotificationInterests()
	{
		return array( ApplicationFacade::TEMPLATE, ApplicationFacade::TOKENIZE, ApplicationFacade::RENDER, ApplicationFacade::RENDER_JSON );
	}
	
	public function handleNotification( INotification $notification )
	{
	 	switch ($notification->getName())
	 	{
	 		case ApplicationFacade::TEMPLATE: 
			
				$this->template()->html = $notification->getBody(); 
				
			break;			
	 		case ApplicationFacade::TOKENIZE: 
			
				$this->template()->html = $this->template()->tokenize( $notification->getBody(), $this->template()->html ); 
				
			break;		

            case ApplicationFacade::RENDER_JSON:
                $this->template()->renderJSON();
            break;		
        
	 		case ApplicationFacade::RENDER: 	
				$this->template()->render(); 
			break;
	 		default:break;
	 	}
	}
	
	public function tokenize( $tokens, $template )
	{
		return $this->template()->tokenize( $tokens, $template );
	}

	public function template()
	{
		$class = $this->getViewComponent();
		return $class;
	}
}
?>
