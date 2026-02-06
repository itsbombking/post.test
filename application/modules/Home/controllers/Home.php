<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends CI_Controller {

    var $data;

    function __construct() {
        parent::__construct();

        if (!$this->ion_auth->logged_in()) {
            redirect('Login');
        }

    }

	public function index() {
		$this->data['user'] = $this->ion_auth->user()->row();
		$this->data['title'] = 'Users';
        $this->template->load('index', 'home', $this->data);
    }
}
