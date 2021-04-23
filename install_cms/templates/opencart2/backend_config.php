<?php
// HTTP
define('HTTP_SERVER', 'http://{{ domain }}/{{ admin_dir }}/');
define('HTTP_CATALOG', 'http://{{ domain }}/');

// HTTPS
define('HTTPS_SERVER', 'https://{{ domain }}/{{ admin_dir }}/');
define('HTTPS_CATALOG', 'https://{{ domain }}/');

// DIR
define('DIR_APPLICATION', '/datas/www/{{ domain }}/{{ admin_dir }}/');
define('DIR_SYSTEM', '/datas/www/{{ domain }}/system/');
define('DIR_IMAGE', '/datas/www/{{ domain }}/image/');
define('DIR_LANGUAGE', '/datas/www/{{ domain }}/{{ admin_dir }}/language/');
define('DIR_TEMPLATE', '/datas/www/{{ domain }}/{{ admin_dir }}/view/template/');
define('DIR_CONFIG', '/datas/www/{{ domain }}/system/config/');
define('DIR_CACHE', '/datas/www/{{ domain }}/system/storage/cache/');
define('DIR_DOWNLOAD', '/datas/www/{{ domain }}/system/storage/download/');
define('DIR_LOGS', '/datas/www/{{ domain }}/system/storage/logs/');
define('DIR_MODIFICATION', '/datas/www/{{ domain }}/system/storage/modification/');
define('DIR_UPLOAD', '/datas/www/{{ domain }}/system/storage/upload/');
define('DIR_CATALOG', '/datas/www/{{ domain }}/catalog/');

// DB
define('DB_DRIVER',  "{{ db_driver }}");
define('DB_HOSTNAME', '127.0.0.1');
define('DB_USERNAME',  "{{ database_user }}");
define('DB_PASSWORD', "{{ database_password }}");
define('DB_DATABASE', "{{ database_name }}");
define('DB_PORT', "{{ database_port }}");
define('DB_PREFIX', "{{ opencart_db_prefix }}");
