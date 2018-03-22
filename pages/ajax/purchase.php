<?php
include_once('../../classes.php');
$pdo = Tools::connect();
foreach ($_COOKIE as $k => $v) {
    $pos = strpos($k, "_");
    if (substr($k, 0, $pos) == $ruser) {
//get the item id
        $id = substr($k, $pos + 1);
//create the item object by id
        $item = Item::fromDb($id);
//sale the item
        $item->Sale();
    }
}
?>

