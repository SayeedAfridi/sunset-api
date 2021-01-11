<?php namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;


class Home extends ResourceController
{
	use ResponseTrait;

	public function index()
	{
		$data = [
			'success' => true,
			'id' => 123
];
		return $this->respond($data);
	}

	//--------------------------------------------------------------------

}
?>