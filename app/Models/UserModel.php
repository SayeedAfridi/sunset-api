<?php namespace App\Models;

use CodeIgniter\Model;
use Exception;

class UserModel extends Model {
  protected $table = 'user';
  protected $allowedFields = [
    'name',
    'email',
    'password',
  ];

  protected $updatedField = 'updated_at';

  protected $beforeInsert = ['beforeInsert'];
  protected $beforeUpdate = ['beforeUpdate'];

  protected function beforeInsert(array $data): array {
    return $this->getUpdatedDataWithHashedPass($data);
  }

  private function getUpdatedDataWithHashedPass(array $data): array {
    if(isset($data['data']['password'])) {
      $plainTextPass = $data['data']['password'];
      $data['data']['password'] = $this->hashPassword($plainTextPass);
    }

    return $data;
  }

  private function hashPassword(string $plaintextPassword): string{
      return password_hash($plaintextPassword, PASSWORD_BCRYPT);
  }

  public function findUserByEmailAddress(string $emailAddress) {
      $user = $this
          ->asArray()
          ->where(['email' => $emailAddress])
          ->first();

      if (!$user) 
          throw new Exception('User does not exist for specified email address');

      return $user;
  }
}

?>