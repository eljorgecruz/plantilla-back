<?php

function responseRequest(bool $isError,string $message, bool $finishConnection, int $code, array $data = []){
    header('Content-Type: application/json');
    http_response_code($code);
    echo json_encode(["error"=> $isError, "message" => $message, "data" => $data]);
    if($finishConnection){
        die();
    }
}

?>