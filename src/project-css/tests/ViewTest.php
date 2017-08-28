<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ViewTest extends TestCase
{

    public function setUp(){
      parent::setUp();
      Artisan::call('migrate');
      Artisan::call('db:seed');
    }

    /**
     * Check the views for the home page
     *
     * @return void
     */
    public function testWelcomeView()
    {
      // Check for the title of home page
      $this->visit('/')->see('Constituent Correspondence Data Tool');

      // Check for the links
      $this->visit('/')->click('CCDT')->seePageIs('/');
      $this->visit('/')->click('Login')->seePageIs('/login');
    }

    /**
     * Check the views for the admin page on logging
     * into the view
     *
     * @return void
     */
    public function testAdminView()
    {
      // Go to login page and enter credentials
      //credentials
      $adminEmail = "admin@admin.com";
      $adminPass = "testing";
      // Type some valid values
      $this->visit('/login')
           ->type($adminEmail,'email')
           ->type($adminPass,'password')
           ->press('Login')
           ->seePageIs('/home')
           ->see('Dashboard');

      //Check for the links with in the page
      $this->visit('/home')->click('Dashboard')->seePageIs('/home');
      // $this->visit('/home')->click('Create Collection')->seePageIs('/collection/create');
    }

    /**
     * Check the views for the admin page on logging
     * into the view
     *
     * @return void
     */
     public function testDashboardViews(){
       // Click on the all the links on dashboard
       //credentials
       $adminEmail = "admin@admin.com";
       $adminPass = "testing";
       // Type some valid values
       $this->visit('/login')
            ->type($adminEmail,'email')
            ->type($adminPass,'password')
            ->press('Login')
            ->seePageIs('/home')
            ->see('Dashboard');

        // Check for the dashboard views
        $this->visit('/home')
             ->click('User(s)')
             ->see('Create User(s)');

        // Check for the dashboard views
        $this->visit('/home')
            ->click('Collection(s)')
            ->see('Create Collection(s)');

        // Check for the dashboard views
        $this->visit('/home')
            ->click('Table(s)')
            ->see('Create Table(s)')
            ->see('Load Data');

        // Check for the dashboard views
        $this->visit('/help')
            ->see('Help')
            ->see('Search');
     }
}
