<?php

class Router
{

    private  $routes = [];
    private  $version = 'v1';
    private  $params = [];
    private  $routeFound;
    private  $body;
    private  $errorStr = 'API not found';
    private  $errorCode = 404;
    private bool $hasAccess = true;


    public function __construct()
    {
        $this->routeFound = false;
    }

    public function setRouteVersion($version = 'v1')
    {
        $this->version = $version;
    }

    
    private function middlewares($middlewares)
    {
        if (count($middlewares) == 0) {
            return true;
        }
        $access = array_search(true, $middlewares);
        if ($access === false) {
            $this->hasAccess = false;
            $this->errorStr = 'Usuario sin acceso';
            $this->errorCode = 403;
        }
        return $access !== false;
    }

    public function get($URL, $callback, $middlewares = [])
    {
        if ($_SERVER['REQUEST_METHOD'] != 'GET') {
            return;
        }

        if (!$this->middlewares($middlewares)) {
            return;
        }

        $this->setRoute($URL);
        if ($this->executeThisURL()) {
            $req['params'] = (object) $this->params;
            $callback((object) $req);
            return;
        };
    }

    public function delete($URL, $callback, $middlewares = [])
    {
        if ($_SERVER['REQUEST_METHOD'] != 'DELETE') {
            return;
        }

        if (!$this->middlewares($middlewares)) {
            return;
        }

        $this->setRoute($URL);
        if ($this->executeThisURL()) {
            $req['params'] = (object) $this->params;
            $callback((object) $req);
            return;
        };
    }

    public function post($URL, $callback, $middlewares = [])
    {
        if ($_SERVER['REQUEST_METHOD'] != 'POST') {
            return;
        }
        if (!$this->middlewares($middlewares)) {
            return;
        }
        $this->setRoute($URL);
        if ($this->executeThisURL()) {
            $req['params'] = (object) $this->params;
            $req['body'] = (object) $this->body;

            $callback((object) $req);
            return;
        }
    }

    public function put($URL, $callback, $middlewares = [])
    {
        if ($_SERVER['REQUEST_METHOD'] != 'PUT') {
            return;
        }

        if (!$this->middlewares($middlewares)) {
            return;
        }

        $this->setRoute($URL);
        if ($this->executeThisURL()) {
            $req['params'] = (object) $this->params;
            $req['body'] = (object) $this->body;

            $callback((object) $req);
        }
    }

    public function dafault($callback)
    {
        if ($this->routeFound == false) {
            $callback(
                (object) [
                    "message" => $this->errorStr,
                    "statusCode" => $this->errorCode,
                    "hasAccess" => $this->hasAccess
                ]
            );
        }
    }

    private function executeThisURL()
    {
        $response = $this->start();
        return $response;
    }
    public function setRoute($routeName)
    {
        $exist = $this->existsRoute($routeName);
        if ($exist !== false) {
            throw new Error("This URL: $routeName already exists");
        }
        $splitedRoutName = explode('/', $routeName);
        array_push($this->routes, (object) array(
            "url" => $this->version . $routeName,
        ));
    }

    private function existsRoute($routeName)
    {
        $newRoute = $this->version . $routeName;
        $found = array_search($newRoute, array_column($this->routes, 'url'));
        return $found;
    }

    private function hasSameParams($currentURLSplited, $routeUrlSplited)
    {
        $res = true;
        foreach ($routeUrlSplited as $key => $item) {
            if (strpos($item, ':') !== false) {
                $valuePram = $currentURLSplited[$key];
                $res = !empty($valuePram);
            }
        }
        return $res;
    }

    private function isSameURLString($currentURLSplited, $routeUrlSplited)
    {
        $isSame = true;
        foreach ($routeUrlSplited as $key => $item) {
            if (strpos($item, ':') !== false) {
                $currentURLSplited[$key] = $item;
            }
        }
        $isSame = count(array_diff($currentURLSplited, $routeUrlSplited)) == 0;
        return $isSame;
    }
    public function start()
    {
        $foundRoute = false;
        $currentURL = isset($_GET['path']) ? $_GET['path'] : explode("index.php", $_SERVER['REQUEST_URI'])[1];
        // remove first / 
        $currentURL = isset($_GET['path']) ? $currentURL :  substr($currentURL, 1);
        $urlSplited = explode('/', $currentURL);
        $sizeURL = count($urlSplited);
        foreach ($this->routes as $route) {
            $routeSize = count(explode('/', $route->url));
            $routeUrlSplited = explode('/', $route->url);
            //var_dump($route->url);
            if ($sizeURL == $routeSize && $this->hasSameParams($urlSplited, $routeUrlSplited) && $this->isSameURLString($urlSplited, $routeUrlSplited)) {
                foreach ($routeUrlSplited as $key => $item) {
                    if (strpos($item, ':') !== false) {
                        $valuePram = $urlSplited[$key];
                        $namePram = explode(':', $item)[1];

                        $this->params[$namePram] = $valuePram;
                    }
                }
                unset($_GET['path']);
                $this->params['extraParms'] = $_GET;
                $this->body = json_decode(file_get_contents('php://input'));
                $foundRoute = true;
            }
        }
        $this->routeFound = $foundRoute;
        return $foundRoute;
    }
}
