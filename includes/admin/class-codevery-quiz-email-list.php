<?php
/**
 * Email list functionality
 *
 * @since      1.0.0
 *
 * @package    Codevery_Quiz
 * @subpackage Codevery_Quiz/admin
 */

if ( ! class_exists( 'Codevery_Quiz_Email_List' ) ) {
    /**
     * Class Codevery_Quiz_Email_List
     */
    class Codevery_Quiz_Email_List {

        public function __construct() {

            add_action( 'admin_menu', array( $this, 'add_admin_menu_page' ) );

        }

        /**
         * Add Page to Dashboard Menu
         */
        public function add_admin_menu_page() {

            $email_list_admin = add_submenu_page(
                'codevery-quiz',
                __( 'Email List', 'codevery-quiz' ),
                __( 'Email List', 'codevery-quiz' ),
                'manage_options',
                'email_list',
                array( $this, 'cquiz_email_list_admin_page' )
            );

            add_action( 'load-' . $email_list_admin, array( $this, 'load_emails_list_admin' ), 10 );
        }

        /**
         * Add Page Content
         */
        public function cquiz_email_list_admin_page() {

            if ( ! class_exists( 'Email_List_Table' ) ) {
                require_once CODEVERY_QUIZ_PLUGIN_DIR_ADMIN . 'class-email-list-table.php';
            }
            $list_table = new Email_List_Table();
            $list_table->prepare_items();

            ?>
            <div class="wrap">

                <h1 class="wp-heading-inline"><?php echo esc_html( __( 'Quiz Emails', 'codevery-quiz' ) ); ?></h1>

                <?php
                if ( ! empty( $_REQUEST['s'] ) ) {
                    echo sprintf( '<span class="subtitle">'
                        . __( 'Search results for &#8220;%s&#8221;', 'codevery-quiz' )
                        . '</span>', esc_html( wp_unslash( $_REQUEST['s'] ) ) );
                }
                ?>

                <hr class="wp-header-end">

                <?php
                $message_type = isset( $_REQUEST['message'] ) ? $_REQUEST['message'] : '';
                if ( 'emaildeleted' == $message_type ) {
                    $message = __( 'Email deleted.', 'codevery-quiz' );
                    echo sprintf(
                        '<div id="message" class="notice notice-success is-dismissible"><p>%s</p></div>',
                        esc_html( $message )
                    );
                } elseif ( 'emailsdeleted' == $message_type ) {
                    $message = __( 'Emails deleted.', 'codevery-quiz' );
                    echo sprintf(
                        '<div id="message" class="notice notice-success is-dismissible"><p>%s</p></div>',
                        esc_html( $message )
                    );
                }
                ?>

                <div class="cquiz-export-wrap">
                    <input type="submit" name="export" id="cquiz_email_export" class="cquiz-button" value="<?php esc_html_e( 'Export', 'codevery-quiz' ); ?>">
                    <p class="description"><?php esc_html_e( 'Export your email list as a CSV file quickly and easily.', 'codevery-quiz' ); ?></p>
                </div>

                <form method="get" action="">
                    <input type="hidden" name="page" value="<?php echo ! empty( $_REQUEST['page'] ) ? esc_attr( wp_unslash( $_REQUEST['page'] ) ) : ''; ?>" />
                    <?php $list_table->search_box( esc_html__( 'Search emails', 'codevery-quiz' ), 'cquiz-email' ); ?>
                    <?php $list_table->display(); ?>
                </form>

            </div>
            <?php
        }

        function load_emails_list_admin() {

            $action = '';
            if ( isset( $_REQUEST['action'] ) and -1 != $_REQUEST['action'] ) {
                $action =  $_REQUEST['action'];
            }

            $redirect_to = menu_page_url( 'email_list', false );

            if ( ! empty( $_REQUEST['page'] ) && $_REQUEST['page'] == 'email_list' && 'delete' == $action && ! empty( $_REQUEST['email'] ) ) {
                $deleted = 0;

                foreach ( (array) $_REQUEST['email'] as $email ) {
                    $post = get_post( $email );

                    if ( empty( $post ) ) {
                        continue;
                    }

                    if ( ! current_user_can( 'manage_options', $post->ID ) ) {
                        wp_die( __( 'You are not allowed to delete this item.', 'codevery-quiz' ) );
                    }

                    if ( ! wp_delete_post( $post->ID, true ) ) {
                        wp_die( __( 'Error in deleting.', 'codevery-quiz' ) );
                    }

                    $deleted += 1;
                }

                if ( ! empty( $deleted ) ) {
                    $message_type = $deleted > 1 ? 'emailsdeleted' : 'emaildeleted';
                    $redirect_to = add_query_arg(
                        array( 'message' => $message_type ), $redirect_to );
                }

                wp_safe_redirect( $redirect_to );
                exit();
            }

        }

        public function get_email_post( $post = null ) {
            $post_obj = new stdClass();
            if ( ! empty( $post ) and $post = get_post( $post ) ) {
                $post_obj->id = $post->ID;
                $post_obj->email = $post->post_title;
                $post_obj->source = get_post_meta( $post->ID, '_source', true );
                $post_obj->last_contacted = get_post_meta( $post->ID, '_last_pasted_quiz', true );
            }

            return $post_obj;
        }

    }

    $codevery_quiz_email_list = new Codevery_Quiz_Email_List();
}
