<?php

declare(strict_types=1);

namespace App;

use App\Contracts\AuthInterface;
use App\Entity\Users;
use Doctrine\ORM\EntityManager;
use Valitron\Validator as ValitronValidator;

class Validator
{
    private ValitronValidator $v;

    public function __construct(
        private DB $db,
        private EntityManager $entityManager,
        private readonly AuthInterface $auth,
    ) {
    }

    public function validateRegister(array $data): bool|array
    {
        $this->v = new ValitronValidator($data);


        $this->v->rule('required', ['name', 'username', 'email', 'password', 'confirmPassword']);
        $this->v->rule('email', 'email');
        $this->v->rule('equals', 'confirmPassword', 'password')->label('Confirm Password must be the same as Password');
        $this->v->rule(
            fn ($field, $value, $params, $fields) => $this->db->validateEmailExistence($value),
            'email'
        )->message('User with the given email address already exists');
        $this->v->rule(
            fn ($field, $value, $params, $fields) => $this->db->validateUserName($value),
            'username'
        )->message('User with the given username already exists');

        if (!$this->v->validate()) {
            return $this->v->errors();
        }

        return true;
    }

    public function validateLogin(array $data): bool|array
    {
        $v = new ValitronValidator($data);

        $v->rule('required', ['email', 'password']);
        $v->rule('email', 'email');

        if (!$this->auth->attemptLogin($data)) {
            return false;
        };

        return true;
    }

    public function validateLogOut(): void
    {
        $this->auth->logOut();
    }

    public function validatePasswordReset(array $data): bool|array
    {
        $this->v = new ValitronValidator($data);

        $this->v->rule('required', ['email']);
        $this->v->rule('email', 'email');
        $this->v->rule(
            fn ($field, $value, $params, $fields) => $this->db->validateEmailExistence($value),
            'email'
        )->message('You have entered an Invalid username or password');

        if (!$this->v->validate()) {
            return $this->v->errors();
        }

        return true;
    }

    public function validateNewPassword(array $data): bool|array
    {
        $this->v = new ValitronValidator($data);

        $this->v->rule('required', ['email', 'token'])->message('Password reset link expired please make new request');

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
