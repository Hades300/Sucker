<?php


function heyaoDump($a)
{
    echo $a . "<br/>";
}


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
        call_user_func_array(array($meta["class"], $meta["method"]), $param);
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
    static function CORS()
    {
        header("Access-Control-Allow-Origin: *");
    }

    static function Failure($message, $code)
    {
        Response::CORS();
        header("Content-Type: json/application");
        if ($code != NULL) $res = array("Code" => 400, "Message" => $message);
        else $res = array("Code" => $code, "Message" => $message);
        echo json_encode($res);
    }

    static function Success($message, $data)
    {
        Response::CORS();
        header("Content-Type: json/application");
        $res = array("Code" => 200, "Message" => $message, "Data" => $data);
        echo json_encode($res);
    }

}




global $client;

function GetClient()
{
    if (isset($client)) {
        return $client;
    } else {
        $client = new PDO("mysql:host=localhost;dbname=sucker", "sucker", "sucker");
        return $client;
    }
}

function Exist($id)
{
    $cmd = GetClient()->prepare("select 1 from posts where id = ?");
    $cmd->execute(array($id));
    $res = $cmd->fetchAll();
    if (count($res) >= 1) {
        return true;
    } else {
        return false;
    }

}



class Sucker
{

    static public function ShowOne()
    {
        $records = GetClient()->query("select 1 from posts")->fetchAll();
        $count = count($records);
        $num = random_int(1, $count);
        $record = GetClient()->query("select * from posts limit $num,1")->fetch();
        $res = array();
        foreach ($record as $key => $val) {
            if (!is_numeric($key)) {
                $res[$key] = $val;
            }
        }
        Response::Success("获取成功", $res);

    }

    static public function ShowDozen()
    {
        $records = GetClient()->query("select 1 from posts")->fetchAll();
        $count = count($records);
        $num = random_int(1, $count - 10);
        $records = GetClient()->query("select * from posts limit $num,10")->fetchAll();
        $resCollection=array();
        foreach ($records as $record) {
            $res = array();
            foreach ($record as $key => $val) {
                if (!is_numeric($key)) {
                    $res[$key] = $val;
                }
            }
            array_push($resCollection,$res);
        }
        Response::Success("获取成功", $resCollection);
    }


    static public function Like()
    {
        $data = file_get_contents("php://input");
        $target = json_decode($data,true);
        if (Exist($target["id"])) {
            $cmd = GetClient()->prepare("update posts set likes= likes +1 where id = ?");
            $cmd->execute(array($target["id"]));
            Response::Success("收到啦❤️", NULL);
        } else {
            Response::Failure("日记不存在👀", 400);
        }

    }

   static  public function Dislike()
    {
        $data = file_get_contents("php://input");
        $target = json_decode($data,true);
        if (Exist($target["id"])) {
            $cmd = GetClient()->prepare("update posts set dislikes= dislikes +1 where id =?");
            $cmd->execute(array($target["id"]));
            Response::Success("那好吧️💔", NULL);
        } else {
            Response::Failure("日记不存在👀", 400);
        }
    }

    static public function Post()
    {
        $data = file_get_contents("php://input");
        $target = json_decode($data);
        if (!isset($target["content"])) {
            Response::Failure("内容是必须要有的嘛~", 400);
        } else {
            $cmd = GetClient()->query("Insert into posts (`content`,`date`,`tags`) values (?,?,?)");
            $cmd->execute(array($target["content"], date("Y-m-d H:i:s"), NULL));
            Response::Success("添加成功", 200);
        }
    }

    static public function DeleteOnPoorPost()
    {

    }

    static public function RandomBg(){
        $number = random_int(1,1000);
        $cmd = `sed -n $number,1p imgs`;
	    Header("Location: http://image.hades300.top/images/$cmd");
    }

}

$route = new HeyaoRoute();
$route->Dispatch();