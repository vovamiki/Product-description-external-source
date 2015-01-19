<?php

class List_view
{
	public $title;
	public $price;
	public $img;
	public $links;
}


class List_products_hotline
{
	// Метод поиска получает в качестве аргумента - искомоый товар(samsung, велосипед или GT-I9300)
	function search($get)
	{
	
		$this_tovar = new List_view();
		
		// Переводим в кодировку "windows-1251" как на hotline 	
		$get = iconv("UTF-8", "windows-1251",  $get);

		$get = 'http://hotline.ua/sr/?q=' .$get;
		$get__ = file_get_contents($get);

		preg_match_all('|<div class="price">(.*?)<span class="date">|is', $get__, $new);
											
		$i=0;
		
		if(count($new[0])==0)
		{
			echo "По вашему запросу ничего не найдено!";
		}
		else
		{
			while($i<count($new[0]) AND $i<5)
			{
				// Находим все линки с введённой моделью
				preg_match("!<a.*?href=\"?'?([^ \"'>]+)\"?'?.*?>(.*?)</a>!is", $new[0][$i], $link);
				$link[1] = str_replace("/?tab=2","",$link[1]);
				$link = "http://hotline.ua".$link[1] . '/';
				$this_tovar->links[$i] = $link;
					
				// Название товара	
				preg_match('|<h3>(.*?)</h3>|is',   $new[0][$i],   $title);
				$title = iconv( "windows-1251", "UTF-8",  $title[0]);
				$this_tovar->title[$i] = strip_tags(trim($title));
	
				// Цена товара
				preg_match('|<span class="orng">(.*?)<i class="blck">|is',   $new[0][$i],   $price);
				$price[0] = strip_tags($price[0]);
				$price = str_replace("&nbsp;", "", $price[0]);
				$this_tovar->price[$i] = (int) trim($price);

				// Изображение товара
				preg_match('|hlTip="(.*?).jpg|is',   $new[0][$i],   $image);
				$this_tovar->img[$i] = str_replace('hlTip="', 'http://hotline.ua' ,$image[0]);
				
				$i++;
			}
			return    $this_tovar;	
		
		}
	}
}
