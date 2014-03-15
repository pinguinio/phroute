<?php namespace FastRoute;

class RouteCollector {
    private $routeParser;
    private $dataGenerator;
    private $filters;
    private $before = array();
    private $after = array();
    
    private $globalFilters = array();

    public function __construct(RouteParser $routeParser, DataGenerator $dataGenerator) {
        $this->routeParser = $routeParser;
        $this->dataGenerator = $dataGenerator;
    }

    public function addRoute($httpMethod, $route, $handler, array $filters = array()) {
        $routeData = $this->routeParser->parse($route);
        $this->dataGenerator->addRoute($httpMethod, $routeData, $handler, array_merge_recursive($this->globalFilters, $filters));
        return $this;
    }
    
    public function group(array $filters, \Closure $callback)
    {
        $this->globalFilters = array_intersect_key($filters, array(Route::AFTER => 1, Route::BEFORE => 1));
        $callback();
        $this->globalFilters = array();
    }
    
    public function before($handler)
    {
        $this->before[] = $handler;
    }
    
    public function after($handler)
    {
        $this->after[] = $handler;
    }
    
    public function filter($name, $handler)
    {
        $this->filters[$name] = $handler;
    }
    
    public function get($route, $handler, array $filters = array())
    {
        return $this->addRoute(Route::GET, $route, $handler, $filters);
    }
    
    public function head($route, $handler, array $filters = array())
    {
        return $this->addRoute(Route::GET, $route, $handler, $filters);
    }
    
    public function post($route, $handler, array $filters = array())
    {
        return $this->addRoute(Route::POST, $route, $handler, $filters);
    }
    
    public function put($route, $handler, array $filters = array())
    {
        return $this->addRoute(Route::PUT, $route, $handler, $filters);
    }
    
    public function delete($route, $handler, array $filters = array())
    {
        return $this->addRoute(Route::DELETE, $route, $handler, $filters);
    }
    
    public function options($route, $handler, array $filters = array())
    {
        return $this->addRoute(Route::OPTIONS, $route, $handler, $filters);
    }
    
    public function any($route, $handler, array $filters = array())
    {
        return $this->addRoute(Route::ANY, $route, $handler, $filters);
    }

    public function getData() 
    {
        return $this->dataGenerator->getData();
    }
    
    public function getFilters() 
    {
        return array($this->before, $this->after, $this->filters);
    }
    
    public function getValidMethods()
    {
        return array(
            Route::ANY,
            Route::GET,
            Route::POST,
            Route::PUT,
            Route::DELETE,
            Route::OPTIONS,
            Route::HEAD,
        );
    }
}
