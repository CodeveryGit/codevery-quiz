/**
 * CQ Admin scripts
 *
 * @package Codevery_Quiz
 */
(function( $ ) {
    'use strict';

    /**
     * Tabs
     */
    $(function() {
        $( '.tabs-nav a' ).on( 'click', function (e) {
            e.preventDefault();
            var $this = $( this );
            $( '.tabs-nav a' ).removeClass('active');
            $this.addClass( 'active' );
            $( '.tab-content' ).hide();
            $( $this.attr( 'href' ) ).show();
        } );
    });

    /**
     * Repeater
     */
    $( function(){
        var quizRepeater = $( '#quiz-repeater, .quiz-repeater' );
        if ( quizRepeater.length > 0 ) {
            quizRepeater.createRepeater();
            $( '#quiz-repeater-items' ).sortable({
                handle: '.drag-item',
                cursor: 'grabbing',
                start: function( event, ui ) {
                    let start_pos = ui.item.index();
                    ui.item.data( 'start_pos', start_pos );
                },
                update: function( event, ui ) {
                    let index = ui.item.index();
                    let start_pos = ui.item.data( 'start_pos' );
                    cquiz_repeater_reorder( index, start_pos );
                },
            });
        }
        $( document ).on('click', '.remove-item', function( e ) {
            e.preventDefault();
            if ( confirm( quizParams.confirmRemoveMsg ) ) {
                let index = $( this ).parents( '.item' ).data('index');
                $( this ).parents( '.item' ).remove();
                cquiz_repeater_reorder( index, index + 1 );
            }
        });
        $( document ).on('click', '.cquiz-delete-email-action', function( e ) {
            if ( ! confirm( quizParams.confirmDeleteEmailMsg ) ) e.preventDefault();
        });

        function cquiz_repeater_reorder( index, start_pos ) {
            $( '#quiz-repeater-items .item:nth-child(' + (index + 1) + ')' ).data('index', index - 1 );
            $( '#quiz-repeater-items .item:nth-child(' + (index + 1) + ') .item-order-number' ).html( index );
            if ( start_pos < index ) {
                //update the items before the re-ordered item
                for( let i = index; i > 0; i-- ) {
                    $( '#quiz-repeater-items .item:nth-child(' + i + ')' ).data('index', i - 2 );
                    $( '#quiz-repeater-items .item:nth-child(' + i + ') .item-order-number' ).html( i - 1 );
                }
            } else {
                //update the items after the re-ordered item
                for( let i = index+2; i <= $( '#quiz-repeater-items .item' ).length + 1; i++ ) {
                    $( '#quiz-repeater-items .item:nth-child(' + i + ')' ).data('index', i - 2 );
                    $( '#quiz-repeater-items .item:nth-child(' + i + ') .item-order-number' ).html( i - 1 );
                }
            }
        }
    });

    /**
     * Copy Shortcode
     */
    function quizCopyToClipboard( element ) {
        let $elem = $( element );
        let text = $elem.get( 0 );
        let selection = window.getSelection();
        let range = document.createRange();
        range.selectNodeContents( text );
        selection.removeAllRanges();
        selection.addRange( range );
        //add to clipboard.
        document.execCommand( 'copy' );
        let preText = $elem.html();
        $elem.html( "Copied!" );
        $elem.css( { background: "rgba(0,255,0,0.15)" } );

        setTimeout( () => {
            $elem.html(preText);
            $elem.css( { background: "#eef3fd" } );
        }, 1000 );
    }
    $( function() {
        $( '.copy_shortcode' ).each( function( index, el ) {
            $( el ).on( 'click', function( e ) {
                e.preventDefault();
                quizCopyToClipboard( $( this ).data( 'copied_text_id' ) );
            } );
        });
    });

    /**
     * Changing settings fields
     */
    $( function() {
        $( '#cquiz_coupon_amount' ).on('input', function() {
            $( '.sale-percent-amount' ).html( $( this ).val() );
        });
        if ( $( '#coupon' ).length > 0 ) {
            $( '#exp_date_format' ).on( 'change', function() {
                var selectedDate = $( this ).find( 'option:selected' ).text();
                $( '.certificate-exp-date' ).html( selectedDate );
            } );
            $( '#exp_date_format' ).change();
        }
        $( '.display_email_form' ).on( 'click', function () {
            $( '.display-email-form-fields' ).slideToggle();
        });
        $( '.cquiz-has-dependent-fields' ).on( 'click', function () {
            let fieldName = $(this).attr('name');
            $( '.' + fieldName + '-fields' ).slideToggle();
        });
        $( 'input[name="question_type"]' ).on('change', function() {
            var value = $( this ).val();
            $( '.option-type-field' ).removeClass( 'active-type' );
            $( '.option-type-'+value ).addClass( 'active-type' );
        });

        var colorOptions = {
            change: function( event, ui ){
                var element = event.target;
                var color = ui.color.toString();
                if ( $( element ).hasClass( 'highlighted_color' ) ) {
                    $( '.sale-percent span, .certificate-sale-title' ).css( { color: ui.color.toString() } );
                    $( '.promo, .ribbon-bg' ).css( { backgroundColor: ui.color.toString() } );
                    $( '.bow-bg svg' ).css( { fill: ui.color.toString() } );
                }
                if ( $(element).hasClass('coupon_background_color') ) {
                    $( '.certificate-wrap' ).css( { background: 'linear-gradient(110deg, white 0%, white, 55%, '+ui.color.toString()+' 55%, '+ui.color.toString()+' 100%)' } );
                }
            },
        };
        $( '.color_field' ).wpColorPicker( colorOptions );
    });

    /**
     * Upload Image field
     */
    $( function() {
        if ( $( '.quiz-repeater' ).length > 0 ) {
            if ( typeof wp !== 'undefined' && wp.media && wp.media.editor ) {
                $( document ).on( 'click', '.set_custom_images', function( e ) {
                    e.preventDefault();
                    var button = $( this );
                    var id = button.prev();
                    wp.media.editor.send.attachment = function( props, attachment ) {
                        id.val( attachment.id );
                        button.prev().prev().html( '<img src="' + attachment.url + '">' ).show();
                        button.hide().next().show();
                    };
                    wp.media.editor.open( button );
                    return false;
                });
            }
            $( document ).on( 'click', '.remove_option_image', function( e ) {
                e.preventDefault();
                var button = $( this );
                button.prev().prev().val( '' ).prev().html( '' );
                button.hide().prev().show();
            });
        }
    });

    /**
     * Edit questions
     */
    $( document ).on( 'click', '.quiz-repeater .item-head', function ( e ) {
        e.preventDefault();
        $( this ).toggleClass( 'open' );
        $( this ).siblings( '.item-body' ).toggle( 200 );
    } );

    /**
     * New question modal
     */
    $( document ).on( 'click', '.cquiz-modal', function ( e ) {
        e.preventDefault();
        let pUrl = this.href;
        let pTitle = this.title;
        let questionFieldId = this.closest( '.question' ).querySelector( '[name^="question"]' ).getAttribute( 'id' );
        pUrl += `&field_id=${questionFieldId}`;
        $( 'body' ).append("<div id='cquiz-modal_overlay'></div><div id='cquiz-modal_window' class='cquiz-modal-loading'></div>");
        $( '#cquiz-modal_window' ).append("<div id='cquiz-modal_wrap'><div id='cquiz-modal_heading'>" +
            "<div id='cquiz-modal_title'>" + pTitle + "</div>" +
            "<div><button type='button' id='cquiz-modal_close-window'><span class='dashicons dashicons-no-alt'></span></button></div></div>" +
            "<iframe frameborder='0' hspace='0' allowtransparency='true' src='" + pUrl + "' id='cquizModalContent' name='cquizModalContent" + Math.round(Math.random() * 1000) + "' width='1024' height='800' style='width:100%;max-height: 75vh;' ></iframe></div>");
        $( 'body' ).addClass( 'modal-open' );
        $( '#cquiz-modal_close-window' ).on( 'click', cqCloseModal );
    } );

    window.addEventListener( 'message', ( event ) => {
        if ( event.origin === window.location.origin && event.data.result?.success ) {
            let resultData = event.data.result.data;
            let select = document.querySelector( `[data-select2-id="${event.data.select_id}"]` );
            select.value = resultData.question_id;
            document.getElementById( `select2-${event.data.select_id}-container` ).textContent = resultData.question_title;

            let option = document.createElement( 'option' );
            option.text = resultData.question_title;
            option.setAttribute( 'value', resultData.question_id );
            option.setAttribute( 'selected', 'selected' );
            select.add( option );

            let itemWrap = select.closest( '.item-wrap' );
            itemWrap.querySelector( '.item-title' ).textContent = resultData.question_title;
            cqCloseModal( true );

            // show message.
            let itemContent = select.closest( '.item-body-content' );
            let message = document.createElement( 'div' );
            message.classList.add( 'quiz_success-message', 'cquiz-message-slideup', 'closed' );
            message.textContent = resultData.success_msg;
            itemContent.before( message );
            message.classList.remove( 'closed' );
            setTimeout( () => {
                message.classList.add( 'closed' );
            }, 4000 );
            setTimeout( () => {
                message.remove();
            }, 4500 );
        }
    });

    $( document ).on( 'change', 'select.quiz-question', function() {
        let $this = $(this);
        $this.closest( '.item' ).find( '.item-title' ).text( $this.find( 'option:selected' ).text() );
    });

    function cqCloseModal( result = false ) {
        $( '#cquiz-modal_window' ).fadeOut( 'fast', () => {
            $( '#cquiz-modal_window, #cquiz-modal_overlay' ).remove();
        });
        $( 'body' ).removeClass( 'modal-open' );
        return result;
    }

    $( document ).on( 'click', '.cquiz-save-item', function(e) {
        if ( $( '#cquiz_question:invalid' ).length === 0 ) {
            e.preventDefault();
            let formData = {};
            let cquizForm = $( '#cquiz_question' );
            let inputs = cquizForm.serializeArray();
            let select_id = this.dataset.field_id;
            $.each( inputs, ( i, input ) => {
                formData[input.name] = input.value;
            } );
            formData['action'] = 'cquiz_add_new_question';
            $.ajax( {
                url: quizParams.ajaxUrl,
                type: 'post',
                dataType: 'json',
                data: formData,
                beforeSend: () => {
                    $( '.cquiz-action-buttons .spinner' ).css( 'visibility', 'visible' );
                },
                success: ( response ) => {
                    window.parent.postMessage( {
                        'value': 'post_id',
                        'option_name': response.data.question_title,
                        'select_id': select_id,
                        'result': response
                    }, window.location.origin );
                    $( '.cquiz-action-buttons .spinner' ).css( 'visibility', 'hidden' );
                    if ( response.success ) {
                        setTimeout( () => {
                            cqCloseModal();
                        }, 1000 );
                    }
                }
            });
        }

    });

    $( document ).on( 'click', '#cquiz_email_export', function(e) {
        e.preventDefault();
        var exportButton = $(this);
        $.ajax( {
            url: quizParams.ajaxUrl,
            type: 'post',
            dataType: 'json',
            data: {
                action: 'cquiz_export_email_list',
                nonce: document.getElementById( 'cquiz_email_export_nonce' ).value
            },
            beforeSend: () => {
                exportButton.css( 'pointer-events', 'none' );
                document.body.style.opacity = '0.7';
            },
            success: ( response ) => {
                exportButton.css( 'pointer-events', 'auto' );
                document.body.style.opacity = '1';
                if ( response.success ) {
                    let link = document.createElement( 'a' );
                    let file = new Blob( [response.data.file_content], { type: 'text/csv' } );
                    const currentDate = new Date();
                    let year = currentDate.getFullYear();
                    let month = currentDate.getMonth();
                    let day = currentDate.getDate();
                    link.href = URL.createObjectURL( file );
                    link.download = `quiz-emails-${year}${month}${day}${currentDate.getHours()}${currentDate.getMinutes()}${currentDate.getSeconds()}.csv`;
                    link.click();
                    URL.revokeObjectURL( link.href );
                } else {
                    alert( response.data );
                }
            },
            error: function( error ) {
                alert( error.responseJSON.data );
                console.log( error );
            }
        });
    });

})( jQuery );
