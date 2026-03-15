<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// ── Public home page ────────────────────────────────────────────────────────
$routes->get('/', 'Public\HomeController::index');
$routes->post('survey/access', 'Public\HomeController::accessByPasscode');

// ── Admin auth (public routes) ──────────────────────────────────────────────────
$routes->get('admin/index',   'Admin\AuthController::index');
$routes->get('admin/login',   'Admin\AuthController::index');  // Redirect from old URL
$routes->post('admin/login',  'Admin\AuthController::doLogin');
$routes->get('admin/register',  'Admin\AuthController::register');
$routes->post('admin/register', 'Admin\AuthController::doRegister');
$routes->get('admin/logout',  'Admin\AuthController::logout');

// ── Admin (protected) ────────────────────────────────────────
$routes->group('admin', ['filter' => 'auth'], static function ($routes) {
    $routes->get('',           'Admin\DashboardController::index');
    $routes->get('dashboard',  'Admin\DashboardController::index');

    // Surveys CRUD
    $routes->get('surveys',                          'Admin\SurveyController::index');
    $routes->get('surveys/create',                   'Admin\SurveyController::create');
    $routes->post('surveys',                         'Admin\SurveyController::store');
    $routes->get('surveys/(:num)/edit',              'Admin\SurveyController::edit/$1');
    $routes->post('surveys/(:num)',                  'Admin\SurveyController::update/$1');
    $routes->post('surveys/(:num)/delete',           'Admin\SurveyController::delete/$1');
    $routes->post('surveys/(:num)/share-link',       'Admin\SurveyController::shareLink/$1');
    $routes->post('surveys/(:num)/revoke-link',      'Admin\SurveyController::revokeLink/$1');

    // Sections
    $routes->get('surveys/(:num)/sections/(:num)',              'Admin\SectionController::show/$1/$2');
    $routes->post('surveys/(:num)/sections',                   'Admin\SectionController::store/$1');
    $routes->post('surveys/(:num)/sections/(:num)',             'Admin\SectionController::update/$1/$2');
    $routes->post('surveys/(:num)/sections/(:num)/delete',      'Admin\SectionController::delete/$1/$2');
    $routes->post('surveys/(:num)/sections/reorder',            'Admin\SectionController::reorder/$1');

    // Questions
    $routes->post('surveys/(:num)/sections/(:num)/questions',                      'Admin\QuestionController::store/$1/$2');
    $routes->get('surveys/(:num)/sections/(:num)/questions/(:num)/edit',            'Admin\QuestionController::edit/$1/$2/$3');
    $routes->post('surveys/(:num)/sections/(:num)/questions/(:num)',                'Admin\QuestionController::update/$1/$2/$3');
    $routes->post('surveys/(:num)/sections/(:num)/questions/(:num)/delete',         'Admin\QuestionController::delete/$1/$2/$3');

    // Results
    $routes->get('surveys/(:num)/results',                                          'Admin\ResultsController::index/$1');
    $routes->get('surveys/(:num)/results/analytics',                               'Admin\ResultsController::analytics/$1');
    $routes->get('surveys/(:num)/results/(:num)',                                  'Admin\ResultsController::show/$1/$2');
    $routes->get('surveys/(:num)/results/questions/(:num)/text-responses',         'Admin\ResultsController::textResponses/$1/$2');
    $routes->get('surveys/(:num)/results/questions/(:num)/file-responses',         'Admin\ResultsController::fileResponses/$1/$2');
    $routes->post('surveys/(:num)/results/(:num)/delete',                          'Admin\ResultsController::deleteRespondent/$1/$2');
});

// ── Public survey routes (NOT protected - open for respondents) ────────────────────────────────────────────────
$routes->group('s', static function ($routes) {
    $routes->get('',                  'Public\SurveyController::index');
    $routes->get('(:num)',            'Public\SurveyController::show/$1');
    $routes->post('(:num)/submit',    'Public\SurveyController::submit/$1');
    $routes->get('(:num)/thank-you',  'Public\SurveyController::thankYou/$1');
});

