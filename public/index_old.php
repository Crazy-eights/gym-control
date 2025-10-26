<!DOCTYPE html>
<html>
<head>
  	<meta charset="utf-8">
  	<meta http-equiv="X-UA-Compatible" content="IE=edge">
  	<title>Gym Control | Login</title>
  	<meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  	
    <link rel="stylesheet" href="{{ asset('bower_components/bootstrap/dist/css/bootstrap.min.css') }}">
  	<link rel="stylesheet" href="{{ asset('bower_components/font-awesome/css/font-awesome.min.css') }}">
  	<link rel="stylesheet" href="{{ asset('dist/css/AdminLTE.min.css') }}">

  	<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">

</head>
<body class="hold-transition login-page">
<div class="login-box">
  	<div class="login-logo">
  		<b>Gym Control</b>
  	</div>
  
  	<div class="login-box-body">
    	<p class="login-box-msg">Ingresa tus credenciales de Administrador</p>

    	<form action="{{ route('admin.login.attempt') }}" method="POST">
      		@csrf <div class="form-group has-feedback @error('username') has-error @enderror">
        		<input type="text" class="form-control" name="username" placeholder="Usuario" value="{{ old('username') }}" required>
        		<span class="glyphicon glyphicon-user form-control-feedback"></span>
            @error('username')
                <span class="help-block">{{ $message }}</span>
            @enderror
      		</div>
          
      		<div class="form-group has-feedback">
        		<input type="password" class="form-control" name="password" placeholder="Contraseña" required>
        		<span class="glyphicon glyphicon-lock form-control-feedback"></span>
      		</div>
      		
      		<div class="row">
    			<div class="col-xs-12">
          			<button type="submit" class="btn btn-primary btn-block btn-flat">
                <i class="fa fa-sign-in"></i> Ingresar
              </button>
        		</div>
      		</div>
    	</form>
  	</div>
</div>
	
<script src="{{ asset('bower_components/jquery/dist/jquery.min.js') }}"></script>
<script src="{{ asset('bower_components/bootstrap/dist/js/bootstrap.min.js') }}"></script>
</body>
</html>