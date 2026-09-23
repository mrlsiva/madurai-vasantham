<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

     <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Attendance App') }}</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" >
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css">
    <link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.0.0/css/bootstrap-glyphicons.css">  
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
  </head>
<body class="pos-realtive">

    <!-- Custom Fonts -->
    <link rel="stylesheet" href="{{asset('/resources/css/app.css')}}">

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet" type="text/css">  
</head>

<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container-fluid">
   
    <a class="navbar-brand" href="{{ route('attendance.index') }}">{{ config('app.name', 'Laravel') }}</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item">
            <a class="nav-link active" aria-current="page" href="{{ route('attendance.index') }}">  Users</a>
        </li>
        <li class="nav-item">
            <a class="nav-link active" aria-current="page" href="{{ route('users.create') }}">  Add User</a>
        </li>
        <li class="nav-item">
            <a class="nav-link active" aria-current="page" href="{{ route('attendance.logs') }}">  Logs</a>
        </li>
        <li class="nav-item">
            <a class="nav-link active" aria-current="page" href="{{ route('salary.index') }}">  Salary</a>
        </li>
        <li class="nav-item">
            <a class="nav-link active" aria-current="page" href="{{ route('salary.workingDaysYear') }}">  Working Days</a>
        </li>
      </ul>
      <form action="{{ route('attendance.logout') }}" method="POST" class="d-flex">
        @csrf
        <button type="submit" class="btn btn-outline-light btn-sm"><i class="fa fa-power-off"></i> Logout</button>
      </form>

    </div>
  </div>
</nav>

    <div class="container-fluid py-5">
            @yield('content')
    </div>
    <!-- /.container -->

    <!-- Jquery -->
    <!-- Bootstrap Core JavaScript  -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.18.1/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
	<script src="https://cdn.datatables.net/2.1.8/js/dataTables.js"></script>
	<script src="https://cdn.datatables.net/2.1.8/js/dataTables.bootstrap5.js"></script>
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.0/sweetalert.min.js"></script> 
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bs5-lightbox@1.8.0/dist/index.bundle.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.0/sweetalert.min.js"></script>
    <script src="{{asset('/resources/js/app.js')}}"></script> 
  </body>

</html>
