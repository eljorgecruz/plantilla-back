<?php
$allowedOrigins = [

  'http://localhost:3000',

  'http://localhost:5173',

  'http://localhost:8080',

  'http://127.0.0.1:5500',

  'http://127.0.0.1:5173',

];

$origin = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : '';

if (in_array($origin, $allowedOrigins)) {

  header("Access-Control-Allow-Origin: $origin");

  header('Access-Control-Allow-Credentials: true');

  header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');

  header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');

}

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {

  exit(0);

}

header('Content-Type: application/json');

$DB = new DatabaseConnection();
$Router = new Router();


// api con conexion a db
$Router->get('/conection/db', function ($req) {
  global $DB;
  $conn = $DB->connect();
});

/* # Ejemplo acceder api 
  http://localhost/api/v1/test 
*/
$Router->get('/test', function () {
  responseRequest(200, 'succces', true, []);
});

/* # Ejemplo GET con parametros
  http://localhost/api/v1/test/1/otroTest 
*/
$Router->get('/test/:id_test/otroTest', function ($req) {
  responseRequest(200, 'succces', true, ["params" => $req->params]);
});

/* 
  # Ejemplo GET con dos parametros
  http://localhost/api/v1/test/1/2/test 
*/
$Router->get('/test/:id_test/:id_extra_params/test', function ($req) {
  responseRequest(200, 'succces', true, ["params" => $req->params]);
});

/* # Ejemplo POST 
  http://localhost/api/v1/test 
*/
$Router->post('/test', function ($req) {
  responseRequest(200, 'succces', true, ["Body" => $req->body]);
});


/* # Ejemplo PUT 
  http://localhost/api/v1/test 
*/
$Router->put('/test', function ($req) {
  responseRequest(200, 'succces', true, ["Body" => $req->body]);
});


// Cambiar version de rutas
$Router->setRouteVersion('v2');


/* # Ejemplo acceder api 
  http://localhost/api/v2/test 
*/
$Router->post('/test', function ($req) {
  responseRequest(200, 'succces', true, ["Body" => $req->body]);
});

$Router->dafault(function () {
  responseRequest(
    404,
    'API not found',
    true,0
  );
});

?>
