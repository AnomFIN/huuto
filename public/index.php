<?php
/**
 * Huuto - Main Entry Point
 */

session_start();

// Error handling
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if installed
if (!file_exists(__DIR__ . '/../config/config.php')) {
    // Redirect to setup at root level (setup.php is in parent directory)
    header('Location: ../setup.php');
    exit;
}

// Load core classes
require_once __DIR__ . '/../app/Database.php';
require_once __DIR__ . '/../app/Security.php';
require_once __DIR__ . '/../app/Router.php';

// Initialize router
$router = new Router();

// Public routes
$router->get('/', 'HomeController@index');
$router->get('/haku', 'SearchController@index');
$router->get('/kategoriat', 'CategoryController@index');
$router->get('/kategoria/{slug}', 'CategoryController@show');
$router->get('/kohde/{id}/{slug}', 'ListingController@show');
$router->get('/paattyvat', 'ListingController@ending');

// Static pages
$router->get('/sivu/{slug}', 'PageController@show');
$router->get('/blogi', 'BlogController@index');
$router->get('/blogi/{slug}', 'BlogController@show');

// Auth routes
$router->get('/rekisteroidy', 'AuthController@register');
$router->post('/rekisteroidy', 'AuthController@doRegister');
$router->get('/kirjaudu', 'AuthController@login');
$router->post('/kirjaudu', 'AuthController@doLogin');
$router->get('/kirjaudu-ulos', 'AuthController@logout');
$router->get('/vahvista/{token}', 'AuthController@verify');

// User routes
$router->get('/profiili', 'UserController@profile');
$router->get('/omat-ilmoitukset', 'UserController@myListings');
$router->get('/omat-huudot', 'UserController@myBids');
$router->get('/omat-voitot', 'UserController@myWins');
$router->get('/luo-ilmoitus', 'UserController@createListing');
$router->post('/luo-ilmoitus', 'UserController@doCreateListing');

// Bidding
$router->post('/huuda/{id}', 'BidController@place');

// Admin routes
$router->get('/admin', 'AdminController@index');
$router->get('/admin/kayttajat', 'AdminController@users');
$router->get('/admin/ilmoitukset', 'AdminController@listings');
$router->get('/admin/kategoriat', 'AdminController@categories');
$router->post('/admin/kategoriat', 'AdminController@saveCategory');
$router->get('/admin/blogi', 'AdminController@blog');
$router->post('/admin/blogi', 'AdminController@saveBlogPost');

// Dispatch
$router->dispatch();
