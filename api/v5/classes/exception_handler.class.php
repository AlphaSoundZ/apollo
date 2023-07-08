<?php

set_exception_handler(function ($e) {
    if ($e instanceof CustomException) {
      http_response_code($e->getCode());
      echo json_encode(["response" => $e->response_code, "message" => $e->getMessage(), "fields" => $e->fields]);
    } else {
      http_response_code(500);
      $line = $e->getLine();
      $path = $e->getFile();
      $file = basename($path);         // $file is set to "index.php"
      $file = basename($path, ".php"); // $file is set to "index"
      echo json_encode(["response" => 500, "message" => "Internal Server Error (line $line in $file): ".$e->getMessage()]);
    }
  } );

class CustomException extends Exception
{
	public $response_code = 9;
  public $fields = [];

    public function __construct($message, $response_code, int $code, array $fields = []) {
        parent::__construct($message, $code);
        if (!Response::isValidName($response_code))
        	throw new Exception("Invalid response code");
        $this->response_code = $response_code;
        $this->fields = $fields;
    }
}
