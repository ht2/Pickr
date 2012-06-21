<?php
class ExtendedSimpleCommand extends SimpleCommand
{
	//Setup vars
	protected $session, $mysql, $view, $command, $id;
	
	//HTML vars
	protected $includes, $inits, $module, $header, $navbar = "", $userbar= "", $bc_links, $page_title="", $container, $layout, $content, $sidebar, $footer, $json = array();
	
	//Token vars
	protected $pre_tokens = array(), $post_tokens = array(), $global_tokens = array();
	
	//Proxies
    protected $films_proxy, $user_proxy, $template_proxy;
    
	//Error vals
	public $error_vals = array( 'There was an error', 'You must fill in the required fields (*)' ), $actions;
	
	public function __construct(){		
		parent::__construct();	
		$this->mysql 		= new MySQL();	
		$this->template 	= new Template();
        
		
        //Layouts
        $this->container    = $this->loadTemplate('common/container.html');
        $this->layout       = $this->loadTemplate('layouts/onecol.html');
        $this->header = $this->loadTemplate('common/header.html');
        $this->footer = $this->loadTemplate('common/footer.html');
        
        
        //Requests (get and post)
		$this->last_page = isset($_SESSION['last_page']) ? $_SESSION['last_page'] : 'index.php';        
        $this->view = strtolower($this->checkPost('view'));
        $this->command = strtolower($this->checkPost('command'));
        $this->id = $this->checkPost('id');
        $this->error = $this->checkPost('error', 0, 2);
		
        
        //Includes and Initialisations
		$this->includes 	= '';
		$this->inits 		= '';
		$this->addInclude('jquery');
		$this->addInclude('datatables');
                
		$this->module		= "";
		$this->logout_link	= constructURL("/login", array( "command"=>"logout") );		
		$this->site_title 	= $this->mysql->site_name;
        
		$this->bc_links = array();
        
        //Proxies
        $this->films_proxy = $this->facade->retrieveProxy(FilmsProxy::NAME);
        $this->user_proxy = $this->facade->retrieveProxy(UserProxy::NAME);
        $this->template_proxy = $this->facade->retrieveProxy(TemplateProxy::NAME);
		
	}
	
	public function execute( INotification $notification ){
		//This function is run in all pages where we need to be logged in
		//Check we are actually a valid user
        $this->loginCheck();
                
        $this->addPostTokens( array(
            '{MY_U_ID}'            => $this->session->user_id,
            '{MY_U_FNAME}'         => $this->session->fname,
            '{MY_U_LNAME}'         => $this->session->lname,
            '{MY_U_EMAIL}'         => $this->session->email
        ));
        
		$this->userbar = "Logged in ".easylink("(logout)", $this->logout_link );
	}
	
    protected function addInclude( $include_type ){
		$this->includes .= $this->facade->retrieveProxy( IncludesProxy::NAME )->includes( $include_type );
    }
    
	protected function menuBreadcrumb( $links )
	{
        if( sizeof($links)==0) return;
        
		$html = "";
        $i = 0;
		foreach( $links as $l )
		{		
			if( $i>0 ) $html.= "<div class='breadcrumb_arrow'>&gt;</div>";
			$html.= $l;	
            $i++;
		}
		return $html;
	}
	
	protected function getUniversalTokens()
	{		
		$valid_file_types 	= $this->template_proxy->validUploadsExt();
		$valid_image_types 	= $this->template_proxy->validImageExt();
		
		$max_file_size 		= $this->template_proxy->maxUploadSize();
		$max_image_size 	= $this->template_proxy->maxImageSize();
        
        
		return array(	
			'{INCLUDES}' 			=> $this->includes,
			'{INITIALISERS}' 		=> $this->inits,
			'{HEADER}'				=> $this->header,	
			'{USERBAR}'				=> $this->userbar,		
			'{NAVBAR}'				=> $this->navbar,	
            '{CONTENT}'             => $this->content,
			'{FOOTER}'				=> $this->footer,
			'{BREADCRUMB}'			=> $this->menuBreadcrumb($this->bc_links),
			'{SIDEBAR}'             => $this->sidebar,
			'{MODULE}'				=> $this->module,
            '{PAGE_TITLE}'          => $this->page_title,
			'{SITE_TITLE}'			=> $this->site_title,
			'{ERRORS}' 				=> $this->errorHandler(),
			'{ID}' 					=> $this->id,
			'{VIEW}' 				=> $this->view,
			'{COMMAND}' 			=> $this->command,
			'{SITE_ROOT}'			=> $this->mysql->site_root,
			'{YEAR}'				=> date("Y"),
			'{ACCEPTED_FILE_TYPES}'			=> $valid_file_types,
			'{NICE_ACCEPTED_FILE_TYPES}'	=> str_replace( '|', ', ', $valid_file_types ),
			'{ACCEPTED_IMAGE_TYPES}'		=> $valid_image_types,
			'{NICE_ACCEPTED_MAGE_TYPES}'	=> str_replace( '|', ', ', $valid_image_types ),
			'{MAX_UPLOAD_SIZE}'				=> $max_file_size,
			'{NICE_UPLOAD_SIZE}'			=> $max_file_size/1048576 . "MB",
			'{MAX_IMAGE_SIZE}'				=> $max_image_size,
			'{NICE_IMAGE_SIZE}'				=> $max_image_size/1048576 . "MB",		
			
		);
	}
	
	public function loginCheck()
	{
		$this->session = new Session();
		$_SESSION['last_page'] = $_SERVER['REQUEST_URI'];
        
		if( !$this->session->valid() )
		{
			$this->redirect('/login');		
		} else {
			$user = $this->user_proxy->getUser( $this->session->user_id );
			$this->session->user( $user );
		}
        
        $this->navbar = "<ul class='cf'><li class='home'><a href='/home'>Home</a></li><li class='films'><a href='/films'>Films</a></li><li class='genres'><a href='/genres'>Genres</a></li><li class='results'><a href='/results'>Results</a></li></ul>";
	}
    
	protected function drawActions( $actions ){
		if( sizeof($actions) == 0 ) return "";
		
		$html = "<h4>Actions</h4>";
		foreach( $actions as $a )
		{
			$html.= para( $a );
		}		
		return $html;
	}
	
	protected function loadFile( $file ){ return $this->facade->retrieveProxy(TemplateProxy::NAME)->loadFile( $file ); }	
	protected function loadTemplate( $file ){ return $this->loadFile( HTML.$file ); }
	
    protected function addPreTokens( $array ){
        $this->pre_tokens[] = $array;
    }
    protected function addPostTokens( $array ){
        $this->post_tokens[] = $array;
    }
	
	public function buildPage( $pdf = false )
	{
		//Select the template
        
		$this->facade->sendNotification( ApplicationFacade::TEMPLATE, $this->container );
        
		$this->facade->sendNotification( ApplicationFacade::TOKENIZE, array( '{MAIN}' => $this->layout ) );
		
		//Add pre-universal tokens
		foreach($this->pre_tokens as $pre_t)
			if(is_array($pre_t)) 
				$this->facade->sendNotification( ApplicationFacade::TOKENIZE, $pre_t );
		
		//Add universal tokens
		$this->facade->sendNotification( ApplicationFacade::TOKENIZE, $this->getUniversalTokens() );
		
		//Add post-universal tokens
		foreach($this->post_tokens as $post_t)
			if( is_array($post_t) ) 
				$this->facade->sendNotification( ApplicationFacade::TOKENIZE, $post_t );
           
        $this->facade->sendNotification( ApplicationFacade::TOKENIZE, $this->global_tokens );
         
		//Render page
        $this->facade->sendNotification( ApplicationFacade::RENDER );
	}
    
    public function printJSON()
	{	
		$this->facade->sendNotification( ApplicationFacade::TEMPLATE, json_encode( $this->json, JSON_NUMERIC_CHECK ) );	
        
		//Add pre-universal tokens
		foreach($this->pre_tokens as $pre_t)
			if(is_array($pre_t)) 
				$this->facade->sendNotification( ApplicationFacade::TOKENIZE, $pre_t );
            
		$this->facade->sendNotification( ApplicationFacade::TOKENIZE, $this->getUniversalTokens() );
		
		foreach($this->post_tokens as $post_t)
			if(is_array($post_t)) 
				$this->facade->sendNotification( ApplicationFacade::TOKENIZE, $post_t );	
				
		$this->facade->sendNotification( ApplicationFacade::RENDER_JSON );
        exit();
	}
	
	public function errorHandler(){
		if( $this->error == 0 ) return "";
		
		return para( $this->error_vals[ $this->error-1 ], "error" );
	}
	
    
	protected function checkPost( $val, $default="", $type=1 )
	{
		$return_val = isset( $_REQUEST[$val] ) ?	$_REQUEST[$val]	 : $default;
							  
		switch( $type )
		{
			default:
			case 1:
				return trim($return_val);
			break;
			
			case 2:
				return intval($return_val);
			break;
			
			case 3:
				return (boolean)$return_val;
			break;
        
            case 4:
                return (array)$return_val;
            break;
		}
	}
	
	protected function redirect( $goto='/home' )
	{
		header('Location:'.$goto );
		exit();
	}
	
	protected function deniedAccess( $message )
	{
		$this->main = $message;
		$this->buildPage();
	}
}
?>