<?php

class Tools {

    static function connect(
    $host = "localhost:3306"
    , $user = "root"
    , $pass = ""
    , $dbname = "shop"
    ) {

        $cs = 'mysql:host=' . $host . ';dbname=' . $dbname . ';charset=utf8;';
        $options = array(
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES UTF8'
        );
        try {
            $pdo = new PDO($cs, $user, $pass, $options);
            return $pdo;
        } catch (PDOException $e) {
            echo mb_convert_encoding($e->getMessage(), 'UTF-8', 'Windows-1251');
            return false;
        }
    }

    static function register($name, $pass, $imagepath) {
        $name = trim($name);
        $pass = trim($pass);
        $imagepath = trim($imagepath);
        if ($name == "" || $pass == "") {
            echo "<h3/><span style='color:red;'>Fill All Required Fields!</span><h3/>";
            return false;
        }
        if (strlen($name) < 3 ||
                strlen($name) > 30 ||
                strlen($pass) < 3 ||
                strlen($pass) > 30) {
            echo "<h3/><span style='color:red;'>Values Length Must Be Between 3 And 30!</span><h3/>";
            return false;
        }
        Tools::connect();
        $customer = new Customer($name, $pass, $imagepath);
        $err = $customer->intoDb();
        if ($err) {
            if ($err == 1062)
                echo "<h3/><span style='color:red;'>This Login Is Already Taken!</span><h3/>";
            else
                echo "<h3/><span style='color:red;'>Error code:" . $err . "!</span><h3/>";
            return false;
        }
        return true;
    }

}

/* Entities */

class Customer {

    protected $id; //user id
    protected $login;
    protected $pass;
    protected $roleid;
    protected $discount; //customer's personal discount
    protected $total; //total ammount of purchases
    protected $imagepath; //path to the image

    function __construct($login, $pass, $imagepath, $id = 0) {
        $this->login = $login;
        $this->pass = $pass;
        $this->imagepath = $imagepath;
        $this->id = $id;
        $this->total = 0;
        $this->discount = 0;
        //TODO get role id by role name
        $this->roleid = 2;
    }

    //object -> DB
    function intoDb() {
        try {
            $pdo = Tools::connect();
            $ps = $pdo->prepare("INSERT INTO Customers (login,pass,roleid,discount,total,imagepath)
                                VALUES (:login,:pass,:roleid,:discount,:total,:imagepath)");
            
            //$ar = (array) $this;

            $ar = get_object_vars($this);
            //var_dump($ar);
            array_shift($ar);
            
            //var_dump($ar);
            //var_dump($ps->queryString);
            $ps->execute($ar);
        } catch (PDOException $e) {
            $err = $e->getMessage();
            if (substr($err, 0, strrpos($err, ":")) == 'SQLSTATE[23000]:Integrity constraint violation') {

                return 1062;
            } else {

                return $e->getMessage();
            }
        }
    }

    //DB -> object
    static function fromDB(int $id) : Customer {

        $customer = null;
        try {
            $pdo = Tools::connect();
            $ps = $pdo->prepare("SELECT * FROM Customers WHERE id = ?");
            //$ps = $pdo->prepare("SELECT * FROM Customers");
            //var_dump($ps->queryString);
            //var_dump($id);
            $resultCode = $ps->execute([$id]);
            //$res = $ps->execute();
            //var_dump($resultCode);
            if ($resultCode) {
                
                $row = $ps->fetch();
                $customer =
                    new Customer(
                            $row['login']
                            , $row['pass']
                            , $row['imagepath']
                            , $row['id']
                        );
                return $customer;
            } else {
                $pdo->errorInfo();
                $pdo->errorCode();
                return new Customer("", "", "");
            }
        } catch (PDOException $e) {
            echo $e->getMessage();
            return false;
        }
    }
}

/* Test */
//$c1 = new Customer("login1", "password", "path");
//echo $c1->intoDb();

//$c2 = Customer::fromDB(1);
//var_dump($c2);
