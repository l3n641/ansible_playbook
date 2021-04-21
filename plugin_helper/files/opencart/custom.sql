/*
 Navicat Premium Data Transfer

 Source Server         : myaql8
 Source Server Type    : MySQL
 Source Server Version : 80013
 Source Host           : localhost:3306

 Target Server Type    : MySQL
 Target Server Version : 80013
 File Encoding         : 65001

 Date: 21/04/2021 10:13:34
*/

SET NAMES utf8mb4;

-- ----------------------------
-- Table structure for orders
-- ----------------------------
CREATE TABLE IF NOT EXISTS `orders`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `create_time` datetime NULL DEFAULT CURRENT_TIMESTAMP,
  `modified_time` datetime NULL DEFAULT CURRENT_TIMESTAMP,
  `order_sn` varchar(64) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `order_id` int(11) NOT NULL,
  `invoice_no` int(11) NOT NULL DEFAULT 0,
  `invoice_prefix` varchar(26) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `store_id` int(11) NOT NULL DEFAULT 0,
  `store_name` varchar(64) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `store_url` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `customer_id` int(11) NOT NULL DEFAULT 0,
  `customer_group_id` int(11) NOT NULL DEFAULT 0,
  `firstname` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `lastname` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `email` varchar(96) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `telephone` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `fax` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `custom_field` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `payment_firstname` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `payment_lastname` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `payment_company` varchar(60) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `payment_address_1` varchar(128) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `payment_address_2` varchar(128) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `payment_city` varchar(128) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `payment_postcode` varchar(10) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `payment_country` varchar(128) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `payment_country_id` int(11) NOT NULL,
  `payment_zone` varchar(128) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `payment_zone_id` int(11) NOT NULL,
  `payment_address_format` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `payment_custom_field` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `payment_method` varchar(128) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `payment_code` varchar(128) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `shipping_firstname` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `shipping_lastname` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `shipping_company` varchar(40) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `shipping_address_1` varchar(128) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `shipping_address_2` varchar(128) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `shipping_city` varchar(128) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `shipping_postcode` varchar(10) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `shipping_country` varchar(128) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `shipping_country_id` int(11) NOT NULL,
  `shipping_zone` varchar(128) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `shipping_zone_id` int(11) NOT NULL,
  `shipping_address_format` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `shipping_custom_field` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `shipping_method` varchar(128) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `shipping_code` varchar(128) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `comment` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `total` decimal(15, 4) NOT NULL DEFAULT 0.0000,
  `order_status_id` int(11) NOT NULL DEFAULT 0,
  `affiliate_id` int(11) NOT NULL,
  `commission` decimal(15, 4) NOT NULL,
  `marketing_id` int(11) NOT NULL,
  `tracking` varchar(64) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `language_id` int(11) NOT NULL,
  `currency_id` int(11) NOT NULL,
  `currency_code` varchar(3) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `currency_value` decimal(15, 8) NOT NULL DEFAULT 1.00000000,
  `ip` varchar(40) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `forwarded_ip` varchar(40) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `user_agent` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `accept_language` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `date_added` datetime NOT NULL,
  `date_modified` datetime NOT NULL,
   PRIMARY KEY (`id`)
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_bin ROW_FORMAT = Dynamic;


CREATE TABLE IF NOT EXISTS `order_products`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `create_time` datetime NULL DEFAULT CURRENT_TIMESTAMP,
  `modified_time` datetime NULL DEFAULT CURRENT_TIMESTAMP,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `model` varchar(64) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `quantity` int(4) NOT NULL,
  `color` varchar(64) ,
  `size` varchar(64) ,
  `price` decimal(15, 4) NOT NULL DEFAULT 0.0000,
  `total` decimal(15, 4) NOT NULL DEFAULT 0.0000,
  `tax` decimal(15, 4) NOT NULL DEFAULT 0.0000,
  `reward` int(8) NOT NULL,
   PRIMARY KEY (`id`)
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_bin ROW_FORMAT = Dynamic;
