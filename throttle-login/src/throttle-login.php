<?php

class ThrottleLogin
{
  public function __construct()
  {
    add_action("plugins_loaded", array($this, "init"), 100);
  }

  public function init()
  {
    add_action("admin_menu", array($this, "registerMenuPage"));
    add_action("admin_init", array($this, "registerSettings"));
    add_action('login_enqueue_scripts', array($this, 'enqueueScripts'));
    add_action('admin_enqueue_scripts', array($this, 'enqueueScripts'));
    add_action('login_form', array($this, 'recaptchaForm'));
    add_action('register_form', array($this, 'recaptchaForm'), 99);
    add_action('signup_extra_fields', array($this, 'recaptchaForm'), 99);
    add_action('lostpassword_form', array($this, 'recaptchaForm'));


  }

  public function registerMenuPage()
  {
    add_options_page("Throttle Login Options", "Throttle Login", "manage_options", plugin_dir_path(__FILE__) . "admin-dashboard.php");
  }

  public function registerSettings()
  {
    add_option("tl_recaptcha_key", "");
    add_option("tl_recaptcha_secret", "");
    add_option("tl_recaptcha_whitelist", "");

    register_setting("tl_recaptcha", "tl_recaptcha_key");
    register_setting("tl_recaptcha", "tl_recaptcha_secret");
    register_setting("tl_recaptcha", "tl_recaptcha_whitelist");
  }

  public function enqueueScripts()
  {
    if (false === wp_script_is("tl_recaptcha_google_api")) {
      wp_register_script('tl_recaptcha_google_api', 'https://www.google.com/recaptcha/api.js?onload=submitDisable', array(), null);
    }

    if ((false === empty($GLOBALS['pagenow']) && ($GLOBALS['pagenow'] == 'options-general.php' || $GLOBALS['pagenow'] == 'wp-login.php')) || (function_exists('is_account_page') && is_account_page()) || (function_exists('is_checkout') && is_checkout())) {
      wp_enqueue_script('tl_recaptcha_google_api');
    }
  }

  /**
   * @return bool
   */
  public function isWhiteList()
  {
    /* get whitelist and convert to array */
    $whitelist = get_option('tl_recaptcha_whitelist');
    if (false === empty($whitelist)) {
      $whitelist = explode("\r\n", trim($whitelist));
    } else {
      $whitelist = array();
    }

    /* get ip address */
    $ip = $this->getIPAddress();

    if (!empty($ip) && !empty($whitelist) && in_array($ip, $whitelist)) {
      return true;
    } else {
      return false;
    }
  }

  public function getIPAddress()
  {
    foreach (array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR') as $key) {
      if (true === array_key_exists($key, $_SERVER)) {
        foreach (explode(',', $_SERVER[$key]) as $ip) {
          $ip = trim($ip); // just to be safe
          $ip = filter_var($ip, FILTER_VALIDATE_IP);
          if (!empty($ip)) {
            return $ip;
          }
        }
      }
    }
    return false;
  }

  public function recaptchaForm() {
    echo sprintf('<div class="g-recaptcha" id="g-recaptcha" data-sitekey="%s" data-callback="submitEnable" data-expired-callback="submitDisable"></div>', get_option('tl_recaptcha_key'))."\n";
    echo '<script>'."\n";
    echo "function submitEnable() {\n";
    echo "var button = document.getElementById('wp-submit');\n";
    echo "if (button === null) {\n";
    echo "button = document.getElementById('submit');\n";
    echo "}\n";
    echo "if (button !== null) {\n";
    echo "button.removeAttribute('disabled');\n";
    echo "}\n";
    echo "     }\n";
    echo "function submitDisable() {\n";
    echo "var button = document.getElementById('wp-submit');\n";
    // do not disable button with id "submit" in admin context, as this is the settings submit button
    if (!is_admin()) {
        echo "if (button === null) {\n";
        echo "button = document.getElementById('submit');\n";
        echo "}\n";
    }
    echo "if (button !== null) {\n";
    echo "button.setAttribute('disabled','disabled');\n";
    echo "}\n";
    echo " }\n";
    echo '</script>'."\n";
    echo '<noscript>'."\n";
    echo '<div style="width: 100%; height: 473px;">'."\n";
    echo '<div style="width: 100%; height: 422px; position: relative;">'."\n";
    echo '<div style="width: 302px; height: 422px; position: relative;">'."\n";
    echo sprintf('<iframe src="https://www.google.com/recaptcha/api/fallback?k=%s"', get_option('tl_recaptcha_key'))."\n";
    echo 'frameborder="0" title="captcha" scrolling="no"'."\n";
    echo 'style="width: 302px; height:422px; border-style: none;">'."\n";
    echo '</iframe>'."\n";
    echo '</div>'."\n";
    echo '<div style="width: 100%; height: 60px; border-style: none;'."\n";
    echo 'bottom: 12px; left: 25px; margin: 0px; padding: 0px; right: 25px; background: #f9f9f9; border: 1px solid #c1c1c1; border-radius: 3px;">'."\n";
    echo '<textarea id="g-recaptcha-response" name="g-recaptcha-response"'."\n";
    echo 'title="response" class="g-recaptcha-response"'."\n";
    echo 'style="width: 250px; height: 40px; border: 1px solid #c1c1c1;'."\n";
    echo 'margin: 10px 25px; padding: 0px; resize: none;" value="">'."\n";
    echo '</textarea>'."\n";
    echo '</div>'."\n";
    echo '</div>'."\n";
    echo '</div><br>'."\n";
    echo '</noscript>'."\n";
  }
}
