<?php
if (isset($_GET['page'])) {
    $page = $_GET['page'];
} else {
    $page = 1;
}
?>
<ul class="nav nav-pills">
    <li role="presentation" class="nav-item"><a class="nav-link <?php echo ($page == 1) ? 'active' : '' ?>" href="/index.php?page=1">1</a></li>
    <li role="presentation" class="nav-item"><a class="nav-link <?php echo ($page == 2) ? 'active' : '' ?>" href="/index.php?page=2">2</a></li>
    <li role="presentation" class="nav-item"><a class="nav-link <?php echo ($page == 3) ? 'active' : '' ?>" href="/index.php?page=3">Registration</a></li>
<!--    <li role="presentation" class="nav-item"><a class="nav-link <?php echo ($page == 3) ? 'active' : '' ?>" href="/index.php?page=3">Registration</a></li>
    <li role="presentation" class="nav-item"><a class="nav-link <?php echo ($page == 4) ? 'active' : '' ?>" href="/index.php?page=4">Admin</a></li>-->
    <?php
//    if (isset($_SESSION['radmin'])) {
//        echo '<li role="presentation" class="nav-item"><a class="nav-link ' . (($page == 5) ? 'active' : '') . '" href="/index.php?page=5">Private</a></li>';
//    }
    ?>
</ul>
