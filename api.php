<?php

highlight_file(__FILE__);

function heyaoDump($a)
{
    echo $a . "<br/>";
}

heyaoDump($_SERVER["REQUEST_URI"]);

// want to wriite a dispatcher

class HeyaoRoute
{

    function GetMeta($route)
    {
        $data = $_REQUEST;
        $className = $data["c"];
        $methodName = $data["m"];
        if (class_exists($className) && in_array($methodName, get_class_methods($className))) {
            return array("class" => $className, "method" => $methodName);
        } else {
            throw new Exception("Class $className Or Method $methodName Not Exists");
        }
    }


    function Dispatch()
    {
        $path = $_SERVER["REQUEST_URI"];
        $meta = $this->GetMeta($path);
        $param = $this->ExtractParam($path);
        var_dump($meta["class"]);
        call_user_func_array(array($meta["class"],$meta["method"]),$param);
    }

    function ExtractParam($route)
    {
        $res = str_replace($route, "", $_SERVER["REQUEST_URI"]);
        $data = explode("/", $res);
        $data = array_filter($data, function ($item) {
            if ($item != "") {
                return true;
            } else {
                return false;
            }
        });
        return $data;
    }

}

class Response
{
    static function Failure($message, $code)
    {
        header("Content-Type", "json/application");
        if ($code != NULL) $res = array(["Code" => 400, "Message" => $message]);
        else $res = array("Code" => $code, "Message" => $message);
        echo json_encode($res);
    }

    static function Successs($message, $data)
    {
        header("Content-Type", "json/application");
        $res = array(["Code" => 200, "Message" => $message, "Data" => $data]);
        echo json_encode($res);
    }

}


// so the base path is /sucker
// TODO: And I Want To Set

global $client;

function GetClient()
{
    if (isset($client)){return $client;}
    else{
        $client= new PDO("mysql:host=localhost;dbname=sucker", "sucker", "sucker");
        return $client;
    }
}



class Sucker
{

   static public function Show()
    {
        $records = GetClient()->query("select 1 from posts")->fetchAll();
        $count = count($records);
        echo "We have $count records in db;";

    }

    public function Like()
    {

    }

    public function Dislike()
    {

    }

    public function Post()
    {

    }

    public function DeleteOnPoorPost()
    {

    }

}

$route = new HeyaoRoute();
$route->Dispatch();