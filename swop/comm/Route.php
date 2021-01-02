<?php

namespace comm;

use Exception;

/**
 * Class Route
 * @package comm
 */

class Route
{
    static private $router;

    /** 加入get route表 */
    static public function get(...$args)
    {
        return self::addMethod(Method::GET, ...$args);
    }

    /** 加入post route表 */
    static public function post(...$args)
    {
        return self::addMethod(Method::POST, ...$args);
    }

    /** 加入put route表 */
    static public function put(...$args)
    {
        return self::addMethod(Method::PUT, ...$args);
    }

    /** 加入delete route表 */
    static public function delete(...$args)
    {
        return self::addMethod(Method::DELETE, ...$args);
    }

    /** 加入any route表 */
    static public function any(...$args)
    {
        return self::addMethod(Method::ANY, ...$args);
    }

    /** 加入route表 */
    static private function addMethod($method, ...$args)
    {
        if (!self::$router) {
            self::$router = new Router();
        }
        self::$router->setPath($args[0]);
        self::$router->map[$args[0]] = [
            "method" => $method,
            "callback" => $args[1]
        ];

        return self::$router;
    }

    static public function procRoute()
    {
        return self::$router->procRoute();
    }
}

class Router
{
    private $path = "";
    public $map = [];

    /**
     * regular
     * @param ...$args key, regular /OR/ [ key1 => reg1, key2 => reg2...]
     * @return $this
     */
    public function where(...$args)
    {
        $where = [];
        if (is_array($args[0])) {
            $where = $args[0];
        } else if (count($args) == 2) {
            $where = [$args[0] => $args[1]];
        }
        $this->map[$this->path]["where"] = $where;
        return $this;
    }

    /** 設定名稱，未有實際功能 */
    public function name($name)
    {
        $this->map[$this->path]["name"] = $name;
        return $this;
    }

    /** 尚未有功能 */
    public function middleware(...$keys)
    {
        foreach ($keys as $key) {
            $this->map[$this->path]["middleware"][] = $key;
        }
        return $this;
    }

    /** 設定全域變數 */
    public function __construct()
    {
        $this->requestUri = explode("?", str_replace(config("folder"), "/", $_SERVER["REQUEST_URI"]))[0];
        $this->base_hierarchy = explode("/", $this->requestUri);
    }

    public function procRoute()
    {
        if ($result = $this->checkedIsMatched()) {
            [$params, $match, $method, $args, $path] = $result;
            $context = $this->buildContext($params, $match, $method, $args);
            /** @var callback $func */
            $func = $this->procFunc($args["callback"]);
            $this->matched($func, $context, $path);
        } else {
            $this->notMatched();
        }
    }

    public function checkedIsMatched()
    {
        /** 開始mapping */
        foreach ($this->map as $path => $args) {
            /** 路徑長度檢測 */
            if (count($this->base_hierarchy) != count(explode("/", $path))) continue;

            $method = $args["method"];
            /** method檢測，any沒有檢查必要 */
            if (
                $method !== Method::ANY &&
                !($_SERVER["REQUEST_METHOD"] === Method::POST && $_POST["_method"] === strtoupper($method) || // delete or put
                    $_SERVER["REQUEST_METHOD"] === $method) // get or post
            ) {
                continue;
            }

            $pregvar = [];
            $params = [];
            $len = 0;
            $wheres = $args["where"];

            /** 找到所有的變數名稱 */
            $tmpPath = $path;
            preg_match_all("/\/{([a-zA-Z0-9]+)}/", $tmpPath, $match);

            if ($match) {
                $len = count($match[1]);
                for ($i = 0; $i < $len; $i++) {
                    $pregvar[$match[1][$i]] = "[^\/]+";
                }
            }

            $wheres = array_merge($pregvar, is_array($wheres) ? $wheres : []);
            /** 先將路徑全部加上跳脫字元，含頭尾 */
            $tmpPath = "/" . strtr($tmpPath, ["/" => "\\/",]) . "/";
            foreach ($wheres as $var => $where) {
                $tmpPath = strtr($tmpPath, ["{" . $var . "}" => "(" . $where . ")"]);
                $params[$var] = "";
            }
            /** 取出所有的變數 */
            preg_match($tmpPath, $this->requestUri, $match);

            if ($len != count($match) - 1) {
                continue;
            }
            return [$params, $match, $method, $args, $path];
        }
    }

    public function buildContext($params, $match, $method, $args)
    {
        array_shift($match);
        foreach ($params as $var => $val) {
            $val = array_shift($match);
            $params[$var] = $val;
        }
        /** 取method變數 */
        switch ($method) {
            case Method::GET:
                $context["get"] = $this->collectMethod(Method::GET);
                break;
            case Method::POST:
            case Method::PUT:
            case Method::DELETE:
                $context["post"] = $this->collectMethod(Method::POST);
                break;
            case Method::ANY:
                $context["get"] = $this->collectMethod(Method::GET);
                $context["post"] = $this->collectMethod(Method::POST);
                break;
        }
        /** 塞入共用變數 */
        $controller = config("default_controller");
        $action = config("default_action");
        if (is_string($args["callback"]) && strpos($args["callback"], "@") !== false) {
            list($controller, $action) = explode("@", $args["callback"]);
        }
        $context["submit_link"] = config("folder") . $controller . "/" . $action;
        $context["controller"] = $controller;
        $context["action"] = $action;
        $context["top_layout"] = "shared/top.php";
        $context["layout"] = $action;
        $context["bottom_layout"] = "shared/bottom.php";
        $context["params"] = $params;
        $context["session"] = $_SESSION[config("folder")];
        return $context;
    }

    /**
     * route had matched
     */
    public function matched($func, $context, $path)
    {
        $wrapFunc = function () use ($func, $context) {
            return $func($context);
        };
        $funcResult = $this->procMiddleware($wrapFunc, $path);
        echo $funcResult;
    }

    public function procMiddleware($wrapFunc, $path)
    {
        foreach ($this->map[$path]["middleware"] as $middleware) {
            $wrapFunc = function () use ($middleware, $wrapFunc) {
                return Middleware::$middleware($wrapFunc);
            };
        }
        return $wrapFunc();
    }

    /**
     * 走路徑
     */
    public function notMatched()
    {
        // 因為是/開頭，所以第一個元素是null，走路徑要先移掉
        array_shift($this->base_hierarchy);
        $filePath = config("controller_dir") . $this->base_hierarchy[0] . ".php";
        $store_pos = 2;

        if (file_exists($filePath)) // 驗證 controller 是否存在
        {
            $controller = $this->base_hierarchy[0];
        } else {
            $controller = config("default_controller");
            $filePath = config("controller_dir") . config("default_controller") . ".php";
            $store_pos--;
        }
        $controller_class = $controller . "_Controller";
        require_once $filePath;
        $swop = new $controller_class();

        if (isset($this->base_hierarchy[1]) && method_exists($swop, $this->base_hierarchy[1])) //驗證 controller 與 action 是否存在
        {
            $action = $this->base_hierarchy[1];
        } else {
            $swop->redirect("index/index");
            $action = config("default_action");
            $store_pos--;
        }

        $context["submit_link"] = config("folder") . $controller . "/" . $action;
        $context["controller"] = $controller;
        $context["action"] = $action;
        $context["top_layout"] = "shared/top.php";
        $context["layout"] = $action;
        $context["bottom_layout"] = "shared/bottom.php";
        $a_get = [];
        $len = count($this->base_hierarchy);
        for ($i = $store_pos; $i < $len; $i += 2) {
            if (isset($this->base_hierarchy[$i + 1]) && $this->base_hierarchy[$i]) {
                $a_get[$this->base_hierarchy[$i]] = $this->base_hierarchy[$i + 1];
            }
        }
        foreach ($_GET as $key => $val) {
            $a_get[$key] = $val;
        }
        $context["get"] = $a_get;
        $a_post = [];
        foreach ($_POST as $key => $val) {
            $a_post[$key] = $val;
        }
        $context["post"] = $a_post;
        $swop->getData($context);

        $swop->{$action}();
    }

    /**
     * proccess function
     * @param string /OR/ function
     * @return function
     */
    private function procFunc($arg)
    {
        if (is_callable($arg)) {
            return $arg;
        } else if (is_string($arg)) {
            $map = explode("@", $arg);

            $file_path = dirname(__DIR__) . "/" . config("controller") . $map[0] . ".php";

            if (file_exists($file_path)) {
                require_once($file_path);
                $swop = new $map[0]();
                if (count($map) == 2 && method_exists($swop, $map[1])) {
                    $action = $map[1];
                    return function ($req) use ($swop, $action) {
                        return $swop->{$action}($req);
                    };
                } else {
                    throw new Exception("didn't find the action: {$map[1]}");
                }
            }
            throw new Exception("didn't find the controller: {$file_path}");
        }
    }

    /**
     * get tmp path
     * @param string // route url
     */
    public function setPath($path)
    {
        $this->path = $path;
    }

    /**
     * 收集GET or POST 所有變數
     * @param string // Method::GET or Method::POST
     */
    private function collectMethod(string $method = Method::GET)
    {
        $collectrion = [];
        switch ($method) {
            case Method::GET:
                $collectrion = $_GET;
                break;
            case Method::POST:
                unset($_POST["_method"]);
                $collectrion = $_POST;
                break;
        }
        $res = [];
        foreach ($collectrion as $key => $val) {
            $res[$key] = $val;
        }
        return $res;
    }
}

class Method
{
    const  ANY = "ANY";
    const  GET = "GET";
    const  POST = "POST";
    const  PUT = "PUT";
    const  DELETE = "DELETE";
}
