<!-- Left side column. contains the logo and sidebar -->
<aside class="main-sidebar">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
        <!-- search form (Optional) -->
        {{--<form action="#" method="get" class="sidebar-form">
            <div class="input-group">
                <input type="text" name="q" class="form-control" placeholder="Search ..."/>
              <span class="input-group-btn">
                <button type="submit" name="search" id="search-btn" class="btn btn-flat"><i class="fa fa-search"></i></button>
              </span>
            </div>
        </form>--}}
        <!-- /.search form -->
        <!-- Sidebar Menu -->
        @if (Auth::user() -> role == 1)
            <ul class="sidebar-menu">
                <li>
                    <a href="{{ route('dashboard') }}">
                        <i class="fa fa-dashboard"></i>
                        <span>總覽</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin-doctors-list') }}">
                        <i class="fa fa-plus-square"></i>
                        <span>員工管理</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin-members-list') }}">
                        <i class="fa fa-users"></i>
                        <span>會員管理</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin-services-list') }}">
                        <i class="fa fa-support"></i>
                        <span>服務管理</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin-services-list-by-thismonth') }}">
                        <i class="fa fa-support"></i>
                        <span>本月成交服務</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin-videos-list') }}">
                        <i class="fa fa-video-camera"></i>
                        <span>影片管理</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin-managers-list') }}">
                        <i class="fa fa-video-camera"></i>
                        <span>業務管理</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin-settins-form') }}">
                        <i class="fa fa-cog"></i>
                        <span>系統管理</span>
                    </a>
                </li>
                <li>
                    <a href="https://www.spgateway.com/main/login_center/single_login">
                        <i class="fa fa-link"></i>
                        <span>智付通信用卡</span>
                    </a>
                </li>
                <li>
                    <a href="https://inv.pay2go.com/">
                        <i class="fa fa-link"></i>
                        <span>智付寶電子發票</span>
                    </a>
                </li>
            </ul>
        @elseif(Auth::user() -> manager)
            <ul class="sidebar-menu">
                <li>
                    <a href="{{ route('admin-doctors-add-form') }}">
                        <i class="fa fa-plus-square"></i>
                        <span>新增員工</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin-doctors-list') }}">
                        <i class="fa fa-plus-square"></i>
                        <span>員工管理</span>
                    </a>
                </li>
            </ul>

        @endif
    </section>
</aside>
