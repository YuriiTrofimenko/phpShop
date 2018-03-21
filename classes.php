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

class Item {
    
    public $id
            , $itemname
            , $catid
            , $pricein
            , $pricesale
            , $info
            , $rate
            , $imagepath
            , $action;

    function __construct($itemname, $catid, $pricein, $pricesale, $info, $imagepath, $rate = 0, $action = 0, $id = 0) {
        
        $this->id = $id;
        $this->itemname = $itemname;
        $this->catid = $catid;
        $this->pricein = $pricein;
        $this->pricesale = $pricesale;
        $this->info = $info;
        $this->rate = $rate;
        $this->imagepath = $imagepath;
        $this->action = $action;
    }

    function intoDb() : bool
    {
        try {
            $pdo = Tools::connect();
            $ps = $pdo->prepare("INSERT INTO Items (itemname, catid, pricein, pricesale, info, rate, imagepath, action)
                                VALUES (:itemname, :catid, :pricein, :pricesale, :info, :rate, :imagepath, :action)");
            $ar = (array) $this;
            array_shift($ar);
            $ps->execute($ar);
            return true;
        } catch (PDOException $e) {
            return false;
            var_dump($e->getMessage());
        }
    }
    
    static function fromDb(int $id) : Item
    {
       $customer = null;
        try {
            $pdo = Tools::connect();
            $ps = $pdo->prepare("SELECT * FROM Items WHERE id = ?");
            $res = $ps->execute([$id]);
            $row = $ps->fetch();
            $customer =
                    new Item($row['itemname']
                            , $row['catid']
                            , $row['pricein']
                            , $row['pricesale']
                            , $row['info']
                            , $row['imagepath']
                            , $row['rate']
                            , $row['action']
                            , $row['id']
                         );
            return $customer;
        } catch (PDOException $e) {
            echo $e->getMessage();
            return false;
        }
    }
    
    static function GetItems(int $catid = 0) {
        $ps = null;
        $items = null;
        try {
            $pdo = Tools::connect();
            if ($catid == 0) {
                $ps = $pdo->prepare('select * from items');
                $ps->execute();
            } else {
                $ps = $pdo->prepare('select * from items where catid = ?');
                $ps->execute([$catid]);
            }
            while ($row = $ps->fetch()) {
                $item = new Item($row['itemname'], $row['catid'], $row['pricein'], $row['pricesale'], $row['info'], $row['imagepath'], $row['rate'], $row['action'], $row['id']);
                $items[] = $item;
            }
            return $items;
        } catch (PDOException $e) {
            echo $e->getMessage();
            return false;
        }
    }
    //
    function Draw() {
        echo "<div class='col-sm-3 col-md-3 col-lg-3' style='height:350px;margin:2px;'>";
            echo "<div class='container'>";
                //itemInfo.php contains detailed info about product
                echo "<div class='row' style='margin-top:2px; background-color:#ffd2aa;'>";
                    echo "<a href='pages/itemInfo.php?name=" . $this->id . "'class='pull-left' style='margin-left:10px;' target='_blank'>";
                        echo $this->itemname;
                    echo "</a>";
                    echo "<span class='pull-right' style='marginright:10px;'>";
                        echo $this->rate . "&nbsp;rate";
                    echo "</span>";
                echo "</div>";
            
                echo "<div style='height:100px;margin-top:2px;' class='row'>";
                    echo "<img src='" . $this->imagepath . "' height='100px' />";
                    echo "<span class='pull-right' style='marginleft:10px;color:red;font-size:16pt;'>";
                        echo "$&nbsp;" . $this->pricesale;
                    echo "</span>";
                echo "</div>";
                echo "<div class='row' style='margintop:10px;'>";
                    echo "<p class='text-left col-xs-12' style='background-color:lightblue;overflow:auto;height:60px;'>";
                        echo $this->info;
                    echo "</p>";
                echo "</div>";
                //echo "<div class='row' style='margintop:2px;'>";
                //echo "</div>";
                echo "<div class='row' style='margintop:2px;'>";
                    //creating cookies for the cart
                    //will be explained later
                    $ruser = '';
                    if (!isset($_SESSION['reg']) || $_SESSION['reg'] == "") {
                        $ruser = "cart_" . $this->id;
                    } else {
                        $ruser = $_SESSION['reg'] . "_" . $this->id;
                    }
                    echo "<button class='btn btn-success col-xsoffset-1 col-xs-10' onclick=createCookie('".$ruser."','".$this->id . "')>Add To My Cart</button>";
                echo "</div>";
            echo "</div>";
        echo "</div>";
    }
}

/* Test */
//$item1 = new Item("car", 1, 100000, 120000, "lorem ipsum", "path");
//echo $item1->intoDb();

//$item1 = Item::fromDB(6);
//var_dump($item1);

/* Test */

//$items = Item::GetItems();
//$items = Item::GetItems(2);
//var_dump($items);
    
/* Test */
//$item1->Draw();
