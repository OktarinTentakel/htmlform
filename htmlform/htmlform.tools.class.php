<?php

class HtmlFormTools {

	public static function array_insert($array, $index, $value){
		return array_merge(
						array_slice($array, 0, $index), 
						array($value), 
						array_slice($array, $index)
					);
	}

}

?>