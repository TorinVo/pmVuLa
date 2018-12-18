<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Admin | Dashboard</title>
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Tell the browser to be responsive to screen width -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}" type="text/css">
</head>

<body class="hold-transition sidebar-mini">
    <div class="wrapper" id="app">

        <!-- Navbar -->
        <nav class="main-header navbar navbar-expand bg-white navbar-light border-bottom">
            <!-- Left navbar links -->
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" data-widget="pushmenu" href="#"><i class="fa fa-bars"></i></a>
                </li>
                <li class="nav-item d-none d-sm-inline-block">
                    <a href="index3.html" class="nav-link">Home</a>
                </li>
                <li class="nav-item d-none d-sm-inline-block">
                    <a href="#" class="nav-link">Contact</a>
                </li>
            </ul>

            <!-- SEARCH FORM -->
            <form class="form-inline ml-3">
                <div class="input-group input-group-sm">
                    <input class="form-control form-control-navbar" type="search" placeholder="Search" aria-label="Search">
                    <div class="input-group-append">
                        <button class="btn btn-navbar" type="submit">
                            <i class="fa fa-search"></i>
                        </button>
                    </div>
                </div>
            </form>
        </nav>
        <!-- /.navbar -->

        <!-- Main Sidebar Container -->
        <aside class="main-sidebar sidebar-dark-primary elevation-4">
            <!-- Brand Logo -->
            <a href="index3.html" class="brand-link">
                <img src="{{asset('img/avatar.png')}}" alt="AdminLTE Logo" class="brand-image img-circle elevation-3"
                    style="opacity: .8">
                <span class="brand-text font-weight-light">Project Manage</span>
            </a>

            <!-- Sidebar -->
            <div class="sidebar">
                <!-- Sidebar user panel (optional) -->
                <div class="user-panel mt-3 pb-3 mb-3 d-flex">
                    <div class="image">
                        <img src="{{ (Auth::user()->photo) ? asset('img/profile/'.Auth::user()->photo) : asset('img/avatar.png') }}" class="img-circle elevation-2" alt="User Image">
                    </div>
                    <div class="info">
                        <a href="#" class="d-block">
                            {{ Auth::user()->name }}
                        </a>
                    </div>
                </div>

                <!-- Sidebar Menu -->
                <nav class="mt-2">
                    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                        <li class="nav-item">
                            <router-link :to="{ name: 'dashboard' }" class="nav-link" :class="{ 'active': (navSelect === 'dashboard') }">
                                <i class="nav-icon fas fa-tachometer-alt"></i>
                                <p>
                                    Dashboard
                                </p>
                            </router-link>
                        </li>
                        <li class="nav-item">
                            <router-link :to="{ name: 'tickets' }" class="nav-link" :class="{ 'active': (navSelect === 'tickets' || navSelect === 'ticket') }">
                                <i class="nav-icon fa fa-life-ring"></i>
                                <p>
                                    Tickets <span class="badge badge-danger right">@{{ticketOpen}}</span>
                                </p>
                            </router-link>
                        </li>
                        @hasanyrole('Admin|Designer')
                    <li class="nav-item has-treeview" :class="{ 'menu-open': (navSelect === 'users' || navSelect === 'projects') }">
                            <a href="#" class="nav-link" :class="{ active: (navSelect === 'users' || navSelect === 'projects') }">
                                <i class="nav-icon fas fa-list-alt"></i>
                                <p>
                                    Management
                                    <i class="fa fa-angle-left right"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <router-link :to="{ name: 'users' }" class="nav-link" :class="{ 'active': (navSelect === 'users') }">
                                        <i class="fas fa-users nav-icon"></i>
                                        <p>Users</p>
                                    </router-link>
                                </li>
                                <li class="nav-item">
                                    <router-link :to="{ name: 'projects' }" active-class="active" class="nav-link" :class="{ 'active': (navSelect === 'projects') }">
                                        <i class="fa fa-th-large nav-icon"></i>
                                        <p>Projects</p>
                                    </router-link>
                                </li>
                            </ul>
                        </li>
                        @endhasanyrole
                        @role('Admin')
                        <li class="nav-item">
                            <router-link :to="{ name: 'developer' }" class="nav-link" :class="{ 'active': (navSelect === 'developer') }">
                                <i class="nav-icon fas fa-cogs"></i>
                                <p>
                                    Developer
                                </p>
                            </router-link>
                        </li>
                        @endrole
                        <li class="nav-item">
                            <router-link :to="{ name: 'profile'} " class="nav-link" :class="{ 'active': (navSelect === 'profile') }">
                                <i class="nav-icon fas fa-user-shield"></i>
                                <p>
                                    Profile
                                </p>
                            </router-link>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('logout') }}" onclick="event.preventDefault();
                          document.getElementById('logout-form').submit();">
                                <i class="nav-icon fas fa-sign-out-alt"></i>
                                <p>
                                    {{ __('Logout') }}
                                </p>
                            </a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                @csrf
                            </form>
                        </li>
                    </ul>
                </nav>
                <!-- /.sidebar-menu -->
            </div>
            <!-- /.sidebar -->
        </aside>

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <router-view></router-view>
            <vue-progress-bar></vue-progress-bar>
            <div class="floating-chat">
                <i class="fa fa-comments" style="color: white;" aria-hidden="true"></i>
                <div class="chat">
                    <va-direct-chat
                    theme="vue"
                    title="Direct Chat"
                    :badgeCount="30"
                    placeholder="Type Message ..."
                    ></va-direct-chat>
                </div>
            </div>
        </div>
        <!-- /.content-wrapper -->
        <footer class="main-footer">
            <strong>Copyright &copy; 2018 </strong>
        </footer>

        <!-- Control Sidebar -->
        <aside class="control-sidebar control-sidebar-dark">
            <!-- Control sidebar content goes here -->
        </aside>
        <!-- /.control-sidebar -->
    </div>
    <!-- ./wrapper -->

    <!-- Scripts -->
    @auth
        <script>
            window.user = @json(auth()->user()->load('roles'))
        </script>
    @endauth
    <script src="{{ asset('js/app.js') }}" defer></script>
</body>

</html>
