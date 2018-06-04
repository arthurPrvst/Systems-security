<?php
/**
 * Created by PhpStorm.
 * User: Alexis
 * Date: 26/03/2018
 * Time: 14:58
 */

require('assets/ReCaptcha/autoload.php');

class Password extends CI_Controller
{
    private $session_header = array(
        ISADMIN => 'templates/header_admin',
        ISRESIDENTIALCLIENT => 'templates/header_res',
        ISBUSINESSCLIENT => 'templates/header_bus',
        HASNORIGHTS => 'templates/header'

    );

    public function __construct()
    {
        parent::__construct();
        $this->load->library('form_validation');
        //load models
        $this->load->model('User');
        $this->load->model('Login_model');
    }


    //Default url handling
    public function index()
    {
        $this->view();
    }


    public function view($page = 'password')
    {
        session_start();
        $data = array(); // Recovering of GET parameters
        echo "<script> console.log('PHP: ','PASSWORD CONTROLLER');</script>";
        if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
            show_404();
        }

        if (isset($_SESSION['sessionData']) && $_SESSION['sessionData']['logged_in']==TRUE){
            $this->load->view($this->session_header[$this->User->getRole($_SESSION['sessionData']['username'])], $data);
            $this->load->view('pages/' . $page, $data);
            $this->load->view('templates/footer', $data);
        }
        else{
            redirect();
        }
    }

    public function user_change_password()
    {
        
        if(isset($_POST['btn_login']) && isset($_POST['g-recaptcha-response'])){
            $recaptcha = new \ReCaptcha\ReCaptcha('6LfXEVEUAAAAABsGkOyOUxd2NW2BYh5_5qUuapdy');
            $resp = $recaptcha->verify($_POST['g-recaptcha-response']);
            if (!$resp->isSuccess()) {
                $errors = $resp->getErrorCodes();
                echo("<script>console.log('Captcha: ERROR login captcha');</script>");
                $data['change'] = 'CAPTCHA ERROR';
                redirect(site_url("/Password?change=".$data['change']));
            }
        }
            session_start();
            $change = array();
            $this->form_validation->set_rules('old', 'oldPass', 'trim|required'); // xss clean
            $this->form_validation->set_rules('pass1', 'newPass', 'trim|requiredn'); // xss clean
            $this->form_validation->set_rules('pass2', 'verif', 'trim|required'); // xss clean
            if ($this->form_validation->run() == FALSE) {
                // DO NOTHING
            } else {
                #Check the hashed password
                $pwd = $this->input->post('old');
                $user = $_SESSION['sessionData']['username'];
                $result = $this->Login_model->get_userByUsername($user);
                if (password_verify($pwd, $result["pwd"])) { // right password
                    var_dump($this->input->post('pass1')==$this->input->post('pass2'));
                    if ($this->input->post('pass1')==$this->input->post('pass2') && strlen($this->input->post('pass1'))<=30) { // new password is valid, check if conditions ok
                        if(0){ // ne correspond pas aux params de l'admin
                            $data['change'] = ''; // error message in config file ?
                        }
                        else {
                            // update db
                            $hashedPwd = password_hash($this->input->post('pass1'), PASSWORD_BCRYPT, array('cost' => 12));
                            $result = $this->Login_model->update_password($user,$hashedPwd);
                            // log
                            $data['change'] = 'SUCCESS';
                            log_message('error', $_SESSION['sessionData']['username'] . " : password changed" . "\n");
                        }

                    } else { // new password not valid
                        //display error message
                        $data['change'] = 'DIFFERENT PASSWORDS'; // error message in config file ?
                    }
                } else { // false password
                    $data['change'] = 'WRONG PASSWORD. TRY AGAIN !';
                }
                redirect(site_url("/Password?change=".$data['change']));
            }
    }
}

