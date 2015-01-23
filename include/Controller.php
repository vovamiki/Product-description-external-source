<?php
class Controller		        
{
    function start()
    {      
		// Добавляем хук наше пол для поиска товаров в редактор товара
		add_action( 'add_meta_boxes', 'Controller::add_menu_search_product' );
    }
		
    function add_menu_search_product()
    {
		add_meta_box(
                	'woocommerce-order-YOUR-UNIQUE-REF',
			__( 'Товары по вашему запросу:' ),
			'Controller::order_meta_box_YOURCONTENT',
			'product',
			'normal',
			'default'
                    );
    }
		
    function order_meta_box_YOURCONTENT()
    {

		if(isset($_GET['actionPluginExternalCatalogs']))
		{
            // CONTROLLER
            switch ($_GET['actionPluginExternalCatalogs'])
            {
                case 'full_page':
                	$link[0]=$_GET['url'];
                	$Page_product_hotline = new Page_product_hotline;
                        // Сюда вернётся ОБЪЕКТ уже заполненный товаром
                        $tovar_from_hotline = $Page_product_hotline -> full_page($link);
                        
					$Tovari_Woocomerce = new Tovari_Woocomerce;


					$Tovari_Woocomerce ->add_db_product($tovar_from_hotline);
                    break;
		
                case 'search':
	  
					$data = array(
						'post'=>$_GET['post'],
						'action'=>'edit',
						'actionPluginExternalCatalogs'=>'search',
						'query'=>'');

						$url_search=site_url().'/wp-admin/post.php?'.http_build_query($data);

						echo '<input id="actionPluginExternalCatalogsquery" type="text" value="'.( $_GET['query'] ).'"></input>';
						echo '<input type="button"   value="найти товар"   onclick="var urlS=\'' .$url_search. '\'+encodeURIComponent(document.getElementById(\'actionPluginExternalCatalogsquery\').value);location.href=urlS;">';  
						echo '<br>';

						// Запускаем класс Spisok_Tovarov_HotLine с методом search с аргументом пришедшем в $_GET (модель, название или тип товара)
						$new_Tovar = new Spisok_Tovarov_HotLine();
						$new_Tovar -> search($_GET["query"]);
                        break;
					
				default:
		
						$_GET['actionPluginExternalCatalogs'];
                        break;
            } 
        
		}
		else
		{
            $data = array(
                        'post'=>$_GET['post'],
			'action'=>'edit',
			'actionPluginExternalCatalogs'=>'search',
			'query'=>''
                        );

            $url_search=site_url().'/wp-admin/post.php?'.http_build_query($data);
            echo '<input id="actionPluginExternalCatalogsquery" type="text" value="'.get_the_title( $_GET['post'] ).'"></input>';
            echo '<input type="button"   value="найти товар"   onclick="var urlS=\'' .$url_search. '\'+encodeURIComponent(document.getElementById(\'actionPluginExternalCatalogsquery\').value);location.href=urlS;">';  
            echo '<br>';

		}		
    }
}