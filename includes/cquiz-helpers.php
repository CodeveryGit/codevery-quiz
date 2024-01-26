<?php
if ( ! function_exists( 'codevery_quiz_ua_date_format' ) ) {
    /**
     * Ukraine date format
     *
     * @param $date
     * @param string $year_text
     * @return string
     */
    function codevery_quiz_ua_date_format( $date, $year_text = 'року' ) {
        $months_list = array(
            '01' => 'січня',
            '02' => 'лютого',
            '03' => 'березня',
            '04' => 'квітня',
            '05' => 'травня',
            '06' => 'червня',
            '07' => 'липня',
            '08' => 'серпня',
            '09' => 'вересня',
            '10' => 'жовтня',
            '11' => 'листопада',
            '12' => 'грудня',
        );

        if ( ! $date ) {
            $date = strtotime( 'now' );
        }
        $date_day = date( 'd ', $date );
        $date_month = $months_list[ date( 'm', $date ) ];

        $date_year = date( " Y $year_text", $date );
        $result = $date_day . $date_month . $date_year;

        return $result;
    }
}

if ( ! function_exists( 'codevery_quiz_get_custom_logo' ) ) {
    /**
     * Get site logo
     *
     * @param array $attr
     * @return string
     */
    function codevery_quiz_get_custom_logo( $attr = array() ) {
        $html = '';
        $switched_blog = false;

        $custom_logo_id = get_theme_mod( 'custom_logo' );

        // We have a logo. Logo is go.
        if ( $custom_logo_id ) {
            $custom_logo_attr = array(
                'class'   => 'custom-logo',
                'loading' => false,
            );
            $custom_logo_attr = wp_parse_args( $attr, $custom_logo_attr );

            $unlink_homepage_logo = (bool) get_theme_support( 'custom-logo', 'unlink-homepage-logo' );

            if ( $unlink_homepage_logo && is_front_page() && ! is_paged() ) {
                /*
                 * If on the home page, set the logo alt attribute to an empty string,
                 * as the image is decorative and doesn't need its purpose to be described.
                 */
                $custom_logo_attr['alt'] = '';
            } else {
                /*
                 * If the logo alt attribute is empty, get the site title and explicitly pass it
                 * to the attributes used by wp_get_attachment_image().
                 */
                $image_alt = get_post_meta( $custom_logo_id, '_wp_attachment_image_alt', true );
                if ( empty( $image_alt ) ) {
                    $custom_logo_attr['alt'] = get_bloginfo( 'name', 'display' );
                }
            }

            /*
             * If the alt attribute is not empty, there's no need to explicitly pass it
             * because wp_get_attachment_image() already adds the alt attribute.
             */
            $image = wp_get_attachment_image( $custom_logo_id, 'full', false, $custom_logo_attr );

            if ( $unlink_homepage_logo && is_front_page() && ! is_paged() ) {
                // If on the home page, don't link the logo to home.
                $html = sprintf(
                    '<span class="custom-logo-link">%1$s</span>',
                    $image
                );
            } else {
                $aria_current = is_front_page() && ! is_paged() ? ' aria-current="page"' : '';

                $html = sprintf(
                    '<a href="%1$s" class="custom-logo-link" rel="home"%2$s>%3$s</a>',
                    esc_url( home_url( '/' ) ),
                    $aria_current,
                    $image
                );
            }
        }

        return $html;
    }
}

if ( ! function_exists( 'codevery_quiz_upload_file_by_url' ) ) {
    /**
     * Upload media file by url
     *
     * @param $image_url
     * @return bool|int|WP_Error
     */
    function codevery_quiz_upload_file_by_url( $image_url ) {
        require_once ABSPATH . 'wp-admin/includes/file.php';
        $temp_file = download_url( $image_url );
        if ( is_wp_error( $temp_file ) ) {
            return false;
        }
        $file = array(
            'name'     => basename( $image_url ),
            'type'     => mime_content_type( $temp_file ),
            'tmp_name' => $temp_file,
            'size'     => filesize( $temp_file ),
        );
        $sideload = wp_handle_sideload(
            $file,
            array(
                'test_form' => false, // no needs to check 'action' parameter.
            )
        );

        if ( ! empty( $sideload['error'] ) ) {
            // you may return error message if you want.
            return false;
        }

        // it is time to add our uploaded image into WordPress media library.
        $attachment_id = wp_insert_attachment(
            array(
                'guid'           => $sideload['url'],
                'post_mime_type' => $sideload['type'],
                'post_title'     => basename( $sideload['file'] ),
                'post_content'   => '',
                'post_status'    => 'inherit',
            ),
            $sideload['file']
        );
        if ( is_wp_error( $attachment_id ) || ! $attachment_id ) {
            return false;
        }
        require_once ABSPATH . 'wp-admin/includes/image.php';
        wp_update_attachment_metadata( $attachment_id, wp_generate_attachment_metadata( $attachment_id, $sideload['file'] ) );

        return $attachment_id;

    }
}

if ( ! function_exists( 'codevery_quiz_get_kses_array' ) ) {
    /**
     * Array of allowed tags for wp_kses()
     *
     * @return array
     */
    function codevery_quiz_get_kses_array() {
        return array(
            'a' => array(
                'class'  => array(),
                'href'   => array(),
                'rel'    => array(),
                'title'  => array(),
                'target' => array(),
                'style'  => array(),
            ),
            'abbr' => array(
                'title' => array(),
            ),
            'b' => array(
                'class' => array(),
            ),
            'blockquote' => array(
                'cite' => array(),
            ),
            'button' => array(
                'type'     => array(),
                'class'    => array(),
                'id'       => array(),
                'disabled' => array(),
            ),
            'cite' => array(
                'title' => array(),
            ),
            'code' => array(),
            'pre'  => array(),
            'del'  => array(
                'datetime' => array(),
                'title'    => array(),
            ),
            'dd' => array(),
            'div' => array(
                'id'    => array(),
                'class' => array(),
                'title' => array(),
                'style' => array(),
            ),
            'dl' => array(),
            'dt' => array(),
            'em' => array(),
            'u' => array(),
            'strong' => array(),
            'h1' => array(
                'class' => array(),
            ),
            'h2' => array(
                'class' => array(),
            ),
            'h3' => array(
                'class' => array(),
            ),
            'h4' => array(
                'class' => array(),
            ),
            'h5' => array(
                'class' => array(),
            ),
            'h6' => array(
                'class' => array(),
            ),
            'i' => array(
                'class' => array(),
            ),
            'img' => array(
                'alt' => array(),
                'class' => array(),
                'height' => array(),
                'src' => array(),
                'width' => array(),
                'style' => array(),
                'title' => array(),
                'srcset' => array(),
                'loading' => array(),
                'sizes' => array(),
            ),
            'figure' => array(
                'class' => array(),
            ),
            'li' => array(
                'class' => array(),
            ),
            'ol' => array(
                'class' => array(),
            ),
            'p' => array(
                'class' => array(),
            ),
            'q' => array(
                'cite' => array(),
                'title' => array(),
            ),
            'span' => array(
                'class' => array(),
                'title' => array(),
                'style' => array(),
            ),
            'iframe' => array(
                'width' => array(),
                'height' => array(),
                'scrolling' => array(),
                'frameborder' => array(),
                'allow' => array(),
                'src' => array(),
            ),
            'strike' => array(),
            'style' => array(),
            'br'    => array(),
            'table' => array(),
            'thead' => array(),
            'tbody' => array(),
            'tfoot' => array(),
            'tr' => array(),
            'th' => array(),
            'td' => array(),
            'colgroup' => array(),
            'col' => array(),
            'data-wow-duration' => array(),
            'data-wow-delay' => array(),
            'data-wallpaper-options' => array(),
            'data-stellar-background-ratio' => array(),
            'ul' => array(
                'class' => array(),
            ),
            'svg' => array(
                'class' => true,
                'aria-hidden' => true,
                'aria-labelledby' => true,
                'role' => true,
                'xmlns' => true,
                'width' => true,
                'height' => true,
                'viewbox' => true,
                'preserveaspectratio' => true,
            ),
            'g' => array( 'fill' => true ),
            'title' => array( 'title' => true ),
            'path' => array(
                'd' => true,
                'fill' => true,
            ),
            'label' => array(
                'for' => array(),
                'id' => array(),
                'class' => array(),
            ),
            'input' => array(
                'id' => array(),
                'class' => array(),
                'type' => array(),
                'name' => array(),
                'value' => array(),
                'placeholder' => array(),
                'required' => array(),
                'aria-required' => array(),
            ),
        );
    }
}