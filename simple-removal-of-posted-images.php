<?php
/*
  Plugin Name: Simple Removal of Posted Images
  Plugin URI: #
  Description: Clear Images from the content of a range of posts.
  Version: 1.0
  License: GPL-3.0
 */
?>

<?php
add_action('admin_menu', 'sropi_add_clearImages_menu_page');
function sropi_add_clearImages_menu_page() {
    add_menu_page('Clear Images', 'Clear Images', 'manage_options', 'sropi_clear_post_images', 'sropi_clearImages_settings_page');
}
function sropi_clearImages_settings_page() {
    ?>
    <div class="wrap">
        <h2>Simple Removal of Posted Images Plugin Panel</h2>
        <br class="clear">
        <div id="-plugin-buttons">
            <p>You can delete images from iniside of a range of posts. Enter the ID range of the posts and press Clear Images.</p>
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
                            $newmsg = "Please enter a value greater or equal than 0.";
                        echo "<script type='text/javascript'>alert('$newmsg');</script>";
                        }
                    } else {
                        $msg = "Empty fields are not accepted. Please enter valid numbers!";
                        echo "<script type='text/javascript'>alert('$msg');</script>";
                    }
                }
                wp_nonce_field('sropi_test_button_clicked');
                ?>
                <label for="sropi_fvar">From: </label>
                <input type="number" name="sropi_fromvar" placeholder="0" min="1" step="1" />
                <label for="sropi_tvar">To: </label>
                <input type="number" name="sropi_tovar" placeholder="10" step="1" min="1"/>
                <input type="hidden" value="true" name="sropi_test_button" />
                <?php submit_button('Clear Images'); ?>
            </form>
            <br class="clear">
        </div>
    </div>
    <?php
}
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
    $message = $x . "Content Updated" . $i;
    echo "<script type='text/javascript'>alert('$message');</script>";
}
?>
