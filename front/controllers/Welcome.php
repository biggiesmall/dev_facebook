<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Welcome extends CI_Controller {

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
            'grant_type' => 'client_credentials',
            'default_graph_version' => 'v2.8',
            'persistent_data_handler'=>'session'
        ]);
    }

	public function index()
	{
		
        if(isset($_SESSION['facebook_access_token']))
        {
            $this->fb->setDefaultAccessToken($_SESSION['facebook_access_token']);
            /*Pour poster un message sur le mur de l'utilisateur*/
            /*$data = ['message' => 'test'];
            $this->fb->post('me/feed', $data); permission = publish_actions*/
            //access token de l'application
            $accessTokenApp = '261909090895855|RMMvgf_sNsIHu7mQ7JY07cZTyY8';
            //var_dump($_SESSION);

            $tabAdmin = array();
            //récupère les droits des utilisateurs de l'application
            //$response = $this->fb->get("/261909090895855/roles", $_SESSION['facebook_access_token']);
            $response = $this->fb->get("/261909090895855/roles",$accessTokenApp);
            $userNode = $response->getDecodedBody();
            //recuperation des informations de l'utilisateur
            $response1 = $this->fb->get('/me?fields=id,last_name,first_name,gender,birthday,email');
            $idUser = $response1->getDecodedBody();

            var_dump($idUser);
            //ajoute l'utilisateur sur la base de donnée s'il n'existe pas sinon met à jour ses informations
            $data['id_facebook']  = $idUser['id'];
            $data['first_name']   = $idUser['last_name'];
            $data['last_name']    = $idUser['first_name'];
            $data['email']        = $idUser['email'];
            if ($idUser['gender'] == 'male'){
                $data['gender'] = 'M';
            }else{
                $data['gender'] = 'F';
            }
            $data['date_cretead'] = date('Y-m-d');
            var_dump($data);
            $this->users->insertUser($idUser['id'], $data);

            foreach ($userNode['data'] as $row){
                if($row['role'] == 'administrators'){
                    array_push($tabAdmin, $row['user']);
                }
            }
            for ($i = 0; $i<count($tabAdmin); $i++){
                if($tabAdmin[$i] == $idUser['id']){
                    $_SESSION['idAdmin'] = $idUser['id'];
                    //redirect(base_url('admin'));
                }else{
                    //$this->load->view('welcome_message');
                }
            }
        	$loginUrl = base_url().'welcome/logout';
            echo '<a href="' . htmlspecialchars($loginUrl) . '">Deconnexion!</a>';
        }else{
        	echo 'non connecter';
        	$helper = $this->fb->getRedirectLoginHelper();
	        $permissions = ['email', 'user_photos'];
	        $loginUrl = $helper->getLoginUrl(base_url().'welcome/loginFacebook', $permissions);
	        echo '<a href="' . htmlspecialchars($loginUrl) . '">Se connecter!</a>';
        }
	}
    
   
    public function loginFacebook()
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
            $_SESSION['facebook_access_token'] = (string) $accessToken;
            header('Location: '.base_url());
        }
    }

    public function logout(){
        session_destroy();
        header('Location: '.base_url());
    }
}
