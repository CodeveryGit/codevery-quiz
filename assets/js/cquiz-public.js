(function( $ ) {
    'use strict';

    $( document ).ready( function() {

        const button = $( '.cquiz__button' ),
            form = $( '.cquiz__form' ),
            questionBlock = form.find( '.cquiz__wrap' ),
            maxQuestionPage = parseInt( questionBlock.attr( 'data-question-length' ) ),
            quizId = parseInt( questionBlock.attr( 'data-quiz-id' ) );
        let questionPage = parseInt( questionBlock.attr( 'data-question-page' ) );

        button.on( 'click', function() {

            $( '.cquiz__title h2' ).fadeOut( 300 );
            $( '.cquiz__content[data-page = ' + questionPage + ']' ).fadeOut( 300 );
            $( this ).fadeOut( 300 );
            $( this ).attr( 'disabled', true ).text( $( this ).data( 'next_text' ) );
            $( 'html, body' ).animate( { scrollTop: $( '.cquiz' ).offset().top - 50 }, 500 );
            let that = $( this );

            setTimeout( function() {

                questionBlock.attr( 'data-question-page', ( questionPage + 1 ).toString() );
                questionPage = questionPage + 1;

                $( '.cquiz__title h2' ).html( $( '.cquiz__page-title[data-page = ' + questionPage + ']' ).html() ).fadeIn( 500 );
                $( '.cquiz__content[data-page = ' + questionPage + ']' ).fadeIn( 500 );

                if ( maxQuestionPage >= questionPage ) {
                    that.fadeIn( 300 );
                } else {
                    let result = 0;
                    let i = 0;
                    form.find( 'input:checked' ).each( function() {
                        result += parseInt( $( this ).val() );
                        i++;
                    });

                    $( '.cquiz__wrap, .cquiz' ).css( 'min-height', '100%' );

                    if ( result >= form.data( 'max-points' ) ) {

                        const promoCode = makePromo( 8 );

                        let data = {
                            action: 'cquiz_add_coupon_to_database',
                            coupon: promoCode,
                            quiz_id: quizId,
                            cquiz_display_nonce: form.find( 'input[name="cquiz_display_nonce"]' ).val(),
                            _wp_http_referer: form.find( 'input[name="_wp_http_referer"]' ).val(),
                        };

                        $.ajax( {
                            url: quizParams.ajaxUrl,
                            type: 'post',
                            dataType: 'json',
                            data: data,
                            success: function( response ) {
                                if ( ! response.success ) {
                                    console.log( response );
                                }
                            },
                            error: function( error ) {
                                console.log( error );
                            }
                        });
                        showResult( result, true, promoCode );
                    } else {
                        showResult( result,false );
                    }
                }
            }, 500 );

        } );

        $( '.cquiz__form-label' ).on( 'click', function() {

            const input = $( this ).siblings( '.cquiz__form-input' ),
                desc = $( this ).closest( '.cquiz__card' ).find( '.cquiz__card-description' ),
                quizContainer = $( this ).closest( '.cquiz__content' ),
                answer = quizContainer.find( 'input[data-rule="1"]' ),
                answerDescP = desc.find( '.cquiz__card-description-wrap' ).height();

            if ( quizContainer.find( 'input[ type="radio" ]:checked' ).length < 1 ) {
                if ( $( window ).width() < 576 ) {
                    desc.css( { 'height': ( answerDescP + 45 ) + 'px' } );
                } else {
                    desc.css( { 'height': ( answerDescP + 60 ) + 'px' } );
                }

                if ( parseInt( input.attr( 'data-rule' ) ) ) {
                    desc.find( '.cquiz__card-description-title' ).addClass( 'success-title' )
                } else {
                    desc.find( '.cquiz__card-description-title' ).addClass( 'fail-title' );
                    answer.addClass( 'showAnswer' )
                        .closest( '.cquiz__card-block' ).css( 'background-color', '#2a2a2a' )
                        .find( '.cquiz__form-label' ).css( 'color', '#fff' );
                }

                setTimeout( function() {
                    quizContainer.find( 'input[ type="radio" ]' ).each ( function() {
                        $( this ).attr( 'disabled', true );
                        button.attr( 'disabled', false );
                    } );
                }, 0 );
            }
        } );

        function showResult( result, success = true, promo ='' ) {
            let quizResult  = $( '.cquiz__content.cquiz__result' ),
                elClass = success ? 'cquiz__success' : 'cquiz__fail',
                resultText = quizParams.resultText.replace( '{score}', result );

            $( '.cquiz__title h2' ).html( $( '.cquiz__page-title.' + elClass ).html() ).fadeIn( 500 );
            quizResult.fadeIn( 0 )
                .find( '.' + elClass ).fadeIn( 300 )
                .find( '.cquiz__result-points' ).html( resultText );
            if ( success && promo.length > 0 ) {
                quizResult.find( '.coupon-code' ).text( promo );
            }
        }

        function makePromo( length ) {
            let result = '',
                characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789',
                charactersLength = characters.length;
            for ( let i = 0; i < length; i++ ) {
                result += characters.charAt( Math.floor( Math.random() * charactersLength ) );
            }
            return result;
        }

        function removeIfExists( selector ) {
            var el = document.querySelector( selector );
            if ( el ) el.remove();
        }

        const cqSendCouponForm = document.querySelector( '.cquiz-certificate__send-user-coupon' );
        if ( cqSendCouponForm ) {
            const cqEmail = cqSendCouponForm.querySelector('input[name="email"]');
            const cqEmailError = cqSendCouponForm.querySelector('.error');

            cqEmail.addEventListener('input', () => {
                cqEmailError.textContent = '';
                cqEmail.classList.remove('invalid');
                removeIfExists('.cquiz-message-slideup');
            });

            function addErrorMsgToSendCouponForm(form, errorMessage) {
                let message = document.createElement('div');
                message.classList.add('coupon-error-msg', 'cquiz-message-slideup', 'closed');
                message.textContent = errorMessage;
                form.after(message);
                message.classList.remove('closed');
                setTimeout(function () {
                    message.classList.add('closed');
                }, 5000);
                console.error(errorMessage);
            }

            document.querySelector('.cquiz-certificate__send-user-coupon button').addEventListener('click', (event) => {
                event.preventDefault();
                removeIfExists('.cquiz-message-slideup');
                if (cqEmail.validity.valueMissing) {
                    cqEmailError.textContent = quizParams.emptyEmailMsg ? quizParams.emptyEmailMsg : 'Please enter an email address';
                    cqEmail.classList.add('invalid');
                    return false;
                } else if (cqEmail.validity.typeMismatch || !isEmail(cqEmail.value)) {
                    cqEmailError.textContent = quizParams.invalidEmailMsg ? quizParams.invalidEmailMsg : 'Please enter a valid email address.';
                    cqEmail.classList.add('invalid');
                    return false;
                } else {
                    cqEmailError.textContent = '';
                    cqEmail.classList.remove('invalid');
                }

                const couponCode = document.querySelector('.coupon-code').textContent;
                const cquizSendCouponNonce = cqSendCouponForm.querySelector('input[name="cquiz_send_coupon_nonce"]').value;
                const wpHttpReferer = cqSendCouponForm.querySelector('input[name="_wp_http_referer"]').value;
                const request = new XMLHttpRequest();
                const data = `action=cquiz_send_coupon_to_user&coupon=${couponCode}&email=${encodeURIComponent(cqEmail.value)}&quiz_id=${quizId}&_wp_http_referer=${wpHttpReferer}&cquiz_send_coupon_nonce=${cquizSendCouponNonce}`;
                const requestUrl = quizParams.ajaxUrl;
                request.open('POST', requestUrl, true);
                request.setRequestHeader("Content-type", "application/x-www-form-urlencoded; charset=UTF-8");
                request.onload = function () {
                    if (this.status >= 200 && this.status < 400) {
                        const response = JSON.parse(request.response);
                        if (response.status === 'success') {
                            cqEmail.value = '';
                            let message = document.createElement('div');
                            message.classList.add('coupon-success-msg', 'cquiz-message-slideup', 'closed');
                            message.textContent = response.message;
                            cqSendCouponForm.after(message);
                            message.classList.remove('closed');
                            setTimeout(function () {
                                message.classList.add('closed');
                            }, 5000);
                        } else {
                            cqEmail.value = '';
                            addErrorMsgToSendCouponForm(cqSendCouponForm, response.message);
                        }
                    } else {
                        addErrorMsgToSendCouponForm(cqSendCouponForm, 'Request failed');
                    }
                    cqSendCouponForm.classList.remove('cquiz-loading');
                };
                request.onerror = function () {
                    cqSendCouponForm.classList.remove('cquiz-loading');
                    console.error('Request failed');
                };
                cqSendCouponForm.classList.add('cquiz-loading');
                request.send(data);

            });

            function isEmail(email) {
                var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
                return regex.test(email);
            }
        }

    });

})( jQuery );
