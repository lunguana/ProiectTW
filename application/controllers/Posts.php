<?php
class Posts extends CI_Controller{
	public function __construct() 
	{ 
		parent::__construct(); 
		$this->load->model('Post_model'); 

        // $this->load->library('form_validation'); 

		$this->load->helper('string'); 
	} 

	public function index($offset = 0){
		//pagination config
		$config['base_url'] = base_url() . 'posts/index/';
		$config['total_rows'] = $this->db->count_all('cutremur');
		$config['per_page'] = 3;
		$config['uri_segment'] = 3;
		$config['attributes'] = array('class' => 'pagination-link');

		//init pag
		$this->pagination->initialize($config);

		$data['title'] = 'Last';

		$data['posts'] = $this->post_model->get_posts(FALSE, $config['per_page'], $offset);

		$this->load->view('templates/header');
		$this->load->view('posts/index', $data);
		$this->load->view('templates/footer');

	}

	public function view($IDCUTREMUR = NULL){
		$data['post'] = $this->post_model->get_posts($IDCUTREMUR);

		if(empty($data['post'])){
			show_404();
		}

		$data['title'] = $data['post'];

		$this->load->view('templates/header');
		$this->load->view('posts/view', $data);
		$this->load->view('templates/footer');

	}

	public function create(){
			//verifica login
		if(!$this->session->userdata('logged_in')){
			redirect('users/login');
		}
		else {

			$data['title'] = 'Create Post';

			$this->form_validation->set_rules('IDCUTREMUR', 'ID', 'required');
			$this->form_validation->set_rules('MAGNITUDINE', 'MAGNITUDINE', 'required');

			if($this->form_validation->run() == FAlSE){

				$this->load->view('templates/header');
				$this->load->view('posts/create', $data);
				$this->load->view('templates/footer');		

			}
			else {
				$this->post_model->create_post();

				$this->session->set_flashdata('post_created', 'Ai creat cutremur');
				redirect('posts');
			}
		}
	}

	public function delete($IDCUTREMUR){
			//verifica login
		if(!$this->session->userdata('logged_in')){
			redirect('users/login');
		}
		else {
			$this->post_model->delete_post($IDCUTREMUR);

			$this->sessions->set_flashdata('post_deleted', 'Ai sters');

			redirect('posts');
		}
	}

	public function edit($IDCUTREMUR){
			//verifica login
		if(!$this->session->userdata('logged_in')){
			redirect('users/login');
		}
		else {

			$data['post'] = $this->post_model->get_posts($IDCUTREMUR);

			if(empty($data['post'])){
				show_404();
			}

			$data['title'] = 'Edit post';

			$this->load->view('templates/header');
			$this->load->view('posts/edit', $data);
			$this->load->view('templates/footer');

		}
	}

	public function update(){
			//verifica login
		if(!$this->session->userdata('logged_in')){
			redirect('users/login');
		}
		else {
			$this->post_model->update_post();

			$this->sessions->set_flashdata('post_updated', 'Ai updatat');

			redirect('posts');
		}
	}

	public function getdata_chart($idcutremur){
		$data = $this->Our_chart_model->get_data_for_chart($idcutremur); 

       //data to json 

		$responce->cols[] = array( 
		                          "id" => "", 
		                          "label" => "Topping", 
		                          "pattern" => "", 
		                          "type" => "string" 
		                          ); 
		$responce->cols[] = array( 
		                          "id" => "", 
		                          "label" => "Total", 
		                          "pattern" => "", 
		                          "type" => "number" 
		                          ); 
		foreach($data as $cd) 
		{ 
			$responce->rows[]["c"] = array( 
			                               array( 
			                                     "v" => "$cd->idpersoana", 
			                                     "f" => null 
			                                     ) , 
			                               array( 
			                                     "v" => (int)$cd->idpersoana, 
			                                     "f" => null 
			                                     ) 
			                               ); 
		} 

		echo json_encode($responce);  
	}
}