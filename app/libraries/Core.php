<?php

class Core
{
    protected $currentController = "Pages";
    protected $currentMethod = "index";
    protected $params = [];
    public function __construct() {
        $url = $this->getUrl();
        
        if (isset($url[1]) && file_exists("../app/controllers/" . $url[1] . ".php")) {
            $this->currentController = ucwords($url[1]);
            unset($url[1]);
        }
        
        // Include Controller file
        require_once "../app/controllers/" . $this->currentController . ".php";
        // Initialise Controller
        $this->currentController = new $this->currentController;
        
        if (isset($url[2]) && method_exists($this->currentController, $url[2])) {
            $this->currentMethod = $url[2];
            unset($url[2]);
        }
        
        $this->params = $url ? array_values($url) : [];
        call_user_func_array([$this->currentController, $this->currentMethod], $this->params);
        
    }
    
    public function getUrl() {
        $request_uri = $_SERVER['REQUEST_URI'];
        
        // Used parse_url to get only the path part, without the query string
        // QS like: $_GET['url'];
        $path = parse_url($request_uri, PHP_URL_PATH);
        // $path = parse_url($_GET['url'], PHP_URL_PATH);
        
        // Remove the leading slash
        $url = ltrim($path, '/');
        $url = filter_var($url, FILTER_SANITIZE_URL);
        $url = explode("/", $url);
        // echo '/' . strtolower(SITENAME) . '/' . "\n";
        // print_r($path);
        // print_r($url);
        unset($url[0]);
        return $url;
    }
}
