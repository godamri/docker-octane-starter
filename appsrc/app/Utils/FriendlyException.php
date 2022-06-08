<?php

namespace App\Utils;

class FriendlyException extends \Exception
{

    public $statusCode;
    public $errors;
    public $trace;

    public function __construct($message, $code = 422, $errors=[])
    {
        parent::__construct($message, $code);

        $this->statusCode = $code;
        $this->errors = $errors;
        $this->trace = [
            'file' => last(explode('/', $this->getFile()) ),
            'line' => $this->getLine(),
            'message' => $this->getMessage(),
        ];
    }

}
