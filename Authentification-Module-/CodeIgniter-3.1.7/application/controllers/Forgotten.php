<?php
/**
 * Created by PhpStorm.
 * User: Alexis
 * Date: 26/03/2018
 * Time: 20:11
 */
require('assets/ReCaptcha/autoload.php');
class Forgotten extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->library('form_validation');
        //load models
        $this->load->model('User');
        $this->load->model('Login_model');
        $this->load->model('Admin_model');
    }


    //Default url handling
    public function index()
    {
        $this->view();

    }

    public function view($page = 'forgotten')
    {
        $passwordManagement = $this->Admin_model->get_password_management();
        //Not allowed to change password...
        if($passwordManagement['forgotten']!=1){
            redirect();
        }

        echo "<script> console.log('PHP: ','FORGOTTEN CONTROLLER');</script>";
        if (!file_exists(APPPATH . 'views/pages/' . $page . '.php') || $this->Admin_model->get_password_management()['forgotten'] == 0) {
            show_404();
        }
        else {
            $this->load->view('templates/header');
            $this->load->view('pages/' . $page);
            $this->load->view('templates/footer');
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
                redirect(site_url("/Forgotten?change=".$data['change']));

            }
        }
        session_start();
        
        $passwordPolicy = $this->Admin_model->get_password_policy();

        $this->form_validation->set_rules('username', 'username', 'trim|required'); // xss clean
        $this->form_validation->set_rules('pass1', 'pass1', 'callback_password_check');
        $this->form_validation->set_rules('pass2', 'pass2', 'callback_password_check');
        $this->form_validation->set_rules('secret', 'secret', 'trim|required'); // xss clean
        if ($this->form_validation->run() == FALSE) {
            // DO NOTHING
            redirect(site_url()."/forgotten");
        } else {

            // check username et secret
            $result = $this->Login_model->check_user_secret($this->input->post('username'),$this->input->post('secret'));
            if($result){

                // check 2 même password
                if($this->input->post('pass1')==$this->input->post('pass2') && strlen($this->input->post('pass1')) <=30){ // ok et correspond aux règles admin
                    if(0){ // ne correspond pas aux params de l'admin
                        $data['change'] = ''; // error message in config file ?

                    }
                    else {
                        // update db
                        $hashedPwd = password_hash($this->input->post('pass1'), PASSWORD_BCRYPT, array('cost' => 12));
                        $result = $this->Login_model->update_password($this->input->post('username'),$hashedPwd);
                        // log
                        $data['change'] = 'SUCCESS';
                        log_message('error', $this->input->post('username')." : password changed" . "\n");
                    }
                }
                else {

                    $data['change'] = 'DIFFERENT PASSWORDS'; // error message in config file ?
                }
            }
            else {

                // message erreur
                $data['change'] = 'WRONG USERNAME OR SECRET. TRY AGAIN !';
            }
            redirect(site_url("/Forgotten?change=".$data['change']));
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