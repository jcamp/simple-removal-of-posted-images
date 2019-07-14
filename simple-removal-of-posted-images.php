<?php

/**
 * Simple Removal of Posted Images
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://github.com/my-language-skills/simple-removal-of-posted-images
 * @since             1.0
 * @package           extensions-for-pressbooks
 *
 * @wordpress-plugin
 * Plugin Name:       Simple Removal of Posted Images
 * Plugin URI:        https://github.com/my-language-skills/simple-removal-of-posted-images
 * Description:       Small enhancement for Pressbooks main plugin
 * Version:           1.0.1
 * Author:            My Language Skills team
 * Author URI:        https://github.com/my-language-skills/
 * License:           GPL 3.0
 * License URI:       http://www.gnu.org/licenses/gpl-3.0.txt
 * Text Domain:       simple-removal-of-posted-images
 * Domain Path:       /languages
 */

/* Runs after the basic admin panel menu structure */
add_action('admin_menu', 'sropi_add_clearImages_menu_page');

/**
* Create the menu.
*
* @since
*
*/

function sropi_add_clearImages_menu_page() {
    add_menu_page(__('Clear Images', 'simple-removal-of-posted-images'),
     __('Clear Images', 'simple-removal-of-posted-images'),
      'manage_options', 'sropi_clear_post_images', 'sropi_clearImages_settings_page');
}

/**
* Creates the options for  'Clear Images' menu.
*
* @since 1.0
*
*/

function sropi_clearImages_settings_page() {
    ?>
    <div class="wrap">
        <h2><?php _e('Simple Removal of Posted Images Plugin Panel', 'simple-removal-of-posted-images'); ?></h2>
        <br class="clear">
        <div id="-plugin-buttons">
            <p><?php _e('You can delete images from iniside of a range of posts. Enter the ID range of the posts and press Clear Images.', 'simple-removal-of-posted-images'); ?> </p>
            <br class="clear">
            <form action="admin.php?page=sropi_clear_post_images" method="post">
                <?php
                if (isset($_POST['sropi_test_button']) && check_admin_referer('sropi_test_button_clicked')) {
                    // the button has been pressed AND we've passed the security check
                    if (isset($_POST['sropi_fromvar']) && isset($_POST['sropi_tovar']) && !empty($_POST['sropi_fromvar']) && !empty($_POST['sropi_tovar'])) {
                        $from = sanitize_text_field($_POST['sropi_fromvar']);
                        $to = sanitize_text_field($_POST['sropi_tovar']);
                        if($from >= 1 && $to >= 1) {
                            sropi_clearImages_plugin_clearImages($from, $to);
                        } else {
                            $newmsg = __('Please enter a value greater or equal than 0.', 'simple-removal-of-posted-images');
                        echo "<script type='text/javascript'>alert('$newmsg');</script>";
                        }
                    } else {
                        $msg = __('Empty fields are not accepted. Please enter valid numbers!', 'simple-removal-of-posted-images');
                        echo "<script type='text/javascript'>alert('$msg');</script>";
                    }
                }
                wp_nonce_field('sropi_test_button_clicked');
                ?>
                <label for="sropi_fvar"> <?php _e('From', 'simple-removal-of-posted-images'); ?>: </label>
                <input type="number" name="sropi_fromvar" placeholder="1" min="1" step="1" required/>
                <label for="sropi_tvar"> <?php _e('To', 'simple-removal-of-posted-images'); ?> </label>
                <input type="number" name="sropi_tovar" placeholder="10" step="1" min="1" required/>
                <input type="hidden" value="true" name="sropi_test_button" />
                <?php submit_button(__('Clear Images', 'simple-removal-of-posted-images')); ?>
            </form>
            <br class="clear">
        </div>
    </div>
    <?php
}

/**
 * Deletes images in the posts removing the <img> tag.
 *
 * @param int $from start of the range
 * @param int $to end of the range
 *
 * @since 1.0.0
 * @since 1.0.1.1 fixed overflow and improvements in code readability
 *
 * @return void
 *
 */

function sropi_clearImages_plugin_clearImages($from, $to){
  if ($from > $to){
    //swap variables
    $temp = $to;
    $to = $from;
    $from = $temp;
  }

  for($id = $from; $id <= $to; $id++){
    //get the post by id
    $post = get_post($id);
    if( is_null($post) ){
      //Stop the loop if there are no more posts
      $to = $id;
      break;
    }
    else if($post->post_type == 'post'){
      $content = wpautop($post->post_content);
      $cont = preg_replace("/<img[^>]+\>/i", " ", $content);
      $my_post = array(
          'ID' => $id,
          'post_content' => $cont,
      );
      wp_update_post($my_post);
    }
  }
  $message = sprintf(__(' Content updated from %d to %d', 'simple-removal-of-posted-images'), $from, $to);
  echo '<script type="text/javascript">alert("' . $message . '");</script>';

}

/**
 * Internalization.
 * It loads the MO file for plugin's translation.
 *
 * @param
 *
 * @since 1.0.1
 *
 * @return void
 *
 */
	function sropi_load_plugin_textdomain() {
    load_plugin_textdomain( 'simple-removal-of-posted-images', FALSE, basename( dirname( __FILE__ ) ) . '/languages/' );
}
/**
 * Internalization.
 * Called when the activated plugin has been loaded.
 */
add_action( 'plugins_loaded', 'sropi_load_plugin_textdomain' );

?>
