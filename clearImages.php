<?php
/*

  Plugin Name: Simple Removal of Posted Images
  Plugin URI: #
  Description: Clear Images from all Posts

 */
?>

<?php
add_action('admin_menu', 'add_clearImages_menu_page');

function add_clearImages_menu_page() {
    add_menu_page('Clear Images', 'Clear Images', 'manage_options', 'clear_post_images', 'clearImages_settings_page');
}

function clearImages_settings_page() {
    ?>
    <div class="wrap">
        <h2>Clear Images Plugin Panel</h2>
        <br class="clear">
        <div id="-plugin-buttons">
            <p>You can delete images from iniside of a range of posts. Enter the ID range of the posts and press Clear Images.</p>
            <br class="clear">
            <form action="admin.php?page=clear_post_images" method="post">
                <?php
                if (isset($_POST['test_button']) && check_admin_referer('test_button_clicked')) {
                    // the button has been pressed AND we've passed the security check
                    if (isset($_POST['fromvar']) && isset($_POST['tovar']) && !empty($_POST['fromvar']) && !empty($_POST['tovar'])) {
                        $from = $_POST['fromvar'];
                        $to = $_POST['tovar'];
                        if($from >= 0 && $to >= 0) {
                            clearImages_plugin_clearImages($from, $to);
                        } else {
                            $newmsg = "Please enter a value greater or equal than 0.";
                        echo "<script type='text/javascript'>alert('$newmsg');</script>";
                        }
                    } else {
                        $msg = "Empty fields are not accepted. Please enter valid numbers!";
                        echo "<script type='text/javascript'>alert('$msg');</script>";
                    }
                }

                wp_nonce_field('test_button_clicked');
                ?>

                <label for="fvar">From: </label>
                <input type="number" name="fromvar" placeholder="0" min="1" step="1" />
                <label for="tvar">To: </label>
                <input type="number" name="tovar" placeholder="10" step="1" min="1"/>
                <input type="hidden" value="true" name="test_button" />
                <?php submit_button('Clear Images'); ?>

            </form>
            <br class="clear">
        </div>
    </div>
    <?php
}

function clearImages_plugin_clearImages($i, $x) {

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

    $message = "Content Updated";
    echo "<script type='text/javascript'>alert('$message');</script>";
}

function clearImages_plugin_deactivate() {
    deactivate_plugin(basename(__FILE__));
}

?>
