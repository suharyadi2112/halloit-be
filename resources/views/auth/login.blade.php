<!DOCTYPE html>
<html lang="en">
<head>
    <script>
        fetch(window.location.origin + '/api/check_valid_token', {
            method: 'GET',
            headers: {
                'Authorization': localStorage.getItem('access_token')
            }
        })
        .then(response => {
            if (response.ok) {
                return response.json();
            }
            throw new Error('Network response was not ok.');
        })
        .then(data => {
            console.log(data);
            if (data.status === 'success') {
                window.location.href = "{{ route('dashboard') }}";
            }
        })
        .catch(error => {
            console.error('There was a problem with the fetch operation:', error);
        });
    </script>
    <title>Login - {{ config('app.name') }}</title>
	@include('partial.asset.head')
</head>
<body class="login">
	<div class="wrapper wrapper-login">
		<div class="container container-login">
            
			<h3 class="text-center">
                <img src="{{ asset('/assets/img/logologin.png') }}" alt="" style="width: 70%">
            </h3>
			<div class="login-form">
                <form method="post" action="{{ route('login.index') }}" id="login">
                    <div class="form-group form-floating-label">
                        <input id="email" name="email" type="email" class="form-control input-border-bottom">
                        <label for="email" class="placeholder">Email</label>
                    </div>
                    <div class="form-group form-floating-label">
                        <input id="password" name="password" type="password" class="form-control input-border-bottom">
                        <label for="password" class="placeholder">Password</label>
                        <div class="show-password">
                            <i class="icon-eye"></i>
                        </div>
                    </div>
                    <div class="form-action mb-3">
                        <button type="submit" class="btn btn-rounded btn-login" style="background-color: #00BF63;color:white">Sign In</button>
                    </div>
                </form>
			</div>
		</div>
	</div>        
    @include('partial.asset.script')
    <script src="{{ asset('assets/js/plugin/bootstrap-notify/bootstrap-notify.min.js') }}"></script>
    <script>
            $(document).ready(function() {
                $('#login').submit(function(e) {
                    e.preventDefault();
                    var token = $('meta[name="csrf-token"]').attr('content');
                    var url = window.location.origin +"/api/login";   
                    $.ajax({
                        method:'POST',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        url: url,
                        dataType: 'json', 
                        contentType:'application/json', 
                        data: JSON.stringify({
                            email: $("#email").val(),
                            password: $("#password").val()
                        }),
                        beforeSend: function(xhr) {
                            $('#login').find('button[type="submit"]').text('Loading...').attr('disabled', true);
                            $('#email').prop('disabled', true);
                            $('#password').prop('disabled', true);
                        },
                        success: function(data) {
                            response = data;
                            localStorage.setItem('access_token', response.data.access_token);
                            showNotification('flaticon-success', 'Success', response.message, 'success');
                            setTimeout(function() {
                                window.location.href = "{{ route('dashboard') }}";
                            }, 450);
                        },
                        error: function(data) {
                            error = data.responseJSON.message
                            if(typeof error === 'object'){
                                var errors='';
                                    $.each(error, function(key, value){
                                    errors += value+'<br>';
                                });
                                console.log(errors);
                                showNotification('flaticon-error', 'Error', errors, 'danger');

                            }else{
                                showNotification('flaticon-error', 'Error', error, 'danger');
                            }
                        },
                        complete: function(data) {
                            $('#login').find('button[type="submit"]').text('Sign In').attr('disabled', false);
                            $('#email').prop('disabled', false);
                            $('#password').prop('disabled', false);
                        }
                    })
                });
            });

            function showNotification(icon, title, message, type) {
                $.notify({
                    icon: icon,
                    title: title,
                    message: message
                },{
                    type: type,
                    placement: {
                        from: "top",
                        align: "right"
                    },
                    time: 500,
                });
            }
    </script>
</body>
</html>