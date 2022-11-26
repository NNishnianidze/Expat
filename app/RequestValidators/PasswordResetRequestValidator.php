<?php

declare(strict_types=1);

namespace App\RequestValidators;

use App\Contracts\RequestValidatorInterface;
use App\Exception\ValidationException;
use App\DB;
use Valitron\Validator;

class PasswordResetRequestValidator implements RequestValidatorInterface
{
    public function validate(array $data): array
    {
        $v = new Validator($data);

        $v->rule('required', ['email']);
        $v->rule('email', 'email');;

        if (!$v->validate()) {
            throw new ValidationException($v->errors());
        }

        return $data;
    }
}
