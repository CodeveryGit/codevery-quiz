<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @since      1.0.0
 *
 * @package    Codevery_Quiz
 * @subpackage Codevery_Quiz/admin
 */

if ( ! class_exists( 'Codevery_Quiz_Admin' ) ) {

    class Codevery_Quiz_Admin {

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

            add_action( 'admin_menu', array( $this, 'add_menu_page' ), 10 );

            add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_styles' ) );
            add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

            add_action( 'admin_init', array( $this, 'rerender_meta_options' ) );
            add_action( 'save_post', array( $this, 'save_meta_options' ), 10, 2 );

            add_action( 'admin_init', array( $this, 'preview_email' ) );

            /** ajax */
            add_action( 'wp_ajax_cquiz_get_questions', array( $this, 'get_questions' ) );
            add_action( 'wp_ajax_cquiz_add_new_question', array( $this, 'add_new_question' ) );

            /** modal */
            add_action( 'admin_action_cquiz_modal_window', array( $this, 'modal_window_content' ) );

        }

        public function add_menu_page() {

            $quiz_admin = add_menu_page(
                __( 'Quizzes', 'codevery-quiz' ),
                __( 'Quizzes', 'codevery-quiz' ),
                'manage_options',
                'codevery-quiz',
                array( $this, 'main_admin_page' ),
                'dashicons-list-view',
                19
            );

        }

        public function main_admin_page() {
            wp_safe_redirect( admin_url( 'edit.php?post_type=' . CODEVERY_QUIZ_POST_TYPE ) );
        }

        /**
         * Register the stylesheets for the admin area.
         *
         * @since    1.0.0
         */
        public function enqueue_styles() {

            wp_enqueue_style( $this->plugin_name, CODEVERY_QUIZ_PLUGIN_URI_ASSETS . 'css/cquiz-admin.css', array( 'wp-color-picker' ), $this->version );
            wp_enqueue_style( 'cquiz-select2', CODEVERY_QUIZ_PLUGIN_URI_ASSETS . 'css/select2.min.css', array(), $this->version );

        }

        /**
         * Register the JavaScript for the admin area.
         *
         * @since    1.0.0
         */
        public function enqueue_scripts() {

            $min = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
            $current_screen = get_current_screen();
            if ( $current_screen->post_type == CODEVERY_QUIZ_POST_TYPE || $current_screen->post_type == 'quiz_question' || ( isset( $_REQUEST['action'] ) && $_REQUEST['action'] == 'cquiz_modal_window' ) || str_contains( $current_screen->base, 'page_email_list' ) ) {
                wp_enqueue_script( 'cquiz-select2', CODEVERY_QUIZ_PLUGIN_URI_ASSETS . 'js/select2.min.js', array( 'jquery' ), $this->version, false );
                wp_enqueue_script( 'jquery-ui-sortable' );
                wp_enqueue_script($this->plugin_name . '-repeater', CODEVERY_QUIZ_PLUGIN_URI_ASSETS . 'js/cquiz-repeater' . $min . '.js', array( 'jquery', 'cquiz-select2' ), $this->version, false );
                wp_enqueue_script( $this->plugin_name, CODEVERY_QUIZ_PLUGIN_URI_ASSETS . 'js/cquiz-admin' . $min . '.js', array( 'jquery', 'cquiz-select2', $this->plugin_name . '-repeater', 'wp-color-picker', 'jquery-ui-sortable' ), $this->version, false );
                wp_localize_script( $this->plugin_name, 'quizParams', array(
                    'ajaxUrl'               => admin_url( 'admin-ajax.php' ),
                    'confirmRemoveMsg'      => __( 'Are you sure you want to remove this question from the list?', 'codevery-quiz' ),
                    'confirmDeleteEmailMsg' => __( 'Are you sure you want to delete this email?', 'codevery-quiz' ),
                ));
            }

        }

        /**
         * Preview email template in quiz settings
         */
        public function preview_email() {
            if ( isset( $_GET['preview_quiz_email'] ) ) {
                if ( ! ( isset( $_REQUEST['_wpnonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_REQUEST['_wpnonce'] ) ), 'cquiz-preview-mail' ) ) ) {
                    die( 'Security check' );
                }

                if ( isset( $_GET['quiz_id'] ) ) {
                    $quiz_id = absint( wp_unslash( $_GET['quiz_id'] ) );
                    $quiz_settings = $this->get_quiz_settings( $quiz_id );
                    $message = get_post_meta( $quiz_id, 'email_layout', true );
                    $message = str_replace( '{CODE}', 'XXXXXXXX', $message );
                    $message = str_replace( '{COUPON_AMOUNT}', $quiz_settings['coupon_amount'], $message );
                    $exp_date_format = $quiz_settings['exp_date_format'];
                    $expiration_date = isset( $quiz_settings['expiration_date'] ) ? $quiz_settings['expiration_date'] : '+2 weeks';
                    if ( $exp_date_format == 'ukraine_format' ) {
                        $exp_date = codevery_quiz_ua_date_format( strtotime( $expiration_date ) );
                    } else {
                        $exp_date = date( $exp_date_format, strtotime( $expiration_date ) );
                    }
                    $message = str_replace( '{EXP_DATE}', $exp_date, $message );
                }

                // print the preview email.
                echo wp_kses_post( $message );

                exit;
            }
        }

        /**
         * Save custom fields
         *
         * @param $post_id
         * @param $post
         * @return mixed
         */
        public function save_meta_options( $post_id, $post ) {

            if ( ! isset( $_POST['cquiz_question_metabox_nonce'] )
                || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['cquiz_question_metabox_nonce'] ) ), basename( __FILE__ ) ) ) {
                return $post_id;
            }

            if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) { return $post_id; }

            if ( $post->post_type == CODEVERY_QUIZ_POST_TYPE ) {

                $question = '';
                if ( isset( $_POST['question'] ) ) {
                    $question = map_deep( wp_unslash( $_POST['question'] ), 'absint' );
                    $question = array_values( $question );
                    $question = wp_json_encode( $question, JSON_UNESCAPED_UNICODE );
                }
                update_post_meta( $post_id, 'quiz_questions', $question );

                $quiz_settings = array(
                    'winner_points'           => isset( $_POST['winner_points'] ) ? absint( $_POST['winner_points'] ) : 0,
                    'start_button_text'       => isset( $_POST['start_button_text'] ) ? sanitize_text_field( $_POST['start_button_text'] ) : '',
                    'progress_bar'            => isset( $_POST['progress_bar'] ) ? sanitize_text_field( wp_unslash( $_POST['progress_bar'] ) ) : '',
                    'quiz_timer'              => isset( $_POST['quiz_timer'] ) ? sanitize_text_field( wp_unslash( $_POST['quiz_timer'] ) ) : '',
                    'quiz_time'               => isset( $_POST['quiz_time'] ) ? absint( $_POST['quiz_time'] ) : 0,
                    'expiration_date'         => isset( $_POST['expiration_date'] ) ? sanitize_text_field( $_POST['expiration_date'] ) : '',
                    'coupon_amount'           => isset( $_POST['coupon_amount'] ) ? absint( $_POST['coupon_amount'] ) : 0,
                    'exp_date_format'         => isset( $_POST['exp_date_format'] ) ? sanitize_text_field( $_POST['exp_date_format'] ) : '',
                    'coupon_background_color' => isset( $_POST['coupon_background_color'] ) ? sanitize_hex_color( $_POST['coupon_background_color'] ) : '',
                    'highlighted_color'       => isset( $_POST['highlighted_color'] ) ? sanitize_hex_color( $_POST['highlighted_color'] ) : '',
                    'title_quiz_winner'       => isset( $_POST['title_quiz_winner'] ) ? sanitize_text_field( $_POST['title_quiz_winner'] ) : '',
                    'text_quiz_winner'        => isset( $_POST['text_quiz_winner'] ) ? preg_replace( "/\r|\n/", '', wp_kses_post( wp_unslash( $_POST['text_quiz_winner'] ) ) ) : '',
                    'title_quiz_looser'       => isset( $_POST['title_quiz_looser'] ) ? sanitize_text_field( $_POST['title_quiz_looser'] ) : '',
                    'text_quiz_looser'        => isset( $_POST['text_quiz_looser'] ) ? preg_replace( "/\r|\n/", '', wp_kses_post( wp_unslash( $_POST['text_quiz_looser'] ) ) ) : '',
                    'progress_bar_color'      => isset( $_POST['progress_bar_color'] ) ? sanitize_hex_color( $_POST['progress_bar_color'] ) : '',
                    'hover_answer_color'      => isset( $_POST['hover_answer_color'] ) ? sanitize_hex_color( $_POST['hover_answer_color'] ) : '',
                    'correct_answer_color'    => isset( $_POST['correct_answer_color'] ) ? sanitize_hex_color( $_POST['correct_answer_color'] ) : '',
                    'incorrect_answer_color'  => isset( $_POST['incorrect_answer_color'] ) ? sanitize_hex_color( $_POST['incorrect_answer_color'] ) : '',
                    'certificate_text'        => isset( $_POST['certificate_text'] ) ? sanitize_text_field( $_POST['certificate_text'] ) : '',
                    'promocode_text'          => isset( $_POST['promocode_text'] ) ? sanitize_text_field( $_POST['promocode_text'] ) : '',
                    'certificate_sale_text'   => isset( $_POST['certificate_sale_text'] ) ? sanitize_text_field( $_POST['certificate_sale_text'] ) : '',
                    'promocode_exp_text'      => isset( $_POST['promocode_exp_text'] ) ? sanitize_text_field( $_POST['promocode_exp_text'] ) : '',
                    'coupon_description'      => isset( $_POST['coupon_description'] ) ? preg_replace( "/\r|\n/", '', wp_kses_post( wp_unslash( $_POST['coupon_description'] ) ) ) : '',
                    'form_description'        => isset( $_POST['form_description'] ) ? preg_replace( "/\r|\n/", '', wp_kses_post( wp_unslash( $_POST['form_description'] ) ) ) : '',
                    'display_email_form'      => isset( $_POST['display_email_form'] ) ? sanitize_text_field( wp_unslash( $_POST['display_email_form'] ) ) : '',
                );
                $quiz_settings = array_map( function( $value ) {
                    $value = htmlspecialchars( $value, ENT_QUOTES );
                    return $value;
                }, $quiz_settings );
                $quiz_settings_json = wp_json_encode( $quiz_settings, JSON_UNESCAPED_UNICODE );
                if ( $this->json_validator( $quiz_settings_json ) ) {
                    update_post_meta( $post_id, 'quiz_settings', $quiz_settings_json );
                }
                $email_layout = isset( $_POST['email_layout'] ) ? wp_kses_post( wp_unslash( $_POST['email_layout'] ) ) : '';
                update_post_meta( $post_id, 'email_layout', preg_replace( "/\r|\n/", '', $email_layout ) );
            }

            if ( $post->post_type == 'quiz_question' ) {
                $answers        = isset( $_POST['answers'] ) ? map_deep( wp_unslash( $_POST['answers'] ), 'sanitize_text_field' ) : '';
                $correct_answer = isset( $_POST['answer'] ) ? absint( wp_unslash( $_POST['answer'] ) ) : 0;
                $question_type  = isset( $_POST['question_type'] ) ? sanitize_text_field( wp_unslash( $_POST['question_type'] ) ) : '';
                $this->save_quiz_question_data( $post_id, $answers, $correct_answer, $question_type );
            }

            return $post_id;

        }

        /**
         * Save quiz question data to db
         *
         * @param $post_id
         * @param $answers
         * @param $correct_answer
         * @param $question_type
         */
        public function save_quiz_question_data( $post_id, $answers, $correct_answer, $question_type ) {
            if ( $answers ) {
                $answers[ $correct_answer ]['answer'] = true;
                $answers = array_values( $answers );
                $answers = array_map( function( $value ) {
                    $pattern = '/[\[\]{}]/';
                    $value['option'] = htmlspecialchars( preg_replace( $pattern, '', $value['option'] ), ENT_QUOTES );
                    $value['description'] = htmlspecialchars( preg_replace( $pattern, '', $value['description'] ), ENT_QUOTES );
                    return $value;
                }, $answers );

                $question_options = wp_json_encode( $answers, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES );
                update_post_meta( $post_id, 'question_options', $question_options );
            }
            if ( $question_type ) {
                update_post_meta( $post_id, 'question_type', $question_type );
            }
        }

        /**
         * Add Meta boxes
         */
        public function rerender_meta_options() {
            add_meta_box( 'quiz_settings', __( '&nbsp;', 'codevery-quiz' ), array( $this, 'display_meta_quiz_setting' ), CODEVERY_QUIZ_POST_TYPE, 'normal', 'low' );
            add_meta_box( 'quiz_shortcode', __( 'Quiz Shortcode', 'social-image' ), array( $this, 'display_shortcode_box' ), CODEVERY_QUIZ_POST_TYPE, 'side', 'high' );
            add_meta_box( 'questions', __( 'Answers', 'codevery-quiz' ), array( $this, 'display_meta_quiz_answers' ), 'quiz_question', 'normal', 'low' );
        }

        /**
         * Display meta box content with quiz settings
         *
         * @param $post_object
         */
        public function display_meta_quiz_setting( $post_object ) {
            $default_settings = array(
                'progress_bar_color'      => '#7777EF',
                'hover_answer_color'      => '#7777EF',
                'correct_answer_color'    => '#61bd65',
                'incorrect_answer_color'  => '#d34141',
                'winner_points'           => '',
                'start_button_text'       => __( 'Start', 'codevery-quiz' ),
                'progress_bar'            => '',
                'quiz_timer'              => '',
                'quiz_time'               => 600,
                'expiration_date'         => '+2 weeks',
                'coupon_amount'           => 5,
                'exp_date_format'         => 'F j, Y',
                'coupon_background_color' => '#15154c',
                'highlighted_color'       => '#c261e8',
                'title_quiz_winner'       => __( 'Congratulations!', 'codevery-quiz' ),
                'text_quiz_winner'        => '',
                'title_quiz_looser'       => __( 'Better luck next time!', 'codevery-quiz' ),
                'text_quiz_looser'        => '',
                'certificate_text'        => __( 'Certificate', 'codevery-quiz' ),
                'promocode_text'          => __( 'Promo code', 'codevery-quiz' ),
                'certificate_sale_text'   => __( 'Sale', 'codevery-quiz' ),
                'promocode_exp_text'      => __( 'Valid until:', 'codevery-quiz' ),
                'coupon_description'      => __( 'You can send it to your friend. The discount can be used only once in the specified period of time.', 'codevery-quiz' ),
                'form_description'        => __( 'We can send you this coupon by email', 'codevery-quiz' ),
                'display_email_form'      => 'yes',
            ); // TODO apply_filters().

            $quiz_settings = $this->get_quiz_settings( $post_object->ID );
            $quiz_settings = array_merge( $default_settings, $quiz_settings ); // TODO apply_filters().

            $args = array(
                'post_type'   => 'quiz_question',
                'post_status' => 'publish',
                'numberposts' => -1,
            );
            $questions = get_posts( $args );
            $quiz_questions = $this->get_quiz_questions( $post_object->ID );

            $expiration_date = $quiz_settings['expiration_date'];
            $exp_date_format = $quiz_settings['exp_date_format'];

            wp_enqueue_script( 'wp-color-picker' );
            wp_nonce_field( basename( __FILE__ ), 'cquiz_question_metabox_nonce' );

            require_once CODEVERY_QUIZ_PLUGIN_DIR_ADMIN . 'partials/quiz-settings.php';
        }

        /**
         * Display meta box content with quiz shortcode
         *
         * @param $post_object
         */
        public function display_shortcode_box( $post_object ) {
            ?>
            <div class="codevery-quiz-shortcode">
                <p><?php esc_html_e( 'Copy this shortcode and paste it into your page or post:', 'codevery-quiz' ); ?></p>
                <code id="cquiz_shortcode_<?php echo esc_attr( $post_object->ID ); ?>" class="cquiz-blue-bg">[codevery_quiz id=<?php echo esc_html( $post_object->ID ); ?>]</code>
                <a href="#" id="copy_shortcode" class="copy_shortcode" data-copied_text_id="#cquiz_shortcode_<?php echo esc_attr( $post_object->ID ); ?>" >
                    <span class="dashicons dashicons-admin-page"></span>
                </a>
            </div>
            <?php
        }

        /**
         * Display meta box content with answers for question
         *
         * @param $post_object
         */
        public function display_meta_quiz_answers( $post_object ) {
            $meta_options = get_post_meta( $post_object->ID );
            wp_nonce_field( basename( __FILE__ ), 'cquiz_question_metabox_nonce' );

            $question_type = isset( $meta_options['question_type'][0] ) ? $meta_options['question_type'][0] : 'text';
            $question_options = get_post_meta( $post_object->ID, 'question_options', true );
            $question_options = json_decode( stripslashes( $question_options ), true );

            require_once CODEVERY_QUIZ_PLUGIN_DIR_ADMIN . 'partials/question-settings.php';
        }

        /**
         * Ajax request to get list of questions
         */
        public function get_questions() {
            if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), basename( __FILE__ ) ) ) {
                return '';
            }
            if ( empty( $_GET['q'] ) ) {
                return '';
            }
            $return = array();
            $args = array(
                'search'      => sanitize_text_field( wp_unslash( $_GET['q'] ) ),
                'post_type'   => 'quiz_question',
                'post_status' => 'publish',
            );
            $questions = new WP_Query( $args );
            if ( $questions->have_posts() ) :
                while ( $questions->have_posts() ) : $questions->the_post();
                    $title    = ( mb_strlen( $questions->post->post_title ) > 50 ) ? mb_substr( $questions->post->post_title, 0, 49 ) . '...' : $questions->post->post_title;
                    $return[] = array( $questions->post->ID, $title );
                endwhile;
            endif;
            echo wp_json_encode( $return );
            die;
        }

        /**
         * Ajax request for adding new question (in iframe)
         */
        public function add_new_question() {
            if ( ! check_ajax_referer( 'cquiz_new_question', 'cquiz_new_question_nonce', false ) ) {
                wp_send_json_error( 'bad_nonce', 400 );
            }

            if ( ! current_user_can( 'customize' ) ) {
                wp_send_json_error( 'customize_not_allowed', 403 );
            }

            $result = array();
            $title = isset( $_POST['title'] ) ? sanitize_text_field( wp_unslash( $_POST['title'] ) ) : '';
            $description = isset( $_POST['description'] ) ? sanitize_text_field( wp_unslash( $_POST['description'] ) ) : '';
            $post_data = array(
                'post_title'   => $title,
                'post_content' => $description,
                'post_status'  => 'publish',
                'post_type'    => 'quiz_question',
            );
            $post_id = wp_insert_post( $post_data );

            $result['question_id']    = $post_id;
            $result['question_title'] = $title;
            $result['success_msg']    = __( 'New question has been saved', 'codevery-quiz' );

            $answers        = isset( $_POST['answers'] ) ? map_deep( wp_unslash( $_POST['answers'] ), 'sanitize_text_field' ) : '';
            $correct_answer = isset( $_POST['answer'] ) ? absint( wp_unslash( $_POST['answer'] ) ) : 0;
            $question_type  = isset( $_POST['question_type'] ) ? sanitize_text_field( wp_unslash( $_POST['question_type'] ) ) : '';
            $this->save_quiz_question_data( $post_id, $answers, $correct_answer, $question_type );
            wp_send_json_success( $result );
            die;
        }

        /**
         * Get Quiz Settings by ID
         *
         * @param $id
         * @return array|mixed|null
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

        /**
         * iframe for adding new question
         */
        public function modal_window_content() {
            iframe_header();
            wp_enqueue_media();
            require 'partials/modal-add-new-question.php';
            iframe_footer();
            exit;
        }

        /**
         * JSON validator
         *
         * @param $data
         * @return bool
         */
        public function json_validator( $data ) {
            if ( ! empty( $data ) ) {
                @json_decode( $data ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
                return ( json_last_error() === JSON_ERROR_NONE );
            }
            return false;
        }

    }

    $codevery_quiz_admin = new Codevery_Quiz_Admin();
}
