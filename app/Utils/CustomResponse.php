<?php

namespace App\Utils;
use Illuminate\Contracts\Support\Arrayable;
use Throwable;

class CustomResponse implements Arrayable {
    private $error;
    private $validation;
    private $errors;
    private $message;
    private $data;

    public function __construct($error, $validation, $data, $message = '', $errors = []) {
        $this->error = $error;
        $this->validation = $validation;
        $this->errors = $errors;
        $this->message = $message;
        $this->data = $data;
    }

    public function toArray() {
        return [
            'error' => $this->error,
            'validation' => $this->validation,
            'errors' => $this->errors,
            'message' => $this->message,
            'data' => $this->data,
        ];
    }

    public static function success($data, $message = null, $status = 200) {
        $response = new CustomResponse(false, false, $data, $message, []);
        return response()->json($response, $status);
    }

    public static function error($message, $status = 500) {
        $response = new CustomResponse(true, false, null, $message, []);
        return response()->json($response, $status);
    }

    public static function validationError($validators, $status = 400) {
        $response = new CustomResponse(true, true, null, 'Erreur de validation', $validators->errors());
        return response()->json($response, $status);
    }

    public static function catchException(Throwable $th, $status = 500) {
        $response = new CustomResponse(true, false, null, $th->getMessage(), []);
        return response()->json($response, $status);
    }
}