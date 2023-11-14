<?php
/**
 * Display quiz html content
 *
 * This file is used to markup the quiz content on front-end.
 *
 * @since      1.0.0
 *
 * @package    Codevery_Quiz
 * @subpackage Codevery_Quiz/public/partials
 */

$quiz_id = $args['id'];
$quiz    = $this->get_quiz( $quiz_id );
?>
<?php if ( $quiz->have_posts() ) : ?>
    <?php while ( $quiz->have_posts() ) : $quiz->the_post();
        $quiz_settings   = $this->get_quiz_settings( $quiz_id );
        $quiz_questions  = $this->get_quiz_questions( $quiz_id );
        $count_questions = is_array( $quiz_questions ) ? count( $quiz_questions ) : 0;
        ?>
        <div class="cquiz">
            <div class="cquiz__title"><h2><?php the_title(); ?></h2></div>
            <div class="cquiz__form" data-max-points="<?php echo esc_attr( $quiz_settings['winner_points'] ); ?>">
                <?php wp_nonce_field( 'cquiz_display', 'cquiz_display_nonce' ); ?>
                <div class="cquiz__wrap" data-quiz-id="<?php echo esc_attr( get_the_ID() ); ?>" data-question-page="0" data-question-length="<?php echo esc_attr( count( $quiz_questions ) ); ?>">

                    <p class="cquiz__page-title" data-page=""> <?php echo esc_html( get_the_title() ); ?> </p>
                    <?php if ( $count_questions ) : ?>
                        <?php foreach ( $quiz_questions as $index => $quiz_question ) : ?>
                            <p class="cquiz__page-title" data-page="<?php echo esc_attr( $index + 1 ); ?>"> <?php echo esc_html( get_the_title( $quiz_question['question'] ) ); ?> </p>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    <p class="cquiz__page-title cquiz__fail"> <?php echo esc_html( $quiz_settings['title_quiz_looser'] ); ?> </p>
                    <p class="cquiz__page-title cquiz__success"> <?php echo esc_html( $quiz_settings['title_quiz_winner'] ); ?> </p>

                    <div class="cquiz__content" data-page="0">
                        <div><?php the_content(); ?></div>
                        <div><?php the_post_thumbnail( 'post-thumbnail', array( 'class' => 'cquiz__main-img' ) ); ?></div>
                    </div>

                    <?php if ( $count_questions ) : ?>
                        <?php foreach ( $quiz_questions as $index => $quiz_question ) : ?>
                            <?php
                            $question_id   = $quiz_question['question'];
                            $question_type = get_post_meta( $question_id, 'question_type', true );
                            $questions     = preg_replace( '/\\\\/', '', get_post_meta( $question_id, 'question_options', true ) );
                            $questions     = json_decode( $questions, true );
                            ?>
                            <div class="cquiz__content cquiz__content-<?php echo esc_attr( $question_type ); ?>" data-page="<?php echo esc_attr( $index + 1 ); ?>">
                                <?php if ( $quiz_description = get_post_field( 'post_content', $question_id ) ) : ?>
                                    <div class="cquiz__content-description"><p><?php echo wp_kses_post( $quiz_description ); ?></p></div>
                                <?php endif; ?>
                                <div class="cquiz__content-answers cquiz__content-answers-<?php echo esc_attr( $question_type ); ?>">
                                <?php if ( $questions ) : ?>
                                    <?php foreach ( $questions as $option_index => $question ) : ?>
                                        <?php if ( $question['option'] || $question['image_id'] ) :
                                            $data_rule = (int) isset( $question['answer'] );
                                            $i = str_replace( 'option_', '', $option_index );
                                            $option_content = ( 'image' == $question_type ) ? '<span class="cquiz__form-image-label"  style="background-image: url(' . wp_get_attachment_image_src( $question['image_id'], 'full' )[0] . ')"></span>' : esc_attr( $question['option'] );
                                            ?>
                                            <div class="cquiz__<?php echo esc_attr( $question_type ); ?>-input cquiz__card">
                                                <div class="cquiz__card-block">
                                                    <input class="cquiz__form-input cquiz__form-input-<?php echo esc_attr( $question_type ); ?>" type="radio" name="question<?php echo esc_attr( $index + 1 ); ?>" id="question<?php echo esc_attr( $index + 1 ); ?>-<?php echo esc_attr( $i ); ?>" value="<?php echo $data_rule ? esc_attr( $quiz_question['points'] ) : 0; ?>" data-rule="<?php echo esc_attr( $data_rule ); ?>">
                                                    <?php if ( 'image' == $question_type ) : ?>
                                                        <label class="cquiz__form-label" for="question<?php echo esc_attr( $index + 1 ); ?>-<?php echo esc_attr( $i ); ?>" >
                                                            <span class="cquiz__card-subtitle">
                                                               <?php echo esc_html( $question['option'] ); ?>
                                                            </span>
                                                            <?php echo wp_kses_post( $option_content ); ?>
                                                        </label>
                                                    <?php else : ?>
                                                        <label class="cquiz__form-label" for="question<?php echo esc_attr( $index + 1 ); ?>-<?php echo esc_attr( $i ); ?>" >
                                                            <span class="cquiz__radio_button"></span>
                                                            <?php echo wp_kses_post( $option_content ); ?>
                                                        </label>
                                                    <?php endif; ?>
                                                </div><!-- .cquiz__card-block -->
                                                <div class="cquiz__card-description">
                                                    <div class="cquiz__card-description-wrap">
                                                        <span class="cquiz__card-description-title"><?php echo $data_rule ? esc_html__( 'Correct', 'codevery-quiz' ) : esc_html__( 'Incorrect', 'codevery-quiz' ); ?></span>
                                                        <?php echo wp_kses_post( $question['description'] ); ?>
                                                    </div>
                                                </div><!-- .cquiz-description -->
                                            </div><!-- .cquiz__card -->
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                                </div>
                            </div><!-- .cquiz__content -->
                        <?php endforeach; ?>
                    <?php endif; ?>


                    <div class="cquiz__content cquiz__result" data-page="">
                        <div class="cquiz__success">
                            <p class="cquiz__result-points"></p>
                            <div class="cquiz__description">
                                <?php
                                preg_match_all( '/' . get_shortcode_regex() . '/', $quiz_settings['text_quiz_winner'], $matches, PREG_SET_ORDER );
                                $text_quiz_winner = '';
                                if ( $matches ) {
                                    foreach ( $matches as $match ) {
                                        $shortcode = do_shortcode( $match[0] );
                                        $text_quiz_winner .= str_replace( $match[0], $shortcode, $quiz_settings['text_quiz_winner'] );
                                    }
                                } else {
                                    $text_quiz_winner = $quiz_settings['text_quiz_winner'];
                                }
                                // To allow form fields.
                                echo wp_kses( $text_quiz_winner, cquiz_get_kses_array() );
                                ?>
                                <?php do_action( 'cquiz_result_page' ); ?>
                            </div>
                        </div>
                        <div class="cquiz__fail">
                            <p class="cquiz__result-points"></p>
                            <div class="cquiz__description">
                                <?php echo wp_kses_post( $quiz_settings['text_quiz_looser'] ); ?>
                                <?php do_action( 'cquiz_result_page' ); ?>
                            </div>
                        </div>
                    </div>

                    <div class="cquiz__footer">
                        <button type="button" class="btn btn-secondary cquiz__button" data-next_text="<?php esc_attr_e( 'Next', 'codevery-quiz' ); ?>">
                            <?php echo esc_html( $quiz_settings['start_button_text'] ); ?>
                        </button>
                    </div>

                </div>
            </div>
        </div>



    <?php endwhile; ?>
<?php endif; ?>

<?php wp_reset_postdata(); ?>
