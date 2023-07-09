<?php

set_exception_handler(function ($e) {
    if ($e instanceof CustomException) {
      http_response_code($e->getCode());
      echo json_encode($e->payload, JSON_NUMERIC_CHECK);
    } else {
      http_response_code(Response::INTERNAL_SERVER_ERROR["code"]);

      $line = $e->getLine();
      $path = $e->getFile();
      $file = basename($path); // $file is set to "index.php"
      //$file = basename($path, ".php"); // $file is set to "index"
      $payload = [
        "status" => Response::INTERNAL_SERVER_ERROR["status"],
        "message" => "Internal Server Error (line $line in $file): ".$e->getMessage(),
        "code" => Response::INTERNAL_SERVER_ERROR["code"],
        "version" => "v5",
        "timestamp" => time(),
        "request" => $_SERVER["REQUEST_URI"] ?? "",
        "method" => $_SERVER["REQUEST_METHOD"] ?? "",
      ];

      echo json_encode($payload, JSON_NUMERIC_CHECK);
    }
  } );

class CustomException extends Exception
{
    public $payload = [];
    public function __construct($payload, int $code) {
        parent::__construct($payload["message"], $code);
        $this->payload = $payload;
    }
}
