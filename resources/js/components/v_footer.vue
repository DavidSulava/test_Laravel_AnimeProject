<template>

    <div  class="footer_wrapper">

        <div v-show="v_mail"   class="emessageWrap container">


            <form @submit.prevent="contactMail" class="form-group row col-xs-10 col-xs-offset-1 col-md-6 col-md-offset-3">



                <a v-on:click= "show_hide_mail()" class="closeMe foterCl">×</a>
                <label>Email:</label><br>
                <input ref="email" type="email" class="sender form-control" placeholder="Enter your email">
                <span class="mailErr text-danger"><small ref="email_error"></small></span><br>



                <div class="form-group">
                    <label for="comment">Message:</label>
                    <textarea ref="comment" class="form-control" rows="5" id="comment" type="text" placeholder="Enter your message"></textarea>
                    <span class="textErr text-danger"><small ref="comment_error"></small></span>
                </div>

                <label class="dummy"></label>

                <div class="input-group">
                    <div class="g-recaptcha" :data-sitekey="capcha_siteKay">
                    </div>
                </div>

                <span v-show="v_email_error" class="mailErr   text-danger">
                    <small ref="send_email_error"></small>
                </span><br>

                <span v-show="v_email_OK"    class="mailOk  text-success">
                    <small ref="send_email_OK">   </small><br>
                </span><br>

                <div class="form-group">
                    <input   type='submit'  value="SEND" class="btn btn-success col-sm-1 form-control"><br>
                </div>

            </form>


        </div>

        <div class="disc">
            <p  v-on:click= "show_hide_mail()" class="contact">Contact as : <span title="Click to send a message">✉</span></p>
            <p class="f_2">
                <span>Disclaimer: This site does not store any files on its server. All contents are provided by non-affiliated third parties.</span>
            </p>
        </div>
    </div>


</template>

<script>
    import { getFetch } from '../app';

    export default
        {
            name: "v_footer",
            data()
                {
                    return {
                        v_filter_btn  : false,
                        v_mail        : false,
                        v_email_OK    : false,
                        v_email_error : false,
                        capcha_siteKay: process.env.MIX_APP_CAPCHA_SITEKAY,
                        rootUrl_f     : document.querySelector("meta[name='root_url_f']").getAttribute('content'),
                    }
                },
            methods:
                {
                    show_hide_mail: function  ( )
                        {
                            this.v_mail = !this.v_mail;
                        },
                },
            computed:
                {
                    contactMail: function()
                        {
                            return async function ( ev )
                                {

                                    let email         = this.$refs.email.value;
                                    let comment       = this.$refs.comment.value;
                                    let recapcha_resp = ev.target['g-recaptcha-response'].value;

                                    this.$refs.email_error.innerText   = this.validateEmail(email)           ? '': 'The email must be a valid email address';
                                    this.$refs.comment_error.innerText = /^[\w-\d\W]{5,1000}$/.test(comment) ? '': 'The message must be a valid message ( more than 5  and less than 1000 characters )';


                                    if ( !this.$refs.email_error.innerText && !this.$refs.comment_error.innerText && email && comment )
                                        {

                                            //----[ laravel request to - api/default/getMovie ]---------
                                            let url_prepared = this.rootUrl_f.replace(/public[/]?.*/, 'public/ajax/contactmail');

                                            let payload = { 'email': email, 'message': comment, 'g-recaptcha-response': recapcha_resp };

                                            let getM    = await getFetch(url_prepared, "POST", payload );
                                                getM    = typeof getM? getM : JSON.parse(getM);


                                            if(getM == 'true')
                                                {

                                                    //--email  has ben send
                                                    this.v_email_OK    = true;
                                                    this.v_email_error = false;
                                                    this.$refs.send_email_OK.innerText    = 'Your message has been sent. Thank you!';
                                                    this.$refs.send_email_error.innerText = '';

                                                    setTimeout(function(){ location.reload(); }, 2000);
                                                }
                                            else if( getM == 'false' )
                                                {
                                                    this.v_email_OK    = false;
                                                    this.v_email_error = true;
                                                    //--email  somthing went wrong
                                                    this.$refs.send_email_OK.innerText    = '';
                                                    this.$refs.send_email_error.innerText = 'Something went wrong. Your message has not been sent!';
                                                }
                                            else if( getM['erCaptcha'] )
                                                {

                                                    this.v_email_OK    = false;
                                                    this.v_email_error = true;

                                                    //--email  recapcha responce error
                                                    this.$refs.send_email_OK.innerText    = '';
                                                    this.$refs.send_email_error.innerText = `${getM['erCaptcha']}`;


                                                }
                                            else
                                                {
                                                    this.v_email_OK    = false;
                                                    this.v_email_error = false;
                                                }

                                        };


                                };
                        },
                    validateEmail: function (email)
                        {
                            return function (email )
                                {
                                    var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
                                    return re.test(String(email).toLowerCase());
                                }
                        },
                },
            mounted()
                {
                    let scriptEl = document.createElement('script');
                        scriptEl.setAttribute('src', 'https://www.google.com/recaptcha/api.js');
                        scriptEl.defer = true;

                    this.$el.querySelector( '.emessageWrap' ).appendChild(scriptEl);
                },
        }
</script>