<?php

/**
 * Plugin Name:       Codevery Quiz
 * Plugin URI:        https://quiz-plugin.codevery.com/
 * Description:       Create engaging quizzes on your WordPress site and offer incentives for high scores. Users can earn discount coupons based on their quiz results.
 * Version:           1.1.0
 * Author:            Codevery
 * Author URI:        https://profiles.wordpress.org/codevery/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       codevery-quiz
 * Domain Path:       /languages
 *
 * @package Codevery_Quiz
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}

define( 'CODEVERY_QUIZ_VERSION', '1.1.0' );
define( 'CODEVERY_QUIZ_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'CODEVERY_QUIZ_PLUGIN_DIR_ADMIN', plugin_dir_path( __FILE__ ) . 'includes/admin/' );
define( 'CODEVERY_QUIZ_PLUGIN_URI', plugin_dir_url( __FILE__ ) );
define( 'CODEVERY_QUIZ_PLUGIN_URI_ASSETS', plugin_dir_url( __FILE__ ) . 'assets/' );
define( 'CODEVERY_QUIZ_POST_TYPE', 'quiz' );

/**
 *  Load the plugin text domain for translation.
 */
function codevery_quiz_load_plugin_textdomain() {
    load_plugin_textdomain(
        'codevery-quiz',
        false,
        plugin_basename( CODEVERY_QUIZ_PLUGIN_DIR ) . '/languages/'
    );
}
add_action( 'plugins_loaded', 'codevery_quiz_load_plugin_textdomain' );

if ( is_admin() ) {
    require_once CODEVERY_QUIZ_PLUGIN_DIR_ADMIN . 'codevery-quiz-admin.php';
    require_once CODEVERY_QUIZ_PLUGIN_DIR_ADMIN . 'class-codevery-quiz-email-list.php';
}
require_once CODEVERY_QUIZ_PLUGIN_DIR . 'includes/public/class-codevery-quiz-public.php';

require CODEVERY_QUIZ_PLUGIN_DIR . 'includes/cquiz-post-types.php';
require CODEVERY_QUIZ_PLUGIN_DIR . 'includes/cquiz-helpers.php';

/**
 * Codevery Quiz activation hook.
 */
function codevery_quiz_activate_plugin() {
    require_once CODEVERY_QUIZ_PLUGIN_DIR . 'includes/cquiz-activator.php';
    Codevery_Quiz_Activator::activate();
}

register_activation_hook( __FILE__, 'codevery_quiz_activate_plugin' );
