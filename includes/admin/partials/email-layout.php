<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly.

if ( function_exists( 'the_custom_logo' ) && has_custom_logo() ) {
    $logo = codevery_quiz_get_custom_logo( array( 'style' => 'max-width:320px;height:auto;margin:auto;' ) );
} else {
    $logo = '<a href="' . esc_url( home_url( '/' ) ) . '" style="font-weight:bold;margin-bottom:30px;color:inherit;text-decoration:none;text-transform:uppercase;font-size: 2rem;">' . esc_html( get_bloginfo( 'name' ) ) . '</a>';
}
?>
<div style="background: #fff; width: 655px; margin: 0 auto; border-top: 10px solid #7777EF; padding: 30px 0 50px;font-family:'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif;">
    <div style="margin-bottom: 57px;text-align: center">
        <?php echo wp_kses_post( $logo ); ?>
    </div>
    <p style="text-align: center; font-weight: bold; margin: 0; text-transform: uppercase; margin-bottom: 30px;"><?php esc_html_e( 'Your Discount', 'codevery-quiz' ); ?> {COUPON_AMOUNT}%</p>
    <div style="border-radius: 10px; border: 2px dashed #000; padding: 25px; width: 40%; margin: 0 auto 30px;">
        <p style="text-transform: uppercase; font-size: 30px; text-align: center; font-weight: bold; margin: 0 0 15px;">{CODE}</p>
        <p style="font-size: 18px; text-align: center; margin: 0;"><?php esc_html_e( 'Valid until', 'codevery-quiz' ); ?> {EXP_DATE}</p>
    </div>
    <div style="width: 80%; margin: 0 auto 30px;">
        <p style="font-size: 18px; text-align: center; margin: 0;"><?php esc_html_e( 'You can send it to your friend. The discount can be used only once in the specified period of time.', 'codevery-quiz' ); ?></p>
    </div>
    <div style="text-align: center;"><a style="background: #7777EF; text-transform: uppercase; font-size: 18px; font-weight: 600; text-align: center; padding: 16px 60px; border: 3px solid #fff; color: #fff; border-radius: 30px; text-decoration: none; display: inline-block; line-height: 1; margin-bottom: 30px;" href="<?php echo esc_url( site_url( '/' ) ); ?>"> <?php esc_html_e( 'Go to the website', 'codevery-quiz' ); ?> </a></div>
</div>