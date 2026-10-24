<?php

namespace VG_Catalog\Api;

class Response {
    private $data;
    private $action;

    private int $code = 200;

    private bool $success = true;

    public static function success($data = null, $action = null, $code = 200): Response
    {
        $response = new Response();
        $response->data = $data;
        $response->action = $action;
        $response->code = $code;
        $response->success = true;

        return $response;
    }

    public static function error($data = null, $action = null, $code = 500): Response
    {
        $response = new Response();

        $response->data = $data;
        $response->action = $action;
        $response->code = $code;
        $response->success = false;

        return $response;
    }

    public function getData()
    {
        return $this->data;
    }

    public function isSuccess()
    {
        return $this->success === true;
    }

    public function isError()
    {
        return $this->success === false;
    }

    public function json()
    {
        http_response_code($this->code);
        header('Content-Type: application/json');
        echo json_encode([
            'success' => $this->success,
            'action' => $this->action,
            'data' => $this->data,
        ], JSON_UNESCAPED_UNICODE);
        exit();
    }
}