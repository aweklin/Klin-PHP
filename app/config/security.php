<?php

// security configurations
define('SECURITY_CURRENT_LOGGED_IN_USER_ID', str_replace(' ', '', $appDevelopmentDirectoryName) . 'S3ssi0nN@me');
define('SECURITY_COOKIE_REMEMBER_ME_NAME', str_replace(' ', '', $appDevelopmentDirectoryName) . 'C0oki3N@me');
define('SECURITY_COOKIE_EXPIRY', 2592000);   // expires after 30 days
define('SECURITY_FORM_TOKEN', 'form_token');