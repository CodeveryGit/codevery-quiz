<?php
/**
 * Register custom post type
 *
 * @since      1.0.0
 *
 * @package    Codevery_Quiz
 * @subpackage Codevery_Quiz/includes
 */

if ( ! class_exists( 'Codevery_Quiz_Post_Types' ) ) {

    class Codevery_Quiz_Post_Types {

        public function __construct() {

            add_action( 'init', array( $this, 'create_custom_post_type' ), 999 );

            // Custom columns.
            add_action( 'manage_' . CODEVERY_QUIZ_POST_TYPE . '_posts_columns', array( $this, 'set_custom_edit_quiz_columns' ), 10, 2 );
            add_filter( 'manage_quiz_question_posts_columns', array( $this, 'manage_quiz_question_posts_columns' ) );
            add_action( 'manage_posts_custom_column', array( $this, 'custom_quiz_column' ), 10, 2 );

            // Remove menu item.
            add_action( 'admin_menu', array( $this, 'remove_admin_menu_item' ) );

            add_filter( 'use_block_editor_for_post_type', array( $this, 'use_classic_editor' ), 10, 2 );

        }

        /**
         * Registers post types used for this plugin.
         */
        public function create_custom_post_type() {

            /** Quiz */
            $args = array(
                'label'               => __( 'Quiz', 'codevery-quiz' ),
                'labels'              => array(
                    'name'           => _x( 'Quiz', 'Post Type General Name', 'codevery-quiz' ),
                    'singular_name'  => _x( 'Quiz', 'Post Type Singular Name', 'codevery-quiz' ),
                    'menu_name'      => __( 'Quizzes', 'codevery-quiz' ),
                    'name_admin_bar' => __( 'Quiz', 'codevery-quiz' ),
                    'all_items'      => __( 'Quizzes', 'codevery-quiz' ),
                    'add_new'        => __( 'Add New Quiz', 'codevery-quiz' ),
                    'add_new_item'   => __( 'Add New Quiz', 'codevery-quiz' ),
                    'new_item'       => __( 'New Quiz', 'codevery-quiz' ),
                    'edit_item'      => __( 'Edit Quiz', 'codevery-quiz' ),
                    'view_item'      => __( 'View Quiz', 'codevery-quiz' ),
                ),
                'supports'            => array( 'title', 'editor', 'thumbnail' ),
                'hierarchical'        => false,
                'public'              => false,
                'show_ui'             => true,
                'show_in_menu'        => 'codevery-quiz',
                'menu_position'       => 19,
                'menu_icon'           => 'dashicons-list-view',
                'show_in_admin_bar'   => true,
                'show_in_nav_menus'   => false,
                'show_in_rest'        => true,
                'can_export'          => true,
                'has_archive'         => false,
                'exclude_from_search' => true,
                'publicly_queryable'  => false,
                'capability_type'     => 'page',
            );
            register_post_type( CODEVERY_QUIZ_POST_TYPE, $args );

            /** Quiz Questions */
            $args = array(
                'label'               => __( 'Question', 'codevery-quiz' ),
                'labels'              => array(
                    'name'           => _x( 'Questions', 'Post Type General Name', 'codevery-quiz' ),
                    'singular_name'  => _x( 'Question', 'Post Type Singular Name', 'codevery-quiz' ),
                    'menu_name'      => __( 'Questions', 'codevery-quiz' ),
                    'name_admin_bar' => __( 'Questions', 'codevery-quiz' ),
                    'all_items'      => __( 'Questions', 'codevery-quiz' ),
                    'add_new'        => __( 'Add New Question', 'codevery-quiz' ),
                    'add_new_item'   => __( 'Add New Question', 'codevery-quiz' ),
                    'new_item'       => __( 'New Question', 'codevery-quiz' ),
                    'edit_item'      => __( 'Edit Question', 'codevery-quiz' ),
                    'view_item'      => __( 'View Question', 'codevery-quiz' ),
                ),
                'supports'            => array( 'title', 'editor' ),
                'hierarchical'        => false,
                'public'              => false,
                'show_ui'             => true,
                'show_in_menu'        => 'codevery-quiz',
                'menu_position'       => 20,
                'menu_icon'           => 'dashicons-format-chat',
                'show_in_admin_bar'   => true,
                'show_in_nav_menus'   => false,
                'show_in_rest'        => true,
                'can_export'          => true,
                'has_archive'         => false,
                'exclude_from_search' => true,
                'publicly_queryable'  => false,
                'capability_type'     => 'page',
            );
            register_post_type( 'quiz_question', $args );

            /** Emails list */
            register_post_type( 'cquiz_email', array(
                'labels'            => array(
                    'name'          => __( 'Quiz Contacts', 'codevery-quiz' ),
                    'singular_name' => __( 'Quiz Contact', 'codevery-quiz' ),
                ),
                'label'             => __( 'Quiz Contacts', 'codevery-quiz' ),
                'rewrite'           => false,
                'query_var'         => false,
                'show_ui'           => true,
                'supports'          => array(),
                'hierarchical'      => false,
                'public'            => false,
                'show_in_admin_bar' => false,
                'show_in_nav_menus' => false,
                'show_in_menu'      => false,
                'capability_type'   => 'post',
            ) );

        }

        /**
         * Add the custom columns to the quiz post type
         *
         * @param $columns
         * @return mixed
         */
        public function set_custom_edit_quiz_columns( $columns ) {
            $old_column       = $columns['date'];
            $old_title_column = $columns['title'];
            unset( $columns['date'] );
            unset( $columns['title'] );

            $columns['quiz_image'] = __( 'Image', 'codevery-quiz' );
            $columns['title']      = $old_title_column;
            $columns['shortcode']  = __( 'Shortcode', 'codevery-quiz' );
            $columns['usage']      = __( 'Usage', 'codevery-quiz' );
            $columns['date']       = $old_column;

            return $columns;
        }

        /**
         * Add the custom columns to the quiz_question post type
         *
         * @param $columns
         * @return mixed
         */
        public function manage_quiz_question_posts_columns( $columns ) {
            $old_column = $columns['date'];
            unset( $columns['date'] );
            $columns['question_type'] = __( 'Question Type', 'codevery-quiz' );

            $columns['date'] = $old_column;

            return $columns;
        }

        /**
         * Add the data to the custom columns for the quiz post type
         *
         * @param $column
         * @param $post_id
         */
        public function custom_quiz_column( $column, $post_id ) {
            switch ( $column ) {

                case 'quiz_image':
                    $quiz_image = get_the_post_thumbnail_url( $post_id, array( 100, 100 ) );
                    echo $quiz_image ? '<img src="' . esc_attr( $quiz_image ) . '" alt="' . esc_attr( get_the_title() ) . '" width="100" height="100" >' : '<div class="cquiz-default-image-column"><span class="dashicons dashicons-format-image" ></span></div>';
                    break;
                case 'shortcode':
                    echo '<code id="cquiz_shortcode_' . esc_attr( $post_id ) . '" class="cquiz-blue-bg">[codevery_quiz id=' . esc_html( $post_id ) . ']</code><a href="#" id="copy_shortcode" class="copy_shortcode" title="Copy to Clipboard" data-copied_text_id="#cquiz_shortcode_' . esc_attr( $post_id ) . '" ><span class="dashicons dashicons-admin-page"></span></a>';
                    break;
                case 'usage':
                    $shortcode = "[codevery_quiz id=$post_id]";
                    $pages     = $this->find_pages_with_shortcode( $shortcode );
                    if ( ! empty( $pages ) ) {
                        echo '<ul>';
                        foreach ( $pages as $page ) {
                            $page_title = $page->post_title ? $page->post_title : __( '(no title)' );
                            echo '<li><a href="' . esc_url( get_permalink( $page->ID ) ) . '">' . esc_html( $page_title ) . '</a></li>';
                        }
                        echo '</ul>';
                    }
                    break;
                case 'question_type':
                    echo esc_html( ucfirst( get_post_meta( $post_id, 'question_type', true ) ) );
                    break;

            }
        }

        /**
         * Remove submenu item 'Add New'
         */
        public function remove_admin_menu_item() {
            remove_submenu_page( 'edit.php?post_type=quiz', 'post-new.php?post_type=quiz' );
        }

        /**
         * Use classic editor for Quizzes and Questions
         *
         * @param $use_classic_editor
         * @param $post_type
         * @return bool
         */
        public function use_classic_editor( $use_classic_editor, $post_type ) {
            if ( CODEVERY_QUIZ_POST_TYPE === $post_type || 'quiz_question' === $post_type ) {
                return false;
            }
            return $use_classic_editor;
        }

        /**
         * Find all pages with a specific shortcode in their content
         *
         * @param $shortcode
         * @return array|object|null
         */
        public function find_pages_with_shortcode( $shortcode ) {
            global $wpdb;
            $pages_with_shortcode = $wpdb->get_results(
                $wpdb->prepare(
                    "SELECT ID, post_title 
                    FROM $wpdb->posts 
                    WHERE ( post_type = 'page' OR post_type = 'post' )
                    AND post_status = 'publish' 
                    AND post_content LIKE %s",
                    '%' . $wpdb->esc_like( $shortcode ) . '%'
                )
            );

            return $pages_with_shortcode;
        }

    }

    $codevery_quiz_post_types = new Codevery_Quiz_Post_Types();
}
