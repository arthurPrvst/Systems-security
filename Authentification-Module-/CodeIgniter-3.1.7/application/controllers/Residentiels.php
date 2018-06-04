<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Residentiels extends CI_Controller {


    private $session_header =  array(
        ISADMIN => 'templates/header_admin',
        ISRESIDENTIALCLIENT =>'templates/header_res',
        ISBUSINESSCLIENT => 'templates/header_bus',
        HASNORIGHTS => 'templates/header'

    );
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Residentiels_model');
        $this->load->model('User');
    }


    //Default url handling
    public function index()
    {
        $this->view();
    }


    public function view($page = 'clientsResidentiels')
    {
        session_start();
        if ( ! file_exists(APPPATH.'views/residentiels/'.$page.'.php')){
            show_404();
        }

        echo "<script> console.log('PHP: ','RESIDENTIELS CONTROLLER');</script>";
        $data['residentiels'] = $this->Residentiels_model->get_residentiels();

        if (isset($_SESSION['sessionData']) && $_SESSION['sessionData']['logged_in']==TRUE) {
            $this->load->view($this->session_header[$this->User->getRole($_SESSION['sessionData']['username'])],$data);
        }
        else {
            $this->load->view('templates/header_res', $data);
        }
        $this->load->view('residentiels/clientsResidentiels', $data);
        $this->load->view('templates/footer', $data);
    }
}
