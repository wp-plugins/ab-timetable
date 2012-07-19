<?php
/*
  Plugin Name: AB Time Table
  Plugin URI: http://www.briffett.net/1208/web-development/ab-timetable-plugin-for-wordpress/
  Description: A simple hello world wordpress plugin to allow custom time table by days of week.
  Version: 1.0
  Author: Alex Briffett
  Author URI: http://www.briffett.net
  License: GPL
 */

include_once(ABSPATH . 'wp-includes/pluggable.php');
add_shortcode('abtimetable', 'timetable_page');
add_action('init', 'ab_button');

function ab_button() {

    if (!current_user_can('manage_options')) {
        return;
    }

    if (current_user_can('manage_options')) {
        add_filter('mce_external_plugins', 'add_plugin');
        add_filter('mce_buttons', 'register_button');
    }
}

function register_button($buttons) {
    array_push($buttons, "|", "abplugin");
    return $buttons;
}

function add_plugin($plugin_array) {
    $plugin_array['abplugin'] = plugins_url('ab_timetable/abbuttons.js', dirname(__FILE__));
    return $plugin_array;
}

/* Runs when plugin is activated */
register_activation_hook(__FILE__, 'ab_timetable_install');

/* Runs on plugin deactivation */
register_deactivation_hook(__FILE__, 'ab_timetable_remove');

function ab_timetable_install() {
    /* Creates new database field */
    add_option("ab_timetable_data_monday", '', '', 'yes');
    add_option("ab_timetable_data_tuesday", '', '', 'yes');
    add_option("ab_timetable_data_wednesday", '', '', 'yes');
    add_option("ab_timetable_data_thursday", '', '', 'yes');
    add_option("ab_timetable_data_friday", '', '', 'yes');
    add_option("ab_timetable_data_saturday", '', '', 'yes');
    add_option("ab_timetable_data_sunday", '', '', 'yes');
}

function ab_timetable_remove() {
    /* Deletes the database field */
    delete_option('ab_timetable_data_monday');
    delete_option('ab_timetable_data_tuesday');
    delete_option('ab_timetable_data_wednesday');
    delete_option('ab_timetable_data_thursday');
    delete_option('ab_timetable_data_friday');
    delete_option('ab_timetable_data_saturday');
    delete_option('ab_timetable_data_sunday');
}

if (current_user_can('manage_options') || is_admin()) {

    /* Call the html code */
    add_action('admin_menu', 'ab_timetable_admin_menu');

    function ab_timetable_admin_menu() {
        add_options_page('AB Timetable', 'AB Timetable', 'manage_options', 'ab-timetable', 'ab_timetable_html_page');
    }

    function ab_timetable_html_page() {
        if (!current_user_can('manage_options') && !is_admin()) {
            wp_die(__('You do not have sufficient permissions to access this page.'));
        }
        ?>
        <div>
            <h2>AB Timetable Options</h2>
            <p>Put each event on a seperate line in the textbox for the day.</p>

            <form method="post" action="options.php">
                <?php wp_nonce_field('update-options'); ?>

                <table width="510">
                    <tr valign="top">
                        <th width="92" scope="row">Monday</th>
                        <td width="406">
                            <textarea name="ab_timetable_data_monday" type="text" id="ab_timetable_data_monday"><?php echo get_option('ab_timetable_data_monday'); ?></textarea>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th width="92" scope="row">Tuesday</th>
                        <td width="406">
                            <textarea name="ab_timetable_data_tuesday" type="text" id="ab_timetable_data_tuesday"><?php echo get_option('ab_timetable_data_tuesday'); ?></textarea></td>
                    </tr>
                    <tr valign="top">
                        <th width="92" scope="row">Wednesday</th>
                        <td width="406">
                            <textarea name="ab_timetable_data_wednesday" type="text" id="ab_timetable_data_wednesday"><?php echo get_option('ab_timetable_data_wednesday'); ?></textarea>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th width="92" scope="row">Thursday</th>
                        <td width="406">
                            <textarea name="ab_timetable_data_thursday" type="text" id="ab_timetable_data_thursday"><?php echo get_option('ab_timetable_data_thursday'); ?></textarea>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th width="92" scope="row">Friday</th>
                        <td width="406">
                            <textarea name="ab_timetable_data_friday" type="text" id="ab_timetable_data_friday"><?php echo get_option('ab_timetable_data_friday'); ?></textarea>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th width="92" scope="row">Saturday</th>
                        <td width="406">
                            <textarea name="ab_timetable_data_saturday" type="text" id="ab_timetable_data_saturday"><?php echo get_option('ab_timetable_data_saturday'); ?></textarea>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th width="92" scope="row">Sunday</th>
                        <td width="406">
                            <textarea name="ab_timetable_data_sunday" type="text" id="ab_timetable_data_sunday"><?php echo get_option('ab_timetable_data_sunday'); ?></textarea>
                        </td>
                    </tr>
                </table>

                <input type="hidden" name="action" value="update" />
                <input type="hidden" name="page_options" value="ab_timetable_data_monday,ab_timetable_data_tuesday,ab_timetable_data_wednesday,ab_timetable_data_thursday,ab_timetable_data_friday,ab_timetable_data_saturday,ab_timetable_data_sunday" />

                <p>
                    <input type="submit" value="<?php _e('Save Changes') ?>" />
                </p>

            </form>
        </div>
        <?php
    }

    add_action('admin_init', 'add_ab_page');

    function add_ab_page() {

        add_menu_page('AB Timetable', 'AB Timetable', 'manage_options', 'ab-timetable', 'ab_timetable_html_page', plugin_dir_url(__FILE__) . 'images/calendar.png');
    }

}

function timetable_page() {

    $day_array = array('monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday');
    foreach ($day_array as $day) {
        ?><div class="abtimetable"><h2><?= ucfirst($day) ?></h2><p><?
        $data = get_option('ab_timetable_data_' . $day);
        $data_array = explode("\n", $data);
        foreach ($data_array as $da) {
            if (trim($da)) {
                echo trim($da) . '<br />';
            }
        }
        ?></p></div><?
    }
}

function timetable_head() {

    $todays_day_of_week = date(l);
    $todays_data = explode("\n", get_option('ab_timetable_data_' . strtolower($todays_day_of_week)));
    ?><div id="abtimetable_head">
        <div class="abtimetable_single">
            <h2><?= $todays_day_of_week ?></h2><?
    foreach ($todays_data as $td) {
        if (trim($td)) {
            ?><div><?= trim($td) ?></div><?
        }
    }
    ?>  </div>
    </div><?
    }
?>