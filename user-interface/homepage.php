<?php
require_once '/var/www/html/libs/helpers.php';
session_start();
if( !is_admin_logged_in()){
    header('Location: http://localhost/index.nginx-debian.php' );
    exit;
}
view('header');
?>
<body>
    <?php view('navbar'); ?>
    <h1>ADMIN DASHBOARD</h1>
    <div class="books_data">
        <ul>
            <li>Issued:</li>
            <li>Returned:</li>
            <li>Recently added:</li>
        </ul>
    </div>
</body>
<?php view('footer'); ?>