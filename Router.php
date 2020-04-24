<?php

function Redirect($url, $permanent = false)
{
    header('Location: ' . $url, true, $permanent ? 301 : 302);
    exit();
}

if (preg_match('/\.(?:txt|json|hvc|sql)$/', $_SERVER["REQUEST_URI"])) {
    // Redirect every access to files that are not required for navigation
    Redirect('/', false);
} else {
    // serve the requested resource as-is.
    return false;
}

class Router
{
    private $request;
    private $supportedHttpMethods = array(
        "GET",
        "POST"
    );

    function __construct(Request $request)
    {
        $this->request = $request;
    }

    function __call($name, $args)
    {
        list($route, $method) = $args;
        if (!in_array(strtoupper($name), $this->supportedHttpMethods)) {
            $this->invalidMethodHandler();
        }
        $this->{strtolower($name)}[$this->formatRoute($route)] = $method;
    }

    /**
     * Removes trailing forward slashes from the right of the route.
     * @param string
     * @return string
     */
    private function formatRoute($route)
    {
        $result = rtrim($route, '/');
        if (strpos($route, '?') !== false) {
            $result = substr($result, 0, strpos($result, "?"));
        }
        if ($result === '') {
            return '/';
        }
        return $result;
    }

    private function invalidMethodHandler()
    {
        header("{$this->request->serverProtocol} 405 Method Not Allowed");
    }

    private function defaultRequestHandler()
    {
        header("{$this->request->serverProtocol} 404 Not Found");
    }

    /**
     * Resolves a route
     */

    function resolve()
    {
        $methodDictionary = $this->{strtolower($this->request->requestMethod)};
        $formatedRoute = $this->formatRoute($this->request->requestUri);
        $method = $methodDictionary[$formatedRoute];
        if (is_null($method)) {
            $this->defaultRequestHandler();
            return;
        }
        echo call_user_func_array($method, array($this->request));
    }

    function __destruct()
    {
        $this->resolve();
    }
}
