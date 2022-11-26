<?php

declare(strict_types=1);

namespace App\RequestValidators;

use App\Contracts\RequestValidatorInterface;
use App\Exception\ValidationException;
use Valitron\Validator;

class NewPasswordRequestValidator implements RequestValidatorInterface
{
    public function validate(array $data): array
    {
        $v = new Validator($data);

        $v->rule('required', ['email', 'token'])->message('Password reset link is expired please make new request');

        if (!$v->validate()) {
            throw new ValidationException($v->errors());
        }

        return $data;
    }
}
