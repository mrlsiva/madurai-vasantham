<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="">
    <meta name="author" content="">

     <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Vasantham Siru Semippu Thittam') }}</title>

    <!--favicon-->
	<link rel="icon" href="{{asset('/resources/assets_chitfund/images/favicon-32x32.png')}}" type="image/png" />


    <!-- Custom Fonts -->
    <link rel="stylesheet" href="{{asset('/resources/assets_chitfund/css/app.css')}}">

    <!--plugins-->
	<link href="{{asset('/resources/assets_chitfund/plugins/simplebar/css/simplebar.css')}}" rel="stylesheet" />
	<link href="{{asset('/resources/assets_chitfund/plugins/datetimepicker/css/classic.css')}}" rel="stylesheet" />
	<link href="{{asset('/resources/assets_chitfund/plugins/datetimepicker/css/classic.time.css')}}" rel="stylesheet" />
	<link href="{{asset('/resources/assets_chitfund/plugins/datetimepicker/css/classic.date.css')}}" rel="stylesheet" />
	<link rel="stylesheet" href="{{asset('/resources/assets_chitfund/plugins/bootstrap-material-datetimepicker/css/bootstrap-material-datetimepicker.min.css')}}">
	<link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
	<!--Data Tables -->
	<link href="{{asset('/resources/assets_chitfund/plugins/datatable/css/dataTables.bootstrap4.min.css')}}" rel="stylesheet" type="text/css">
	<link href="{{asset('/resources/assets_chitfund/plugins/datatable/css/buttons.bootstrap4.min.css')}}" rel="stylesheet" type="text/css">
	
	<link href="{{asset('/resources/assets_chitfund/plugins/perfect-scrollbar/css/perfect-scrollbar.css')}}" rel="stylesheet" />
	<link href="{{asset('/resources/assets_chitfund/plugins/metismenu/css/metisMenu.min.css')}}" rel="stylesheet" />
	<!-- loader-->
	<link href="{{asset('/resources/assets_chitfund/css/pace.min.css')}}" rel="stylesheet" />
	<script src="{{asset('/resources/assets_chitfund/js/pace.min.js')}}"></script>
	<!-- Bootstrap CSS -->
	<link rel="stylesheet" href="{{asset('/resources/assets_chitfund/css/bootstrap.min.css')}}" />
	<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;600&family=Roboto&display=swap" />
	<!-- Icons CSS -->
	<link rel="stylesheet" href="{{asset('/resources/assets_chitfund/css/icons.css')}}" />
	<!-- App CSS -->
	<link rel="stylesheet" href="{{asset('/resources/assets_chitfund/css/app.css')}}" />
	<link rel="stylesheet" href="{{asset('/resources/assets_chitfund/css/dark-sidebar.css')}}" />
	<link rel="stylesheet" href="{{asset('/resources/assets_chitfund/css/dark-theme.css')}}" />
</head>

<body>
	<!-- wrapper -->
	<div class="wrapper">
		<!--page-wrapper-->
		<div class="page-wrapper mt-4">

			<div>
				@yield('content')
			</div>		
			
		</div><!-- /.page-wrapper -->
		
		<!--start overlay-->
		<div class="overlay toggle-btn-mobile"></div>
		<!--end overlay-->
		<!--Start Back To Top Button--> <a href="javaScript:;" class="back-to-top"><i class='bx bxs-up-arrow-alt'></i></a>
		<!--End Back To Top Button-->
		<!--footer -->
		<div class="footer">
			<p class="mb-0">Vasantham @2024</p>
		</div>
		<!-- end footer -->
	</div><!-- /.wrapper -->

    <script src="{{asset('/resources/assets_chitfund/js/bootstrap.bundle.min.js')}}"></script>
	
	<!--plugins-->
	<script src="{{asset('/resources/assets_chitfund/js/jquery.min.js')}}"></script>
	<script src="{{asset('/resources/assets_chitfund/plugins/simplebar/js/simplebar.min.js')}}"></script>
	<script src="{{asset('/resources/assets_chitfund/plugins/metismenu/js/metisMenu.min.js')}}"></script>
	<script src="{{asset('/resources/assets_chitfund/plugins/perfect-scrollbar/js/perfect-scrollbar.js')}}"></script>
	<script src="{{asset('/resources/assets_chitfund/plugins/datetimepicker/js/legacy.js')}}"></script>
	<script src="{{asset('/resources/assets_chitfund/plugins/datetimepicker/js/picker.js')}}"></script>
	<script src="{{asset('/resources/assets_chitfund/plugins/datetimepicker/js/picker.time.js')}}"></script>
	<script src="{{asset('/resources/assets_chitfund/plugins/datetimepicker/js/picker.date.js')}}"></script>
	<script src="{{asset('/resources/assets_chitfund/plugins/bootstrap-material-datetimepicker/js/moment.min.js')}}"></script>
	<script src="{{asset('/resources/assets_chitfund/plugins/bootstrap-material-datetimepicker/js/bootstrap-material-datetimepicker.min.js')}}"></script>
	<script>
		$('.datepicker').pickadate({
			selectMonths: true,
	        selectYears: true,
			formatSubmit: 'yyyy-mm-dd'
		}),
		$('.timepicker').pickatime();
	</script>
	<script>
		$(function () {
			$('#date-time').bootstrapMaterialDatePicker({
				format: 'YYYY-MM-DD HH:mm'
			});
			$('#date').bootstrapMaterialDatePicker({
				time: false
			});
			$('#time').bootstrapMaterialDatePicker({
				date: false,
				format: 'HH:mm'
			});
		});
	</script>
	
	<!-- Response message poup box -->
	@if( session()->has('success') || $errors->any() )
	<script>
		$(function () {
			$('#ResponseMsgModal').modal('show');
		});
	</script>
	@endif
	
	<!--Data Tables js-->
	<script src="{{asset('/resources/assets_chitfund/plugins/datatable/js/jquery.dataTables.min.js')}}"></script>
	<script>
		$(document).ready(function () {
			//Default data table
			$('#example').DataTable();
			var table = $('#example2').DataTable({
				lengthChange: false,
				buttons: ['copy', 'excel', 'pdf', 'print', 'colvis'],				
				"order": []
			});
			table.buttons().container().appendTo('#example2_wrapper .col-md-6:eq(0)');
		});
	</script>
			
	<!-- App JS -->
	<script src="{{asset('/resources/assets_chitfund/js/app.js')}}"></script>
   
  </body>

</html>
