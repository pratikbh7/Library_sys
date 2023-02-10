<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>LIBRARY SYSTEM</title>
        <meta name="description" content="library_sys">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="assets/css/main.css">
        <script src="assets/js/jquery.js"></script>
        <script src="assets/js/uifunc.js"></script>
    </head>
    <body>
        <h1>LIBRARY SYSTEM v<?php echo VERSION; ?> </h1>
        <div id="admin-login">
            <form id="input-form" method="POST"> 
                <div class="input-group" id="username">
                    <label for="username">Username:</label>
                    <input required type="text" name="login_input[username]" id="username" autocomplete="off" placeholder="username" value=""> 
                </div>
                <div class="input-group" id="password">
                    <label for="password">Password:</label>
                    <input required type="password" name="login_input[password]" id="password" autocomplete="off" placeholder="password" value=""> 
                </div>
                <div class="button" id="button">
                    <button id="login-button" type="submit">LOGIN</button>
                </div>
                <div id="new-admin">
                    <p><a href="javascript:void(0)" id="add-admin" onclick="add_admin_form(event);">ADD NEW ADMIN</a></p>
                </div>
            </form>
            <form id="add-admin-form" method="POST"> 
                <div class="input-group" id="username">
                    <label for="new_username">New Username:</label>
                    <input required type="text" name="register_input[new_username]" id="new_username" autocomplete="off" placeholder="new username" value=""> 
                </div>
                <div class="input-group" id="na_password">
                    <label for="na_password">Password:</label>
                    <input required type="password" name="register_input[na_password]" id="na_password" autocomplete="off" placeholder="password" value=""> 
                </div>
                <div class="button" id="na_button">
                    <button id="add-admin" type="submit" >ADD ADMIN</button>
                </div>
                <div id="login">
                    <p><a href="javascript:void(0)" id="login-admin" onclick="add_admin_form(event);">LOGIN</a></p>
                </div>
            </form>
            <div class="error_class"></div>
            <script>
                const admin_form_offset = document.getElementById('add-admin-form');
                admin_form_offset.style.top = - (admin_form_offset.offsetHeight) + 'px';
            </script>
        </div>
    </body>
</html>