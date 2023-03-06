<?php

// urls
define('APP_BASE_URL', (IS_DEVELOPMENT ? '/' . $appDevelopmentDirectoryName. '/' : 'http' . (isset($_SERVER['HTTPS']) ? 's' : '') . '://' . "{$_SERVER['HTTP_HOST']}/"));
define('URL_PUBLIC', APP_BASE_URL . 'app/public/');
define('URL_PUBLIC_JS', URL_PUBLIC . 'js/');
define('URL_PUBLIC_CSS', URL_PUBLIC . 'css/');
define('URL_PUBLIC_FONTS', URL_PUBLIC . 'fonts/');
define('URL_PUBLIC_IMG', URL_PUBLIC . 'img/');