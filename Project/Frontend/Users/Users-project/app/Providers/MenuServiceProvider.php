<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Http;

class MenuServiceProvider extends ServiceProvider
{
  /**
   * Register services.
   */
  public function register(): void
  {
    //
  }

  /**
   * Bootstrap services.
   */
  public function boot(): void
  {
    /**
     * TODO:
     * This is not actually a todo, just to keep track of things with todo vs code extension
     * So What i want to convey is I've moved the below code to '', as there are dynamic menus are there which will be fetched from the api requests
     * about api request
     *  - this request is to get all the corporate components and linked sub components with the specific corporate id 
     *  - GET api url =>  https://api-user.hygeiaes.com/corporate/corporate-components/getAllComponent/corpId/<corpid>
     * 
     * Why I've moved ??
     *  - As corporate id is required to get the response from the api, corporate id is stored in the session and this file is a menuservice provider file 'providers',
     *    so in the providers boot method session variables cant be accessed as this is the very initial step when the project loads on the website
     * 
     * By, M S Praveen Kumar, Full Stack Developer :),
     * mspraveenkumar77@gmail.com
     */
    // $verticalMenuJson = file_get_contents(base_path('resources/menu/verticalMenu.json'));
    // $verticalMenuData = json_decode($verticalMenuJson);
    // $horizontalMenuJson = file_get_contents(base_path('resources/menu/horizontalMenu.json'));
    // $horizontalMenuData = json_decode($horizontalMenuJson);
    // $this->app->make('view')->share('menuData', [$verticalMenuData, $horizontalMenuData]);
  }
  // TODO: To Integrate This .......
  private function getDynamicMenuData($corporateId)
  {
    $response = Http::get("https://api-user.hygeiaes.com/corporate/corporate-components/getAllComponent/corpId/{$corporateId}");
    if ($response->successful()) {
      return $response;
    }
    return [];
  }
}
