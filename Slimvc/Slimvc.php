<?php
/**
 * User: imyxz
 * Date: 2017/5/24
 * Time: 14:18
 * Github: https://github.com/imyxz/
 */
class Slimvc{
    public $processor;
    public function __construct()
    {
        $this->processor=new SlimvcProcessor();
        SlimvcController::$DB = new SlimvcDB();
        global $Config;
        SlimvcController::$DB->connect($Config);
    }
    static public function ErrorNotice($info)
    {
        echo $info;
        exit();
    }
    static public function Filter($arr,$func)
    {
        if(!is_array($arr)) return $func($arr);
        $ret=array();
        foreach($arr as $key=>&$one)
        {
            if(is_array($one))
                $ret[$key]=Slimvc::Filter($one,$func);
            else
                $ret[$key]=$func($one);
        }
        return $ret;
    }
}
class SlimvcProcessor{
    public $controllerName;
    public $controllerFilePath;
    public $actionName;
    public $controller;
    public $cliArg;
    public function initProcess()
    {
        global $Config;
        $parameter=explode('/',trim($_SERVER['REQUEST_URI'],' /'));
        if(count($parameter)<2)
        {
            $this->controllerName="indexs";
            $this->actionName='IndexAction';
        }
        else
        {
            $this->controllerName=$parameter[0];
            $this->actionName=$parameter[1];
            for($i=3;$i<count($parameter);$i+=2)//���GET����
            {
                $_GET[$parameter[$i-1]]=$parameter[$i];
            }
        }
        $this->controllerFilePath=_Controller . $this->controllerName . '.php';
        if(dirname($this->controllerFilePath) . _DS_ !=_Controller)
            Slimvc::ErrorNotice("Controller Not Exist!");//��ֹinclude����include���ļ�
        if($Config['Session'])
            session_start();
    }
    public function initCliProcess()
    {
        global $Config;
        global $argv;
        $cliArg=array();
        if(count($argv)<3)
        {
            $this->controllerName="indexs";
            $this->actionName='IndexAction';
        }
        else
        {
            $this->controllerName=$argv[1];
            $this->actionName=$argv[2];
            for($i=4;$i<count($argv);$i+=2)//���cli����
            {
                $cliArg[$argv[$i-1]]=$argv[$i];
            }
        }
        SlimvcControllerCli::$cliArg=$cliArg;
        $this->controllerFilePath=_Controller . $this->controllerName . '.php';
        if(dirname($this->controllerFilePath) . _DS_ !=_Controller)
            Slimvc::ErrorNotice("Controller Not Exist!");//��ֹinclude����include���ļ�
    }
    public function startController()
    {
        if(!is_file($this->controllerFilePath))
            Slimvc::ErrorNotice("Controller Not Exist!");
        include_once($this->controllerFilePath);
        if(!class_exists($this->controllerName))
            Slimvc::ErrorNotice("Controller Class not Exist!");
        $allMethods=get_class_methods($this->controllerName);
        if(!in_array($this->actionName,$allMethods))
            Slimvc::ErrorNotice("Controller action not Exist!");
        $this->controller=new $this->controllerName;
        $this->controller->{$this->actionName}();
    }
}
class SlimvcControllerBasic
{
    public static $DB;
    protected static $models = array();
    protected static $helpers=array();
    public function __construct()
    {


    }


    public function model($filename, $className = NULL)
    {
        $target = _Model . _DS_ . $filename . '.php';
        if ($className == NULL)
            $className = $filename;
        if (!is_file($target))
            Slimvc::ErrorNotice("Model File $filename not Exist!");
        include_once($target);
        if (!class_exists($className))
            Slimvc::ErrorNotice("Model Class $className not Exist!");
        if (empty(self::$models[$className])) {
            self::$models[$className] = new $className;
            self::$models[$className]->Mysqli = self::$DB->mysqli;
        }
        return self::$models[$className];
    }
    public function helper($filename,$className=NULL)
    {
        $target = _Helper . _DS_ . $filename . '.php';
        if ($className == NULL)
            $className = $filename;
        if (!is_file($target))
            Slimvc::ErrorNotice("Helper File $filename not Exist!");
        include_once($target);
        if (!class_exists($className))
            Slimvc::ErrorNotice("Helper Class $className not Exist!");
        if (empty(self::$helpers[$className])) {
            self::$helpers[$className] = new $className;
            self::$helpers[$className]->Mysqli = self::$DB->mysqli;
        }
        return self::$helpers[$className];
    }
    public function newClass($filename,$className=NULL)
    {
        $target = _Class . _DS_ . $filename . '.php';
        if ($className == NULL)
            $className = $filename;
        if (!is_file($target))
            Slimvc::ErrorNotice("Class File $filename not Exist!");
        include_once($target);
        if (!class_exists($className))
            Slimvc::ErrorNotice("Class Class $className not Exist!");
        return new $className;
    }

}
class SlimvcController extends SlimvcControllerBasic
{
    protected $view_var=array();
    public function __set($name, $value)
    {
        $this->view_var[$name]=$value;
    }
    public function view($filename)
    {
        global $Config;
        $viewer= new SlimvcViewer();
        if($Config['XSS'])
            $viewer->vars=Slimvc::Filter($this->view_var,"htmlspecialchars");
        else
            $viewer->vars=$this->view_var;
        $viewer->view($filename);

    }
    public function outputJson($arr)
    {
        header("Content-type: application/json");
        echo json_encode($arr);
    }
    public function getRequestJson()
    {
        return json_decode(file_get_contents("php://input"),true);
    }

}
class SlimvcControllerCli extends SlimvcControllerBasic
{
    public static $cliArg;

}
class SlimvcHelper extends SlimvcControllerBasic{

}
class SlimvcModel{
    public $Mysqli;
    public $InsertId;
    public $Affected;
    public $ResultSum;
    public $QueryStatus;
    public $LastError;
    public $DebugForSQL="";
    public function query($sql)
    {
        global $Config;

        try{
            if(!$this->Mysqli)
                throw new Exception("Connection Faild!");
            $result=mysqli_query($this->Mysqli,$sql);
            $this->InsertId=mysqli_insert_id($this->Mysqli);
            $this->Affected=mysqli_affected_rows($this->Mysqli);
            $this->LastError=mysqli_error($this->Mysqli);
            if($Config['DebugSql'])
                $this->DebugForSQL = $this->DebugForSQL .  "<!-- SQL:$sql ERROR: " . $this->LastError . "\n";
            if($this->LastError)
                throw new Exception("SQL QUERY ERROR:" .  $this->LastError ." #SQL: $sql");
            if(!$result)
                return false;
            else
                return new SlimvcModelResult($result);

        }
        catch(Exception $e){
            $this->_log($e->getMessage(),$e->getFile(),$e->getLine());
            return false;
        }

    }
    public function queryStmt($prepare,$types,...$values)
    {
        global $Config;

        try{
            if(!$this->Mysqli)
                throw new Exception("Connection Faild!");
            if(!$stmt=mysqli_prepare($this->Mysqli,$prepare))
                throw new Exception('STMT SQL PREPARE ERROR:' .$this->LastError . '#prepare: ' . $prepare);
            array_unshift($values,$types);
            if(!call_user_func_array(array($stmt,"bind_param"),$this->refArr($values)))
                throw new Exception('STMT SQL bind ERROR:' .$stmt->error . '#para: ' . var_export($values,true));
            if(!$stmt->execute())
                throw new Exception('STMT SQL EXECUTE ERROR:' . $stmt->error ." #preapare: $prepare #para" . var_export($values,true));
            $this->InsertId=$stmt->insert_id;
            $this->Affected=$stmt->affected_rows;
            if($Config['DebugSql'])
                $this->DebugForSQL = $this->DebugForSQL .  "<!-- SQL:$prepare\n";
            if($result=$stmt->get_result())
                return new SlimvcModelResult($result);
            else
                return true;

        }
        catch(Exception $e){
            $this->_log($e->getMessage(),$e->getFile(),$e->getLine());
            return false;
        }

    }
    protected function _log($info,$filename,$line)
    {
        echo "$filename : $line : $info \n";
    }
    private function refArr($arr)
    {
        $refs = array();
        foreach($arr as $key => $value)
            $refs[$key] = &$arr[$key];
        return $refs;
    }
}
class SlimvcModelResult{
    public $result;
    public function SlimvcModelResult(mysqli_result &$a)
    {
        $this->result=$a;
    }
    public function row()
    {
        return $this->result->fetch_assoc();
    }
    public function all()
    {
        $return = array();
        while ($row = $this->result->fetch_assoc())
            $return[] = $row;
        return $return;
    }
    public function sum()
    {
        return $this->result->num_rows;
    }
}
class SlimvcViewer{
    public $vars=array();
    function view($filename)
    {
        $target = _View . _DS_ . $filename . '.php';
        if (!is_file($target))
            Slimvc::ErrorNotice("View File $filename not Exist!");
        @extract($this->vars);
        include($target);
    }
}
class SlimvcDB{
    public $mysqli;
    public function connect($Config)
    {
        $this->mysqli=mysqli_connect($Config['Host'], $Config['User'], $Config['Password'],$Config['DBname']);
        if(!$this->mysqli)
            Slimvc::ErrorNotice("Database Connection Fail!");
        $CharSet = str_replace('-', '', $Config['CharSet']);
        mysqli_query($this->mysqli, "SET NAMES '$CharSet'");
        return $this->mysqli;
    }
}