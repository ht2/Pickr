<?php
class ExtendedSimpleCommand extends SimpleCommand
{
	protected $module;
	protected $session;
	protected $view;
	protected $sub_view;
	protected $command;
	protected $page_title;
	protected $header_image = 'default.png';
	protected $secnavbar;
	protected $includes;
	protected $inits;
	protected $id;
	protected $error;
	
	protected $footer;
    
	protected $content = "";
	
    protected $pre_tokens = array(), $post_tokens = array();


    public function __construct()
	{		
		parent::__construct();
		$this->session 		= new Session();		
		$this->mysql 		= new MySQL();
		
		$this->view 		= isset( $_REQUEST['view'] ) 	? strtolower(trim($_REQUEST['view']))       : "home";
		$this->command 		= isset( $_REQUEST['command'] )	? strtolower(trim($_REQUEST['command']))    : "";
		$this->id           = isset( $_REQUEST['id'] )      ? intval($_REQUEST['id'])                   : 0;
		$this->error        = isset( $_REQUEST['error'] ) 	? intval($_REQUEST['error'])                : 0;
			
		$base_includes 	 	= $this->facade->retrieveProxy( IncludesProxy::NAME )->includes( "jquery" );	
		$base_inits	  	= '';
		
		$this->includes 	= $base_includes;
		$this->inits 		= $base_inits;
		
		$this->module		= "";
		$this->site_title 	= "Pickr";
		$this->logout_link	= constructURL("index.php", array("view"=>"login", "command"=>"logout") );
		
		$this->container	= $this->loadFile( HTML.'common/container.html' );		
		$this->header 		= $this->loadFile( HTML.'common/header.html' );		
		$this->footer 		= $this->loadFile( HTML.'common/footer.html' );				
	}
	
	public function execute( INotification $notification ){		
		$this->loginCheck();
	}
		
	
	protected function menuBreadcrumb( $links )
	{
		$i = 1;
		$html = "";
		foreach( $links as $l )
		{
			$html.= $l;			
			if( $i < sizeof( $links ) ) $html.= "<div class='breadcrumb_arrow'>&gt;</div>";
			$i+=1;
		}
		return "<div class='fleft'>You are here:&nbsp;</div>" . $html;
	}
	
	protected function getUniversalTokens()
	{		
		return array(	
			'{INCLUDES}' 		=> $this->includes,
			'{INITIALISERS}' 	=> $this->inits,
			'{PAGE_TITLE}'		=> $this->page_title,
			'{HEADER}'			=> $this->header,
			'{FOOTER}'			=> $this->footer,
			'{MODULE}'			=> $this->module,
			'{SITE_TITLE}'		=> $this->site_title,
			'{VIEW}' 			=> $this->view,
			'{COMMAND}' 		=> $this->command
		);
	}
	
	protected function loginCheck()
	{
		if( !$this->session->valid() )
		{
			$this->redirect('index.php?view=login');
		}
	}
    
    protected function redirect( $location = "index.php"){
        header('Location:'.$location);
        exit();
    }
	
	protected function loadFile( $file ){ return $this->facade->retrieveProxy( TemplateProxy::NAME )->loadFile( $file ); }
	protected function loadTemplate( $file ){ return $this->facade->retrieveProxy( TemplateProxy::NAME )->loadFile( HTML.$file ); }
	
	protected function buildPage()
	{
		$args = func_get_args();
		//Select the template
		$this->facade->sendNotification( ApplicationFacade::TEMPLATE, $this->container );
				
		$this->facade->sendNotification( ApplicationFacade::TOKENIZE, array( '{CONTENT}' => $this->content ) );
		
		//Add pre-universal tokens
		foreach($this->pre_tokens as $pre_t)
			if(is_array($pre_t)) 
				$this->facade->sendNotification( ApplicationFacade::TOKENIZE, $pre_t );
		
		//Add universal tokens
		$this->facade->sendNotification( ApplicationFacade::TOKENIZE, $this->getUniversalTokens() );
		
		//Add post-universal tokens
		foreach($this->post_tokens as $post_t)
			if(is_array($post_t)) 
				$this->facade->sendNotification( ApplicationFacade::TOKENIZE, $post_t );
		
		//Render page
		$this->facade->sendNotification( ApplicationFacade::RENDER );
	}
}

?>