<?php
/**
 * Email List custom table
 *
 * @since      1.0.0
 *
 * @package    Codevery_Quiz
 * @subpackage Codevery_Quiz/admin
 */

if ( ! class_exists( 'WP_List_Table' ) ) {
    require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

if ( ! class_exists( 'Codevery_Quiz_Email_List_Table' ) ) {

    /**
     * Class Codevery_Quiz_Email_List_Table
     */
    class Codevery_Quiz_Email_List_Table extends WP_List_Table {

        /**
         * Codevery_Quiz_Email_List_Table constructor.
         */
        public function __construct() {
            parent::__construct( array(
                'singular' => 'email',
                'plural'   => 'emails',
                'ajax'     => false,
            ) );
        }

        /**
         * Gets a list of columns.
         *
         * @return array|string[]
         */
        public function get_columns() {
            $columns = array(
                'cb'               => '<input type="checkbox" />',
                'email'            => __( 'Email', 'codevery-quiz' ),
                'source'           => __( 'Source', 'codevery-quiz' ),
                'last_passed_quiz' => __( 'Last Passed', 'codevery-quiz' ),
            );

            return $columns;
        }

        /**
         * Gets a list of sortable columns.
         *
         * @return array
         */
        protected function get_sortable_columns() {
            $columns = array(
                'email'            => array( 'email', false ),
                'last_passed_quiz' => array( 'last_passed_quiz', true ),
            );

            return $columns;
        }

        /**
         * The list of bulk actions available for this table.
         *
         * @return array
         */
        protected function get_bulk_actions() {
            $actions = array(
                'delete' => __( 'Delete permanently', 'codevery-quiz' ),
            );

            return $actions;
        }

        /**
         * Define the data that should be displayed in the table
         */
        public function prepare_items() {
            // Define the columns, sortable columns, and the data to display.
            $columns  = $this->get_columns();
            $sortable = $this->get_sortable_columns();
            $per_page = 10;
            $args     = array(
                'post_type'      => 'cquiz_email',
                'posts_per_page' => $per_page,
                'offset'         => ( $this->get_pagenum() - 1 ) * $per_page,
                'orderby'        => 'meta_value',
                'order'          => 'DESC',
                'meta_key'       => '_last_pasted_quiz',
            );

            if ( ! empty( $_REQUEST['s'] ) ) {
                $args['s'] = sanitize_text_field( wp_unslash( $_REQUEST['s'] ) );
            }

            if ( ! empty( $_REQUEST['orderby'] ) ) {
                if ( 'email' == $_REQUEST['orderby'] ) {
                    unset( $args['meta_key'] );
                    $args['orderby'] = 'post_title';
                }
            }

            if ( ! empty( $_REQUEST['order'] )
                && 'asc' == strtolower( sanitize_text_field( wp_unslash( $_REQUEST['order'] ) ) ) ) {
                $args['order'] = 'ASC';
            }

            $q = new WP_Query();
            $posts = $q->query( $args );

            $data = array();
            foreach ( (array) $posts as $post ) {
                $data[] = $this->get_post_obj( $post );
            }

            // Define the pagination.
            $current_page = $this->get_pagenum();
            $total_items  = count( $data );
            $total_pages  = ceil( $total_items / $per_page );

            // Slice the data to display only the current page.
            $data = array_slice( $data, ( ( $current_page - 1 ) * $per_page ), $per_page );

            // Set the properties of the table.
            $this->_column_headers = array( $columns, array(), $sortable );
            $this->items           = $data;
            $this->set_pagination_args( array(
                'total_items' => $total_items,
                'total_pages' => $total_pages,
                'per_page'    => $per_page,
            ) );
        }

        /**
         * Get post object with needed data
         *
         * @param $post
         * @return stdClass
         */
        public function get_post_obj( $post ) {
            $post_obj = new stdClass();
            if ( ! empty( $post ) && $post = get_post( $post ) ) {
                $post_obj->id          = $post->ID;
                $post_obj->email       = $post->post_title;
                $post_obj->source      = get_post_meta( $post->ID, '_source', true );
                $post_obj->last_pasted = get_post_meta( $post->ID, '_last_pasted_quiz', true );
            }

            return $post_obj;
        }

        /**
         * @param array|object $item
         * @return string|void
         */
        protected function column_cb( $item ) {
            return sprintf(
                '<input type="checkbox" name="%1$s[]" value="%2$s" />',
                $this->_args['singular'],
                $item->id
            );
        }

        /**
         * @param $item
         * @return string
         */
        protected function column_email( $item ) {
            return sprintf(
                '<strong>%1$s</strong>',
                esc_html( $item->email )
            );
        }

        /**
         * @param $item
         * @return string
         */
        protected function column_source( $item ) {
            $source = array();
            if ( $sources = json_decode( $item->source ) ) {
                foreach ( $sources as $quiz_id => $count ) {
                    $quiz_title = get_the_title( $quiz_id );
                    $source[] = sprintf(
                        '<a href="%2$s">%1$s</a>',
                        "$quiz_title ($count)",
                        get_edit_post_link( $quiz_id )
                    );
                }
            }
            $output = '';
            foreach ( $source as $item ) {
                $output .= sprintf( '<li>%s</li>', $item );
            }

            return sprintf( '<ul class="contact-source">%s</ul>', $output );
        }

        /**
         * @param $item
         * @return string
         */
        protected function column_last_passed_quiz( $item ) {
            if ( empty( $item->last_pasted )
                || '0000-00-00 00:00:00' === $item->last_pasted ) {
                return '';
            }

            $datetime = date_create_immutable_from_format(
                'Y-m-d H:i:s',
                $item->last_pasted,
                wp_timezone()
            );

            if ( false === $datetime ) {
                return '';
            }

            $t_time = sprintf(
            /* translators: 1: date, 2: time */
                __( '%1$s at %2$s', 'codevery-quiz' ),
                /* translators: date format, see https://www.php.net/date */
                $datetime->format( __( 'Y/m/d', 'codevery-quiz' ) ),
                /* translators: time format, see https://www.php.net/date */
                $datetime->format( __( 'g:i a', 'codevery-quiz' ) )
            );

            return $t_time;
        }

        /**
         * @param array|object $item
         * @param string $column_name
         * @param string $primary
         * @return string
         */
        protected function handle_row_actions( $item, $column_name, $primary ) {
            if ( $column_name !== $primary ) {
                return '';
            }

            $actions = array();

            $link = add_query_arg(
                array(
                    'email'  => $item->id,
                    'action' => 'delete',
                ),
                menu_page_url( 'email_list', false )
            );

            $link = wp_nonce_url( $link, 'delete' );

            if ( current_user_can( 'manage_options', $item->id ) ) {
                $actions['delete'] = sprintf(
                    '<a class="cquiz-delete-email-action" href="%1$s">%2$s</a>',
                    esc_url( $link ),
                    esc_html__( 'Delete' )
                );
            }

            return $this->row_actions( $actions );
        }

    }

}
