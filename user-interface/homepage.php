<?php
require_once '/var/www/html/libs/helpers.php';
session_start();
if( !is_admin_logged_in()){
    header('Location: http://localhost/index.nginx-debian.php' );
    exit;
}
if( is_get_request()){
    if( isset($_GET['getPage'])){
        $value = filter_var( $_GET['getPage'], FILTER_SANITIZE_STRING);
        $allowed = ["list_books", "list_issued", "add_form_get"];
        if( in_array($value, $allowed)){
            switch($value){
                
                case "list_books":
                    // $data = $homepage_ajax->list_books(0);
                    $get = "listtable";
                    break;
                
                case "list_issued":
                    // $data = $homepage_ajax->issued_books(0);
                    $get = "listtable";
                    break;

                case "add_form_get":
                    $get = "addbook";
                    break;
            
            }
        }
        else{
            $get = "dashboard";
        }
    }
    else{
        $get = "dashboard";
    }
}
view('header');
?>
<body>
    <?php view('navbar'); ?>
    <h1>ADMIN DASHBOARD</h1>
    <div class="books_data">
       <?php view($get); ?>
    </div>
</body>
<?php view('footer'); ?>