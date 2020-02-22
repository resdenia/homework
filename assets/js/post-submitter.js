jQuery( document ).ready( function ( $ ) {
    $( '#register' ).on( 'submit', function(e) {
        e.preventDefault();
        var company = $( '.company_form' ).val();
        var name = $( '.name_form' ).val();
        var  phone = $( '.number_form' ).val();
        var email = $( '.email_form' ).val();
        var status = 'draft';
 
        var data = {
            title: company,
            excerpt: 'Name: '+ name +',Phone: '+ phone +', Email: '+ email,
        };
 
        $.ajax({
            method: "POST",
            url: POST_SUBMITTER.root + 'wp/v2/leads',
            data: data,
            beforeSend: function ( xhr ) {
                xhr.setRequestHeader( 'X-WP-Nonce', POST_SUBMITTER.nonce );
            },
            success : function( response ) {
                console.log( response );
               // alert( POST_SUBMITTER.success );
               $( '#register' ).css('display', 'none');
               $('.confirm').append('<p>Thank you for subscription</p> Name: ' + name + ', Phone: '+ phone + ', Email: '+ email);
            },
            fail : function( response ) {
                console.log( response );
                alert( POST_SUBMITTER.failure );
            }
 
        });
 
    });
 
} );