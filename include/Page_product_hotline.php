<?php

class  Page_product_hotline
{
    // Функция получает одну ссылку на целую страницу товара 
    function full_page($link)
    {
		$this_tovar = new Tovar();
		// Ссылка на товар источник		
		$this_tovar->link = $link;

        for($t=0; $t<1; $t++)
    	{
            // Затягиваем по ссылке ($get_all_page) всю страницу из Hotline
            $get_all_page = file_get_contents($link[$t]);
            // Название товара	
            preg_match('|<h1(.*?)</h1>|is', $get_all_page  ,   $title);
            $this_tovar->title  = trim(strip_tags($title[0]));
	
            // Изображение товара (Массив изображений)
            preg_match_all('|<div class="thumbs"(.*?)</ul>|is',   $get_all_page, $ii);
            
            if (!empty($ii[0][0]))
            {
				preg_match_all('|/img/(.*?)jpg|',  $ii[0][0], $images);
				for($i=0; $i<count($images[0])/2; $i++)
				{
							$img[$i] = 'http://hotline.ua'.$images[0][$i];
							$img[$i] = str_replace('1.jpg','3.jpg', $img[$i]);
							$this_tovar->url_links[$i] = $img[$i];
				}
            }
            else
            {
				preg_match_all('|class="hl-gallery-item(.*?)</a>|is',   $get_all_page, $ii2);
				preg_match_all('|/img/(.*?)jpg|',  $ii2[0][0], $images2);
				$img[0] = 'http://hotline.ua'.$images2[0][0];
				$this_tovar->url_links[0] = $img[0];
            }


            // Цена товара
            preg_match('|<div class="box">(.*?)<span>|is',   $get_all_page,   $price);
            preg_match('|<i>(.*?)</i>|is',   $price[0],   $price);
            $price = str_replace("&nbsp;", "",$price[0]);
            $price1 = strip_tags($price);
            $this_tovar->price = (int) $price1;
			
 
            preg_match('|<p class="short-desc">(.*?)</p>|is', $get_all_page  ,   $description_short);
            if(!$description_short[0] == 0)
            {
				// Краткое ОПИСАНИЕ
				$this_tovar->post_excerpt = iconv("windows-1251", "UTF-8", trim(strip_tags($description_short[0])));
									
				// Полное  ОПИСАНИЕ
				preg_match('|<p class="full-desc" style="display:none;">(.*?)</p>|is', $get_all_page  ,   $description_full);
				$this_tovar->post_content = iconv("windows-1251", "UTF-8", trim(strip_tags($description_full[1])));
            }
            else
            {
                // Полное  ОПИСАНИЕ2
				preg_match('|<p class="full-desc">(.*?)</p>|is', $get_all_page  ,   $description_full);
				$this_tovar->post_excerpt = $post_content = iconv("windows-1251", "UTF-8", trim(strip_tags($description_full[1])));
            }

            // Характиристики товара:
            preg_match_all('|<th>(.*?)</th>|is', $get_all_page, $tab1);
            preg_match_all('|<td>(.*?)</td>|is', $get_all_page, $tab2);

            for($I=0;  $I<16;  $I++)
            { 
				$key = iconv("windows-1251", "UTF-8", trim(strip_tags($tab1[0][$I])));
				$value = iconv("windows-1251", "UTF-8", trim(strip_tags($tab2[0][$I])));
				// Строим массив с характиристиками
				$_product_attributes["key$I"]["name"] .= $key;             
				$_product_attributes["key$I"]["value"] .= $value;
				$_product_attributes["key$I"]["position"] .= $I+1;
				$_product_attributes["key$I"]["is_visible"] .= 1;             
				$_product_attributes["key$I"]["is_variation"] .= 0;
				$_product_attributes["key$I"]["is_taxonomy"] .= 0;
            }
            $this_tovar->product_attributes  = $_product_attributes;

            // Затягиваем коментарии 
            preg_match('|<span class="nik">(.*?)</span>|is', $get_all_page  ,  $_comments);
            if($_comments[0]==!0)
            {
				// Ник оставившего коментарий
				$nik = iconv("windows-1251", "UTF-8", trim(strip_tags($_comments[0])));

				// Коментарии кратко
				preg_match('|<div class="ocenka">(.*?)</div>|is', $get_all_page  ,  $_comments2);
				preg_match_all('|<i(.*?)</i>|is', $_comments2[0]  ,  $_comments2);
					
				$lik_a	= iconv("windows-1251", "UTF-8", $_comments2[0][0]);
				$lik_a	= str_replace('</b>', '</b> - ' , $lik_a) ;
				$lik_b	= iconv("windows-1251", "UTF-8", $_comments2[0][1]);
				$lik_b = str_replace('</b>', '</b> - ' ,$lik_b) ;
				$lik_c	= iconv("windows-1251", "UTF-8", $_comments2[0][2]);
				$lik_c = str_replace('</b>', '</b> - ' ,$lik_c);
				
				// Коментарий полный
				preg_match('|itemprop="reviewBody">(.*?)<span|is', $get_all_page  ,  $_comments3);
				$com3 = iconv("windows-1251", "UTF-8", trim($_comments3[1])) ;
			
				$comment_array[author] = $nik;
				$comment_array[content] = $lik_a   ."<br>".  $lik_b ."<br>".  $lik_c ."<br>";
				$comment_array[content_full] =  $com3;
						
				$this_tovar->comment_array = $comment_array;
            }
            return $this_tovar;
		}		
    }
}
