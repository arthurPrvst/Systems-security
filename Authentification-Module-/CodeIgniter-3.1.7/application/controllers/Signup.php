<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
//To handle Google ReCaptcha
require('assets/ReCaptcha/autoload.php');

class Signup extends CI_Controller
{

	public function __construct()
	{
		//	Obligatoire
		parent::__construct();
		//if( ! isAdmin())
		//	exit("Not allowed to see this page");
        $this->load->library('form_validation');
        $this->load->model('Login_model');
        //To handle form checkign for password policy
        $this->load->model('Admin_model');
    }


	//Default url handling
    public function index()
    {
        $this->view();
    }


    public function view($page = 'signup'){

	    $data = array(); // Recovering of GET parameters
        echo "<script> console.log('PHP: ','SIGNUP CONTROLLER');</script>";

        $this->new_user_registration();
    }

    // Validate and store registration data in database
    public function new_user_registration() {

        if(isset($_POST['btn_login']) && isset($_POST['g-recaptcha-response'])){
            $recaptcha = new \ReCaptcha\ReCaptcha('6LfXEVEUAAAAABsGkOyOUxd2NW2BYh5_5qUuapdy');
            $resp = $recaptcha->verify($_POST['g-recaptcha-response']);
            if (!$resp->isSuccess()) {
                $errors = $resp->getErrorCodes();
                echo("<script>console.log('Captcha: ERROR signup captcha');</script>");
                redirect();
            } 
        }

        // Check validation for user input in SignUp form
        $this->form_validation->set_rules('first_name', 'First name', 'trim|required');
        $this->form_validation->set_rules('last_name', 'Last name', 'trim|required');
        $this->form_validation->set_rules('login', 'Login', 'trim|required');
        $this->form_validation->set_rules('password', 'Password', 'callback_password_check');
        $this->form_validation->set_rules('email', 'Email', 'trim|required');
        $this->form_validation->set_rules('code', 'Code permanent', 'trim|required');
        $this->form_validation->set_rules('role', 'Role', 'required');
        $this->form_validation->set_rules('secret','Secret','trim|required');
        echo("<script>console.log('Role: ".$this->input->post('role')."');</script>");


        if ($this->form_validation->run() == FALSE) {
            echo("<script>console.log('PHP registration : FORM INVALID');</script>");
            
            redirect();
        }
        else {
            if (!is_numeric($this->input->post('code'))) {
                $this->load->view('templates/toast_NaN');
                redirect();
            } else {
                //Secure storage of password using slow hashing with BCRYPT, salt generated randomly automaticcaly foreach user
                $hashedPwd = password_hash($this->input->post('password'), PASSWORD_BCRYPT, array('cost' => 12));
                $data = array( // lengths limit in db
                    'login' => substr($this->input->post('login'),0,30),
                    'pwd' => $hashedPwd,
                    'name' => substr($this->input->post('first_name'),0,60),
                    'surname' => substr($this->input->post('last_name'),0,60),
                    'mail' => substr($this->input->post('email'),0,100),
                    'codepermanent' => $this->input->post('code'),
                    'secret' => substr($this->input->post('secret'),0,20),
                    'lastConnectionOrAttempt' => time(), //current timestamp
                    'banned' => FALSE,
                    );

                $result = $this->Login_model->registration_insert($data, $this->input->post('role'));

                if ($result == TRUE) { //Login name is free to use
                    echo("<script>console.log('PHP registration : SUCCESS');</script>");
                    $data['message_display'] = 'Registration Success !';

                    if ($this->input->post('role') == ISBUSINESSCLIENT) {
                        redirect('Affaires');
                    } else {
                        redirect('Residentiels');
                    }
                } else {
                    echo("<script>console.log('PHP registration : FAILED');</script>");
                    $data['message_display'] = 'Username already exists!';
                    redirect();
                }
            }
        }
    }

    public function password_check($str){
        $passwordPolicy = $this->Admin_model->get_password_policy();
        //Create the pattern
        $pattern = ".{".$passwordPolicy['minLength'].",}$/";
        if($passwordPolicy['number']==1){
            $pattern = "(?=.*\d)".$pattern;
        }
        if($passwordPolicy['lowerAndUpper']==1){
            $pattern = "(?=.*[a-z])(?=.*[A-Z])".$pattern;
        }
        if($passwordPolicy['specialCharacter']==1){
            $pattern = "(?=.*[\W])".$pattern;
        }
        $pattern = "/^".$pattern;
        
        if (preg_match($pattern, $str)){
            return TRUE;
        }
        else
        {
            $this->form_validation->set_message('password_check', 'The field doesn\'t match the good format');
            return FALSE;
        }
    }

}