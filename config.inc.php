<?php

/*
 +-----------------------------------------------------------------------+
 | Local configuration for the Roundcube Webmail installation.           |
 |                                                                       |
 | This is a sample configuration file only containing the minimum       |
 | setup required for a functional installation. Copy more options       |
 | from defaults.inc.php to this file to override the defaults.          |
 |                                                                       |
 | This file is part of the Roundcube Webmail client                     |
 | Copyright (C) 2005-2013, The Roundcube Dev Team                       |
 |                                                                       |
 | Licensed under the GNU General Public License version 3 or            |
 | any later version with exceptions for skins & plugins.                |
 | See the README file for a full license statement.                     |
 +-----------------------------------------------------------------------+
*/

$RC_VARS = array('RC_DB_USER', 'RC_DB_PASS', 'RC_DB_HOST', 'RC_DB_NAME', 'RC_DES_KEY', 'RC_IMAP_SERVER_NAME', 'RC_SMTP_SERVER_NAME');
$RC_ERROR = false;
foreach ($RC_VARS AS $RC_VAR) {
	if (!isset($_ENV[$RC_VAR])) {
		$RC_ERROR = true;
		break;
	}
}
if ($RC_ERROR) {
	echo '<h1>Configuration Error</h1>You must set environment these variables:<ul>';
	foreach ($RC_VARS AS $RC_VAR) {
		echo '<li>'.$RC_VAR.': '.(isset($_ENV[$RC_VAR]) ? '<span style="color: green; font-weight: bold;">configured</span>' : '<span style="color: red; font-weight: bold;">missing</span>').'</li>';
	}
	echo '</ul>';
	exit(1);
}

if(getenv('RC_ENABLE_INSTALLER') &&
    ($root_pass = getenv("DB_ROOT_PASS"))
)
{
        $db = getenv("RC_DB_NAME");

        $conn = new mysqli(getenv("RC_DB_HOST"), "root", $root_pass);

        if ($conn->connect_error) {
            print("Connection failed: " . $conn->connect_error);
            exit(1);
        }

        $res = $conn->query("CREATE DATABASE IF NOT EXISTS " . $db)
            && $conn->query("GRANT ALL PRIVILEGES ON `${db}`.* TO '" . getenv("RC_DB_USER") ."'@'%' WITH GRANT OPTION")
            && $conn->query("FLUSH PRIVILEGES");

        $conn->close();

        if (!$res) {
            print("DB error: " . $conn->error);
            exit(1);
        }
}


$config = array();

// Database connection string (DSN) for read+write operations
// Format (compatible with PEAR MDB2): db_provider://user:password@host/database
// Currently supported db_providers: mysql, pgsql, sqlite, mssql, sqlsrv, oracle
// For examples see http://pear.php.net/manual/en/package.database.mdb2.intro-dsn.php
// NOTE: for SQLite use absolute path (Linux): 'sqlite:////full/path/to/sqlite.db?mode=0646'
//       or (Windows): 'sqlite:///C:/full/path/to/sqlite.db'
$config['db_dsnw'] = 'mysql://'.$_ENV['RC_DB_USER'].':'.$_ENV['RC_DB_PASS'].'@'.$_ENV['RC_DB_HOST'].'/'.$_ENV['RC_DB_NAME'];

// The IMAP host chosen to perform the log-in.
// Leave blank to show a textbox at login, give a list of hosts
// to display a pulldown menu or set one host as string.
// To use SSL/TLS connection, enter hostname with prefix ssl:// or tls://
// Supported replacement variables:
// %n - hostname ($_SERVER['SERVER_NAME'])
// %t - hostname without the first part
// %d - domain (http hostname $_SERVER['HTTP_HOST'] without the first part)
// %s - domain name after the '@' from e-mail address provided at login screen
// For example %n = mail.domain.tld, %t = domain.tld
$config['imap_host'] = 'tls://'.$_ENV['RC_IMAP_SERVER_NAME'];
$config['imap_conn_options'] = array(
        'ssl' => array('verify_peer' => false, 'verify_peer_name' => false),
        'tls' => array('verify_peer' => false, 'verify_peer_name' => false),
);
// SMTP server host (for sending mails).
// Enter hostname with prefix tls:// to use STARTTLS, or use
// prefix ssl:// to use the deprecated SSL over SMTP (aka SMTPS)
// Supported replacement variables:
// %h - user's IMAP hostname
// %n - hostname ($_SERVER['SERVER_NAME'])
// %t - hostname without the first part
// %d - domain (http hostname $_SERVER['HTTP_HOST'] without the first part)
// %z - IMAP domain (IMAP hostname without the first part)
// For example %n = mail.domain.tld, %t = domain.tld
$config['smtp_host'] = 'tls://'.$_ENV['RC_SMTP_SERVER_NAME'];
$config['smtp_conn_options'] = array(
        'ssl' => array('verify_peer' => false, 'verify_peer_name' => false),
        'tls' => array('verify_peer' => false, 'verify_peer_name' => false),
);

// SMTP username (if required) if you use %u as the username Roundcube
// will use the current username for login
$config['smtp_user'] = '%u';

// SMTP password (if required) if you use %p as the password Roundcube
// will use the current user's password for login
$config['smtp_pass'] = '%p';

// provide an URL where a user can get support for this Roundcube installation
// PLEASE DO NOT LINK TO THE ROUNDCUBE.NET WEBSITE HERE!
$config['support_url'] = '';

// Name your service. This is displayed on the login screen and in the window title
$config['product_name'] = 'Roundcube Webmail';

// this key is used to encrypt the users imap password which is stored
// in the session record (and the client cookie if remember password is enabled).
// please provide a string of exactly 24 chars.
// YOUR KEY MUST BE DIFFERENT THAN THE SAMPLE VALUE FOR SECURITY REASONS
$config['des_key'] = $_ENV['RC_DES_KEY'];

// List of active plugins (in plugins/ directory)
$config['plugins'] = array(
    'archive',
    'zipdownload',
);

// skin name: folder from skins/
$config['skin'] = 'elastic';

$config['enable_installer'] = boolval(getenv('RC_ENABLE_INSTALLER'));

if ($_ENV['RC_DEFAULT_DOMAIN']) {
	$config['username_domain'] = $_ENV['RC_DEFAULT_DOMAIN'];
}

