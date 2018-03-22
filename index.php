<?php
session_start();
include_once("classes.php");
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Shop</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="css/bootstrap.min.css">
        <link rel="stylesheet" href="css/custom.css">
    </head>
    <body>
        <script src="js/jquery-3.3.1.min.js"></script>
        <div class="container">
            <div class="row">
                <header class="col-sm-12 col-md-12 col-lg-12">
                     <?php //include_once("pages/login.php");?>
                </header>
            </div>
        </div>
        <div class="container">
            <div class="row">
                <nav class="col-sm-12 col-md-12 col-lg-12 navbar navbar-light bg-faded">
                    <?php include_once('pages/menu.php'); ?>
                </nav>
            </div>
        </div>
        <div class="container">
            <div class="row">
                <section class="col-sm-12 col-md-12 col-lg-12">
                    <?php
                    if (!isset($_GET['page'])) {
                        $page = 1;
                    } else {
                        
                        $page = $_GET['page'];
                    }
                        
                        //var_dump($page);
                        //die();
                        
                        switch ($page) {
                            case 1 : {
                                    echo '<div class="container">';
                                        include_once('pages/catalog.php');
                                    echo '</div>';
                                    break;
                                }
                            case 2 : {
                                    include_once('pages/cart.php');
                                    break;
                                }
                            case 3 : {
                                    include_once('pages/register.php');
                                    break;
                                }
                            case 4 : {
                                    include_once('pages/admin.php');
                                    break;
                                }
                            /*case 5 : {
                                    if (isset($_SESSION['radmin'])) {
                                        include_once('pages/private.php');
                                        break;
                                    }
                                }*/
                            default : {echo '<span>Error 404</span>'; }
                        }
                    
                    ?>
                </section>
            </div>
        </div>
        <div class="container">
            <div class="row">
                <footer class="col-sm-12 col-md-12 col-lg-12">
                    Step Academy &copy;
                </footer>
            </div>
        </div>
        <script src="js/jquery.validate.min.js"></script>
        <script src="js/bootstrap.min.js"></script>
        <script src="js/custom.js"></script>
    </body>
</html>
