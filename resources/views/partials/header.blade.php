<!-- Main Header -->
<header class="main-header">

    <!-- Logo -->
    <a href="{{ route('dashboard') }}" class="logo">
        <!-- mini logo for sidebar mini 50x50 pixels -->
        <span class="logo-mini"><b>Hi</b></span>
        <!-- logo for regular state and mobile devices -->
        <span class="logo-lg"><b>Hi</b> - Health</span>
    </a>

    <!-- Header Navbar -->
    <nav class="navbar navbar-static-top" role="navigation">
        <!-- Sidebar toggle button-->
        <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
            <span class="sr-only">Toggle navigation</span>
        </a>
        <!-- Navbar Right Menu -->
        <div class="navbar-custom-menu">
            <ul class="nav navbar-nav">
                <!-- User Account Menu -->
                @if (Auth::user())
                <li class="dropdown user user-menu">
                    <!-- Menu Toggle Button -->
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <!-- The user image in the navbar-->
                        <!--<img src="" class="user-image" title="User Image" alt="" />-->
                        <!-- hidden-xs hides the username on small devices so only the image appears. -->
                        <span class="hidden-xs">{{ Auth::user()->name }}</span>
                    </a>
                    <ul class="dropdown-menu">
                        <!-- The user image in the menu -->
                        <li class="user-header">
                            <!--<img src="" class="img-circle" alt="User Image" />-->
                            <p>
                                
                                <!--<small>{{ trans('adminlte_lang::message.login') }} Nov. 2012</small>-->
                            </p>
                        </li>
                        <!-- Menu Body -->
                        <li class="user-body">
                            
                        </li>
                        <!-- Menu Footer-->
                        <li class="user-footer">
                            <div class="pull-left">
                                {{--<a href="{{ route('user-profile') }}" class="btn btn-default btn-flat">Profile</a>--}}
                            </div>
                            <div class="pull-right">
                                <a href="{{ route('admin-logout') }}" class="btn btn-default btn-flat"
                                   onclick="event.preventDefault();
                                             document.getElementById('logout-form').submit();">
                                    Logout
                                </a>
                                <form id="logout-form" action="{{ route('admin-logout') }}" method="POST" style="display: none;">
                                    {{ csrf_field() }}
                                    <input type="submit" value="logout" style="display: none;">
                                </form>
                            </div>
                        </li>
                    </ul>
                </li>
                @endif
                {{--<li>
                    <a href="#" data-toggle="control-sidebar"><i class="fa fa-gears"></i></a>
                </li>--}}
            </ul>
        </div>
    </nav>
</header>
