<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <!--favicon-->
	<link rel="icon" href="{{asset('/resources/assets_chitfund/images/favicon-32x32.png')}}" type="image/png" />
    <!-- Custom Fonts -->
    <link rel="stylesheet" href="{{asset('/resources/assets_chitfund/css/app.css')}}">
	<link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
	<!--Data Tables -->
	<link href="{{asset('/resources/assets_chitfund/plugins/datatable/css/dataTables.bootstrap4.min.css')}}" rel="stylesheet" type="text/css">
	<link href="{{asset('/resources/assets_chitfund/plugins/datatable/css/buttons.bootstrap4.min.css')}}" rel="stylesheet" type="text/css">

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
@php
use App\Http\Controllers\ChitFund\ChitFundController;
use Carbon\Carbon;
@endphp

@if( !$user->isEmpty() )
	@php
		$plan_name  = $user[0]->plan_name;
		$plan_amount  = $user[0]->plan_amount;
		$billId    = $user[0]->due_id;
		$user_name = $user[0]->user_name;
		$mobile_no = $user[0]->mobile_no;
		$due_date = $user[0]->due_date;
		$due_date_paid = $user[0]->due_date_paid != null ? Carbon::parse($user[0]->due_date_paid)->format('d-m-Y') : '-' ;
		$status_code = $user[0]->due_status;
		$status = ChitFundController::getDueStatus($status_code, $key='string');
		$amount_paid = $status_code == 1 ? $plan_amount : 0;
		$total_dues_paid = $user[0]->total_dues_paid;
		$total_amount_paid = ($total_dues_paid * $amount_paid);
	@endphp
@endif

<style>
/* Print */
.centered {
	text-align: center;
	align-content: center;
}

.printInvoice {
	width: 375px;
	max-width: 375px;
	margin: 0 auto;
}

.printInvoice td,
.printInvoice th,
.printInvoice tr,
.printInvoice table {
	border-bottom: 1px solid black;
	border-collapse: collapse;
}
.token {
	font-size: 68px;
    line-height: 74px;
    border: 1px solid #000;
    width: fit-content;
    margin: 0 auto;
    padding: 0px 24px;
}
.pace-activity {
	display: none;
}
@media print {
    a[href]:after {
        content: none !important;
    }
}
@media print { 
	@page { margin: 0; } 
	body { margin: 1cm; } 
}
</style>
<!-- print block -->
<div id="printInvoice" class="printInvoice">
	<div class="print-header-wrapper centered">
		<img src="{{asset('/resources/assets_chitfund/images/logo-icon-1.png')}}" style="height: 150px;" alt="VASANTHAM SIRU SEMIPPU THITTAM">
		<p class="centered fw-bold mt-3 mb-0">Iyer Bungalow | Alanganallur | Palamedu | Valasai</p>
		<p class="centered fw-bold mt-0">Contact: 99943 33605</p>
		<p class="centered fw-bold mt-2 mb-0">VASANTHAM SIRU SEMIPPU THITTAM</p>
		<p class="centered fw-bold mt-2 mb-1">Token Number</p>
		<p class="centered fw-bold mt-0 mb-3 token">{{ $user[0]->user_id }}</p>
	</div>

	<table class="  mt-2 " style="width:100%">
		<thead>
			<tr>
				<th>Invoice No: <span class="invoice-no"> #{{$billId}}</span></th>
				<th style="text-align: right;">Date: <span class="invoice-date">{{ Carbon::now()->format('Y-m-d H:i:s') }}</span></th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td>User Name</td>
				<td  style="text-align: right;">{{ $user_name }}</td>
			</tr>
			<tr>
				<td>Mobile No</td>
				<td  style="text-align: right;">{{ $mobile_no }}</td>
			</tr>
			<tr>
				<td>Plan Name</td>
				<td  style="text-align: right;">{{ $plan_name }}</td>
			</tr>
			<tr>
				<td>Plan Amount</td>
				<td  style="text-align: right;">{{ $plan_amount }}</td>
			</tr>
			<tr>
				<td>Status</td>
				<td  style="text-align: right;">{{ $status }}</td>
			</tr>
			<tr>
				<td>Amount Paid</td>
				<td  style="text-align: right;">Rs {{ $amount_paid }}</td>
			</tr>
			<tr>
				<td>Amount Paid Date</td>
				<td  style="text-align: right;">{{ $due_date_paid }}</td>
			</tr>
		</tbody>
	</table>
	<table class="  mt-2 " style="width:100%">
		<thead>
			<tr>
				<th class="">No of Dues Paid</th>
				<th  style="text-align: right;" class="price"> {{ $total_dues_paid }}</th>
			</tr>
			<tr>
				<th class="">Total Amount Paid</th>
				<th  style="text-align: right;" class="price">Rs: {{ $total_amount_paid }}</th>
			</tr>
		</thead>

	</table>
	<p class="centered mt-3 mb-0">Thank You! </p>
	<div class="centered">
		<img src="{{asset('/resources/assets_chitfund/images/footer.png')}}" style="height: 70px;" alt="VASANTHAM SIRU SEMIPPU THITTAM">
	</div>
</div>
<!-- end print -->

<script src="{{asset('/resources/assets_chitfund/js/jquery.min.js')}}"></script>
<script type="text/javascript">
	(function($) {
		setTimeout(function(){
			window.print();
			window.close();
		}, 1000);
	})(jQuery);
</script>
</body>
</html>