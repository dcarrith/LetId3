<?php namespace Dcarrith\LetID3;

class LetID3Facade extends \Illuminate\Support\Facades\Facade {

	/**
	 * Get the registered name of the component.
	 *
	 * @return string
	 */
	protected static function getFacadeAccessor() { return 'letid3'; }

}
