<?php 
/** before usage
 * 1. create user 'librarian' with all privliges granted and a database called 'library'
 * 2. enable pdo_mysql driver in php.ini
 * 3. create configuration file consisting database user and password in /var/ under [creds]
 */
defined('VERSION') OR define('VERSION', '1.0.0');
require_once '/var/www/html/user-interface/main-front.php';

?>