<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Admin extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();
		$this->load->library('form_validation');
		$this->load->model('Admin_model');

	}
	

	//Default url handling
	public function index()
	{
		$this->view();
	}


	public function view($page = 'admin'){
		session_start();
		if ( ! file_exists(APPPATH.'views/pages/'.$page.'.php')){
			show_404();
		}

	    $data = array(); // Recovering of GET parameters
	    echo "<script> console.log('PHP: ','ADMIN CONTROLLER');</script>";


	    $this->load->view('templates/header_admin', $data);
	    $this->load->view('pages/'.$page, $data);
	    $this->load->view('templates/footer', $data);
	}


	public function update_password_policy(){
		$this->form_validation->set_rules('minLength','minLength','required'); // xss clean
        $this->form_validation->set_rules('containSpecialCharacter','containSpecialCharacter','required'); // xss clean
        $this->form_validation->set_rules('containNumber','containNumber','required'); // xss clean
        $this->form_validation->set_rules('containLowerAndUpper','containLowerAndUpper','required'); // xss clean

        if($this->form_validation->run() == FALSE){

        	$data = array(
                'minLength' => $this->input->post('minLength'),
                'specialCharacter' => FALSE,
                'number' => FALSE,
                'lowerAndUpper' => FALSE,
            );

        	if(isset($_POST['containSpecialCharacter'])){
        		$data['specialCharacter'] = TRUE;
        	}
        	if(isset($_POST['containNumber'])){
        		$data['number'] = TRUE;
        	}
        	if(isset($_POST['containLowerAndUpper'])){
        		$data['lowerAndUpper'] = TRUE;
        	}

        	$this->Admin_model->update_password_policy($data);

        }

        redirect(base_url()."index.php/Admin");

    }


    public function update_password_management(){

        $data = array(

            'forgotten' => FALSE,
        );

        if(isset($_POST['changeForgotten'])){
            $data['forgotten'] = TRUE;
        }
        $this->Admin_model->update_password_management($data);

        redirect(base_url()."index.php/Admin");
    }


    public function update_bruteforce_protection(){
        $this->form_validation->set_rules('delay','delay','required'); // xss clean
        $this->form_validation->set_rules('lockingAccount','lockingAccount','required'); // xss clean

        $data = array(
            'delay' => $this->input->post('delay'),
            'lockingAccount' => FALSE,
        );
            
        if(isset($_POST['lockingAccount'])){
            $data['lockingAccount'] = TRUE;
        }

        $this->Admin_model->update_bruteforce_policy($data);
        redirect(base_url()."index.php/Admin");
    }

}