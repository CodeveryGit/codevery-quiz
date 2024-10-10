<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly. ?>

<div>
    <div class="tabs-nav">
        <a href="#questions" class="active">
            <i class="dashicons dashicons-insert"></i>
            <span><?php esc_html_e( 'Questions', 'codevery-quiz' ); ?></span>
        </a>
        <a href="#settings">
            <i class="dashicons dashicons-admin-generic"></i>
            <span><?php esc_html_e( 'Settings', 'codevery-quiz' ); ?></span>
        </a>
        <a href="#coupon">
            <i class="dashicons dashicons-tickets-alt"></i>
            <span><?php esc_html_e( 'Coupon', 'codevery-quiz' ); ?></span>
        </a>
    </div>
    <div class="tabs-content">
        <div id="questions" class="tab-content" style="display: block">
            <?php if ( $questions ) : ?>
                <div id="quiz-repeater" class="quiz-repeater">
                    <div id="quiz-repeater-items">
                        <div class="item-hidden" data-group="question">
                            <!-- Repeater Content -->
                            <div class="item-wrap">
                                <div class="item-head">
                                    <svg class="drag-item" width="20" height="20" viewBox="0 0 15 15" fill="none" xmlns="http://www.w3.org/2000/svg" id="IconChangeColor"> <path fill-rule="evenodd" clip-rule="evenodd" d="M5.5 4.625C6.12132 4.625 6.625 4.12132 6.625 3.5C6.625 2.87868 6.12132 2.375 5.5 2.375C4.87868 2.375 4.375 2.87868 4.375 3.5C4.375 4.12132 4.87868 4.625 5.5 4.625ZM9.5 4.625C10.1213 4.625 10.625 4.12132 10.625 3.5C10.625 2.87868 10.1213 2.375 9.5 2.375C8.87868 2.375 8.375 2.87868 8.375 3.5C8.375 4.12132 8.87868 4.625 9.5 4.625ZM10.625 7.5C10.625 8.12132 10.1213 8.625 9.5 8.625C8.87868 8.625 8.375 8.12132 8.375 7.5C8.375 6.87868 8.87868 6.375 9.5 6.375C10.1213 6.375 10.625 6.87868 10.625 7.5ZM5.5 8.625C6.12132 8.625 6.625 8.12132 6.625 7.5C6.625 6.87868 6.12132 6.375 5.5 6.375C4.87868 6.375 4.375 6.87868 4.375 7.5C4.375 8.12132 4.87868 8.625 5.5 8.625ZM10.625 11.5C10.625 12.1213 10.1213 12.625 9.5 12.625C8.87868 12.625 8.375 12.1213 8.375 11.5C8.375 10.8787 8.87868 10.375 9.5 10.375C10.1213 10.375 10.625 10.8787 10.625 11.5ZM5.5 12.625C6.12132 12.625 6.625 12.1213 6.625 11.5C6.625 10.8787 6.12132 10.375 5.5 10.375C4.87868 10.375 4.375 10.8787 4.375 11.5C4.375 12.1213 4.87868 12.625 5.5 12.625Z" fill="#737373" id="mainIconPathAttribute"></path> </svg>
                                    <div class="item-order-number"></div>
                                    <div class="item-title"><?php esc_html_e( 'Choose Question', 'codevery-quiz' ); ?></div>
                                    <span class="dashicons dashicons-arrow-down-alt2 edit-item"></span>
                                </div>
                                <div class="item-body">
                                    <div class="item-body-wrap">
                                        <div class="item-body-content">
                                            <div class="question">
                                                <div class="existing-question">
                                                    <label class="control-label"><?php esc_html_e( 'Question', 'codevery-quiz' ); ?> </br>
                                                        <select class="quiz-question form-control" data-name="question" style="width:100%;">
                                                            <option value=""><?php esc_html_e( 'Choose Question', 'codevery-quiz' ); ?></option>
                                                            <?php foreach ( $questions as $question ) : ?>
                                                                <option value="<?php echo esc_attr( $question->ID ); ?>"><?php echo esc_attr( $question->post_title ); ?></option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                    </label>
                                                </div>
                                                <div class="new-question">
                                                    <?php
                                                    $modal_nonce = wp_create_nonce( 'cquiz_modal_window_nonce' );
                                                    $url = add_query_arg( array( 'action' => 'cquiz_modal_window', 'nonce' => $modal_nonce ), admin_url( 'admin.php' ) );
                                                    echo '<a href="' . esc_attr( $url ) . '" class="cquiz-modal" title="' . esc_html__( 'Add New Question', 'codevery-quiz' ) . '">' . esc_html__( '+ Add New Question', 'codevery-quiz' ) . '</a>';
                                                    ?>
                                                </div>

                                            </div>
                                            <div class="points">
                                                <label class="control-label"><?php esc_html_e( 'Points', 'codevery-quiz' ); ?></label>
                                                <input type="number" class="form-control" min="0" data-name="points" value="0">
                                            </div>
                                            <!-- Repeater Remove Btn -->
                                            <div class="remove-item-btn">
                                                <a href="#" type="button" class="btn-danger remove-item" >
                                                    <span class="dashicons dashicons-trash"></span><span class="hidden"><?php esc_html_e( 'Remove', 'codevery-quiz' ); ?></span>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php
                        if ( is_array( $quiz_questions ) ) :
                            foreach ( $quiz_questions as $index => $quiz_question ) :
                                $question_data = array_filter( $questions, function( $var ) use ( $quiz_question ) {
                                    return $quiz_question['question'] == $var->ID ? $var->post_title : false;
                                });
                                $question_data = array_values( $question_data );
                                $question_title = isset( $question_data[0] ) ? $question_data[0]->post_title : '';
                                ?>
                                <div class="item" data-index="<?php echo esc_attr( $index ); ?>">
                                    <div class="item-wrap">
                                        <div class="item-head">
                                            <svg class="drag-item" width="20" height="20" viewBox="0 0 15 15" fill="none" xmlns="http://www.w3.org/2000/svg" id="IconChangeColor"><path fill-rule="evenodd" clip-rule="evenodd" d="M5.5 4.625C6.12132 4.625 6.625 4.12132 6.625 3.5C6.625 2.87868 6.12132 2.375 5.5 2.375C4.87868 2.375 4.375 2.87868 4.375 3.5C4.375 4.12132 4.87868 4.625 5.5 4.625ZM9.5 4.625C10.1213 4.625 10.625 4.12132 10.625 3.5C10.625 2.87868 10.1213 2.375 9.5 2.375C8.87868 2.375 8.375 2.87868 8.375 3.5C8.375 4.12132 8.87868 4.625 9.5 4.625ZM10.625 7.5C10.625 8.12132 10.1213 8.625 9.5 8.625C8.87868 8.625 8.375 8.12132 8.375 7.5C8.375 6.87868 8.87868 6.375 9.5 6.375C10.1213 6.375 10.625 6.87868 10.625 7.5ZM5.5 8.625C6.12132 8.625 6.625 8.12132 6.625 7.5C6.625 6.87868 6.12132 6.375 5.5 6.375C4.87868 6.375 4.375 6.87868 4.375 7.5C4.375 8.12132 4.87868 8.625 5.5 8.625ZM10.625 11.5C10.625 12.1213 10.1213 12.625 9.5 12.625C8.87868 12.625 8.375 12.1213 8.375 11.5C8.375 10.8787 8.87868 10.375 9.5 10.375C10.1213 10.375 10.625 10.8787 10.625 11.5ZM5.5 12.625C6.12132 12.625 6.625 12.1213 6.625 11.5C6.625 10.8787 6.12132 10.375 5.5 10.375C4.87868 10.375 4.375 10.8787 4.375 11.5C4.375 12.1213 4.87868 12.625 5.5 12.625Z" fill="#737373" id="mainIconPathAttribute"></path> </svg>
                                            <div class="item-order-number"><?php echo esc_html( $index + 1 ); ?></div>
                                            <div class="item-title"><?php echo esc_html( $question_title ); ?></div>
                                            <span class="dashicons dashicons-arrow-down-alt2 edit-item"></span>
                                        </div>
                                        <div class="item-body">
                                            <div class="item-body-wrap">
                                                <div class="item-body-content">
                                                    <div class="question">
                                                        <div class="existing-question">
                                                            <label class="control-label"><?php esc_html_e( 'Question', 'codevery-quiz' ); ?></br>
                                                                <select class="quiz-question" data-name="question" name="question[<?php echo esc_attr( $index ); ?>][question]" id="question_<?php echo esc_attr( $index ); ?>_question" style="width:100%;">
                                                                    <option value=""><?php esc_html_e( 'Choose Question', 'codevery-quiz' ); ?></option>
                                                                    <?php foreach ( $questions as $question ) : ?>
                                                                        <option value="<?php echo esc_attr( $question->ID ); ?>" <?php selected( $quiz_question['question'], $question->ID ); ?> ><?php echo esc_attr( $question->post_title ); ?></option>
                                                                    <?php endforeach; ?>
                                                                </select>
                                                            </label>
                                                        </div>
                                                    </div>
                                                    <div class="points">
                                                        <label class="control-label"><?php esc_html_e( 'Points', 'codevery-quiz' ); ?></label>
                                                        <input type="number" class="form-control" min="0" data-name="points" name="question[<?php echo esc_attr( $index ); ?>][points]" id="question_<?php echo esc_attr( $index ); ?>_points" value="<?php echo $quiz_question['points'] ? absint( $quiz_question['points'] ) : 0; ?>">
                                                    </div>
                                                    <!-- Repeater Remove Btn -->
                                                    <div class="remove-item-btn">
                                                        <a href="#" type="button" class="btn-danger remove-item" >
                                                            <span class="dashicons dashicons-trash"></span><span class="hidden"><?php esc_html_e( 'Remove', 'codevery-quiz' ); ?></span>
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else : ?>
                            <div class="item" data-index="0">
                                <!-- Repeater Content -->
                                <div class="item-wrap">
                                    <div class="item-head">
                                        <svg class="drag-item" width="20" height="20" viewBox="0 0 15 15" fill="none" xmlns="http://www.w3.org/2000/svg" id="IconChangeColor"> <path fill-rule="evenodd" clip-rule="evenodd" d="M5.5 4.625C6.12132 4.625 6.625 4.12132 6.625 3.5C6.625 2.87868 6.12132 2.375 5.5 2.375C4.87868 2.375 4.375 2.87868 4.375 3.5C4.375 4.12132 4.87868 4.625 5.5 4.625ZM9.5 4.625C10.1213 4.625 10.625 4.12132 10.625 3.5C10.625 2.87868 10.1213 2.375 9.5 2.375C8.87868 2.375 8.375 2.87868 8.375 3.5C8.375 4.12132 8.87868 4.625 9.5 4.625ZM10.625 7.5C10.625 8.12132 10.1213 8.625 9.5 8.625C8.87868 8.625 8.375 8.12132 8.375 7.5C8.375 6.87868 8.87868 6.375 9.5 6.375C10.1213 6.375 10.625 6.87868 10.625 7.5ZM5.5 8.625C6.12132 8.625 6.625 8.12132 6.625 7.5C6.625 6.87868 6.12132 6.375 5.5 6.375C4.87868 6.375 4.375 6.87868 4.375 7.5C4.375 8.12132 4.87868 8.625 5.5 8.625ZM10.625 11.5C10.625 12.1213 10.1213 12.625 9.5 12.625C8.87868 12.625 8.375 12.1213 8.375 11.5C8.375 10.8787 8.87868 10.375 9.5 10.375C10.1213 10.375 10.625 10.8787 10.625 11.5ZM5.5 12.625C6.12132 12.625 6.625 12.1213 6.625 11.5C6.625 10.8787 6.12132 10.375 5.5 10.375C4.87868 10.375 4.375 10.8787 4.375 11.5C4.375 12.1213 4.87868 12.625 5.5 12.625Z" fill="#737373" id="mainIconPathAttribute"></path> </svg>
                                        <div class="item-order-number"></div>
                                        <div class="item-title"><?php esc_html_e( 'Choose Question', 'codevery-quiz' ); ?></div>
                                        <span class="dashicons dashicons-arrow-down-alt2 edit-item"></span>
                                    </div>
                                    <div class="item-body">
                                        <div class="item-body-wrap">
                                            <div class="item-body-content">
                                                <div class="question">
                                                    <div class="existing-question">
                                                        <label class="control-label"><?php esc_html_e( 'Question', 'codevery-quiz' ); ?> </br>
                                                            <select class="quiz-question form-control" data-name="question" name="question[0][question]" id="question_0_question"  style="width:100%;">
                                                                <option value=""><?php esc_html_e( 'Choose Question', 'codevery-quiz' ); ?></option>
                                                                <?php foreach ( $questions as $question ) : ?>
                                                                    <option value="<?php echo esc_attr( $question->ID ); ?>"><?php echo esc_attr( $question->post_title ); ?></option>
                                                                <?php endforeach; ?>
                                                            </select>
                                                        </label>
                                                    </div>
                                                    <div class="new-question">
                                                        <?php
                                                        $modal_nonce = wp_create_nonce( 'cquiz_modal_window_nonce' );
                                                        $url = add_query_arg( array( 'action' => 'cquiz_modal_window', 'nonce' => $modal_nonce ), admin_url( 'admin.php' ) );
                                                        echo '<a href="' . esc_attr( $url ) . '" class="cquiz-modal" title="' . esc_html__( 'Add New Question', 'codevery-quiz' ) . '">' . esc_html__( '+ Add New Question', 'codevery-quiz' ) . '</a>';
                                                        ?>
                                                    </div>

                                                </div>
                                                <div class="points">
                                                    <label class="control-label"><?php esc_html_e( 'Points', 'codevery-quiz' ); ?></label>
                                                    <input type="number" class="form-control" min="0" data-name="points" name="question[0][points]" id="question_0_points" value="0">
                                                </div>
                                                <!-- Repeater Remove Btn -->
                                                <div class="remove-item-btn">
                                                    <a href="#" type="button" class="btn-danger remove-item" >
                                                        <span class="dashicons dashicons-trash"></span><span class="hidden"><?php esc_html_e( 'Remove', 'codevery-quiz' ); ?></span>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="repeater-heading">
                        <button type="button" class="cquiz-button cquiz-add-item">
                            <?php esc_html_e( 'Add Question', 'codevery-quiz' ); ?>
                        </button>
                    </div>
                </div>
            <?php
            else :
                printf(
                /* translators: 1: URL to Add New Question screen. */
                    esc_html__( 'You need to create questions first. Click %1$s to create a new question.', 'codevery-quiz' ),
                    '<a href="' . esc_url( admin_url( 'post-new.php?post_type=quiz_question' ) ) . '" target="_parent">' . esc_html__( 'here', 'codevery-quiz' ) . '</a>'
                );
            endif;
            ?>
        </div><!-- #questions -->

        <div id="settings" class="tab-content">
            <div class="cquiz-settings-table">

                <div class="cquiz-settings-row">
                    <label for="winner_points" class="post-attributes-label"><?php esc_html_e( 'Winner points', 'codevery-quiz' ); ?></label>
                    <input type="number" id="winner_points" name="winner_points" class="cquiz-input" min="0" value="<?php echo isset( $quiz_settings['winner_points'] ) ? absint( $quiz_settings['winner_points'] ) : ''; ?>">
                </div>

                <div class="cquiz-settings-row">
                    <label for="start_button_text" class="post-attributes-label"><?php esc_html_e( 'Start button text', 'codevery-quiz' ); ?></label>
                    <input type="text" id="start_button_text" name="start_button_text" class="cquiz-input" value="<?php echo isset( $quiz_settings['start_button_text'] ) ? esc_html( $quiz_settings['start_button_text'] ) : esc_html__( 'Start', 'codevery-quiz' ); ?>">
                </div>

                <div class="cquiz-settings-row">
                    <label for="progress_bar" class="post-attributes-label"><?php esc_html_e( 'Progress Bar', 'codevery-quiz' ); ?></label>
                    <label for="progress_bar" class="cquiz-toggle-label">
                        <input type="checkbox" id="progress_bar" name="progress_bar" class="cquiz-input cquiz-toggle-input" value="yes" <?php checked( $quiz_settings['progress_bar'], 'yes' ); ?> >
                        <span class="cquiz-toggle"></span>
                    </label>
                </div>

                <div class="cquiz-settings-row">
                    <label for="quiz_timer" class="post-attributes-label"><?php esc_html_e( 'Timer', 'codevery-quiz' ); ?></label>
                    <label for="quiz_timer" class="cquiz-toggle-label">
                        <input type="checkbox" id="quiz_timer" name="quiz_timer" class="cquiz-input cquiz-has-dependent-fields cquiz-toggle-input" value="yes" <?php checked( $quiz_settings['quiz_timer'], 'yes' ); ?> >
                        <span class="cquiz-toggle"></span>
                    </label>
                </div>

                <div class="cquiz-settings-row quiz_timer-fields" <?php echo isset( $quiz_settings['quiz_timer'] ) && $quiz_settings['quiz_timer'] == 'yes' ? '' : 'style="display:none;"'; ?>>
                    <label for="quiz_time" class="post-attributes-label"><?php esc_html_e( 'Time (in seconds)', 'codevery-quiz' ); ?></label>
                    <input type="number" id="quiz_time" name="quiz_time" class="cquiz-input" value="<?php echo isset( $quiz_settings['quiz_time'] ) ? esc_html( $quiz_settings['quiz_time'] ) : 600; ?>">
                </div>

                <div class="cquiz-settings-row">
                    <label for="title_quiz_winner" class="post-attributes-label"><?php esc_html_e( 'Success title', 'codevery-quiz' ); ?></label>
                    <input type="text" id="title_quiz_winner" name="title_quiz_winner" class="cquiz-input" value="<?php echo isset( $quiz_settings['title_quiz_winner'] ) ? esc_html( $quiz_settings['title_quiz_winner'] ) : ''; ?>">
                </div>

                <div class="cquiz-settings-row">
                    <label for="text_quiz_winner" class="post-attributes-label"><?php esc_html_e( 'Success description', 'codevery-quiz' ); ?></label>
                    <?php
                    $text_quiz_winner = isset( $quiz_settings['text_quiz_winner'] ) ? wp_kses_post( $quiz_settings['text_quiz_winner'] ) : '';
                    wp_editor( $text_quiz_winner, 'text_quiz_winner', array(
                        'wpautop'       => false,
                        'media_buttons' => false,
                        'textarea_name' => 'text_quiz_winner',
                        'editor_height' => 230,
                        'textarea_rows' => 10,
                        'teeny'         => true,
                    ) );
                    ?>
                </div>

                <div class="cquiz-settings-row">
                    <label for="title_quiz_looser" class="post-attributes-label"><?php esc_html_e( 'Fail title', 'codevery-quiz' ); ?></label>
                    <input type="text" id="title_quiz_looser" name="title_quiz_looser" class="cquiz-input" value="<?php echo isset( $quiz_settings['title_quiz_looser'] ) ? esc_html( $quiz_settings['title_quiz_looser'] ) : ''; ?>">
                </div>

                <div class="cquiz-settings-row">
                    <label for="text_quiz_looser" class="post-attributes-label"><?php esc_html_e( 'Fail description', 'codevery-quiz' ); ?></label>
                    <?php
                    $text_quiz_looser = isset( $quiz_settings['text_quiz_looser'] ) ? wp_kses_post( $quiz_settings['text_quiz_looser'] ) : '';
                    wp_editor( $text_quiz_looser, 'text_quiz_looser', array(
                        'wpautop'       => false,
                        'media_buttons' => false,
                        'textarea_name' => 'text_quiz_looser',
                        'editor_height' => 230,
                        'textarea_rows' => 10,
                        'teeny'         => true,
                    ) );
                    ?>
                </div>

                <h4><?php esc_html_e( 'Colors', 'codevery-quiz' ); ?></h4>
                <hr><br>

                <div class="cquiz-settings-row">
                    <label for="progress_bar_color" class="post-attributes-label"><?php esc_html_e( 'Progress Bar', 'codevery-quiz' ); ?></label>
                    <input class="color_field progress_bar_color" type="text" name="progress_bar_color" id="progress_bar_color" value="<?php echo esc_attr( $quiz_settings['progress_bar_color'] ); ?>"/>
                </div>

                <div class="cquiz-settings-row">
                    <label for="hover_answer_color" class="post-attributes-label"><?php esc_html_e( 'Option Button (on hover)', 'codevery-quiz' ); ?></label>
                    <input class="color_field hover_answer_color" type="text" name="hover_answer_color" id="hover_answer_color" value="<?php echo esc_attr( $quiz_settings['hover_answer_color'] ); ?>"/>
                </div>

                <div class="cquiz-settings-row">
                    <label for="correct_answer_color" class="post-attributes-label"><?php esc_html_e( 'Correct Answer', 'codevery-quiz' ); ?></label>
                    <input class="color_field correct_answer_color" type="text" name="correct_answer_color" id="correct_answer_color" value="<?php echo esc_attr( $quiz_settings['correct_answer_color'] ); ?>"/>
                </div>

                <div class="cquiz-settings-row">
                    <label for="incorrect_answer_color" class="post-attributes-label"><?php esc_html_e( 'Incorrect Answer', 'codevery-quiz' ); ?></label>
                    <input class="color_field incorrect_answer_color" type="text" name="incorrect_answer_color" id="incorrect_answer_color" value="<?php echo esc_attr( $quiz_settings['incorrect_answer_color'] ); ?>"/>
                </div>

            </div>
        </div><!-- #settings -->

        <div id="coupon" class="tab-content">

            <style>
                .quiz-certificate .certificate-sale-title,
                .quiz-certificate .sale-percent {
                    color: <?php echo esc_html( $quiz_settings['highlighted_color'] ); ?>;
                }
                .quiz-certificate .ribbon-bg,
                .quiz-certificate .promocode .promo {
                    background-color: <?php echo esc_html( $quiz_settings['highlighted_color'] ); ?>;
                }
                .quiz-certificate .bow-bg svg {
                    fill: <?php echo esc_html( $quiz_settings['highlighted_color'] ); ?>;
                }
            </style>
            <div id="coupon-options">
                <?php if ( ! is_plugin_active( 'woocommerce/woocommerce.php' ) ) : ?>
                    <div class="cquiz-error-notification">
                        <?php /* translators: %s: Plugin name. */ echo sprintf( esc_html__( 'The %s plugin must be active for the coupon functionality.', 'codevery-quiz' ), '<b>' . esc_html__( 'WooCommerce' ) . '</b>' ); ?>
                    </div>
                <?php endif; ?>
                <div class="quiz-certificate-shortcode">
                    <p><?php esc_html_e( 'If you want to add a coupon to your quiz, copy this shortcode and paste it into the success description:', 'codevery-quiz' ); ?></p>
                    <code id="quiz_certificate_shortcode">[codevery_quiz_certificate quiz_id=<?php echo esc_html( $post_object->ID ); ?>]</code><a href="#" id="copy_shortcode" class="copy_shortcode" title="Copy to Clipboard" data-copied_text_id="#quiz_certificate_shortcode" ><span class="dashicons dashicons-admin-page"></span></a>
                </div>
                <div class="cquiz-settings-row">
                    <label for="expiration_date" class="post-attributes-label"><?php esc_html_e( 'Coupon amount, %', 'codevery-quiz' ); ?></label>
                    <input type="number" id="cquiz_coupon_amount" name="coupon_amount" value="<?php echo esc_attr( $quiz_settings['coupon_amount'] ); ?>" min="0" max="99" />
                </div>
                <div class="cquiz-settings-row">
                    <label for="expiration_date" class="post-attributes-label"><?php esc_html_e( 'Coupon expiration date', 'codevery-quiz' ); ?></label>
                    <select name="expiration_date" id="expiration_date">
                        <option value="+1 weeks" <?php selected( $expiration_date, '+1 weeks' ); ?>><?php esc_html_e( '1 week', 'codevery-quiz' ); ?></option>
                        <option value="+2 weeks" <?php selected( $expiration_date, '+2 weeks' ); ?>><?php esc_html_e( '2 weeks', 'codevery-quiz' ); ?></option>
                        <option value="+3 weeks" <?php selected( $expiration_date, '+3 weeks' ); ?>><?php esc_html_e( '3 weeks', 'codevery-quiz' ); ?></option>
                        <option value="+4 weeks" <?php selected( $expiration_date, '+4 weeks' ); ?>><?php esc_html_e( '4 weeks', 'codevery-quiz' ); ?></option>
                        <?php // TODO filter. ?>
                    </select>
                </div>
                <div class="cquiz-settings-row">
                    <label for="coupon_background_color" class="post-attributes-label"><?php esc_html_e( 'Background Color', 'codevery-quiz' ); ?></label>
                    <input class="color_field coupon_background_color" type="text" name="coupon_background_color" id="coupon_background_color" value="<?php echo esc_attr( $quiz_settings['coupon_background_color'] ); ?>"/>
                </div>
                <div class="cquiz-settings-row">
                    <label for="highlighted_color" class="post-attributes-label"><?php esc_html_e( 'Highlighted Color', 'codevery-quiz' ); ?></label>
                    <input class="color_field highlighted_color" type="text" name="highlighted_color" id="highlighted_color" value="<?php echo esc_attr( $quiz_settings['highlighted_color'] ); ?>"/>
                </div>

                <div class="cquiz-settings-row">
                    <label for="exp_date_format" class="post-attributes-label"><?php esc_html_e( 'Expire Date Format', 'codevery-quiz' ); ?></label>
                    <select name="exp_date_format" id="exp_date_format">
                        <?php if ( get_locale() === 'uk_UA' ) : ?>
                            <option value="ukraine_format" <?php selected( $exp_date_format, 'ukraine_format' ); ?> ><?php echo esc_html( codevery_quiz_ua_date_format( strtotime( date( 'Y-m-d', strtotime( $expiration_date ) ) ) ) ); ?></option>
                        <?php endif; ?>
                        <option value="F j, Y" <?php selected( $exp_date_format, 'F j, Y' ); ?>><?php echo esc_html( date( 'F j, Y', strtotime( $expiration_date ) ) ); ?></option>
                        <option value="d.m.Y" <?php selected( $exp_date_format, 'd.m.Y' ); ?>><?php echo esc_html( date( 'd.m.Y', strtotime( $expiration_date ) ) ); ?></option>
                        <option value="Y-m-d" <?php selected( $exp_date_format, 'Y-m-d' ); ?>><?php echo esc_html( date( 'Y-m-d', strtotime( $expiration_date ) ) ); ?></option>
                        <option value="m/d/Y" <?php selected( $exp_date_format, 'm/d/Y' ); ?>><?php echo esc_html( date( 'm/d/Y', strtotime( $expiration_date ) ) ); ?></option>
                        <option value="d/m/Y" <?php selected( $exp_date_format, 'd/m/Y' ); ?>><?php echo esc_html( date( 'd/m/Y', strtotime( $expiration_date ) ) ); ?></option>
                    </select>
                </div>

                <div class="cquiz-settings-row cquiz-settings-row-certificate">
                    <div></div>
                    <div>
                        <p class="description"><?php esc_html_e( 'You can change the titles directly on the certificate.', 'codevery-quiz' ); ?></p>
                        <div class="quiz-certificate">
                            <div class="certificate-wrap" style="background: linear-gradient(110deg, white 0%, white, 55%, <?php echo esc_attr( $quiz_settings['coupon_background_color'] ); ?> 55%, <?php echo esc_attr( $quiz_settings['coupon_background_color'] ); ?> 100%);">
                                <div class="column">
                                    <div class="background-bow"><div class="ribbon-bg"></div>
                                        <div class="bow-bg">
                                            <svg version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="483.168px" height="483.168px" viewBox="0 0 483.168 483.168" style="enable-background:new 0 0 483.168 483.168;" xml:space="preserve"><g><g><path d="M52.827,172.69c22.041,0,51.538-5.769,78.7-12.569c3.312-0.832,6.578-1.663,9.798-2.519 c0.134-0.088,0.256-0.181,0.391-0.269c2.914-1.908,5.887-3.783,8.921-5.646c5.68-3.485,11.537-6.885,17.663-10.189 c-1.886-0.06-3.811-0.114-5.781-0.172c-7.957-0.21-16.579-0.353-25.543-0.353c-57.276,0-96.583,5.809-112.417,16.306 c-0.667,1.813-1.1,3.374-1.368,4.584c-0.139,0.611-0.234,1.14-0.303,1.553c1.685,3.206,5.236,5.31,9.582,6.729 C38.5,172.093,46.1,172.69,52.827,172.69z"/> <path d="M16.167,450.686c17.058-53.996,47.164-79.893,48.603-81.095l5.564-4.692l2.182,6.944 c10.089,32.216,25.438,61.237,34.997,77.692c3.849-183.084,69.753-258.768,103.059-285.451c-9.035-1.08-17.167-4.458-23.77-9.521 c-1.533-1.176-2.982-2.403-4.334-3.753c-1.262-1.257-2.526-2.519-3.61-3.91c-6.797,3.519-13.387,7.115-19.651,10.852 c-4.907,2.924-9.647,5.917-14.24,8.969C10.993,255.928,10.474,395.984,16.167,450.686z"/> <path d="M341.45,157.334c0.132,0.088,0.265,0.172,0.385,0.26c3.229,0.848,6.488,1.688,9.798,2.519 c27.162,6.801,56.653,12.569,78.702,12.569c6.716,0,14.314-0.597,20.342-2.553c4.352-1.41,7.919-3.522,9.598-6.734 c-0.064-0.415-0.156-0.936-0.284-1.537c-0.245-1.196-0.662-2.741-1.295-4.524c-15.766-10.545-55.114-16.375-112.513-16.375 c-8.956,0-17.58,0.14-25.543,0.353c-1.972,0.052-3.896,0.11-5.774,0.172c6.119,3.304,11.981,6.712,17.672,10.189 C335.563,153.551,338.541,155.426,341.45,157.334z"/> <path d="M300.474,144.945c-0.137,0.188-0.221,0.403-0.354,0.591c-1.025,1.428-2.224,2.717-3.426,4.017 c-1.25,1.353-2.513,2.671-3.939,3.863c-5.795,4.853-12.948,8.375-20.938,10.047c33.049,26.12,99.969,101.526,103.84,286.072 c9.562-16.467,24.91-45.509,35.001-77.692l2.176-6.944l5.566,4.692c1.439,1.21,31.522,27.09,48.582,81.046 c5.643-54.753,5.014-194.801-128.818-283.924c-4.585-3.05-9.321-6.043-14.223-8.961c-6.34-3.795-13.023-7.438-19.917-10.994 C302.842,146.149,301.664,145.544,300.474,144.945z"/> <path d="M21.204,147.724c1.07-0.613,2.426-1.146,3.619-1.725c2.779-1.353,5.907-2.613,9.375-3.775 c21.446-7.233,55.689-10.953,102.783-10.953c1.304,0,2.561,0.016,3.857,0.024c3.665,0.016,7.246,0.054,10.756,0.106 c7.095,0.12,13.799,0.292,19.979,0.515c-0.032-0.134-0.058-0.26-0.088-0.395c-0.377-1.679-0.449-3.438-0.587-5.188 c-0.08-0.972-0.329-1.899-0.329-2.897V84.066c0-6.155,1.613-11.94,4.33-17.184c0.118-0.23,0.276-0.433,0.403-0.651 c0.336-0.613,0.721-1.2,1.088-1.797c-0.501-0.276-0.996-0.549-1.499-0.826c-5.047-2.763-10.022-5.378-14.862-7.692 c-9.401-4.508-18.376-8.173-26.916-11.189c-27.079-9.538-49.654-12.245-66.463-12.245c-17.318,0-31.675,2.959-42.625,7.284 c-10.151,4.005-17.384,9.177-21.071,14.36c-2.809,3.947-3.619,7.708-2.412,11.193c2.703,7.772,5.41,17.294,7.985,27.146 c5.033,19.233,9.445,39.589,11.956,51.744C20.717,145.372,21,146.72,21.204,147.724z"/> <path d="M459.14,39.766c-10.957-4.324-25.315-7.284-42.644-7.284c-16.803,0-39.381,2.707-66.467,12.245 c-8.54,3.008-17.509,6.672-26.91,11.181c-4.837,2.314-9.811,4.929-14.855,7.692c-1.796,0.982-3.551,1.91-5.366,2.945 c2.829,5.336,4.537,11.223,4.537,17.513v39.371c0,1.515-0.321,2.945-0.506,4.412c-0.168,1.418-0.229,2.871-0.561,4.248 c1.19-0.05,2.461-0.097,3.711-0.135c6.58-0.244,13.81-0.44,21.492-0.567c3.518-0.054,7.089-0.092,10.756-0.108 c1.286-0.008,2.536-0.022,3.847-0.022c47.323,0,81.684,3.749,103.111,11.051c3.338,1.138,6.348,2.364,9.049,3.677 c1.194,0.579,2.553,1.11,3.618,1.725c0.205-1.006,0.489-2.354,0.733-3.524c2.509-12.155,6.925-32.508,11.954-51.744 c2.577-9.851,5.29-19.372,7.987-27.146c1.206-3.477,0.393-7.237-2.413-11.193C476.524,48.943,469.287,43.771,459.14,39.766z"/> <path d="M223.423,154.931h8.009h4.841h10.621h4.837h8.003h1.604c7.405,0,14.282-1.947,20.033-5.235 c1.787-1.02,3.426-2.204,4.969-3.479c1.362-1.114,2.553-2.344,3.703-3.633c0.32-0.36,0.697-0.675,0.994-1.054 c1.029-1.254,1.871-2.623,2.677-4.001c0.108-0.188,0.252-0.369,0.353-0.569c0.008-0.006,0.016-0.022,0.023-0.03 c0.782-1.395,1.371-2.855,1.908-4.362c0.008-0.016,0.008-0.026,0.016-0.042c0.169-0.471,0.369-0.924,0.514-1.411 c0.721-2.473,1.218-5.017,1.218-7.686V84.058c0-4.48-1.118-8.729-3.082-12.591c-0.721-1.431-1.614-2.771-2.568-4.082 c-0.104-0.158-0.185-0.331-0.305-0.487c-0.874-1.154-1.911-2.19-2.938-3.228c-6.677-6.734-16.475-11.101-27.515-11.101h-7.702 h-24.113h-12.859c-9.979,0-19.021,3.502-25.605,9.145c-1.281,1.1-2.389,2.342-3.471,3.588c-0.463,0.537-0.958,1.038-1.383,1.603 c-0.557,0.739-0.981,1.555-1.46,2.331c-2.771,4.446-4.488,9.443-4.488,14.829v39.371c0,2.016,0.265,3.979,0.689,5.895 c0.227,1.028,0.629,1.994,0.966,2.983c0.211,0.621,0.407,1.234,0.66,1.835c0.118,0.285,0.18,0.583,0.306,0.855 c0.361,0.777,0.81,1.509,1.238,2.254c0.23,0.401,0.487,0.777,0.74,1.168c0.653,1.032,1.266,2.066,2.032,3.018 c0.246,0.299,0.553,0.547,0.801,0.84c0.024,0.022,0.05,0.038,0.066,0.06c0.391,0.449,0.815,0.868,1.23,1.288 c1.318,1.343,2.741,2.583,4.292,3.723c1.715,1.249,3.535,2.352,5.49,3.318c5.306,2.615,11.357,4.232,17.897,4.232h6.76V154.931z"/></g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g> </svg>
                                        </div>
                                    </div>
                                    <input type="text" class="certificate-title" name="certificate_text" value="<?php echo esc_attr( $quiz_settings['certificate_text'] ); ?>" />
                                    <div class="promocode">
                                        <input type="text" class="promocode-title" name="promocode_text" value="<?php echo esc_attr( $quiz_settings['promocode_text'] ); ?>" />
                                        <span class="promo">xxxxxxxx</span>
                                    </div>
                                    <div class="cert-footer">
                                        <?php
                                        $custom_logo_id = get_theme_mod( 'custom_logo' );
                                        if ( $custom_logo_id ) {
                                            echo sprintf(
                                                '<a href="javascript:void(0);" class="cquiz-certificate__footer-logo" rel="home">%1$s</a>',
                                                wp_kses_post( wp_get_attachment_image( $custom_logo_id, array( 200, 50 ) ) )
                                            );
                                        }
                                        ?>
                                        <a href="javascript:void(0);"><?php echo esc_html( wp_parse_url( site_url(), PHP_URL_HOST ) ); ?></a>
                                    </div>
                                </div>
                                <div class="column">
                                    <input type="text" class="certificate-sale-title" name="certificate_sale_text" value="<?php echo esc_attr( $quiz_settings['certificate_sale_text'] ); ?>" />
                                    <div class="sale-percent">
                                        <span class="sale-percent-amount"><?php echo isset( $quiz_settings['coupon_amount'] ) ? esc_html( $quiz_settings['coupon_amount'] ) : '5'; ?></span><span class="sale-percent-symbol">%</span>
                                    </div>
                                    <div class="cert-footer">
                                        <input type="text" class="promocode-exp-text" name="promocode_exp_text" value="<?php echo esc_attr( $quiz_settings['promocode_exp_text'] ); ?>" /><br>
                                        <?php
                                        if ( $exp_date_format == 'ukraine_format' ) {
                                            $exp_date = codevery_quiz_ua_date_format( strtotime( $expiration_date ) );
                                        } else {
                                            $exp_date = date( $exp_date_format, strtotime( $expiration_date ) );
                                        }
                                        ?>
                                        <span class="certificate-exp-date"><?php echo esc_html( $exp_date ); ?></span>
                                    </div>
                                </div>
                            </div>
                        </div><!-- .quiz-certificate -->
                    </div>
                </div>

                <div class="cquiz-settings-row">
                    <label for="coupon_description" class="post-attributes-label"><?php esc_html_e( 'Coupon description', 'codevery-quiz' ); ?></label>
                    <?php
                    $coupon_description = isset( $quiz_settings['coupon_description'] ) ? wp_kses_post( $quiz_settings['coupon_description'] ) : esc_html__( 'You can send it to your friend. The discount can be used only once in the specified period of time.', 'codevery-quiz' );
                    wp_editor( $coupon_description, 'coupon_description', array(
                        'wpautop'       => false,
                        'media_buttons' => false,
                        'textarea_name' => 'coupon_description',
                        'textarea_rows' => 5,
                        'teeny'         => true,
                    ) );
                    ?>
                </div>
                <div>
                    <hr>
                    <h3><?php esc_html_e( 'Email Settings', 'codevery-quiz' ); ?></h3>
                    <hr>
                    <br>
                    <div class="cquiz-settings-row">
                        <label for="display_email_form" class="post-attributes-label"><?php esc_html_e( 'Display Email Form', 'codevery-quiz' ); ?></label>
                        <label for="display_email_form" class="cquiz-toggle-label">
                            <input type="checkbox" class="display_email_form cquiz-toggle-input" name="display_email_form" id="display_email_form" value="yes" <?php checked( $quiz_settings['display_email_form'], 'yes' ); ?>>
                            <span class="cquiz-toggle"></span>
                        </label>
                    </div>
                </div>
                <div class="display-email-form-fields" <?php echo $quiz_settings['display_email_form'] == 'yes' ? '' : 'style="display:none"'; ?>>
                    <div>
                        <div class="cquiz-settings-row">
                            <label for="form_description" class="post-attributes-label"><?php esc_html_e( 'Form description', 'codevery-quiz' ); ?></label>
                            <?php
                            $form_description = isset( $quiz_settings['form_description'] ) ? wp_kses_post( $quiz_settings['form_description'] ) : esc_html__( 'We can send you this coupon by email', 'codevery-quiz' );
                            wp_editor( $form_description, 'form_description', array(
                                'wpautop'       => false,
                                'media_buttons' => false,
                                'textarea_name' => 'form_description',
                                'textarea_rows' => 5,
                                'teeny'         => true,
                            ) );
                            ?>
                        </div>
                    </div>
                    <div>
                        <div class="cquiz-settings-row">
                            <label for="email_layout" class="post-attributes-label"><?php esc_html_e( 'Email Layout', 'codevery-quiz' ); ?></label>
                            <div>
                                <?php
                                ob_start();
                                require CODEVERY_QUIZ_PLUGIN_DIR_ADMIN . 'partials/email-layout.php';
                                $default_email = ob_get_clean();
                                $quiz_email = get_post_meta( $post_object->ID, 'email_layout', true );
                                $email_layout = $quiz_email ? $quiz_email : $default_email;
                                wp_editor( wp_kses_post( $email_layout ), 'email_layout', array(
                                    'wpautop'       => false,
                                    'media_buttons' => false,
                                    'textarea_name' => 'email_layout',
                                    'editor_height' => 425,
                                    'textarea_rows' => 20,
                                    'teeny'         => true,
                                ) );
                                ?>
                                <?php if ( $post_object->post_status != 'auto-draft' ) : ?>
                                    <div class="quiz-email-preview-button">
                                        <br>
                                        <a href="<?php echo esc_url( wp_nonce_url( admin_url( '?preview_quiz_email=true&quiz_id=' . $post_object->ID ), 'cquiz-preview-mail' ) ); ?>" class="button" target="popup" onclick="window.open(this.href,'popup','width=700,height=650'); return false;"><?php esc_html_e( 'Preview', 'codevery-quiz' ); ?></a>
                                        <p class="description"><?php esc_html_e( 'You have to save your changes first to preview the current version.', 'codevery-quiz' ); ?></p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div><!-- #coupon -->

        <?php // TODO do_action(); ?>

    </div><!-- .tabs-content -->

</div>
