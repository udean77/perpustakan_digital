<!DOCTYPE html>
<html dir="ltr" lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!-- Tell the browser to be responsive to screen width -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <!-- Favicon icon -->
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('backend/images/icon_bsi.png') }}">
    <title>TokoOnline</title>
    <!-- Custom CSS -->
    <link rel="stylesheet" type="text/css" href="{{ asset('backend/extra-libs/multicheck/multicheck.css') }}">
    <link href="{{ asset('backend/libs/datatables.net-bs4/css/dataTables.bootstrap4.css') }}" rel="stylesheet">
    <link href="{{ asset('backend/dist/css/style.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
<![endif]-->
</head>

<body>
    <!-- ============================================================== -->
    <!-- Preloader - style you can find in spinners.css -->
    <!-- ============================================================== -->
    <div class="preloader">
        <div class="lds-ripple">
            <div class="lds-pos"></div>
            <div class="lds-pos"></div>
        </div>
    </div>
    <!-- ============================================================== -->
    <!-- Main wrapper - style you can find in pages.scss -->
    <!-- ============================================================== -->
    <div id="main-wrapper">
        <!-- ============================================================== -->
        <!-- Topbar header - style you can find in pages.scss -->
        <!-- ============================================================== -->
        <header class="topbar" data-navbarbg="skin5">
            <nav class="navbar top-navbar navbar-expand-md navbar-dark">
                <div class="navbar-header" data-logobg="skin5">
                    <!-- This is for the sidebar toggle which is visible on mobile only -->
                    <a class="nav-toggler waves-effect waves-light d-block d-md-none" href="javascript:void(0)"><i class="ti-menu ti-close"></i></a>
                    <!-- ============================================================== -->
                    <!-- Logo -->
                    <!-- ============================================================== -->
                    <a class="navbar-brand" href="index.html">
                        <!-- Logo icon -->
                        <b class="logo-icon p-l-10">
                            <!--You can put here icon as well // <i class="wi wi-sunset"></i> //-->
                            <!-- Dark Logo icon -->
                            <img src="{{ asset('backend/images/logo-pustaka.png') }}" alt="Logo" height="40">
                           
                        </b>
                        <!--End Logo icon -->
                         <!-- Logo text -->
                        <!-- Logo icon -->
                        <!-- <b class="logo-icon"> -->
                            <!--You can put here icon as well // <i class="wi wi-sunset"></i> //-->
                            <!-- Dark Logo icon -->
                            <!-- <img src="assets/images/logo-text.png" alt="homepage" class="light-logo" /> -->
                            
                        <!-- </b> -->
                        <!--End Logo icon -->
                    </a>
                    <!-- ============================================================== -->
                    <!-- End Logo -->
                    <!-- ============================================================== -->
                    <!-- ============================================================== -->
                    <!-- Toggle which is visible on mobile only -->
                    <!-- ============================================================== -->
                    <a class="topbartoggler d-block d-md-none waves-effect waves-light" href="javascript:void(0)" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation"><i class="ti-more"></i></a>
                </div>
                <!-- ============================================================== -->
                <!-- End Logo -->
                <!-- ============================================================== -->
                <div class="navbar-collapse collapse" id="navbarSupportedContent" data-navbarbg="skin5">
                    <!-- ============================================================== -->
                    <!-- toggle and nav items -->
                    <!-- ============================================================== -->
                    <ul class="navbar-nav float-left mr-auto">
                        <li class="nav-item d-none d-md-block"><a class="nav-link sidebartoggler waves-effect waves-light" href="javascript:void(0)" data-sidebartype="mini-sidebar"><i class="mdi mdi-menu font-24"></i></a></li>
                        <!-- ============================================================== -->
                        <!-- create new -->
                        <!-- ============================================================== -->
                        
                        <!-- ============================================================== -->
                        <!-- Search -->
                        <!-- ============================================================== -->
                        
                    </ul>
                    <!-- ============================================================== -->
                    <!-- Right side toggle and nav items -->
                    <!-- ============================================================== -->
                    <ul class="navbar-nav float-right">
                        <!-- ============================================================== -->
                        <!-- Comment -->
                        <!-- ============================================================== -->
        
                        <!-- ============================================================== -->
                        <!-- End Comment -->
                        <!-- ============================================================== -->
                        <!-- ============================================================== -->
                        <!-- Messages -->
                        <!-- ============================================================== -->
                        <!-- ============================================================== -->
                        <!-- End Messages -->
                        <!-- ============================================================== -->

                        <!-- ============================================================== -->
                        <!-- User profile and search -->
                        <!-- ============================================================== -->
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle text-muted waves-effect waves-dark pro-pic" href="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><img src="{{asset('backend/images/logo-icon.png') }}" alt="user" class="rounded-circle" width="31"></a>
                            <div class="dropdown-menu dropdown-menu-right user-dd animated">
                            <a class="dropdown-item" href="" onclick="event.preventDefault(); document.getElementById('keluar-app').submit();"><i class="fa fa-power-off m-r-5 m-1-5"></i>Keluar</a>
                                <div class="dropdown-divider"></div>
                            </div>
                        </li>
                        <!-- ============================================================== -->
                        <!-- User profile and search -->
                        <!-- ============================================================== -->
                    </ul>
                </div>
            </nav>
        </header>
        <!-- ============================================================== -->
        <!-- End Topbar header -->
        <!-- ============================================================== -->
        <!-- ============================================================== -->
        <!-- Left Sidebar - style you can find in sidebar.scss  -->
        <!-- ============================================================== -->
        <aside class="left-sidebar" data-sidebarbg="skin5">
    <!-- Sidebar scroll-->
    <div class="scroll-sidebar">
        <!-- Sidebar navigation-->
        <nav class="sidebar-nav">
            <ul id="sidebarnav" class="p-t-30">
                <!-- 📊 Statistik Utama -->
                <li class="sidebar-item">
                    <a class="sidebar-link waves-effect waves-dark sidebar-link" href="{{ route('admin.dashboard') }}" aria-expanded="false">
                        <i class="mdi mdi-view-dashboard"></i><span class="hide-menu">Dashboard</span>
                    </a>
                </li>

                <!-- 👥 Manajemen Pengguna -->
                <li class="sidebar-item">
                    <a class="sidebar-link waves-effect waves-dark sidebar-link" href="{{ route('admin.users.index') }}" aria-expanded="false">
                        <i class="mdi mdi-account-multiple"></i><span class="hide-menu">Manajemen Pengguna</span>
                    </a>
                </li>

                <!-- 🏪 Manajemen Penjual -->
                <li class="sidebar-item">
                    <a class="sidebar-link waves-effect waves-dark sidebar-link" href="{{ route('admin.seller.index') }}" aria-expanded="false">
                        <i class="mdi mdi-store"></i><span class="hide-menu">Manajemen Penjual</span>
                    </a>
                </li>

                <!-- 📚 Manajemen Buku -->
                <li class="sidebar-item">
                    <a href="{{ route('admin.books.index') }}" class="sidebar-link waves-effect waves-dark" aria-expanded="false">
                        <i class="mdi mdi-book-open"></i>
                        <span class="hide-menu">Manajemen Buku</span>
                    </a>
                </li>


                <!-- 📦 Manajemen Pesanan -->
                <li class="sidebar-item">
                    <a class="sidebar-link waves-effect waves-dark sidebar-link" href="{{ route('admin.orders.index') }}" aria-expanded="false">
                        <i class="mdi mdi-cart"></i><span class="hide-menu">Manajemen Pesanan</span>
                    </a>
                </li>


                <!-- 💰 Manajemen kode redeem -->
                <li class="sidebar-item">
                    <a class="sidebar-link waves-effect waves-dark sidebar-link" href="{{ route('admin.redeem_code.index') }}" aria-expanded="false">
                        <i class="mdi mdi-cash"></i><span class="hide-menu">Manajemen kode redeem</span>
                    </a>
                </li>

                <!-- 💰 Manajemen Transaksi / Keuangan -->
                <li class="sidebar-item">
                    <a class="sidebar-link waves-effect waves-dark sidebar-link" href="{{ route('admin.transaction.index') }}" aria-expanded="false">
                        <i class="mdi mdi-cash-multiple"></i><span class="hide-menu">Manajemen Keuangan</span>
                    </a>
                </li>

                <!-- 📬 Manajemen Konten / Laporan -->
                <li class="sidebar-item">
                    <a href="{{ route('admin.reports.index') }}" class="sidebar-link waves-effect waves-dark">
                        <i class="mdi mdi-folder"></i><span class="hide-menu">Konten & Laporan</span>
                    </a>
                </li>

                <!-- 💬 Riwayat Chat User -->
                <li class="sidebar-item">
                    <a href="{{ url('/admin/chat-histories') }}" class="sidebar-link waves-effect waves-dark">
                        <i class="mdi mdi-chat"></i><span class="hide-menu">Riwayat Chat User</span>
                    </a>
                </li>

            </ul>
        </nav>
        <!-- End Sidebar navigation -->
    </div>
    <!-- End Sidebar scroll-->
</aside>

<!-- Sidebar overlay for mobile -->
<div class="sidebar-overlay"></div>

        <!-- ============================================================== -->
        <!-- End Left Sidebar - style you can find in sidebar.scss  -->
        <!-- ============================================================== -->
        <!-- ============================================================== -->
        <!-- Page wrapper  -->
        <!-- ============================================================== -->
        <div class="page-wrapper">
            <!-- ============================================================== -->
            <!-- Container fluid  -->
            <!-- ============================================================== -->
            <div class="container-fluid">
                <!-- ============================================================== -->
                <!-- Start Page Content -->
                <!-- ============================================================== -->
                 @yield('content')
                
                <!-- ============================================================== -->
                <!-- End PAge Content -->
                <!-- ============================================================== -->
                <!-- ============================================================== -->
                <!-- Right sidebar -->
                <!-- ============================================================== -->
                <!-- .right-sidebar -->
                <!-- ============================================================== -->
                <!-- End Right sidebar -->
                <!-- ============================================================== -->
            </div>
            <!-- ============================================================== -->
            <!-- End Container fluid  -->
            <!-- ============================================================== -->
            <!-- ============================================================== -->
            <!-- footer -->
            <!-- ============================================================== -->
            <footer class="footer text-center">
               PustakaDigital.co.id &copy; 2025
            </footer>
            <!-- ============================================================== -->
            <!-- End footer -->
            <!-- ============================================================== -->
        </div>
        <!-- ============================================================== -->
        <!-- End Page wrapper  -->
        <!-- ============================================================== -->
    </div>
    <!-- ============================================================== -->
    <!-- End Wrapper -->
    <!-- ============================================================== -->
    <!-- ============================================================== -->
    <!-- All Jquery -->
    <!-- ============================================================== -->
    <script src="{{asset('backend/libs/jquery/dist/jquery.min.js') }}"></script>
    <!-- Bootstrap tether Core JavaScript -->
    <script src="{{ asset('backend/libs/popper.js/dist/popper.min.js') }}"></script>
    <script src="{{ asset('backend/libs/bootstrap/dist/js/bootstrap.min.js') }}"></script>
    <!-- slimscrollbar scrollbar JavaScript -->
    <script src="{{ asset('backend/libs/perfect-scrollbar/dist/perfect-scrollbar.jquery.min.js') }}"></script>
    <script src="{{ asset('backend/extra-libs/sparkline/sparkline.js') }} "></script>
    <!--Wave Effects -->
    <script src="{{ asset('backend/dist/js/waves.js') }}"></script>
    <!--Menu sidebar -->
    <script src="{{ asset('backend/dist/js/sidebarmenu.js') }}"></script>
    <!--Custom JavaScript -->
    <script src="{{ asset('backend/dist/js/custom.min.js') }}"></script>
    <!-- this page js -->
    <script src="{{ asset('backend/extra-libs/multicheck/datatable-checkbox-init.js') }}"></script>
    <script src="{{ asset('backend/extra-libs/multicheck/jquery.multicheck.js') }}"></script>
    <script src="{{ asset('backend/extra-libs/DataTables/datatables.min.js') }}"></script>
    <script>
        /****************************************
         *       Basic Table                   *
         ****************************************/
        $('#zero_config').DataTable();
        
        // Fixed Navbar and Sidebar functionality
        $(document).ready(function() {
            // Handle mobile sidebar toggle
            $('.nav-toggler').on('click', function(e) {
                e.preventDefault();
                $('aside.left-sidebar').toggleClass('show-sidebar');
                $('.sidebar-overlay').toggleClass('show');
            });
            
            // Close sidebar when clicking overlay
            $('.sidebar-overlay').on('click', function() {
                $('aside.left-sidebar').removeClass('show-sidebar');
                $(this).removeClass('show');
            });
            
            // Close sidebar when clicking outside on mobile
            $(document).on('click', function(e) {
                if ($(window).width() <= 767) {
                    if (!$(e.target).closest('aside.left-sidebar, .nav-toggler').length) {
                        $('aside.left-sidebar').removeClass('show-sidebar');
                        $('.sidebar-overlay').removeClass('show');
                    }
                }
            });
            
            // Handle window resize
            $(window).on('resize', function() {
                if ($(window).width() > 767) {
                    $('aside.left-sidebar').removeClass('show-sidebar');
                    $('.sidebar-overlay').removeClass('show');
                }
            });
            
            // Ensure proper scroll behavior
            if (typeof PerfectScrollbar !== 'undefined') {
                $('.scroll-sidebar').each(function() {
                    new PerfectScrollbar(this);
                });
            }
            
            // Add smooth scrolling to sidebar links
            $('.sidebar-link').on('click', function() {
                if ($(window).width() <= 767) {
                    $('aside.left-sidebar').removeClass('show-sidebar');
                    $('.sidebar-overlay').removeClass('show');
                }
            });
            
            // Add active class to current page
            var currentPath = window.location.pathname;
            $('.sidebar-link').each(function() {
                var href = $(this).attr('href');
                if (href && currentPath.includes(href.split('/').pop())) {
                    $(this).addClass('active');
                }
            });
            
            // Handle mini sidebar toggle
            $('.sidebartoggler').on('click', function() {
                $('body').toggleClass('mini-sidebar');
                if ($('body').hasClass('mini-sidebar')) {
                    $('.page-wrapper').css('margin-left', '70px');
                } else {
                    $('.page-wrapper').css('margin-left', '260px');
                }
            });
            
            // Ensure proper height for content
            function adjustContentHeight() {
                var windowHeight = $(window).height();
                var navbarHeight = 70;
                var contentHeight = windowHeight - navbarHeight;
                $('.page-wrapper').css('min-height', contentHeight + 'px');
            }
            
            // Call on load and resize
            adjustContentHeight();
            $(window).on('resize', adjustContentHeight);
            
            // Smooth scroll to top
            $('html, body').animate({
                scrollTop: 0
            }, 300);
        });
    </script>
    <form id="keluar-app" action="{{ route('logout') }}" method="post" class="d-none">
        @csrf
    </form>
</body>

</html>