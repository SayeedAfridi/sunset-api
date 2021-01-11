<?php namespace App\Controllers;

use App\Models\UserModel;
use CodeIgniter\HTTP\Response;
use CodeIgniter\HTTP\ResponseInterface;
use Exception;
use ReflectionException;

class Auth extends BaseController {
  /**
   * @return Response
   * @throws ReflectionException
   */
  public function register () {
    $rules = [
      'name' => 'required',
      'email' => 'required|max_length[50]|valid_email|is_unique[user.email]',
      'password' => 'required|min_length[6]|max_length[255]'
    ];

    $input = $this->getRequestInput($this->request);

    if(!$this->validateRequest($input, $rules)){
      return $this->getResponse($this->validator->getErrors(), ResponseInterface::HTTP_BAD_REQUEST);
    }

    $userModel = new UserModel();
    $userModel->save($input);

    return $this->getJWTForUser($input['email'], ResponseInterface::HTTP_CREATED);
  }

  public function login(){
    $rules = [
      'email' => 'required|max_length[50]|valid_email',
      'password' => 'required|min_length[6]|max_length[255]|validateUser[email, password]'
    ];
    $errors = [
      'password' => [
          'validateUser' => 'Invalid login credentials provided'
      ]
    ];

    $input = $this->getRequestInput($this->request);

    if(!$this->validateRequest($input, $rules, $errors)){
      return $this->getResponse($this->validator->getErrors(), ResponseInterface::HTTP_BAD_REQUEST);
    }

    return $this->getJWTForUser($input['email']);

  }

  public function getJWTForUser (string $email, int $responseCode = ResponseInterface::HTTP_OK) {
    try {
      $model = new UserModel();
      $user = $model->findUserByEmailAddress($email);
      unset($user['password']);
      helper('jwt');
      return $this->getResponse(
        [
          'message'=> 'User authenticated',
          'user' => $user,
          'token' => getSignedJWTForUser($user['email'])
        ],
        $responseCode
      );
    } catch (Exception $e) {
      return $this->getResponse(
        [
          'error' => $e->getMessage()
        ],
        $responseCode
      );
    }
  }
}

?>