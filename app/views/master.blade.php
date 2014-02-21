<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>
          @if ( !empty($pageTitle) && $pageTitle !== '' )
            {{ $pageTitle }}
          @endif
        </title>
        <meta name="description" content="">
        <meta name="viewport" content="width=device-width, initial-scale=1">

		  {{ HTML::style('/css/style.css') }}
      {{ HTML::style('/css/font-awesome-4.0.3/css/font-awesome.min.css') }}
  
  		{{ HTML::script('/js/jquery-1.10.2.min.js') }}
  		{{ HTML::script('/js/modernizr-2.6.2.min.js') }}
    </head>
    <body>
        <!--[if lt IE 7]>
            <p class="browsehappy">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
        <![endif]-->

        <header>
            <nav class="navbar navbar-fixed-top navbar-default" role="navigation">
                <!-- Brand and toggle get grouped for better mobile display -->
                <div class="container">
                    <div class="navbar-header">
                    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                        {{ link_to_route('home', 'Community App', null, array('class' => 'navbar-brand')) }}
                    </div>

                    <!-- The search form -->
                    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                        <div class="col-sm-4 col-md-4">
                        {{ Form::open(array('url' => 'search', 'method' => 'get', 'class'=>'navbar-form'))}}
                        <div class="input-group">
                            <input type="text" class="form-control" placeholder="Search" name="q" id="search" required>
                            <span class="input-group-btn">
                                <button class="btn btn-default" type="submit"><i class="fa fa-search"></i></button>
                            </span>
                        </div>
                        {{ Form::close() }}
                    </div>
                    
                    <ul class="nav navbar-nav">
                        {{ Helper::getActiveClass('topics', '', '', false, '') }}<a href="{{ URL::to('topics')}}">Topics</a></li>
                        <li><a href="#">Explore</a></li>
                    </ul>
                    
                    <ul class="nav navbar-nav navbar-right">
                        @if( ! Auth::check())
                          <li>{{ HTML::link('register', 'Register') }}</li>
                          <li>{{ link_to("#", 'Login', array('data-toggle' => 'modal', 'data-target' => '#login-form')) }}</li>
                        @else
                        <li class="dropdown">
                            <?php $countNotifications = Auth::user()->countNotifications(); ?>
                            <a href="#" class="dropdown-toggle dropdown-animation" data-toggle="dropdown"><i class="fa fa-smile-o fa-fw dark-icon"></i>Hi, {{ Auth::user()->username }} <span class="badge notification-badge">{{ $countNotifications }}</span> <i class="fa fa-angle-down"></i></a>
                            <ul class="dropdown-menu">
                                <li><a href="{{ URL::to('notifications')}}"><i class="fa fa-lightbulb-o fa-fw dark-icon small-margin-right"></i>Notifications <span class="badge notification-badge">{{ $countNotifications }}</span></a></li>
                                <li><a href="{{ URL::to('new-post') }}"><i class="fa fa-pencil fa-fw dark-icon small-margin-right"></i>New Post</a></li>
                                <li><a href="{{ URL::to('my-posts') }}"><i class="fa fa-files-o fa-fw dark-icon small-margin-right"></i>My Posts</a></li>
                                <li><a href="{{ URL::to('favourite-posts') }}"><i class="fa fa-files-o fa-fw dark-icon small-margin-right"></i>Favourite Posts</a></li>
                                <li><a href="{{ URL::to('my-comments') }}"><i class="fa fa-comments-o fa-fw dark-icon small-margin-right"></i>My Comments</a></li>
                                <li><a href="{{ URL::to('logout') }}"><i class="fa fa-sign-out fa-fw dark-icon small-margin-right"></i>Log Out</a></li>
                            </ul>
                        </li>
                        @endif
                    </ul>
                    </div><!-- /.navbar-collapse -->
                </div>
            </nav>
        </header>
        
        <div id="content">
            <div class="container">
                <div class="row">
                    <div class="col-md-8">
                        {{-- yield site main content --}}
                        @yield('main-content')
                    </div>
                    <div class="col-md-4">
                        {{-- yield sidebar content --}}
                        @yield('sidebar')
                    </div>
                    </div>
                </div>
            </div>
        </div>

        <footer id="footer" class="text-center">
          <span class="grey-span">Laravel Community App</span>
        </footer>

        @if( ! Auth::check() )
          {{ User::loginFormHtml() }}
        @endif

        {{ HTML::script('/js/bootstrap.min.js') }}
        {{ HTML::script('/js/plugins.js') }}
        {{ HTML::script('/js/main.js') }}

        <script>
          (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
          (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
          m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
          })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

          ga('create', 'UA-48208708-1', 'nymble.io');
          ga('send', 'pageview');

        </script>
    </body>
</html>
