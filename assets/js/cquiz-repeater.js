(function( $ ) {
    'use strict';
    $.fn.extend( {
        createRepeater: function( options = {} ) {
            let ajaxArgs = {
                url: quizParams.ajaxUrl,
                delay: 150,
                data: function( params ) {
                    return {
                        q: params.term,
                        nonce: document.getElementById( 'cquiz_question_metabox_nonce' ).value,
                        action: 'cquiz_get_questions',
                    }
                },
                dataType: 'json',
                processResults: function( data ) {
                    var options = [];
                    if ( data ) {
                        data.forEach( function( value, index ) {
                            options.push( { id: value[0], text: value[1] } );
                        } );
                    }
                    return {
                        results: options
                    };
                },
                minimumInputLength: 2,
            };
            let hasOption = function( optionKey ) {
                return options.hasOwnProperty( optionKey );
            };

            let option = function( optionKey ) {
                return options[optionKey];
            };

            let generateId = function( string ) {
                return string
                    .replace( /\[/g, '_' )
                    .replace( /\]/g, '' )
                    .toLowerCase();
            };

            let addItem = function( items, key, fresh = true ) {
                let itemContent = items;
                let group = itemContent.data( 'group' );
                let input = itemContent.find( 'input,select,textarea' );

                input.each( function( index, el ) {
                    let attrName = $( el ).data( 'name' );
                    let skipName = $( el ).data( 'skip-name' );
                    let answerRadio = $( el ).data( 'name' ) === 'answer';
                    if ( ! skipName ) {
                        $( el ).attr( 'name', group + '[' + key + ']' + '[' + attrName + ']' );
                    } else {
                        if ( attrName != 'undefined' ) {
                            $( el ).attr( 'name', attrName );
                        }
                    }
                    if ( answerRadio ) {
                        $( el ).attr( 'value', key );
                    }
                    if ( fresh ) {
                        $( el ).attr( 'value', '' );
                    }

                    $( el ).attr( 'id', generateId( $( el ).attr( 'name' ) ) );
                    $( el ).parent().find( 'label' ).attr( 'for', generateId( $( el ).attr( 'name' ) ) );
                });

                let itemClone = items;

                /* Handling remove btn */
                let removeButton = itemClone.find( '.remove-btn' );

                if ( key == 0 ) {
                    removeButton.attr( 'disabled', true );
                } else {
                    removeButton.attr( 'disabled', false );
                }

                removeButton.attr( 'onclick', 'jQuery(this).parents(\'.item\').remove()' );

                let newItem = $( '<div class="item">' + itemClone.html() + '<div/>' );
                newItem.attr( 'data-index', key );
                newItem.addClass( 'add-select2' );
                newItem.find( '.item-order-number' ).html( key + 1 );

                newItem.appendTo( repeater );
                items.find( 'input, select, textarea' ).each( function( index, el ) {
                    $( el ).attr( 'name', '' );
                });
                $( '.add-select2 select' ).select2().closest( '.item' ).removeClass( 'add-select2' );
            };

            /* find elements */
            const repeater = $( '#quiz-repeater-items' );
            let items = repeater.find( '.item-hidden' );
            let addButton = this.find( '.cquiz-add-item' );

            $( document ).ready( function() {
                $( '#quiz_settings .item select.quiz-question' ).select2();
            });


            /* handle click and add items */
            addButton.on( 'click', function() {
                let key = $( '#quiz-repeater-items' ).find( '.item' ).length;
                addItem( $( items[0] ), key, false );
            });
        }
    });
})( jQuery );
