@if(!Auth::check())
window.location.href = "https://canada777.com/login/";
@endif

@php
 $randnum = mt_rand(100000,999999);
 $year = date('Y');
 $current_time = strtotime('now');
 $depo_id = "DEP".$randnum.$year.$current_time;

$mysqli = mysqli_connect("localhost", "www", "2C0q4N5z");
$conn = mysqli_select_db($mysqli, "www");

$active_user = Auth::user()->username;

$query_active_user = mysqli_query($mysqli, "SELECT * FROM w_users WHERE username='$active_user'");
$fetch_active_user = mysqli_fetch_array($query_active_user);

$first_name = $fetch_active_user['first_name'];
$last_name = $fetch_active_user['last_name'];
$phone = $fetch_active_user['phone'];
$email = $fetch_active_user['email'];

@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no">
    <title>Canada777 - My wallet</title>
    <link rel="icon" type="image/x-icon" href="/frontend/userdashboard/assets/img/favicon.ico"/>
    <link href="/frontend/userdashboard/assets/css/loader.css" rel="stylesheet" type="text/css" />
    <script src="/frontend/userdashboard/assets/js/loader.js"></script>

    <!-- BEGIN GLOBAL MANDATORY STYLES -->
    <link href="https://fonts.googleapis.com/css?family=Quicksand:400,500,600,700&display=swap" rel="stylesheet">
    <link href="/frontend/userdashboard/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="/frontend/userdashboard/assets/css/plugins.css" rel="stylesheet" type="text/css" />
    <!-- END GLOBAL MANDATORY STYLES -->

    <!-- BEGIN PAGE LEVEL /frontend/userdashboard/plugins/CUSTOM STYLES -->
    <link href="/frontend/userdashboard/plugins/apex/apexcharts.css" rel="stylesheet" type="text/css">
    <link href="/frontend/userdashboard/assets/css/dashboard/dash_2.css" rel="stylesheet" type="text/css" />
    <!-- END PAGE LEVEL /frontend/userdashboard/plugins/CUSTOM STYLES -->
    <!-- BEGIN PAGE LEVEL STYLES -->
    <link rel="stylesheet" type="text/css" href="/frontend/userdashboard/plugins/table/datatable/datatables.css">
    <link rel="stylesheet" type="text/css" href="/frontend/userdashboard/plugins/table/datatable/dt-global_style.css">
    <!-- END PAGE LEVEL STYLES -->

    <script src="http://code.jquery.com/jquery-1.9.1.js"></script>
    <!-- Global site tag (gtag.js) - Google Analytics -->

    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-185160427-1"></script>

    <script>

        window.dataLayer = window.dataLayer || [];

        function gtag(){dataLayer.push(arguments);}

        gtag('js', new Date());

        gtag('config', 'UA-185160427-1');

    </script>
</head>
<body class="alt-menu sidebar-noneoverflow">
    <!-- BEGIN LOADER -->
    <div id="load_screen"> <div class="loader"> <div class="loader-content">
        <div class="spinner-grow align-self-center"></div>
    </div></div></div>
    <!--  END LOADER -->

    <!--  BEGIN NAVBAR  -->
    <div class="header-container">
        <header class="header navbar navbar-expand-sm">

            <a href="javascript:void(0);" class="sidebarCollapse" data-placement="bottom"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-menu"><line x1="3" y1="12" x2="21" y2="12"></line><line x1="3" y1="6" x2="21" y2="6"></line><line x1="3" y1="18" x2="21" y2="18"></line></svg></a>

            <div class="nav-logo align-self-center">
                <a class="navbar-brand" href="https://canada777.com"><img alt="logo" src="/frontend/userdashboard/assets/img/logo.png"> <span class="navbar-brand-name">Canada777</span></a>
            </div>

            <ul class="navbar-item flex-row mr-auto">
                <li class="nav-item align-self-center search-animated" style="display:none;">
                    <form class="form-inline search-full form-inline search" role="search">
                        <div class="search-bar">
                            <input type="text" class="form-control search-form-control  ml-lg-auto" placeholder="Search...">
                        </div>
                    </form>
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-search toggle-search"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                </li>
            </ul>

            <ul class="navbar-item flex-row nav-dropdowns">




                <li class="nav-item dropdown user-profile-dropdown order-lg-0 order-1">
                    <a href="javascript:void(0);" class="nav-link dropdown-toggle user" id="user-profile-dropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <div class="media">
                            <img src="/frontend/userdashboard/assets/img/90x90.jpg" class="img-fluid" alt="admin-profile">
                            <div class="media-body align-self-center">
                                <h6>{{ Auth::user()->username }}</h6>
                            </div>
                        </div>
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-down"><polyline points="6 9 12 15 18 9"></polyline></svg>
                    </a>
                    <div class="dropdown-menu position-absolute animated fadeInUp" aria-labelledby="user-profile-dropdown">
                        <div class="">

                            <div class="dropdown-item">
                                <a class="" href="https://canada777.com/logout"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-log-out"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg> Sign Out</a>
                            </div>
                        </div>
                    </div>

                </li>
            </ul>
        </header>
    </div>
    <!--  END NAVBAR  -->

    <!--  BEGIN MAIN CONTAINER  -->
    <div class="main-container" id="container">

        <div class="overlay"></div>
        <div class="search-overlay"></div>

        <!--  BEGIN TOPBAR  -->
        <div class="topbar-nav header navbar" role="banner">
            <nav id="topbar">
                <ul class="navbar-nav theme-brand flex-row  text-center">
                    <li class="nav-item theme-logo">
                        <a href="https://canada777.com">
                            <img src="/frontend/userdashboard/assets/img/90x90.jpg" class="navbar-logo" alt="logo">
                        </a>
                    </li>
                    <li class="nav-item theme-text">
                        <a href="https://canada777.com" class="nav-link"> Canada777 </a>
                    </li>
                </ul>

                <ul class="list-unstyled menu-categories" id="topAccordion">

                    <li class="menu single-menu active">
                        <a href="#dashboard"  class="dropdown-toggle autodroprown">
                            <div class="">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-home"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>
                                <span>Dashboard</span>
                            </div>
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-down"><polyline points="6 9 12 15 18 9"></polyline></svg>
                        </a>
                    </li>

                    <li class="menu single-menu">
                        <a href="https://canada777.com"  class="dropdown-toggle">
                            <div class="">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-cpu"><rect x="4" y="4" width="16" height="16" rx="2" ry="2"></rect><rect x="9" y="9" width="6" height="6"></rect><line x1="9" y1="1" x2="9" y2="4"></line><line x1="15" y1="1" x2="15" y2="4"></line><line x1="9" y1="20" x2="9" y2="23"></line><line x1="15" y1="20" x2="15" y2="23"></line><line x1="20" y1="9" x2="23" y2="9"></line><line x1="20" y1="14" x2="23" y2="14"></line><line x1="1" y1="9" x2="4" y2="9"></line><line x1="1" y1="14" x2="4" y2="14"></line></svg>
                                <span>Game lobby</span>
                            </div>
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-down"><polyline points="6 9 12 15 18 9"></polyline></svg>
                        </a>

                    </li>




                </ul>
            </nav>
        </div>
        <!--  END TOPBAR  -->

        <!--  BEGIN CONTENT PART  -->
        <div id="content" class="main-content">
            <div class="layout-px-spacing">


                <div class="row layout-top-spacing">


                        <div id="tabsAlignments" class="col-lg-12 col-12 layout-spacing">
                            <div class="statbox widget box box-shadow">
                                <div class="widget-header">
                                    <div class="row">
                                        <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                                            <h4 class="justify-tab">My Wallet</h4>
                                        </div>
                                    </div>
                                </div>
                                <div class="widget-content widget-content-area justify-tab">

                                    <ul class="nav nav-tabs  mb-3 mt-3 nav-fill" id="justifyTab" role="tablist">
                                        <li class="nav-item">
                                            <a class="nav-link active" id="justify-home-tab" data-toggle="tab" href="#justify-home" role="tab" aria-controls="justify-home" aria-selected="true">Summary</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" id="justify-profile-tab" data-toggle="tab" href="#justify-profile" role="tab" aria-controls="justify-profile" aria-selected="false">Actions</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" id="justify-contact-tab" data-toggle="tab" href="#justify-contact" role="tab" aria-controls="justify-contact" aria-selected="false">Transactions</a>
                                        </li>
                                    </ul>

                                    <div class="tab-content" id="justifyTabContent">
                                        <div class="tab-pane fade show active" id="justify-home" role="tabpanel" aria-labelledby="justify-home-tab">

                                            <br/>

                                              <div class="row">
                                                <div class="col-xl-4 col-lg-6 col-md-6 col-sm-6 col-12 layout-spacing">
                        <div class="widget widget-card-four">
                            <div class="widget-content">
                                <div class="w-content">
                                    <div class="w-info">
                                        <h6 class="value">${{Auth::user()->balance }}</h6>
                                        <p class="">Balance</p>
                                    </div>
                                    <div class="">
                                        <div class="w-icon">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-home"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>
                                        </div>
                                    </div>
                                </div>
                                <div class="progress">
                                    <div class="progress-bar bg-gradient-secondary" role="progressbar" style="width: 100%" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                                                 <div class="col-xl-4 col-lg-6 col-md-6 col-sm-6 col-12 layout-spacing">
                        <div class="widget widget-card-four">
                            <div class="widget-content">
                                <div class="w-content">
                                    <div class="w-info">
                                        <h6 class="value">${{Auth::user()->balance }}</h6>
                                        <p class="">Deposits</p>
                                    </div>
                                    <div class="">
                                        <div class="w-icon">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-home"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>
                                        </div>
                                    </div>
                                </div>
                                <div class="progress">
                                    <div class="progress-bar bg-gradient-secondary" role="progressbar" style="width: 100%" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                                                   <div class="col-xl-4 col-lg-6 col-md-6 col-sm-6 col-12 layout-spacing">
                        <div class="widget widget-card-four">
                            <div class="widget-content">
                                <div class="w-content">
                                    <div class="w-info">
                                        <h6 class="value">${{Auth::user()->balance }}</h6>
                                        <p class="">Withdrawals</p>
                                    </div>
                                    <div class="">
                                        <div class="w-icon">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-home"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>
                                        </div>
                                    </div>
                                </div>
                                <div class="progress">
                                    <div class="progress-bar bg-gradient-secondary" role="progressbar" style="width: 100%" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                                               </div>
                                        </div>
                                        <div class="tab-pane fade" id="justify-profile" role="tabpanel" aria-labelledby="justify-profile-tab">

                                            <div style="margin:100px auto auto auto; " class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12 layout-spacing">

                                  <center>
                                      <!-- Button trigger modal -->
                                    <button style="width:200px; height:100px;" type="button" class="btn btn-dark mb-2 mr-2" data-toggle="modal" data-target="#depositModal">
                                      Deposit
                                    </button>
                                    <!-- Button trigger modal -->
                                    <button style="width:200px; height:100px;" type="button" class="btn btn-secondary mb-2 mr-2" data-toggle="modal" data-target="#withdrawModal">
                                      Withdraw
                                    </button>

                                 </center>


                    </div>


                                        </div>
                                        <div class="tab-pane fade" id="justify-contact" role="tabpanel" aria-labelledby="justify-contact-tab">

                                            table


                                        </div>
                                    </div>
                            </div>
  </div>







                </div>



            </div>
        </div>
        <!--  END CONTENT PART  -->

    </div>
    <!-- END MAIN CONTAINER -->


   <!-- Modal -->
                                    <div class="modal fade login-modal" id="depositModal" tabindex="-1" role="dialog" aria-labelledby="loginModalLabel" aria-hidden="true">
                                      <div class="modal-dialog" role="document">
                                        <div class="modal-content">

                                          <div class="modal-header" id="loginModalLabel">
                                            <h4 class="modal-title">Make Deposit</h4>
                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg></button>
                                          </div>
                                          <div class="modal-body">

                                              <!---frontend/userdashboard/post.php-->
                                            <form id="deposit_form" class="mt-0" class="form-horizontal" action="/frontend/userdashboard/post.php" method="POST">

                                             <div class="row">
                                                 <div class="col-md-12">
                                                      <div class="form-group">
                                                <label>Email</label>
                                                <input type="email"  class="form-control mb-2" placeholder="Email" name="email" value="{{ $email }}">
                                              </div>

                                              <div class="form-group">
                                                <label>Phone Number</label>
                                                <input type="text"  class="form-control mb-4" placeholder="Phone no" value="{{ $phone }}" name="mobile">
                                              </div>

                                               <div class="form-group" style="display:none;">
                                                <label>User id</label>


                                                <input type="text" class="form-control mb-4" placeholder="User ID" name="userId" value="{{ Auth::user()->id }}">
                                              </div>
                                              <div class="form-group" style="display:none;">
                                                <label>transactionId</label>
                                                <input type="text" class="form-control mb-4" placeholder="transaction ID" name="transactionId" value="{{$depo_id}}">
                                              </div>


                                                 </div>

                                                 <div class="col-md-12">

                                               <div class="form-group" style="display:none;">
                                                <label>name</label>
                                                <input type="text"  class="form-control mb-4" placeholder="name" name="name" value="{{ $first_name }} {{ $last_name }}">
                                              </div>

                                                    <div class="form-group">
                                                <label>Please enter the deposit amount</label>
                                                <input type="text" class="form-control mb-4" placeholder="Amount" name="amount" value="">
                                              </div>

                                                 </div>
                                             </div>


                                              <button type="submit" name="deposit" class="btn btn-primary mt-2 mb-2 btn-block">Submit</button>

                                            </form>


                                          </div>
                                        </div>
                                      </div>
                                    </div>


                                    <!-- Modal -->
                                    <div class="modal fade register-modal" id="withdrawModal" tabindex="-1" role="dialog" aria-labelledby="registerModalLabel" aria-hidden="true">
                                      <div class="modal-dialog" role="document">
                                        <div class="modal-content">

                                          <div class="modal-header" id="registerModalLabel">
                                            <h4 class="modal-title">Make a Withdraw</h4>
                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg></button>
                                          </div>
                                          <div class="modal-body">
                                              <!---frontend/userdashboard/post.php-->
                                            <form id="deposit_form" class="mt-0" class="form-horizontal" action="/frontend/userdashboard/get.php" method="POST">

                                             <div class="row">
                                                 <div class="col-md-12">
                                                      <div class="form-group">
                                                <label>Email</label>
                                                <input type="email"  class="form-control mb-2" placeholder="Email" name="email" value="{{ $email }}">
                                              </div>

                                              <div class="form-group">
                                                <label>Phone Number</label>
                                                <input type="text"  class="form-control mb-4" placeholder="Phone no" value="{{ $phone }}" name="mobile">
                                              </div>

                                               <div class="form-group" style="display:none;">
                                                <label>User id</label>


                                                <input type="text" class="form-control mb-4" placeholder="User ID" name="userId" value="{{ Auth::user()->id }}">
                                              </div>
                                              <div class="form-group" style="display:none;">
                                                <label>transactionId</label>
                                                <input type="text" class="form-control mb-4" placeholder="transaction ID" name="transactionId" value="{{$depo_id}}">
                                              </div>


                                                 </div>

                                                 <div class="col-md-12">

                                               <div class="form-group" style="display:none;">
                                                <label>name</label>
                                                <input type="text"  class="form-control mb-4" placeholder="name" name="name" value="{{ $first_name }} {{ $last_name }}">
                                              </div>


                                                    <div class="form-group">
                                                <label>Please enter the withdrawal amount</label>
                                                <input type="text" class="form-control mb-4" placeholder="Amount" name="amount" value="">
                                              </div>

                                                 </div>
                                             </div>


                                              <button type="submit" name="deposit" class="btn btn-primary mt-2 mb-2 btn-block">Submit</button>

                                            </form>


                                          </div>

                                        </div>
                                      </div>
                                    </div>



    <!-- BEGIN GLOBAL MANDATORY SCRIPTS -->
    <script src="/frontend/userdashboard/assets/js/libs/jquery-3.1.1.min.js"></script>
    <script src="/frontend/userdashboard/bootstrap/js/popper.min.js"></script>
    <script src="/frontend/userdashboard/bootstrap/js/bootstrap.min.js"></script>
    <script src="/frontend/userdashboard/plugins/perfect-scrollbar/perfect-scrollbar.min.js"></script>
    <script src="/frontend/userdashboard/assets/js/app.js"></script>
    <script>
        $(document).ready(function() {
            App.init();
        });
    </script>
    <script src="/frontend/userdashboard/assets/js/custom.js"></script>
    <!-- END GLOBAL MANDATORY SCRIPTS -->

    <!-- BEGIN PAGE LEVEL /frontend/userdashboard/plugins/CUSTOM SCRIPTS -->
    <script src="/frontend/userdashboard/plugins/apex/apexcharts.min.js"></script>
    <script src="/frontend/userdashboard/assets/js/dashboard/dash_2.js"></script>
    <!-- BEGIN PAGE LEVEL /frontend/userdashboard/plugins/CUSTOM SCRIPTS -->
    <script src="/frontend/userdashboard/plugins/table/datatable/datatables.js"></script>
    <script>
        $('#default-ordering').DataTable( {
            "oLanguage": {
                "oPaginate": { "sPrevious": '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-left"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>', "sNext": '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-right"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>' },
                "sInfo": "Showing page _PAGE_ of _PAGES_",
                "sSearch": '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-search"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>',
                "sSearchPlaceholder": "Search...",
               "sLengthMenu": "Results :  _MENU_",
            },
            "order": [[ 3, "desc" ]],
            "stripeClasses": [],
            "lengthMenu": [7, 10, 20, 50],
            "pageLength": 7,
            drawCallback: function () { $('.dataTables_paginate > .pagination').addClass(' pagination-style-13 pagination-bordered mb-5'); }
        } );
    </script>
</body>
</html>


