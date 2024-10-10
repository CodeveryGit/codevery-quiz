(function( $ ) {
    'use strict';

    $( document ).ready( function() {

        var codeveryQuiz = {
            init: function() {
                this.button = $( '.cquiz__button' );
                this.form = $( '.cquiz__form' );
                this.questionBlock = this.form.find( '.cquiz__wrap' );
                this.maxQuestionPage = parseInt( this.questionBlock.attr( 'data-question-length' ) );
                this.quizId = parseInt( this.questionBlock.attr( 'data-quiz-id' ) );
                this.pageId = this.form.data( 'page_id' );
                this.timer = this.form.data( 'timer' );
                this.progressBar = this.form.data( 'progress_bar' );
                this.questionPage = parseInt( this.questionBlock.attr( 'data-question-page' ) );

                if ( this.timer ) {
                    this.timerInitialized = false;
                    this.timerInterval = '';
                    this.timeLeft = this.form.data( 'time' ); // Time in seconds (e.g., 120 = 2 minutes)
                    this.timerElement = $('#cquiz__countdown-time');
                }

                this.bindEvents();
            },
            bindEvents: function() {
                this.button.on( 'click', this.quizProcess.bind(this) );
                $( '.cquiz__form-label' ).on( 'click', this.optionClick.bind(this) );
            },
            quizProcess: function(e) {
                let $this = $(e.currentTarget);
                $( '.cquiz__title h2' ).fadeOut( 300 );
                $( '.cquiz__content[data-page = ' + this.questionPage + ']' ).fadeOut( 300 );
                $this.fadeOut( 300 );
                $this.attr( 'disabled', true ).text( $this.data( 'next_text' ) );
                $( 'html, body' ).animate( { scrollTop: $( '.cquiz' ).offset().top - 50 }, 500 );

                setTimeout( function() {

                    this.questionBlock.attr( 'data-question-page', ( this.questionPage + 1 ).toString() );
                    this.questionPage = this.questionPage + 1;

                    if ( this.progressBar ) {
                        let countdownLineWidth = this.questionPage * 100 / this.maxQuestionPage;
                        $('.cquiz__countdown-line-bg').css( 'display', 'block' );
                        $('.cquiz__countdown-line').css('width', countdownLineWidth + '%');
                    }

                    $( '.cquiz__title h2' ).html( $( '.cquiz__page-title[data-page = ' + this.questionPage + ']' ).html() ).fadeIn( 500 );
                    $( '.cquiz__content[data-page = ' + this.questionPage + ']' ).fadeIn( 500 );

                    if ( this.maxQuestionPage >= this.questionPage ) {
                        if ( this.timer && ! this.timerInitialized ) {
                            this.timerElement.css( 'display', 'block' );
                            this.timerInitialized = true;
                            this.setQuizTimer();
                            this.timerInterval = setInterval( this.setQuizTimer.bind(this), 1000 );
                        }
                        $this.fadeIn( 300 );
                    } else {
                        if ( this.progressBar ) {
                            $('.cquiz__countdown-line-bg').css( 'display', 'none' );
                        }
                        this.submitQuiz();
                    }
                }.bind(this), 500 );
            },
            setQuizTimer: function() {
                let minutes = Math.floor(this.timeLeft / 60);
                let seconds = this.timeLeft % 60;
                seconds = seconds < 10 ? '0' + seconds : seconds;
                this.timerElement.html( minutes + ":" + seconds );
                if (this.timeLeft > 0) {
                    this.timeLeft--;
                } else {
                    this.submitQuiz();
                    $( document ).trigger( 'cquiz-timer-end' );
                }
            },
            stopTimer: function() {
                clearInterval(this.timerInterval);
                this.button.fadeOut( 300 );
                this.timerElement.css( 'display', 'none' );
                console.log("Timer stopped.");

            },
            submitQuiz: function() {
                if ( this.timer ) {
                    this.stopTimer();
                }
                if ( this.progressBar ) {
                    $('.cquiz__countdown-line-bg').css( 'display', 'none' );
                }
                let result = 0;
                let i = 0;
                this.form.find( 'input:checked' ).each( function() {
                    result += parseInt( $( this ).val() );
                    i++;
                });

                $( '.cquiz__wrap, .cquiz' ).css( 'min-height', '100%' );

                if ( result >= this.form.data( 'max-points' ) ) {

                    const promoCode = this.makePromo( 8 );

                    let data = {
                        action: 'cquiz_add_coupon_to_database',
                        coupon: promoCode,
                        quiz_id: this.quizId,
                        cquiz_display_nonce: this.form.find( 'input[name="cquiz_display_nonce"]' ).val(),
                        _wp_http_referer: this.form.find( 'input[name="_wp_http_referer"]' ).val(),
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
                    this.showResult( result, true, promoCode );
                } else {
                    this.showResult( result, false );
                }
            },
            showResult: function( result, success = true, promo ='' ) {
                let quizResult  = $( '.cquiz__content.cquiz__result' ),
                    elClass = success ? 'cquiz__success' : 'cquiz__fail',
                    resultText = quizParams.resultText.replace( '{score}', result );

                $( '.cquiz__content:not(.cquiz__result)' ).fadeOut();
                $( '.cquiz__title h2' ).html( $( '.cquiz__page-title.' + elClass ).html() ).fadeIn( 500 );
                quizResult.fadeIn( 0 )
                    .find( '.' + elClass ).fadeIn( 300 )
                    .find( '.cquiz__result-points' ).html( resultText );
                if ( success && promo.length > 0 ) {
                    quizResult.find( '.coupon-code' ).text( promo );
                }
                $( document ).trigger( 'cquiz-show-result', [ result, success ] );
            },
            optionClick: function(e) {
                const $this = $(e.currentTarget),
                    input = $this.siblings( '.cquiz__form-input' ),
                    desc = $this.closest( '.cquiz__card' ).find( '.cquiz__card-description' ),
                    quizContainer = $this.closest( '.cquiz__content' ),
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

                    var codeveryQuizObj = this;
                    setTimeout( () => {
                        quizContainer.find( 'input[ type="radio" ]' ).each ( function() {
                            $this.attr( 'disabled', true );
                            codeveryQuizObj.button.attr( 'disabled', false );
                        } );
                    }, 0 );
                }
            },
            makePromo: function( length ) {
                let result = '',
                    characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789',
                    charactersLength = characters.length;
                for ( let i = 0; i < length; i++ ) {
                    result += characters.charAt( Math.floor( Math.random() * charactersLength ) );
                }
                return result;
            }
        };

        codeveryQuiz.init();

        var codeveryQuizCouponForm = {
            init: function() {
                this.cqSendCouponForm = document.querySelector( '.cquiz-certificate__send-user-coupon' );
                if ( this.cqSendCouponForm ) {
                    this.quizId = parseInt( $( '.cquiz__wrap' ).attr( 'data-quiz-id' ) );
                    this.cqEmail = this.cqSendCouponForm.querySelector('input[name="email"]');
                    this.cqEmailError = this.cqSendCouponForm.querySelector('.error');
                    this.cqEmail.addEventListener('input', () => {
                        this.cqEmailError.textContent = '';
                        this.cqEmail.classList.remove('invalid');
                        this.removeIfExists('.cquiz-message-slideup');
                    });
                    $('.cquiz-certificate__send-user-coupon button').on('click', this.sendUserCoupon.bind(this) );
                }
            },
            addErrorMsgToSendCouponForm: function(form, errorMessage) {
                let message = document.createElement('div');
                message.classList.add('coupon-error-msg', 'cquiz-message-slideup', 'closed');
                message.textContent = errorMessage;
                form.after(message);
                message.classList.remove('closed');
                setTimeout(function () {
                    message.classList.add('closed');
                }, 5000);
                console.error(errorMessage);
            },
            sendUserCoupon: function( event ) {
                event.preventDefault();
                this.removeIfExists('.cquiz-message-slideup');
                if (this.cqEmail.validity.valueMissing) {
                    this.cqEmailError.textContent = quizParams.emptyEmailMsg ? quizParams.emptyEmailMsg : 'Please enter an email address';
                    this.cqEmail.classList.add('invalid');
                    return false;
                } else if (this.cqEmail.validity.typeMismatch || !this.isEmail(this.cqEmail.value)) {
                    this.cqEmailError.textContent = quizParams.invalidEmailMsg ? quizParams.invalidEmailMsg : 'Please enter a valid email address.';
                    this.cqEmail.classList.add('invalid');
                    return false;
                } else {
                    this.cqEmailError.textContent = '';
                    this.cqEmail.classList.remove('invalid');
                }

                const couponCode = document.querySelector('.coupon-code').textContent;
                const cquizSendCouponNonce = this.cqSendCouponForm.querySelector('input[name="cquiz_send_coupon_nonce"]').value;
                const wpHttpReferer = this.cqSendCouponForm.querySelector('input[name="_wp_http_referer"]').value;
                const request = new XMLHttpRequest();
                const requestUrl = quizParams.ajaxUrl;
                let data = `action=cquiz_send_coupon_to_user&coupon=${couponCode}&email=${encodeURIComponent(this.cqEmail.value)}&quiz_id=${this.quizId}&_wp_http_referer=${wpHttpReferer}&cquiz_send_coupon_nonce=${cquizSendCouponNonce}`;
                let couponFormObj = this;
                request.open('POST', requestUrl, true);
                request.setRequestHeader("Content-type", "application/x-www-form-urlencoded; charset=UTF-8");
                request.onload = function () {
                    if (this.status >= 200 && this.status < 400) {
                        const response = JSON.parse(request.response);
                        if (response.status === 'success') {
                            couponFormObj.cqEmail.value = '';
                            let message = document.createElement('div');
                            message.classList.add('coupon-success-msg', 'cquiz-message-slideup', 'closed');
                            message.textContent = response.message;
                            couponFormObj.cqSendCouponForm.after(message);
                            message.classList.remove('closed');
                            setTimeout(function () {
                                message.classList.add('closed');
                            }, 5000);
                        } else {
                            cqEmail.value = '';
                            this.addErrorMsgToSendCouponForm(couponFormObj.cqSendCouponForm, response.message);
                        }
                    } else {
                        this.addErrorMsgToSendCouponForm(couponFormObj.cqSendCouponForm, 'Request failed');
                    }
                    couponFormObj.cqSendCouponForm.classList.remove('cquiz-loading');
                };
                request.onerror = function () {
                    couponFormObj.cqSendCouponForm.classList.remove('cquiz-loading');
                    console.error('Request failed');
                };
                this.cqSendCouponForm.classList.add('cquiz-loading');
                request.send(data);
            },
            removeIfExists: function( selector ) {
                var el = document.querySelector( selector );
                if ( el ) el.remove();
            },
            isEmail: function( email ) {
                var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
                return regex.test(email);
            }
        };

        codeveryQuizCouponForm.init();

    });

})( jQuery );
