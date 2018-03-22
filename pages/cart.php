<?php
//define the current user name
$ruser = '';
if (!isset($_SESSION['reg']) || $_SESSION['reg'] == "") {
    $ruser = "cart";
} else {
    $ruser = $_SESSION['reg'];
}
//total cost of the cart
$total = 0;
foreach ($_COOKIE as $k => $v) {
    $pos = strpos($k, "_");
    if (substr($k, 0, $pos) == $ruser) {
//get the item id
        $id = substr($k, $pos + 1);
//create the item object by id
        $item = Item::fromDb($id);
//increase the total cost
        $total += $item->pricesale;
//draw the item
        echo '<form action="index.php?page=2" method="post">';
        $item->DrawForCart();
        echo '</form>';
    }
}
echo '<hr/>';
echo '<form id="purchase" action="index.php?page=2" method="post">';
echo "<span style='margin-left:100px;color:blue;fontsize:16pt;background-color:#fffff;' class='' >Total cost is: </span><span style='marginleft:10px;color:red;font-size:16pt;background-color:#ffffff;' class='' >" . $total . "</span>";
echo "<button type='submit' class='btn btn-success' name='suborder' style='margin-left:150px;'>Purchase order</button>";
echo '</form>';

/*if (isset($_POST['cookies'])) {
    
    //var_dump($_POST['cookies']);
    $arr = (array) $_POST['cookies'];
    foreach ($arr as $k=>$value) {
        var_dump($k);
        var_dump($value);
    }
}*/

if (isset($_POST['c'])) {
    
    
    foreach ($_POST['c'] as $id) {

        //var_dump($id);
        $item = Item::fromDb($id);

        $item->Sale();
    }
}
?>
<script>
//creating cookie with javascript
function createCookie(uname,id)
{
    var date = new Date(new Date().getTime() + 60 * 1000 * 30);
    document.cookie = uname+"="+id+"; path=/;expires=" + date.toUTCString();
}
//deleting cookie with javascript
function eraseCookie(uname)
{
    var theCookies = document.cookie.split(';');
    for (var i = 1 ; i <= theCookies.length; i++)
    {
        if(theCookies[i-1].indexOf(uname) === 1)
        {
            var theCookie = theCookies[i-1].split('=');
            var date = new Date(new Date().getTime() - 60000);
            document.cookie = theCookie[0]+"="+"id"+"; path=/;expires=" + date.toUTCString();
        }
    }
}

var submited = false;

var getCookies = function(){
    //TODO cart only
  var pairs = document.cookie.split(";");
  var cookies = {};
  for (var i=0; i<pairs.length; i++){
    var pair = pairs[i].split("=");
    
    if (pair[0] != 'PHPSESSID') {
        cookies[(pair[0]+'').trim()] = unescape(pair[1]);
    }
  }
  return cookies;
}

$('form#purchase button[type=submit]').click(function (event){
    
    alert('process');
    
    //<?php
//        $ruser = "cart";
//        if (!isset($_SESSION['reg']) || $_SESSION['reg'] == "") {
//            $ruser = "cart";
//        } else {
//            $ruser = $_SESSION['reg'];
//        }
//    ?>
    
    if (!submited) {
        
        event.preventDefault();
        //eraseCookie(<?php $ruser ?>);
        
        var cookies = getCookies();
        
        //console.log(cookies);
        $cookieInputs = $([]);
        console.log($('form#purchase'));
        $.each(cookies, function(index, value) {
            console.log('from object: ' + value);
            /*$cookieInputs.add(
                $("<input>")
                    .attr("type", "hidden")
                    .attr("name", "c[]").val(value));*/
                    //if (index !== 0) {
                        $('form#purchase').append(
                                        $("<input>")
                                        .attr("type", "hidden")
                                        .attr("name", "c[]").val(value)
                                    );
                    //}
            
        });
        
        /*cookieInputs.forEach(function(item, i, cookieInputs) {
                 
            console.log(item);
        });*/

        /*$cookieInputs.each(function(item) {
                 
            console.log(item);
        });*/
        
        eraseCookie('cart');
        setTimeout(
                function() {
                    
                    submited = true;
                    
                    /*var input = $("<input>")
                        .attr("type", "hidden")
                        .attr("name", "cookies").val(cookies);*/
    
                    /*$cookieInputs.each(function(item) {
                        
                        $('form#purchase').append($(item));
                    });*/

                    $('form#purchase').submit();
                }
                , 250
        );
    }
    
    //creating AJAX object
    /*if (window.XMLHttpRequest)
        ao=new XMLHttpRequest();
    else
        ao=new ActiveXObject('Microsoft.XMLHTTP');
    //anonymous function for result processing
    ao.onreadystatechange=function()
    {
        if (ao.readyState === 4 && ao.status === 200)
        {
            document.getElementById('result').innerHTML = ao.responseText;
        }
    }
    //preparing post AJAX request
    ao.open('post','pages/ajax/lists.php',true);
    ao.setRequestHeader("Content-type","application/x-www-form-urlencoded");
    ao.send("cat="+cat);*/
});
</script>

