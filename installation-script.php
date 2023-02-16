<?php
if( !defined('PATH')){
    require_once "/var/www/html/components/youshallnotpass.php";
    exit;
}

$skip_installation = HelperClass::admin_user_exists(db_link());
if( $skip_installation ){
    require_once PATH . '/user-interface/main-front.php';
}
else{
    view('header');
?>
<body>
<body>
    <h1>ADMIN SETUP </h1>
    <div id="admin-login">
        <form id="installation-form" method="POST"> 
            <div class="input-group" id="ad_name">
                <label for="admin_name">Username:</label>
                <input required type="text" name="set_admin[username]" id="admin_name" autocomplete="off" placeholder="username"> 
            </div>
            <div class="input-group" id="ad_pass">
                <label for="admin_password">Password:</label>
                <input required type="password" name="set_admin[password]" id="admin_password" autocomplete="off" placeholder="password"> 
            </div>
            <div class="button" id="button">
                <button id="setup-button" type="submit">SET ADMIN</button>
            </div>
        </form>
        <div class="error_class"></div>
    </div>
</body>
</body>
<?php
}
view('footer');
?>