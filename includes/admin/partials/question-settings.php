<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly. ?>

<div id="quiz_settings">
    <div class="cquiz-settings-row"></div>
    <div class="cquiz-settings-row">
        <label><?php esc_html_e( 'Type', 'codevery-quiz' ); ?></label>
        <div>
            <label class="question-type-field">
                <input type="radio" name="question_type" value="text" <?php checked( $question_type, 'text' ); ?>>
                <i class="dashicons dashicons-text" title="<?php esc_html_e( 'Text', 'codevery-quiz' ); ?>"></i>
            </label>

            <label class="question-type-field">
                <input type="radio" name="question_type" value="image" <?php checked( $question_type, 'image' ); ?>>
                <i class="dashicons dashicons-format-image" title="<?php esc_html_e( 'Image', 'codevery-quiz' ); ?>"></i>
            </label>
        </div>
    </div>

    <div class="quiz-repeater">
        <div id="quiz-repeater-items">
            <div class="item-hidden" data-group="answers">
                <!-- Repeater Content -->
                <div class="item-content">
                    <svg class="drag-item" width="20" height="20" viewBox="0 0 15 15" fill="none" xmlns="http://www.w3.org/2000/svg" id="IconChangeColor"><path fill-rule="evenodd" clip-rule="evenodd" d="M5.5 4.625C6.12132 4.625 6.625 4.12132 6.625 3.5C6.625 2.87868 6.12132 2.375 5.5 2.375C4.87868 2.375 4.375 2.87868 4.375 3.5C4.375 4.12132 4.87868 4.625 5.5 4.625ZM9.5 4.625C10.1213 4.625 10.625 4.12132 10.625 3.5C10.625 2.87868 10.1213 2.375 9.5 2.375C8.87868 2.375 8.375 2.87868 8.375 3.5C8.375 4.12132 8.87868 4.625 9.5 4.625ZM10.625 7.5C10.625 8.12132 10.1213 8.625 9.5 8.625C8.87868 8.625 8.375 8.12132 8.375 7.5C8.375 6.87868 8.87868 6.375 9.5 6.375C10.1213 6.375 10.625 6.87868 10.625 7.5ZM5.5 8.625C6.12132 8.625 6.625 8.12132 6.625 7.5C6.625 6.87868 6.12132 6.375 5.5 6.375C4.87868 6.375 4.375 6.87868 4.375 7.5C4.375 8.12132 4.87868 8.625 5.5 8.625ZM10.625 11.5C10.625 12.1213 10.1213 12.625 9.5 12.625C8.87868 12.625 8.375 12.1213 8.375 11.5C8.375 10.8787 8.87868 10.375 9.5 10.375C10.1213 10.375 10.625 10.8787 10.625 11.5ZM5.5 12.625C6.12132 12.625 6.625 12.1213 6.625 11.5C6.625 10.8787 6.12132 10.375 5.5 10.375C4.87868 10.375 4.375 10.8787 4.375 11.5C4.375 12.1213 4.87868 12.625 5.5 12.625Z" fill="#737373" id="mainIconPathAttribute"></path></svg>
                    <div class="answer-field">
                        <label class="control-label hidden"><?php esc_html_e( 'Answer', 'codevery-quiz' ); ?></label>
                        <div class="option-type-image option-type-field <?php echo $question_type == 'image' ? 'active-type' : ''; ?>">
                            <div class="option-preview-image"></div>
                            <input type="hidden" class="process_custom_images" data-name="image_id" value="">
                            <button class="set_custom_images button"><?php esc_html_e( 'Upload Image', 'codevery-quiz' ); ?></button>
                            <button class="remove_option_image" style="display: none"><span class="dashicons dashicons-no-alt"></span></button>
                        </div>
                        <div class="option-type-text option-type-field">
                            <input type="text" class="quiz-question form-control" placeholder="<?php esc_html_e( 'Answer', 'codevery-quiz' ); ?>" data-name="option" value="">
                        </div>
                    </div>
                    <div class="answer-description">
                        <label class="control-label hidden"><?php esc_html_e( 'Describe why this option is correct or incorrect', 'codevery-quiz' ); ?></label>
                        <div>
                            <textarea class="form-control" data-name="description" placeholder="<?php esc_html_e( 'Describe why this option is correct or incorrect', 'codevery-quiz' ); ?>"></textarea>
                        </div>
                    </div>

                    <div class="correct-answer">
                        <label class="control-label"><input type="radio" class="form-control" data-name="answer" data-skip-name="1" required>
                            <?php esc_html_e( 'Correct answer', 'codevery-quiz' ); ?></label>
                    </div>

                    <!-- Repeater Remove Btn -->
                    <a href="#" type="button" class="btn-danger remove-item">
                        <span class="dashicons dashicons-trash"></span><span class="hidden"><?php esc_html_e( 'Remove', 'codevery-quiz' ); ?></span>
                    </a>
                </div>

            </div>
            <?php if ( $question_options ) : ?>
                <?php foreach ( $question_options as $key => $options ) : ?>
                    <div class="item" >
                        <div class="item-content">
                            <svg class="drag-item" width="20" height="20" viewBox="0 0 15 15" fill="none" xmlns="http://www.w3.org/2000/svg" id="IconChangeColor"> <path fill-rule="evenodd" clip-rule="evenodd" d="M5.5 4.625C6.12132 4.625 6.625 4.12132 6.625 3.5C6.625 2.87868 6.12132 2.375 5.5 2.375C4.87868 2.375 4.375 2.87868 4.375 3.5C4.375 4.12132 4.87868 4.625 5.5 4.625ZM9.5 4.625C10.1213 4.625 10.625 4.12132 10.625 3.5C10.625 2.87868 10.1213 2.375 9.5 2.375C8.87868 2.375 8.375 2.87868 8.375 3.5C8.375 4.12132 8.87868 4.625 9.5 4.625ZM10.625 7.5C10.625 8.12132 10.1213 8.625 9.5 8.625C8.87868 8.625 8.375 8.12132 8.375 7.5C8.375 6.87868 8.87868 6.375 9.5 6.375C10.1213 6.375 10.625 6.87868 10.625 7.5ZM5.5 8.625C6.12132 8.625 6.625 8.12132 6.625 7.5C6.625 6.87868 6.12132 6.375 5.5 6.375C4.87868 6.375 4.375 6.87868 4.375 7.5C4.375 8.12132 4.87868 8.625 5.5 8.625ZM10.625 11.5C10.625 12.1213 10.1213 12.625 9.5 12.625C8.87868 12.625 8.375 12.1213 8.375 11.5C8.375 10.8787 8.87868 10.375 9.5 10.375C10.1213 10.375 10.625 10.8787 10.625 11.5ZM5.5 12.625C6.12132 12.625 6.625 12.1213 6.625 11.5C6.625 10.8787 6.12132 10.375 5.5 10.375C4.87868 10.375 4.375 10.8787 4.375 11.5C4.375 12.1213 4.87868 12.625 5.5 12.625Z" fill="#737373" id="mainIconPathAttribute"></path></svg>
                            <div class="answer-field">
                                <label class="control-label hidden"><?php esc_html_e( 'Answer', 'codevery-quiz' ); ?></label>
                                <?php $image = wp_get_attachment_image_src( $options['image_id'] ); ?>
                                <div class="option-type-image option-type-field <?php echo $question_type == 'image' ? 'active-type' : ''; ?>" >
                                    <div class="option-preview-image"><?php echo $image ? '<img src="' . esc_url( $image[0] ) . '">' : ''; ?></div>
                                    <input type="hidden" class="process_custom_images" name="answers[<?php echo esc_attr( $key ); ?>][image_id]" value="<?php echo esc_html( $options['image_id'] ); ?>">
                                    <button class="set_custom_images button" <?php echo $image ? 'style="display: none"' : ''; ?>><?php esc_html_e( 'Upload Image', 'codevery-quiz' ); ?></button>
                                    <button class="remove_option_image" <?php echo ! $image ? 'style="display: none"' : ''; ?>><span class="dashicons dashicons-no-alt"></span></button>
                                </div>
                                <div class="option-type-text option-type-field <?php echo $question_type == 'image' ? 'active-type' : ''; ?>" >
                                    <input type="text" class="quiz-question form-control" placeholder="<?php esc_html_e( 'Answer', 'codevery-quiz' ); ?>" name="answers[<?php echo esc_attr( $key ); ?>][option]" value="<?php echo esc_html( $options['option'] ); ?>">
                                </div>
                            </div>
                            <div class="answer-description">
                                <label class="control-label hidden"><?php esc_html_e( 'Description', 'codevery-quiz' ); ?></label>
                                <div>
                                    <textarea class="form-control" name="answers[<?php echo esc_attr( $key ); ?>][description]" placeholder="<?php esc_html_e( 'Describe why this option is correct or incorrect', 'codevery-quiz' ); ?>"><?php echo esc_textarea( $options['description'] ); ?></textarea>
                                </div>
                            </div>

                            <div class="correct-answer">
                                <label class="control-label"><input type="radio" class="form-control" value="<?php echo esc_attr( $key ); ?>" name="answer" data-skip-name="1" required <?php echo isset( $options['answer'] ) && $options['answer'] ? 'checked' : ''; ?>>
                                    <?php esc_html_e( 'Correct answer', 'codevery-quiz' ); ?></label>
                            </div>

                            <!-- Repeater Remove Btn -->
                            <a href="#" type="button" class="btn-danger remove-item">
                                <span class="dashicons dashicons-trash"></span><span class="hidden"><?php esc_html_e( 'Remove', 'codevery-quiz' ); ?></span>
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else : ?>
                <div class="item" >
                    <div class="item-content">
                        <svg class="drag-item" width="20" height="20" viewBox="0 0 15 15" fill="none" xmlns="http://www.w3.org/2000/svg" id="IconChangeColor"> <path fill-rule="evenodd" clip-rule="evenodd" d="M5.5 4.625C6.12132 4.625 6.625 4.12132 6.625 3.5C6.625 2.87868 6.12132 2.375 5.5 2.375C4.87868 2.375 4.375 2.87868 4.375 3.5C4.375 4.12132 4.87868 4.625 5.5 4.625ZM9.5 4.625C10.1213 4.625 10.625 4.12132 10.625 3.5C10.625 2.87868 10.1213 2.375 9.5 2.375C8.87868 2.375 8.375 2.87868 8.375 3.5C8.375 4.12132 8.87868 4.625 9.5 4.625ZM10.625 7.5C10.625 8.12132 10.1213 8.625 9.5 8.625C8.87868 8.625 8.375 8.12132 8.375 7.5C8.375 6.87868 8.87868 6.375 9.5 6.375C10.1213 6.375 10.625 6.87868 10.625 7.5ZM5.5 8.625C6.12132 8.625 6.625 8.12132 6.625 7.5C6.625 6.87868 6.12132 6.375 5.5 6.375C4.87868 6.375 4.375 6.87868 4.375 7.5C4.375 8.12132 4.87868 8.625 5.5 8.625ZM10.625 11.5C10.625 12.1213 10.1213 12.625 9.5 12.625C8.87868 12.625 8.375 12.1213 8.375 11.5C8.375 10.8787 8.87868 10.375 9.5 10.375C10.1213 10.375 10.625 10.8787 10.625 11.5ZM5.5 12.625C6.12132 12.625 6.625 12.1213 6.625 11.5C6.625 10.8787 6.12132 10.375 5.5 10.375C4.87868 10.375 4.375 10.8787 4.375 11.5C4.375 12.1213 4.87868 12.625 5.5 12.625Z" fill="#737373" id="mainIconPathAttribute"></path> </svg>
                        <div class="answer-field">
                            <label class="control-label hidden"><?php esc_html_e( 'Answer', 'codevery-quiz' ); ?></label>
                            <div class="option-type-image option-type-field" >
                                <div class="option-preview-image"></div>
                                <input type="hidden" class="process_custom_images" name="answers[0][image_id]" value="">
                                <button class="set_custom_images button" ><?php esc_html_e( 'Upload Image', 'codevery-quiz' ); ?></button>
                                <button class="remove_option_image" style="display: none" ><span class="dashicons dashicons-no-alt"></span></button>
                            </div>
                            <div class="option-type-text option-type-field" >
                                <input type="text" class="quiz-question form-control" placeholder="<?php esc_html_e( 'Answer', 'codevery-quiz' ); ?>" name="answers[0][option]" value="">
                            </div>
                        </div>
                        <div class="answer-description">
                            <label class="control-label hidden"><?php esc_html_e( 'Description', 'codevery-quiz' ); ?></label>
                            <div>
                                <textarea class="form-control" name="answers[0][description]" placeholder="<?php esc_html_e( 'Describe why this option is correct or incorrect', 'codevery-quiz' ); ?>"></textarea>
                            </div>
                        </div>

                        <div class="correct-answer">
                            <label class="control-label"><input type="radio" class="form-control" value="0" name="answer" data-skip-name="1" required >
                                <?php esc_html_e( 'Correct answer', 'codevery-quiz' ); ?></label>
                        </div>

                        <!-- Repeater Remove Btn -->
                        <a href="#" type="button" class="btn-danger remove-item">
                            <span class="dashicons dashicons-trash"></span><span class="hidden"><?php esc_html_e( 'Remove', 'codevery-quiz' ); ?></span>
                        </a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
        <button type="button" class="cquiz-button cquiz-add-item">
            <?php esc_html_e( 'Add Answer', 'codevery-quiz' ); ?>
        </button>
    </div>
</div>