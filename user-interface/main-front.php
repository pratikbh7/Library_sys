<?php 
if( !defined('PATH') ){
    require_once "/var/www/html/components/youshallnotpass.php";
    exit;
}
session_start();
if( is_admin_logged_in()){
    header('Location:/user-interface/homepage.php' );
}
else{
view('header'); 
if( is_get_request() && isset($_GET['status']) && ($_GET['status'] === 'success')){
    $welcome_notice = true;
    $welcome_text = "<div class='success_class'><p>Admin account setup successful. Please login. </p></div>";
    $welcome_text = htmlspecialchars($welcome_text);
}
?>
<body>
    <h1>LIBRARY SYSTEM v<?php echo VERSION; ?> </h1>
    <?php if(isset($welcome_notice) && $welcome_notice === true){
        echo $welocme_text;
    } ?>
    <div id="admin-login">
        <form id="input-form" method="POST"> 
            <div class="input-group" id="username">
                <label for="username">Username:</label>
                <input required type="text" name="login_input[username]" id="username" autocomplete="off" placeholder="username"> 
            </div>
            <div class="input-group" id="password">
                <label for="password">Password:</label>
                <input required type="password" name="login_input[password]" id="password" autocomplete="off" placeholder="password"> 
            </div>
            <div class="button" id="button">
                <button id="login-button" type="submit">LOGIN</button>
            </div>
        </form>
        <div class="error_class"></div>
    </div>
</body>
<?php
} 
view('footer'); 
?>