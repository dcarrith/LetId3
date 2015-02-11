<?php namespace Dcarrith\LetID3;

use Illuminate\Support\ServiceProvider;
use Dcarrith\LetID3\LetID3;

class LetID3ServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = true;

	/**
	 * Bootstrap the application events.
	 *
	 * @return void
	 */
	public function boot()
	{
		//$this->package('dcarrith/letid3');
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->app['letid3'] = $this->app->share(function($app)
		{
			// Instantiate the LetID3 object
			return new LetID3();
		});
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array('letid3');
	}

}
