<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Product extends MX_Controller {

    var $data;

    function __construct() {
        parent::__construct();
		$this->require_login();
		$this->load->model(array('pos/M_Produk','pos/M_Kategori','pos/M_Status','users/User_model'));
		$this->M_Produk->initialize();
    }
	protected function require_login()
	{
		if (!$this->ion_auth->logged_in()) {
			if ($this->input->is_ajax_request()) {
				http_response_code(401);
				echo json_encode([
					'redirect' => site_url('login')
				]);
				exit;
			}
			redirect('login');
		}
	}

	public function index() {
		$this->data['title'] = 'Products';
		$this->data['user'] = $this->ion_auth->user()->row();
		$this->data['listDataUrl'] = site_url('Pos/Product/listData');
		$this->data['syncUrl'] = site_url('Pos/Product/generatedata');
		$this->template->load('index', 'product/v_list', $this->data);
	}

	public function listData()
	{
		$this->data['products'] = $this->M_Produk->q_data_read_where(' AND (lower(nama_status) = \'bisa dijual\' or status_id = 1) ')->result();
//		$this->data['listDataUrl'] = site_url('Pos/Product/listData');
		$this->load->view('product/v_content', $this->data);
	}

	public function update($id)
	{
		$transaction = $this->M_Produk->q_data_read_where(' AND id = \'' . $id . '\' ')->row();
		$data = array(
			'statusData' => $this->M_Status->q_data_read_where()->result(),
			'categoryData' => $this->M_Kategori->q_data_read_where()->result(),
			'transaction' => $transaction,
			'formAction' => site_url('pos/product/doupdate/'.$transaction->id),
			'modalSize' => 'modal-md',
			'modalTitle' =>  'Ubah data ' . $transaction->text,
			'content' => 'product/modals/v_update',
		);
        $this->load->view($data['content'],$data);
	}

	public function doupdate($id)
	{
		header('Content-Type: application/json');
		$productName = $this->input->get_post('product_name');
		$price = $this->input->get_post('price');
		$categoryId = $this->input->get_post('category_id');
		$statusId = $this->input->get_post('status_id');
		$transaction = $this->M_Produk->q_data_read_where(' AND id = \''.$id.'\' ')->row();
		if (empty($transaction)) {
			http_response_code(404);
			echo json_encode(array(
				'message' => 'Data tidak ditemukan.'
			));
		}else{
			$this->M_Produk->q_data_update(array(
				'nama_produk' => $productName,
				'harga' => $price,
				'kategori_id' => $categoryId,
				'status_id' => $statusId,
			),array(
				'id_produk' => $id,
			));

			http_response_code(200);
			echo json_encode(array(
				'message' => 'Data berhasil disimpan.'
			));
		}
	}

	public function dodelete($id)
	{
		header('Content-Type: application/json');
		$transaction = $this->M_Produk->q_data_read_where(' AND id = \''.$id.'\' ')->row();
		if (empty($transaction)) {
			http_response_code(404);
			echo json_encode(array(
				'message' => 'Data tidak ditemukan.'
			));
		}else{
			$this->M_Produk->q_data_delete(array(
				'id_produk' => $id,
			));

			http_response_code(200);
			echo json_encode(array(
				'message' => 'Data berhasil dihapus.'
			));
		}
	}

	public function generatedata()
	{
		$target = "https://recruitment.fastprint.co.id/tes/tes/programmer/";

		$ch = curl_init($target);

		curl_setopt_array($ch, [
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_USERAGENT      => "Mozilla/5.0",
			CURLOPT_TIMEOUT        => 30,
			CURLOPT_SSL_VERIFYPEER => false,
			CURLOPT_SSL_VERIFYHOST => false,
		]);

		$html = curl_exec($ch);
		if ($html === false) {
			echo "cURL Error: " . curl_error($ch);
			curl_close($ch);
			return;
		}
		curl_close($ch);
		$dom = new DOMDocument();
		@$dom->loadHTML($html);
		$text = $dom->textContent;
		$username = null;
		if (preg_match('/Username:\s*([A-Za-z0-9]+)/', $text, $matches)) {
			$username = $matches[1];
		}

		if (!$username) {
			echo "Username not found";
			return;
		}
		$rawPassword = "bisacoding-" . date("d-m-y");
		$passwordMd5 = md5($rawPassword);

		$endpoint = 'https://recruitment.fastprint.co.id/tes/api_tes_programmer';
		$content = $this->postFormData($endpoint, $username, $passwordMd5);
		header('Content-Type: application/json');
		if (!$content['success']){
			http_response_code($content['http_code']);
			echo json_encode($content['response']);
			exit();
		}else{
			$data = json_decode($content['response'], true);
			foreach ($data['data'] as $index => $item) {
				$categoryName = $item['kategori'];
				$statusName = $item['status'];
				$productId = $item['id_produk'];
				$productName = $item['nama_produk'];
				$price = $item['harga'];
				if (!$this->M_Kategori->q_data_exists(' TRUE AND nama_kategori = \'' . $categoryName . '\' ')) {
					$this->M_Kategori->q_data_create(array(
						'nama_kategori' => $categoryName,
					));
					$categoryData = $this->M_Kategori->q_data_read_where(' AND nama_kategori = \'' . $categoryName . '\' ')->row();
				} else {
					$categoryData = $this->M_Kategori->q_data_read_where(' AND nama_kategori = \'' . $categoryName . '\' ')->row();
				}
				if (!$this->M_Status->q_data_exists(' TRUE AND nama_status = \'' . $statusName . '\' ')) {
					$this->M_Status->q_data_create(array(
						'nama_status' => $statusName,
					));
					$statusData = $this->M_Status->q_data_read_where(' AND nama_status = \'' . $statusName . '\' ')->row();
				} else {
					$statusData = $this->M_Status->q_data_read_where(' AND nama_status = \'' . $statusName . '\' ')->row();
				}
				if (!$this->M_Produk->q_data_exists(' TRUE AND id_produk = \'' . $productId . '\' ')) {
					$this->M_Produk->q_data_create(array(
						'id_produk' => $productId,
						'nama_produk' => $productName,
						'harga' => $price,
						'kategori_id' => $categoryData->id_kategori,
						'status_id' => $statusData->id_status,
					));
				}else{
					$this->M_Produk->q_data_update(array(
						'nama_produk' => $productName,
						'harga' => $price,
						'kategori_id' => $categoryData->id_kategori,
						'status_id' => $statusData->id_status,
					),array(
						'id_produk' => $productId,
					));
				}
			}

			http_response_code($content['http_code']);
			echo json_encode(array(
				'message' => $content['status'],
			));
		}
	}

	function postFormData($url, $username, $password)
	{
		$postFields = [
			'username' => $username,
			'password' => $password
		];

		$ch = curl_init();

		curl_setopt_array($ch, [
			CURLOPT_URL            => $url,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_POST           => true,
			CURLOPT_POSTFIELDS     => $postFields,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_USERAGENT      => "Mozilla/5.0",
			CURLOPT_TIMEOUT        => 30,
			CURLOPT_CONNECTTIMEOUT => 10,
			CURLOPT_SSL_VERIFYPEER => false,
			CURLOPT_SSL_VERIFYHOST => false,
		]);

		$response = curl_exec($ch);
		if ($response === false) {
			$error = curl_error($ch);
			$errno = curl_errno($ch);
			curl_close($ch);

			return [
				'success' => false,
				'status'  => 'NETWORK_ERROR',
				'error'   => $error,
				'code'    => $errno
			];
		}

		$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);
		if ($httpCode < 200 || $httpCode >= 300) {
			return [
				'success'   => false,
				'status'    => 'HTTP_ERROR',
				'http_code' => $httpCode,
				'response'  => 'destination error'
			];
		}
		if (trim($response) === '') {
			return [
				'success' => false,
				'status'  => 'EMPTY_RESPONSE'
			];
		}

		return [
			'success'   => true,
			'status'    => 'OK',
			'http_code' => $httpCode,
			'response'  => $response
		];
	}




}
