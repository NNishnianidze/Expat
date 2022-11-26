<?php

declare(strict_types=1);

namespace App\RequestValidators;

use App\Contracts\RequestValidatorInterface;
use App\Exception\ValidationException;
use App\DB;
use Valitron\Validator;

class RegisterUserRequestValidator implements RequestValidatorInterface
{
    public function __construct(
        private readonly DB $db
    ) {
    }

    public function validate(array $data): array
    {
        $v = new Validator($data);

        $v->rule('required', ['name', 'username', 'email', 'password', 'confirmPassword']);
        $v->rule('email', 'email');
        $v->rule('equals', 'confirmPassword', 'password')->label('Confirm Password must be the same as Password');
        $v->rule(
            fn ($field, $value, $params, $fields) => $this->db->validateEmailExistence($value),
            'email'
        )->message('User with the given email address already exists');
        $v->rule(
            fn ($field, $value, $params, $fields) => $this->db->validateUserName($value),
            'username'
        )->message('User with the given username already exists');

        if (!$v->validate()) {
            throw new ValidationException($v->errors());
        }

        return $data;
    }
}
