<?php
/*
  Plugin Name: Simple Removal of Posted Images
  Plugin URI: https://github.com/my-language-skills/simple-removal-of-posted-images
  Description: Clear Images from the content of a range of posts.
  Version: 1.0.1
  Author: My Language Skills team
  Author URI: https://github.com/my-language-skills
  License: GPL-3.0
  Text Domain: simple-removal-of-posted-images
  Domain Path: /languages
 */
?>

<?php
/* Runs after the basic admin panel menu structure */
add_action('admin_menu', 'sropi_add_clearImages_menu_page');
/* Create the menu */
function sropi_add_clearImages_menu_page() {
    add_menu_page('Clear Images', __('Clear Images', 'simple-removal-of-posted-images'), 'manage_options', 'sropi_clear_post_images', 'sropi_clearImages_settings_page');
}

/**
 * Creates the options for  'Clear Images' menu
 *
 * @since 1.0.0
 * @author tooulakis13
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
                        if($from >= 0 && $to >= 0) {
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
                <input type="number" name="sropi_fromvar" placeholder="0" min="1" step="1" required/>
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
 * Deletes images in the posts removing the <img> tag
 *
 * @since 1.0.0
 * @author tooulakis13
 *
 */
function sropi_clearImages_plugin_clearImages($i, $x) {
    $valuesnum = 0;
    $idarray = '';
    if ($i < $x) {
        for ($i; $i <= $x; $i++) {
            $idarray .= $i . ',';
            $valuesnum++;
        }
    } else if ($x < $i) {
        for ($x; $x <= $i; $x++) {
            $idarray .= $x . ',';
            $valuesnum++;
        }
    } else if ($i == $x) {
        for ($i; $i <= $x; $i++) {
            $idarray .= $i . ',';
            $valuesnum++;
        }
    }
    $counter = 0;
    $id_array = explode(',', $idarray);
    while ($counter < $valuesnum) {
        $post = get_post($id_array[$counter]);
        if ($post->post_type == 'post') {
            $content = wpautop($post->post_content);
            $cont = preg_replace("/<img[^>]+\>/i", " ", $content);
            $my_post = array(
                'ID' => $id_array[$counter],
                'post_content' => $cont,
            );
            wp_update_post($my_post);
        }
        $counter++;
    }
    $message = sprintf(__('%d Content updated %d', 'simple-removal-of-posted-images'), $x, $i);
    echo "<script type='text/javascript'>alert('$message');</script>";
}

/**
 * Internalization
 * It loads the MO file for plugin's translation
 *
 * @param
 *
 * @since 1.0.1
 * @author Davide Cazzorla @davideC00
 *
 * @return void
 */
	function smplads_load_plugin_textdomain() {
    load_plugin_textdomain( 'simple-advertising', FALSE, basename( dirname( __FILE__ ) ) . '/languages/' );
}
/**
 * Internalization
 * Called when the activated plugin has been loaded
 */
add_action( 'plugins_loaded', 'smplads_load_plugin_textdomain' );

?>
