<?php

set_exception_handler(function ($e) {
    if ($e instanceof CustomException) {
      http_response_code($e->getCode());
      echo json_encode(["response" => $e->response_code, "message" => $e->getMessage()]);
    } else {
      http_response_code(500);
      echo json_encode(["response" => 500, "message" => "Internal Server Error: ".$e->getMessage()]);
    }
  } );

class CustomException extends Exception
{
	public $response_code = 9;

    public function __construct($message, $response_code, $code) {
        parent::__construct($message, $code);
        if (!Response::isValidName($response_code)) // if 
        	throw new Exception("Internal Server Error: Invalid response code", 500);
        $this->response_code = $response_code;
    }
}
