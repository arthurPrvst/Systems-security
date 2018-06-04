 <?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
 //To handle Google ReCaptcha
 require('assets/ReCaptcha/autoload.php');


 class Login extends CI_Controller {


    public function __construct()
    {
        parent::__construct();
        $this->load->library('form_validation');
        $this->load->model('Login_model');
        //To handle connection attemps accordign to policy management
        $this->load->model('Admin_model');
    }


    //Default url handling
    public function index()
    {
        $this->view();
    }


    public function view($page = 'login')
    {
        $data = array(); // Recovering of GET parameters
        echo "<script> console.log('PHP: ','LOGIN CONTROLLER');</script>";

        $this->user_login_process();
    }


    // Handle connection with different kind of user : {residentiel, business, admin}
    public function user_login_process(){

        //Verify captcha entry
        if(isset($_POST['btn_login']) && isset($_POST['g-recaptcha-response'])){
            $recaptcha = new \ReCaptcha\ReCaptcha('6LfXEVEUAAAAABsGkOyOUxd2NW2BYh5_5qUuapdy');
            $resp = $recaptcha->verify($_POST['g-recaptcha-response']);
            if (!$resp->isSuccess()) {
                $errors = $resp->getErrorCodes();
                echo("<script>console.log('Captcha: ERROR login captcha');</script>");
                redirect();
            } 
        }

        $this->form_validation->set_rules('log','Login','trim|required'); // xss clean
        $this->form_validation->set_rules('pass','Password','trim|required'); // xss clean
        if($this->form_validation->run() == FALSE){
            if($_SESSION['sessionData']['logged_in']==TRUE){
                // on est déjà connecté et formulaire pas envoyé ! -> afficher message / redirection
                echo("<script>console.log('PHP: DEJA LOG');</script>");
            }
            else {
                //formulaire juste pas envoyé, on reste sur la page
                echo("<script>console.log('PHP: RIEN NE SE PASSE');</script>");
            }
            // afficher sur le home si connexion réussie ou échouée (navbar)
        }
        else { // formulaire envoyé

            #Check the hashed password 
            $username = $this->input->post('log');
            $pwd = $this->input->post('pass');

            $data = array(
                'username' => $username,
            );

            $result = $this->Login_model->get_user($data);
            //User did 3 connection attempts that failed. He has to contact admin
            if($result['banned']==1){ 
                redirect();
            }

            //Update timestamp of this new connection attempt
            if($result!=FALSE){ //Username exist in db

                $delta = time()-$result['lastConnectionOrAttempt'];
                $bruteforcePolicy = $this->Admin_model->get_bruteforce_policy();

                $this->Login_model->updateTimestampAttempt($username,time());

                if( ($delta < 60*$bruteforcePolicy['delay']) || (!password_verify($pwd, $result['pwd'])) ){
                    $this->Login_model->incrementFailedAttempt($username,$result,$bruteforcePolicy['lockingAccount']);
                    redirect();
                }

            }

            ini_set('session.gc_maxlifetime', 3600);
            // each client should remember their session id for EXACTLY 1 hour
            session_set_cookie_params(3600);
            session_start(); // only 1

            //User's login exist, and pwd correspond to the hashed one in the database
            if(password_verify($pwd, $result['pwd'])){
                // réussie, on prépare la session
                $session_data= array(
                    'username' => $result['login'],
                    'email' => $result['mail'],
                    'code' => $result['codepermanent'],
                    'logged_in' => TRUE

                    );

                // on ajoute ces données dans la session
                $_SESSION['sessionData'] = $session_data;
                //logged in

                log_message('error',$data['username']." LOG SUCCESS"."\n");

                //Redirection according to the role of the user
                redirect(""."?log=SUCCESS");
            }
            else {
                echo("<script>console.log('PHP: ECHOUE CONNEXION');</script>");
                // erreur de connexion
                $data = array(
                    'error_message' => 'Invalid Username or Password'
                    );
                // error trying log with username
                log_message('error',"FAILED TO LOG IN WITH USERNAME : ".$this->input->post('log')."\n");

                $this->load->view('templates/toast_connected', $data);
                redirect();

            }

        }
    }

    // TO DO ! ca supprime pas encore les cookies enregistrés :/
    // pour voir où ils sont stockés sur le serveur, faire phpinfo();
    // Logout
    public function logout() {
        session_start();
        // Removing session data
        $sess_array = array(
            'username' => ''
            );
// destroy the session
        session_destroy();
        session_unset();
        $data['message_display'] = 'Successfully Logout';
        redirect(base_url());
    }
}
