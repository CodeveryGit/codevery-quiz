<?php

/**
 * Fired during plugin activation
 *
 * @since      1.0.0
 *
 * @package    Codevery_Quiz
 * @subpackage Codevery_Quiz/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 *
 * @package    Codevery_Quiz
 * @subpackage Codevery_Quiz/includes
 */
class Codevery_Quiz_Activator {

    /**
     * Demo quiz.
     *
     * Long Description.
     *
     * @since    1.0.0
     */
    public static function activate() {
        if ( get_option( 'codevery_quiz' ) ) { return; }
        // Add demo questions.
        $file_name = 'quiz-demo.json';
        if ( get_locale() == 'uk' ) {
            $file_name = 'quiz-demo_uk.json';
        }
        $quiz_demo = file_get_contents( CODEVERY_QUIZ_PLUGIN_DIR_ADMIN . $file_name );
        $quiz_demo = json_decode( $quiz_demo, true );
        $quiz = $quiz_demo['quiz'];
        $quiz_settings = $quiz_demo['quiz_settings'];
        $quiz_questions = $quiz_demo['quiz_questions'];
        $quiz_question_points = $quiz_demo['quiz_question_points'];
        if ( $quiz_questions ) {
            foreach ( $quiz_questions as $key => $question ) {
                $args = array(
                    'post_type'    => 'quiz_question',
                    'post_status'  => 'publish',
                    'post_title'   => $question['post_title'],
                    'post_content' => $question['post_content'],
                );
                $question_id = wp_insert_post( $args );
                update_post_meta( $question_id, 'question_type', $question['question_type'] );
                $question_options = array_map( function( $val ) {
                    $pattern = '/[\[\]{}]/';
                    $val['option'] = htmlspecialchars( preg_replace( $pattern, '', $val['option'] ), ENT_QUOTES );
                    $val['description'] = htmlspecialchars( preg_replace( $pattern, '', $val['description'] ), ENT_QUOTES );
                    $val['image_id'] = $val['image_id'] ? codevery_quiz_upload_file_by_url( CODEVERY_QUIZ_PLUGIN_URI_ASSETS . 'images/' . $val['image_id'] ) : '';
                    return $val;
                }, $question['question_options'] );
                $question_options = wp_json_encode( $question_options, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES );
                update_post_meta( $question_id, 'question_options', $question_options );

                $quiz_question_points[ $key ]['question'] = $question_id;
            }
        }

        // Add demo quiz.
        $args = array(
            'post_type'    => CODEVERY_QUIZ_POST_TYPE,
            'post_status'  => 'publish',
            'post_title'   => $quiz['title'],
            'post_content' => $quiz['content'],
        );
        $demo_quiz_id = wp_insert_post( $args );

        $quiz_image_id = codevery_quiz_upload_file_by_url( CODEVERY_QUIZ_PLUGIN_URI_ASSETS . 'images/' . $quiz['image'] );
        set_post_thumbnail( $demo_quiz_id, $quiz_image_id );

        update_post_meta( $demo_quiz_id, 'quiz_questions', wp_json_encode( $quiz_question_points, JSON_UNESCAPED_UNICODE ) );

        ob_start();
        require CODEVERY_QUIZ_PLUGIN_DIR_ADMIN . 'partials/email-layout.php';
        $default_email = ob_get_clean();

        // fix certificate shortcode id.
        $pattern = '/\[codevery_quiz_certificate (.*?)\]/';
        if ( preg_match( $pattern, $quiz_settings['text_quiz_winner'], $matches ) ) {
            $quiz_settings['text_quiz_winner'] = str_replace( $matches[1], 'quiz_id=' . $demo_quiz_id, $quiz_settings['text_quiz_winner'] );
        }
        // replace image source.
        $quiz_settings = preg_replace( '/{quiz_image_src}/m', CODEVERY_QUIZ_PLUGIN_URI_ASSETS . 'images/', $quiz_settings );

        $quiz_settings = array_map( function( $value ) {
            $value = htmlspecialchars( $value, ENT_QUOTES );
            return $value;
        }, $quiz_settings );

        update_post_meta( $demo_quiz_id, 'quiz_settings', wp_json_encode( $quiz_settings, JSON_UNESCAPED_UNICODE ) );
        update_post_meta( $demo_quiz_id, 'email_layout', preg_replace( "/\r|\n/", '', wp_kses_post( $default_email ) ) );

        update_option( 'codevery_quiz', true );
    }

}
