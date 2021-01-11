<?php
namespace App\Controllers;

/**
 * Class BaseController
 *
 * BaseController provides a convenient place for loading components
 * and performing functions that are needed by all your controllers.
 * Extend this class in any new controllers:
 *     class Home extends BaseController
 *
 * For security be sure to declare any new methods as protected or private.
 *
 * @package CodeIgniter
 */

use CodeIgniter\Controller;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\Validation\Exceptions\ValidationException;
use Config\Services;

class BaseController extends Controller
{

	/**
	 * @var array
	 */
	protected $helpers = [];

	public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
	{
		parent::initController($request, $response, $logger);
	}

	public function getResponse(array $responseBody, int $code = ResponseInterface::HTTP_OK){
		return $this->response->setStatusCode($code)->setJSON($responseBody);
	}

	public function getRequestInput(IncomingRequest $request){
    $input = $request->getPost();
    if (empty($input)) {
        $input = json_decode($request->getBody(), true);
    }
    return $input;
	}

	public function validateRequest($input, array $rules, array $messages =[]){
		$this->validator = Services::Validation()->setRules($rules);
		
    if (is_string($rules)) {
        $validation = config('Validation');

        if (!isset($validation->$rules)) {
            throw ValidationException::forRuleNotFound($rules);
        }

        if (!$messages) {
            $errorName = $rules . '_errors';
            $messages = $validation->$errorName ?? [];
        }

        $rules = $validation->$rules;
    }
    return $this->validator->setRules($rules, $messages)->run($input);
	}
}
