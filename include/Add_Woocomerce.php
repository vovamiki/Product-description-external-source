<?php
class  Add_Woocomerce
{
	// Сюда должны передаватся объект заполненный данными о товаре						
	function add_db_product($object_data_item)						
	{
			$post_title = $object_data_item->title;

			$tags = 	      $object_data_item->product_attributes["key1"]["value"];
			$categories[0] =  $object_data_item->product_attributes["key0"]["value"];
			$categories[1] =  $object_data_item->product_attributes["key1"]["value"];
			$categories[2] =  $object_data_item->product_attributes["key1"]["value"].'-'.$object_data_item->product_attributes["key0"]["value"];
			
			// Записываем время  когда был взяты с HotLine
			date_default_timezone_set("Europe/Kiev");
			$data_load_hotline = date("Y-m-d H:i:s");




			$my_post = array(
                    'ID' 		   => $_GET['post'],
					'post_author'  => $user_ID,
					'post_title'   => $post_title,
					'post_content' => $object_data_item->post_content,   // Полное описание
					'post_excerpt' => $object_data_item->post_excerpt,   // Краткое описани
					'post_name'    => $post_title,   
					'post_status'  => 'publish',
					'post_type'    => 'product',	
				);
            $post_id =    wp_update_post($my_post);

			wp_set_object_terms($post_id, $categories, 'product_cat');
			wp_set_post_terms($post_id, $tags, 'product_tag');

	

	
			//  Записываем МЕТАДАННЫЕ

			update_post_meta( $post_id, '_visibility', 'visible' );
			update_post_meta( $post_id, '_stock_status', 'instock');

			update_post_meta( $post_id, '_downloadable', 'no'); //Загружаемый
			update_post_meta( $post_id, '_virtual', 'no');      //Виртуальный продукт
			// update_post_meta( $post_id, '_regular_price', "1" );
			// update_post_meta( $post_id, '_sale_price', "" );
			// update_post_meta( $post_id, '_purchase_note', "" );
			// update_post_meta( $post_id, '_featured', "no" );
			// update_post_meta( $post_id, '_weight', "3" ); // Вес кг
			// update_post_meta( $post_id, '_length', "0.44" ); //
			// update_post_meta( $post_id, '_width',  "1.5" );  //
			// update_post_meta( $post_id, '_height', "1" ); // Размеры  * *  см
			// update_post_meta( $post_id, 'total_sales', '0');
			update_post_meta( $post_id, '_sku', $post_id);  //Это артикл пусть будет по ИД
			update_post_meta( $post_id, '_product_attributes', $object_data_item->product_attributes);
			// update_post_meta( $post_id, '_sale_price_dates_from', "" );
			// update_post_meta( $post_id, '_sale_price_dates_to', "" );
			update_post_meta( $post_id, '_price', $object_data_item->price );
			// update_post_meta( $post_id, '_sold_individually', "" );
			update_post_meta( $post_id, '_manage_stock', "yes" );
			update_post_meta( $post_id, '_backorders', "yes" );
			// update_post_meta( $post_id, '_stock', "" );

			if($object_data_item->comment_array ==!0)
			{
				$data = array(
					'comment_post_ID'      =>  $post_id,
					'comment_author'       =>  $object_data_item->comment_array[author],
					'comment_author_email' =>  'user@ukr.com',
					'comment_author_url'   => 'http://www.rrr',
					'comment_content'      =>  $object_data_item->comment_array[content] , 
					'comment_type' => '',
					'comment_parent' => 0,
					'user_id' => 1,
					'comment_author_IP' => '127.0.0.1',
					'comment_agent' => 'Mozilla/5.0',
					'comment_date' => current_time('mysql'),
					'comment_approved' => 1,
				   );

				wp_insert_comment($data);
				$data =null;
			}

			$object_data_item->product_attributes=null;

			require_once(ABSPATH . 'wp-admin/includes/image.php');

			$filename = $object_data_item->url_links;

			for($i=0; $i<count($object_data_item->url_links); $i++)
			{	
				$wp_upload_dir = wp_upload_dir();
				$img = $wp_upload_dir['path'] . '/' . basename($filename[$i]);
					
				file_put_contents($img, file_get_contents($filename[$i]));

				$wp_filetype = wp_check_filetype(basename($img), null );
				$attachment = array(
				  'guid' => $wp_upload_dir['url'] . '/' . basename($img),
				  'post_author' => $user_ID,
				  'post_mime_type' => $wp_filetype['type'],
				  'post_status' => 'inherit',
				  'post_parent' => $post_id,
				  'post_content' => $categories[1],
				  'post_title' =>   $categories[1],
				  'post_excerpt' => $categories[1],
				);
				

				$attachment_id = wp_insert_attachment( $attachment, $img ); //   Третьим параметром добавляет (ид родителя)
				
				update_post_meta( $attachment_id, '_wp_attachment_image_alt', $categories[1]); //Добавляем  ALT
				

				$attachment_data = wp_generate_attachment_metadata( $attachment_id, $img );
				wp_update_attachment_metadata( $attachment_id, $attachment_data );
					
				if($i==0)
				{
					set_post_thumbnail($post_id, $attachment_id);  // Миниатюра  id1 thumbnail_id id2
				}

				if($i>=1)
				{
					
					if($i>1)$separator = ",";
					
					$atta .= $separator ." ". $attachment_id;

					update_post_meta( $post_id, '_product_image_gallery', $atta);
				}
			}	
	
		// Перезагружаем эту страницу
		$data = array(	'post'=>$_GET['post'],	'action'=>'edit');
		$url_search = site_url().'/wp-admin/post.php?'.http_build_query($data);
		echo '<script>location.href="'. $url_search  .'";</script>';
	}
}