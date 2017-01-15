<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends CI_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see https://codeigniter.com/user_guide/general/urls.html
	 */
    
    private $fb;
    
    public function __construct()
    {
        parent::__construct();
        $this->fb = new Facebook\Facebook([
            'app_id' => '261909090895855',
            'app_secret' => 'cce0cc1c5b671113ac2ae1e50c91e3fd',
            'default_graph_version' => 'v2.8',
            'persistent_data_handler'=>'session'
        ]);
    }
      
    
    public function login($link)
    {

        $helper = $this->fb->getRedirectLoginHelper();

        try {
            $accessToken = $helper->getAccessToken();
        } catch(Facebook\Exceptions\FacebookResponseException $e) {
            echo 'Graph returned an error: ' . $e->getMessage();
            exit;
        } catch(Facebook\Exceptions\FacebookSDKException $e) {
            echo 'Facebook SDK returned an error: ' . $e->getMessage();
            exit;
        }
        

        if (isset($accessToken)) {
            /*$this->session->set_userdata('facebook_access_token', (string) $accessToken);
            try {
                $response = $this->fb->get('/me?fields=id,name,email,picture', $accessToken);
            } catch(Facebook\Exceptions\FacebookResponseException $e) {
                echo 'Erro da Graph API: ' . $e->getMessage();
                exit;
            } catch(Facebook\Exceptions\FacebookSDKException $e) {
                echo 'Erro da Facebook SDK: ' . $e->getMessage();
                exit;
            }
        } elseif ($helper->getError()) {
            echo "Requisição negada para o usuário.";
            exit;
        }else{
            echo "Erro desconhecido.";
            exit;*/
            // Logged in!
            $_SESSION['facebook_access_token'] = (string) $accessToken;

            // Now you can redirect to another page and use the
            // access token from $_SESSION['facebook_access_token']
            //echo 'connecter';
            //header("Location: http://mario.fbdev.fr/");
            header("Location: ".$link);
        }

        $user = $response->getGraphUser();

        foreach ($user as $key => $value) {
            echo $key.": ".$value." ";
        }

    }
    
    
    public function logout(){
        //session_start();
        session_destroy();
        header("Location: http://mario.fbdev.fr/index.php/welcome");
    }
}