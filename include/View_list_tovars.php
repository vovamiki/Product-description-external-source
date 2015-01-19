<?php
class View_Spisok
{
    //Запускае метод показать список найденых товаров
    $view_this_product = List_products_hotline::search();
    function view_one_tovar()
    {
		foreach ($view_this_product as $product)
		{
			echo $product->img ;
			echo $product->title ;
			echo $product->content ;
			echo $product->price ;
			echo $product->id ;
		}
    }
}
