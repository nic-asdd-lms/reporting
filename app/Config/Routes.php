<?php

namespace Config;

// Create a new instance of our RouteCollection class.
$routes = Services::routes();

/*
 * --------------------------------------------------------------------
 * Router Setup
 * --------------------------------------------------------------------
 */
$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('Home');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override();
// The Auto Routing (Legacy) is very dangerous. It is easy to create vulnerable apps
// where controller filters or CSRF protection are bypassed.
// If you don't want to define all routes, please use the Auto Routing (Improved).
// Set `$autoRoutesImproved` to true in `app/Config/Feature.php` and set the following to true.
// $routes->setAutoRoute(false);

/*
 * --------------------------------------------------------------------
 * Route Definitions
 * --------------------------------------------------------------------
 */

// We get a performance increase by specifying the default
// route since we don't have to scan directories.
$routes->get('/reporting/', 'Home::index');
$routes->get('/reporting/login/checkIgotUser', 'Login::checkIgotUser');
$routes->post('/reporting/login/user_login_process', 'Login::user_login_process');
$routes->get('/reporting/login/logout', 'Login::logout');
$routes->get('/reporting/login', 'Login::index');
$routes->post('/reporting/login', 'Login::index');
$routes->get('/reporting/home', 'Home::index');
$routes->get('/reporting/home/index.php', 'Home::index');
$routes->post('/reporting/home/getCourseWiseEnrolmentReport', 'Home::getCourseWiseEnrolmentReport');
$routes->post('/reporting/home/getCourseReport', 'Home::getCourseReport');
$routes->post('/reporting/home/getMDOReport', 'Home::getMDOReport');
$routes->post('/reporting/home/getRoleReport', 'Home::getRoleReport');
$routes->post('/reporting/home/getDoptReport', 'Home::getDoptReport');
$routes->post('/reporting/home/getAnalytics', 'Home::getAnalytics');
$routes->post('/reporting/home/action', 'Home::action');
$routes->post("/reporting/home/download-report", "Report::exportToExcel");
$routes->post('/reporting/home/search', 'Home::search');
$routes->post('/reporting/home/orgSearch', 'Home::orgSearch');
$routes->get('/reporting/home/getExcelReport', 'Home::getExcelReport');

/*
 * --------------------------------------------------------------------
 * Additional Routing
 * --------------------------------------------------------------------
 *
 * There will often be times that you need additional routing and you
 * need it to be able to override any defaults in this file. Environment
 * based routes is one such time. require() additional route files here
 * to make that happen.
 *
 * You will have access to the $routes object within that file without
 * needing to reload it.
 */
if (is_file(APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php')) {
    require APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php';
}
