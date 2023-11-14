<?php
/**
 * Email template for certificate
 *
 * @since      1.0.0
 *
 * @package    Codevery_Quiz
 * @subpackage Codevery_Quiz/public
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=<?php bloginfo( 'charset' ); ?>" />
        <title><?php echo esc_html( get_bloginfo( 'name', 'display' ) ); ?></title>
    </head>
    <body>
        <?php echo wp_kses_post( get_post_meta( $quiz_id, 'email_layout', true ) ); ?>
    </body>
</html>