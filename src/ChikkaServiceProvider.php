<?php namespace Ivanbay\Chikka;
 
 use Illuminate\Support\ServiceProvider;
 use Illuminate\Container;
 
class ChikkaServiceProvider extends ServiceProvider {

	/**
	 * Bootstrap the application services.
	 *
	 * @return void
	*/
	public function boot()
	{
		//Publishes package config file to applications config folder
		$this->publishes([__DIR__.'/config/chikka.php' => config_path('chikka.php')]);

	}

	/**
	* Register the application services.
	*
	* @return void
	*/
	public function register()
	{
		$this->app->bind('Chikka', function()
		{
			return new \Ivanbay\Chikka\Chikka;
		});

		$this->app->booting(function()
		{
		  $loader = \Illuminate\Foundation\AliasLoader::getInstance();
		  $loader->alias('Chikka', 'Ivanbay\Chikka\Facade\Chikka');
		});
	}

}