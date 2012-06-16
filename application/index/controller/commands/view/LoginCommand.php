<?php
class LoginCommand extends ExtendedSimpleCommand
{
	var $session;
	
	public function execute( INotification $notification )
	{	
        $this->session = new Session();
        
        switch( $this->command ){
            default:
                $this->loginForm();
            break;

            case "login":
                $this->doLogin();
                break;

            case "logout":
                $this->doLogout();
                break;
        }
        
        $this->buildPage();
	}

    public function loginForm() {
        $this->content = $this->loadTemplate( 'common/login_form.html' );
			
        $error_content = "";

        switch( $this->error )
        {
            case 1:
                $error_content.= para("Your login credentials are incorrect.", "error" );
            break;
        }

        $this->post_tokens[] = array( 
            '{LOGIN_ERRORS}' => $error_content
        );
    }
    
    public function doLogin() {
        $user = $this->facade->retrieveProxy( LoginProxy::NAME )->login();
                
        if( $user !== false ){
            // SUCCESS
            $this->session->user( $user ); 
            $this->redirect();
        } else {
            //Incorrect login - Redirect to actual login page
            $this->redirect( 'index.php?view=login&error=1' );
        }
    }
    
    public function doLogout() {
        $this->session = new Session();
		$this->session->destroy();
		$this->redirect();
    }


}

?>
