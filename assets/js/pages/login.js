$(function () {
    $('#btnLogin').click(function(e){
        e.preventDefault();
        $(this).prop('disabled', true);
        $('#btnLogin .spinner-border').removeClass('d-none');
        $.post( "api/admin/auth/login", { 
            username: $('#username').val(), 
            password: $('#password').val() 
            }).done(function( data ) {
                localStorage.setItem('token', data.token);
                location.reload(); 
            }).fail(function($xhr) {
                var data = $xhr.responseJSON;
                alert(data.message);
            }).always(function() {
                $('#btnLogin').prop('disabled', false);
                $('#btnLogin .spinner-border').addClass('d-none');
            });;
    })
});