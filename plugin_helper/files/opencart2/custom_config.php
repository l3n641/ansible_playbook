<?php
define('SHARE_IMAGES', "catalog/share_images/");
define('BATCH_DOWNLOAD_ERROR_LOG_PATH', "/datas/logs/batch_download_error.log");
const CUSTOM_CONTROLLER_PERMISSION=['api/product','api/product/add','api/setting','api/setting/save','api/order','api/order/processing','api/order/status'];
const CUSTOM_IGNORE_ACTION=['api/product/add','api/setting/save','api/order/processing','api/order/status'];
define('SECRET_KEY', "{{ secret_key }}");
