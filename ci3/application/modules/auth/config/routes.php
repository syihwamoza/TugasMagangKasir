<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
 * Routes khusus untuk module Auth.
 * 
 * Ini diperlukan karena HMVC router (MX_Router) 
 * mengharuskan setiap module punya file routes sendiri.
 */

// auth       → Auth::index() → redirect ke login
// auth/login → Auth::login()
// auth/proses → Auth::proses()
// auth/logout → Auth::logout()
$route['auth']          = 'auth/index';
$route['auth/login']    = 'auth/login';
$route['auth/proses']   = 'auth/proses';
$route['auth/logout']   = 'auth/logout';
