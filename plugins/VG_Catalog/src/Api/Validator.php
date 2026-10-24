<?php

namespace VG_Catalog\Api;

use Symfony\Component\Validator\Validation;
use VG_Catalog\Application\Exceptions\ValidationException;

class Validator
{
    protected $validator;

    public function __construct()
    {
        $this->validator = Validation::createValidatorBuilder()
            ->enableAttributeMapping()
            ->getValidator();
    }

    public function validate(object $dto): void
    {
        $errors = $this->validator->validate($dto);

        if (count($errors) > 0) {
            $messages = [];
            foreach ($errors as $error) {
                $messages[$error->getPropertyPath()] = $error->getMessage();
            }

            throw new ValidationException($messages);
        }
    }
}
