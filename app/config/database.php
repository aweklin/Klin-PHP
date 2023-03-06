<?php

// database configuration
define('DATABASE_HOST', (IS_DEVELOPMENT ? '127.0.0.1' : ''));
define('DATABASE_NAME', (IS_DEVELOPMENT ? '' : ''));
define('DATABASE_USER', (IS_DEVELOPMENT ? '' : ''));
define('DATABASE_PASSWORD', (IS_DEVELOPMENT ? '' : ''));
define('DATABASE_TABLE_NAMES_PLURALIZED', true);