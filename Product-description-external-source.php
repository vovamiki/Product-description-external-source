<?php
/* 
 * Plugin Name: Product description external source
 * Plugin URI: http://mikityuk.tk/plugins/Product_description_external_source
 * Description: Product description external source - это бесплатный вордпресс плагин, который добавляет товары к вашему интернет-магазину на Woocommerce.
 * Version: 0.1
 * Author: Mikityuk Vladimir
 * Author URI: http://mikityuk.tk/
*/

defined('ABSPATH') or die("No script kiddies please!");

// 1  Controller   start()  
include "include/Controller.php";


// 2   Вводим искомый товар и если модуль находит его то выводит ссылки на товар 
include "include/List_products_hotline.php";

// 3  Передаём в метод линк на страницу
// Модуль парсит страницу и все данные о товаре возвращает заполниный объект
include "include/Page_product_hotline.php";

// 4 Шаблон по которому выбираем данные о конкретном товаре
include "include/Tovar.php";

// 5 Добавить товар в Wordpress -> Woocomerce в БД
include "include/Add_Woocomerce.php";

Controller::start();
