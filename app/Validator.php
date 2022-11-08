<?php

declare(strict_types=1);

namespace App;

use App\Entity\Users;
use Doctrine\ORM\EntityManager;
use Valitron\Validator as ValitronValidator;

class Validator
{
    private ValitronValidator $v;

    public function __construct(
        private DB $db,
        private EntityManager $entityManager,
    ) {
    }

    public function validateRegister(array $data): bool|array
    {
        $this->v = new ValitronValidator($data);


        $this->v->rule('required', ['name', 'username', 'email', 'password', 'confirmPassword']);
        $this->v->rule('email', 'email');
        $this->v->rule('equals', 'confirmPassword', 'pwd')->message('Confirm Password must be the same as Password');
        $this->v->rule(
            fn ($field, $value, $params, $fields) => $this->db->validateEmailExist($value),
            'email'
        )->message('User with the given email address already exists');
        $this->v->rule(
            fn ($field, $value, $params, $fields) => $this->db->validateUserName($value),
            'username'
        )->message('User with the given User Name already exists');

        if (!$this->v->validate()) {
            return $this->v->errors();
        }

        return true;
    }

    public function validateLogin(array $data): bool|array
    {
        $email = (string) $data['email'];
        $this->v = new ValitronValidator($data);

        $this->v->rule('required', ['email', 'password']);
        $this->v->rule('email', 'email');
        $this->v->rule(
            fn ($field, $value, $params, $fields) => $this->db->validateEmailNotExist($value),
            'email'
        )->message('User with the given email do not exists');
        $this->v->rule(
            fn ($field, $value, $params, $fields) => $this->db->vertifyPassword($email, $value),
            'password'
        )->message('Password is incorect');

        if (!$this->v->validate()) {
            return $this->v->errors();
        }

        return true;
    }

    public function validatePasswordReset(array $data): bool|array
    {
        $this->v = new ValitronValidator($data);

        $this->v->rule('required', ['email']);
        $this->v->rule('email', 'email');
        $this->v->rule(
            fn ($field, $value, $params, $fields) => $this->db->validateEmailNotExist($value),
            'email'
        )->message('User with the given email do not exists');

        if (!$this->v->validate()) {
            return $this->v->errors();
        }

        return true;
    }

    public function validateNewPassword(array $data): bool|array
    {
        $this->v = new ValitronValidator($data);

        $this->v->rule('required', ['email', 'token'])->message('Password Reset link expired please make new request');

        if (!$this->v->validate()) {
            return $this->v->errors();
        }

        return true;
    }

    public function validateNewPass(array $data): bool|array
    {
        $this->v = new ValitronValidator($data);

        $this->v->rule('required', ['password', 'confirmPassword']);

        if (!$this->v->validate()) {
            return $this->v->errors();
        }

        return true;
    }
}
