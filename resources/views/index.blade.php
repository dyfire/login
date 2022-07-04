<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>{{config('admin.title')}} | {{ trans('admin.login') }}</title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <!-- Bootstrap 3.3.5 -->
    <link rel="stylesheet" href="{{ admin_asset("/css/bootstrap.min.css") }}">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ admin_asset("/css/font-awesome.min.css") }}">
    <!-- Theme style -->
    <link rel="stylesheet" href="{{ admin_asset("/css/Adminlogin.min.css") }}">
    <!-- iCheck -->
    <link rel="stylesheet" href="{{ admin_asset("/css/blue.css") }}">
    <link rel="shortcut icon" href="/images/8D.ico">
    <link rel="stylesheet" href="/css/login.css" type="text/css" media="all">
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="//oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="//oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>

<body>
<section class="main">
    <div class="logo text-center">
        <img src="/images/logo.png" width="260" height="55">
    </div>
    <div class="content-w3ls text-center">
        <img src="/images/admin.png" alt="" class="img-responsive">
        <form action="{{ admin_base_path('auth/login') }}" method="post">
            @if($errors->has('username'))
                @foreach($errors->get('username') as $message)
                    <label class="control-label" for="inputError"><i class="fa fa-times-circle-o"></i>{{$message}}
                    </label><br>
                @endforeach
            @endif
            @if($errors->has('password'))
                @foreach($errors->get('password') as $message)
                    <label class="control-label" for="inputError"><i class="fa fa-times-circle-o"></i>{{$message}}
                    </label><br>
                @endforeach
            @endif
            <div class="wthree-field">
                <input type="text" placeholder="用户名" name="username" value="{{ old('username') }}" />
            </div>
            <div class="wthree-field">
                <input type="Password" placeholder="密&nbsp;&nbsp;码" name="password" value="{{ old('username') }}" />
            </div>
            <div class="wthree-field">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <button type="submit" class="btn">登&nbsp;&nbsp;录</button>
            </div>

        </form>
    </div>
    <div class="copyright">
        <p>{!! env('APP_COPYRIGHT') !!} | <a href="https://beian.miit.gov.cn/">苏ICP备19018123号</a></p>
    </div>
</section>
<!-- jQuery 2.1.4 -->
<script src="{{ admin_asset("/js/jQuery-2.1.4.min.js")}} "></script>
<!-- Bootstrap 3.3.5 -->
<script src="{{ admin_asset("/js/bootstrap.min.js")}}"></script>
<!-- iCheck -->
<script src="{{ admin_asset("/js/icheck.min.js")}}"></script>
<script>
    $(function () {
        $('input').iCheck({
            checkboxClass: 'icheckbox_square-blue',
            radioClass: 'iradio_square-blue',
            increaseArea: '20%' // optional
        });
    });
</script>
</body>
</html>