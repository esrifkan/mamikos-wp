<?php

if (false === is_admin()) :
  die();
endif;
?>
<h2>reCAPTCHA Options</h2>
<form method="post" action="options.php">
  <?php echo settings_fields("tl_recaptcha"); ?>
  <table class="form-table form-v2">
    <tr valign="top">
      <th scope="row"><label for="id_tl_recaptcha_key">Site Key: </span></label></th>
      <td><input type="text" id="id_tl_recaptcha_key" name="tl_recaptcha_key" value="<?php echo get_option("tl_recaptcha_key"); ?>" size="40" /></td>
    </tr>
    <tr valign="top">
      <th scope="row"><label for="id_tl_recaptcha_secret">Secret Key: </span></label></th>
      <td><input type="text" id="id_tl_recaptcha_secret" name="tl_recaptcha_secret" value="<?php echo get_option("tl_recaptcha_secret"); ?>" size="40" /></td>
    </tr>
    <tr valign="top">
      <th scope="row"><label for="id_recaptcha_whitelist">Whitelist IP ( 1 per line ): </span></label></th>
      <td><textarea type="text" id="id_recaptcha_whitelist" name="recaptcha_whitelist" cols="39" rows="5"><?php echo get_option("tl_recaptcha_whitelist"); ?></textarea></td>
    </tr>
  </table>
  <p><?php echo sprintf('Please get your site and secret key from Google reCaptcha. <a href="%s" target="_blank">Click here</a>','https://www.google.com/recaptcha/admin#list'); ?></p>
  <p>
    <input type="submit" name="submit" id="submit" class="button button-primary" value="Save Changes">
  </p>
</form>