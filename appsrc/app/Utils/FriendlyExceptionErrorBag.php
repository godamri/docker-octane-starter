<?php

namespace App\Utils;

class FriendlyExceptionErrorBag
{

    public array $errors = [];

    public function __construct($instance)
    {
    }

    /**
     * @param array|mixed $errors
     */
    public function setErrors(mixed $errors): void
    {
        $this->errors[] = $errors;
    }

    /**
     * @return array|mixed
     */
    public function getErrors(): mixed
    {
        $err = $this->errors;
        $this->flushErrors();
        return $err;
    }

    private function flushErrors(): void
    {
        $this->errors = [];
    }

}
