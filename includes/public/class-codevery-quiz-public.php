<?php
/**
 * The public-facing functionality of the plugin.
 *
 * @since      1.0.0
 *
 * @package    Codevery_Quiz
 * @subpackage Codevery_Quiz/public
 */

if ( ! class_exists( 'Codevery_Quiz_Public' ) ) {
    /**
     * Class Codevery_Quiz_Public
     *
     * @since      1.0.0
     */
    class Codevery_Quiz_Public {

        /**
         * The ID of this plugin.
         *
         * @since    1.0.0
         * @access   private
         * @var      string    $plugin_name    The ID of this plugin.
         */
        private $plugin_name;

        /**
         * The version of this plugin.
         *
         * @since    1.0.0
         * @access   private
         * @var      string    $version    The current version of this plugin.
         */
        private $version;

        /**
         * Initialize the class and set its properties.
         *
         * @since    1.0.0
         */
        public function __construct() {

            if ( defined( 'CODEVERY_QUIZ_VERSION' ) ) {
                $this->version = CODEVERY_QUIZ_VERSION;
            } else {
                $this->version = '1.0.0';
            }
            $this->plugin_name = 'codevery_quiz';

            add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ) );
            add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

            /**
             * Register shortcodes
             */
            add_shortcode( 'codevery_quiz', array( $this, 'quiz_shortcode' ) );
            add_shortcode( 'codevery_quiz_certificate', array( $this, 'certificate_shortcode' ) );
            /**
             * Ajax
             */
            add_action( 'wp_ajax_cquiz_send_coupon_to_user', array( $this, 'send_coupon_to_user' ) );
            add_action( 'wp_ajax_nopriv_cquiz_send_coupon_to_user', array( $this, 'send_coupon_to_user' ) );

            add_action( 'wp_ajax_cquiz_add_coupon_to_database', array( $this, 'add_coupon_to_database' ) );
            add_action( 'wp_ajax_nopriv_cquiz_add_coupon_to_database', array( $this, 'add_coupon_to_database' ) );

            add_action( 'wp_ajax_cquiz_export_email_list', array( $this, 'export_email_list' ) );
        }

        /**
         * Register the stylesheets for the public-facing side of the site.
         *
         * @since    1.0.0
         */
        public function enqueue_styles() {

            $min = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
            wp_register_style( $this->plugin_name, CODEVERY_QUIZ_PLUGIN_URI_ASSETS . 'css/cquiz-public' . $min . '.css', array(), $this->version, 'all' );

        }

        /**
         * Register the JavaScript for the public-facing side of the site.
         *
         * @since    1.0.0
         */
        public function enqueue_scripts() {

            $min = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

            wp_register_script( $this->plugin_name, CODEVERY_QUIZ_PLUGIN_URI_ASSETS . 'js/cquiz-public' . $min . '.js', array( 'jquery' ), $this->version, false );
            wp_localize_script( $this->plugin_name, 'quizParams', apply_filters( 'cquiz_frontend_params', array(
                'ajaxUrl'         => admin_url( 'admin-ajax.php' ),
                'resultText'      => __( 'You got <b>{score}</b> points', 'codevery-quiz' ),
                'emptyEmailMsg'   => __( 'Please enter an email address.', 'codevery-quiz' ),
                'invalidEmailMsg' => __( 'Please enter a valid email address.', 'codevery-quiz' ),
            ) ) );

        }

        /**
         * Quiz Shortcode
         *
         * @param $atts
         * @return false|string
         */
        public function quiz_shortcode( $atts ) {

            $args = shortcode_atts( array(
                'id' => '',
            ), $atts );
            wp_enqueue_script( $this->plugin_name );
            wp_enqueue_style( $this->plugin_name );

            $quiz_settings = $this->get_quiz_settings( $atts['id'] );
            ob_start(); ?>
            <?php if ( isset( $quiz_settings['progress_bar_color'] ) ) : ?>
                .cquiz__countdown .cquiz__countdown-line {
                    background-color: <?php echo esc_attr( $quiz_settings['progress_bar_color'] ); ?>;
                }
            <?php endif; ?>
            .cquiz__form-input + label.cquiz__form-label:hover .cquiz__card-subtitle:after,
            .cquiz__form-input + label.cquiz__form-label:hover span:after {
                background-color: <?php echo esc_attr( $quiz_settings['hover_answer_color'] ); ?>;
            }
            .cquiz__form-input:checked[data-rule="1"] + label.cquiz__form-label .cquiz__card-subtitle:after,
            .cquiz__form-input.showAnswer[data-rule="1"] + label.cquiz__form-label .cquiz__card-subtitle:after,
            .cquiz__form-input:checked[data-rule="1"] + label.cquiz__form-label span:after,
            .cquiz__form-input.showAnswer[data-rule="1"] + label.cquiz__form-label span:after {
                background-color: <?php echo esc_attr( $quiz_settings['correct_answer_color'] ); ?>;
            }
            .cquiz__card-description-wrap .cquiz__card-description-title.success-title {
                color: <?php echo esc_attr( $quiz_settings['correct_answer_color'] ); ?>;
            }
            .cquiz__form-input:checked[data-rule="0"] + label.cquiz__form-label .cquiz__card-subtitle:after,
            .cquiz__form-input.showAnswer[data-rule="0"] + label.cquiz__form-label .cquiz__card-subtitle:after,
            .cquiz__form-input:checked[data-rule="0"] + label.cquiz__form-label span:after,
            .cquiz__form-input.showAnswer[data-rule="0"] + label.cquiz__form-label span:after {
                background-color: <?php echo esc_attr( $quiz_settings['incorrect_answer_color'] ); ?>;
            }
            .cquiz__card-description-wrap .cquiz__card-description-title.fail-title {
                color: <?php echo esc_attr( $quiz_settings['incorrect_answer_color'] ); ?>;
            }

            .cquiz-certificate .cquiz-certificate__sale,
            .cquiz-certificate .cquiz-certificate__sale-percent p {
            color: <?php echo esc_html( $quiz_settings['highlighted_color'] ); ?>;
            }
            .cquiz-certificate .cquiz-certificate__wrap .ribbon-bg,
            .cquiz-certificate .cquiz-certificate__promocode .coupon-code {
            background-color: <?php echo esc_html( $quiz_settings['highlighted_color'] ); ?>;
            }
            .cquiz-certificate .cquiz-certificate__wrap .bow-bg svg {
            fill: <?php echo esc_html( $quiz_settings['highlighted_color'] ); ?>;
            }
            <?php
            $styles = ob_get_clean();
            wp_register_style( $this->plugin_name . '-dynamic', false, '', $this->version );
            wp_enqueue_style( $this->plugin_name . '-dynamic' );
            wp_add_inline_style( $this->plugin_name . '-dynamic', $styles );
            ob_start();
            require 'partials/quiz-display.php';
            $quiz_html = ob_get_clean();

            return apply_filters( 'cquiz_display_quiz_html', $quiz_html, $quiz_settings, $atts );
        }

        /**
         * Certificate Shortcode
         *
         * @param $attr
         * @return false|string
         */
        public function certificate_shortcode( $attr ) {

            $atts  = shortcode_atts( array(
                'quiz_id' => '',
            ), $attr, 'codevery_quiz_certificate' );

            ob_start();
            require 'partials/certificate-display.php';
            $certificate_html = ob_get_clean();

            return apply_filters( 'cquiz_display_certificate_html', $certificate_html, $attr );
        }

        public function send_coupon_to_user() {
            if ( ! check_ajax_referer( 'cquiz_send_coupon', 'cquiz_send_coupon_nonce', false ) ) {
                wp_send_json_error( 'bad_nonce', 400 );
            }
            $coupon_code = isset( $_POST['coupon'] ) ? sanitize_text_field( wp_unslash( $_POST['coupon'] ) ) : '';
            $user_email = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';
            $quiz_id = isset( $_POST['quiz_id'] ) ? absint( wp_unslash( $_POST['quiz_id'] ) ) : '';

            $quiz_settings = $this->get_quiz_settings( $quiz_id );
            $amount = $quiz_settings['coupon_amount'];
            $exp_date = isset( $quiz_settings['expiration_date'] ) ? $quiz_settings['expiration_date'] : '+2 weeks';
            $exp_date_format = $quiz_settings['exp_date_format'];

            if ( is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
                if ( ! $this->check_if_coupon_valid( $coupon_code ) ) {
                    $coupon = $this->create_coupon( $coupon_code, $amount, $exp_date, $quiz_id );
                } else {
                    $coupon_id = wc_get_coupon_id_by_code( $coupon_code );
                    $coupon = new WC_Coupon( $coupon_id );
                }
                $coupon_code = $coupon->get_code();
                $expiry_date = $coupon->get_date_expires( 'edit' ) ? $coupon->get_date_expires( 'edit' )->date( $exp_date_format ) : '';
            } else {
                if ( $exp_date_format == 'ukraine_format' ) {
                    $expiry_date = codevery_quiz_ua_date_format( strtotime( $exp_date ) );
                } else {
                    $expiry_date = date( $exp_date_format, strtotime( $exp_date ) );
                }
            }

            // Send mail to user.
            ob_start();
            require 'partials/certificate-email.php';
            $message = ob_get_clean();
            $message = str_replace( '{CODE}', $coupon_code, $message );
            $message = str_replace( '{EXP_DATE}', $expiry_date, $message );
            $message = str_replace( '{COUPON_AMOUNT}', $amount, $message );
            $headers = array();
            $content_type = apply_filters( 'cquiz_mail_content_type', 'text/html' );

            $from_name = apply_filters( 'cquiz_mail_from_name', get_option( 'blogname' ) );
            $from_email = apply_filters( 'cquiz_mail_from', get_option( 'admin_email' ) );
            $headers[] = "Content-Type:  $content_type; charset=UTF-8";
            $headers[] = "From: $from_name <$from_email> \r\n";

            /* translators: %s: Site name. */
            $subject = sprintf( esc_html__( '[%s]: Certificate', 'codevery-quiz' ), get_bloginfo( 'name' ) );

            if ( function_exists( 'wc_get_logger' ) && WP_DEBUG ) {
                wc_get_logger()->info( 'SEND EMAIL', array( 'source' => 'CodeveryQuiz certificate email' ) );
                wc_get_logger()->info( 'Message $message=' . $message, array( 'source' => 'CodeveryQuiz certificate email' ) );
                wc_get_logger()->info( '$subject=' . $subject . ' $headers=' . wp_json_encode( $headers ), array( 'source' => 'CodeveryQuiz certificate email' ) );
                wc_get_logger()->info( '$user_email=' . $user_email, array( 'source' => 'CodeveryQuiz certificate email' ) );
            }

            $email_sent = wp_mail( $user_email, $subject, $message, $headers );

            // Save emails.
            $post_exists = post_exists( $user_email, '', '', 'cquiz_email' );
            $last_pasted_quiz = '0000-00-00 00:00:00';
            if ( $datetime = date_create_immutable( 'now', wp_timezone() ) ) {
                $last_pasted_quiz = $datetime->format( 'Y-m-d H:i:s' );
            }
            if ( ! $post_exists ) {
                $post_id = wp_insert_post( array(
                    'post_type'   => 'cquiz_email',
                    'post_status' => 'publish',
                    'post_title'  => $user_email,
                ) );
                update_post_meta( $post_id, '_last_pasted_quiz', $last_pasted_quiz );
                update_post_meta( $post_id, '_source', wp_json_encode( array( $quiz_id => 1 ) ) );
            } else {
                update_post_meta( $post_exists, '_last_pasted_quiz', $last_pasted_quiz );
                $source = get_post_meta( $post_exists, '_source', true );
                if ( $source ) {
                    $source = json_decode( $source, true );
                    if ( $source && array_key_exists( $quiz_id, $source ) ) {
                        $source[ $quiz_id ]++;
                    } else {
                        $source[ $quiz_id ] = 1;
                    }
                    update_post_meta( $post_exists, '_source', wp_json_encode( $source ) );
                }
            }

            $status = 'error';
            $response_msg = __( 'There was an error trying to send your message. Please try again later.', 'codevery-quiz' );
            if ( $email_sent ) {
                $status = 'success';
                $response_msg = __( 'The certificate has been successfully sent!', 'codevery-quiz' );
            }
            $response = array(
                'status'      => $status,
                'expiry_date' => $expiry_date,
                'percent'     => $amount,
                'message'     => $response_msg,
            );

            echo wp_json_encode( $response );

            wp_die();
        }

        /**
         * Create Woocommerce Coupon
         *
         * @param $coupon_code
         * @param $amount
         * @param string $exp_date
         * @param bool $quiz_id
         * @return array|WC_Coupon
         */
        public function create_coupon( $coupon_code, $amount, $exp_date = '+2 weeks', $quiz_id = false ) {
            $discount_type = 'percent';
            $date_in_two_weeks = date( 'Y-m-d', strtotime( $exp_date ) );

            if ( ! $this->check_if_coupon_valid( $coupon_code ) ) {
                $coupon_args = array(
                    'post_title'   => $coupon_code,
                    'post_status'  => 'publish',
                    'post_type'    => 'shop_coupon',
                    'post_excerpt' => 'Quiz #' . $quiz_id,
                );

                $new_coupon_id = wp_insert_post( apply_filters( 'cquiz_create_coupon_args', $coupon_args, $quiz_id ) );

                // Add coupon meta.
                update_post_meta( $new_coupon_id, 'discount_type', $discount_type );
                update_post_meta( $new_coupon_id, 'coupon_amount', $amount );
                update_post_meta( $new_coupon_id, 'individual_use', 'no' );
                update_post_meta( $new_coupon_id, 'product_ids', '' );
                update_post_meta( $new_coupon_id, 'exclude_product_ids', '' );
                update_post_meta( $new_coupon_id, 'usage_limit', '1' );
                update_post_meta( $new_coupon_id, 'expiry_date', $date_in_two_weeks );
                update_post_meta( $new_coupon_id, 'date_expires', $date_in_two_weeks );
                update_post_meta( $new_coupon_id, 'apply_before_tax', 'yes' );
                update_post_meta( $new_coupon_id, 'free_shipping', 'no' );

                $coupon = new WC_Coupon( $new_coupon_id );

                return $coupon;
            }
        }

        /**
         * Check if coupon code is valid
         *
         * @param $couponcode
         * @return bool
         */
        public function check_if_coupon_valid( $couponcode ) {
            if ( ! class_exists( 'WC_Coupon' ) ) {
                return false;
            }
            $coupon = new WC_Coupon( $couponcode );
            $discounts = new WC_Discounts( WC()->cart );
            $valid_response = $discounts->is_coupon_valid( $coupon );

            return ! is_wp_error( $valid_response );
        }

        /**
         * Add coupon to woocommerce database (ajax)
         *
         * @return bool
         */
        public function add_coupon_to_database() {
            if ( ! check_ajax_referer( 'cquiz_display', 'cquiz_display_nonce', false ) ) {
                wp_send_json_error( 'bad_nonce', 400 );
            }

            $quiz_id = isset( $_POST['quiz_id'] ) ? absint( wp_unslash( $_POST['quiz_id'] ) ) : '';

            /**
             * Fires when the user wins and before the coupon is added to the database
             *
             * @since 1.1.0
             *
             * @param int $quiz_id current quiz ID
             */
            do_action( 'cquiz_before_adding_coupon', $quiz_id );

            if ( ! class_exists( 'WC_Coupon' ) ) {
                return false;
            }

            $coupon_code = isset( $_POST['coupon'] ) ? sanitize_text_field( wp_unslash( $_POST['coupon'] ) ) : '';
            $quiz_settings = $this->get_quiz_settings( $quiz_id );
            $exp_date = $quiz_settings['expiration_date'];
            $amount = $quiz_settings['coupon_amount'];
            $coupon = $this->create_coupon( $coupon_code, $amount, $exp_date, $quiz_id );

            if ( ! is_wp_error( $coupon ) ) {
                $result = array(
                    'status' => 'success',
                );
            } else {
                $result = array(
                    'status'  => 'error',
                    'message' => $coupon->get_error_message(),
                );
            }

            echo wp_json_encode( $result );

            wp_die();
        }

        /**
         * Get Quiz by ID
         *
         * @param $id
         * @param array $args
         * @return WP_Query
         */
        public function get_quiz( $id, $args = array() ) {
            $default_args = array(
                'post_type'   => CODEVERY_QUIZ_POST_TYPE,
                'post_status' => 'publish',
                'post__in'    => array( $id ),
            );
            $args = array_merge( $default_args, $args );

            return new WP_Query( $args );
        }

        /**
         * Get Quiz Settings by ID
         *
         * @param $id
         * @return array
         */
        public function get_quiz_settings( $id ) {
            $quiz_settings = get_post_meta( $id, 'quiz_settings', true );
            $quiz_settings = $quiz_settings ? json_decode( stripslashes( $quiz_settings ), true ) : array();
            $quiz_settings = array_map( function( $value ) {
                $value = htmlspecialchars_decode( $value );
                return $value;
            }, $quiz_settings );

            return $quiz_settings;
        }
        /**
         * Get Quiz Questions by ID
         *
         * @param $id
         * @return array
         */
        public function get_quiz_questions( $id ) {
            $quiz_questions = json_decode( get_post_meta( $id, 'quiz_questions', true ), true );

            return $quiz_questions;
        }

        public function export_email_list() {
            if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'cquiz_email_list_export' ) ) {
                wp_send_json_error( 'Invalid nonce value', 400 );
                wp_die();
            }
            $csv_file = "Email\n";
            $args = array(
                'post_type'      => 'cquiz_email',
                'post_status'    => 'publish',
                'posts_per_page' => -1,
            );
            $emails = get_posts( $args );

            if ( $emails ) {
                foreach ( $emails as $email ) {
                    $csv_file .= $email->post_title . "\n";
                }
                wp_send_json_success( array( 'file_content' => $csv_file ) );
            } else {
                wp_send_json_error( 'Unable to export emails.' );
            }

            wp_die();
        }


    }
    $codevery_quiz_public = new Codevery_Quiz_Public();
}
