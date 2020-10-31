<?php
/*
Plugin Name: Throttle Login
Description: Captcha and limit the login attempts.
Version: 1.0.0
*/

define("TL_PLUGIN_DIR", plugin_dir_path(__FILE__));
define("TL_PLUGIN_URL", plugin_dir_url(__FILE__));

// require_once(TL_PLUGIN_DIR . "/src/Helpers.php");
require_once(TL_PLUGIN_DIR . "/src/throttle-login.php");

$instance = new ThrottleLogin();