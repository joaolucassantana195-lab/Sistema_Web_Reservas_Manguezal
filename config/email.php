<?php
require_once __DIR__ . '/../vendor/autoload.php';

define('EMAIL_HOST', $_ENV['SMTP_HOST']);
define('EMAIL_USER', $_ENV['SMTP_USER']);
define('EMAIL_PASS', $_ENV['SMTP_PASS']);
define('EMAIL_PORT', $_ENV['SMTP_PORT']);
define('EMAIL_NAME', $_ENV['SMTP_NAME']);
?>