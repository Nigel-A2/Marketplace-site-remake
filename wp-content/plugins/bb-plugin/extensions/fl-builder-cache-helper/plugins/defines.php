<?php
namespace FLCacheClear;
class Defines {

	var $actions = array(
		'fl_builder_init_ui',
	);

	static function run() {
		\FLCacheClear\Plugin::define( 'DONOTMINIFY' );
		\FLCacheClear\Plugin::define( 'DONOTCACHEPAGE' );
	}
}
