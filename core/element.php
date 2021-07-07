<?php
/**
* Element Class エレメントロードのためのクラス
*/
class Element{
	/**
	* load elemntをロードするためのmethod
	* @param  string element_name ロードしたいエレメントの名前
	* @return void
	*/
	public static function load($element_name){
		require get_root_dir().'/element/'.$element_name.'.php';
	}
}
?>
