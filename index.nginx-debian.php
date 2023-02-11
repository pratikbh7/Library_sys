<?php 
/** before usage
 * 1. create user 'librarian' with all privliges granted and a database called 'library'
 * 2. enable pdo_mysql driver in php.ini
 * 3. create configuration file consisting database user and password in /var/ under [creds]
 */
require_once '/var/www/html/libs/helpers.php';
require_once '/var/www/html/installation-script.php';
// require_once PATH . 'installation-script.php';
//  session,cookies, installation script
/**
 * personal tasks:
 * 1. __sleep and __wakeup to commit and commit and reestablish db data
 */
?>