<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Pages extends CI_Controller
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
        $this->load->model('User');
        $this->load->model('Admin_model');
    }


    //Default url handling
    public function index()
    {
        $this->view();
    }


    public function view($page = 'home')
    {
        session_start();
        if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
            show_404();
        }

        $data = array(); // Recovering of GET parameters
        echo "<script> console.log('PHP: ','PAGES CONTROLLER');</script>";


        if (isset($_GET['log']) && $_GET['log'] == "SUCCESS") {
            $this->load->view('templates/toast_connected', $data);
            $this->load->view($this->session_header[$this->User->getRole($_SESSION['sessionData']['username'])], $data);
        }
        elseif (isset($_SESSION['sessionData']) && $_SESSION['sessionData']['logged_in']==TRUE){
            $this->load->view($this->session_header[$this->User->getRole($_SESSION['sessionData']['username'])], $data);
        }
       else {
                $this->load->view('templates/header', $data);
            }
            $this->load->view('pages/' . $page, $data);
            $this->load->view('templates/footer', $data);
        }
    }



