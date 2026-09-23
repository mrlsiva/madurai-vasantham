@extends('layouts.chitfund.base-login')

@section('content')

<!--page-content-wrapper-->
	<div class="container py-5">
		<style>
			.home-title { font-size: 36px; font-weight: 800; color: #ed1c24; }
			.home-card { height: 100%; }
			.home-card .card-body > i { font-size: 44px; }
		</style>

		<div class="text-center mb-5">
			<img src="{{asset('/resources/assets_chitfund/images/logo-icon.png')}}" class="mx-auto d-block" style="width: 100px;" alt="">
			<h1 class="home-title mt-3">Madurai Vasantham</h1>
		</div>

		<div class="row">
			<div class="col-12 col-lg-4 mb-3">
				<div class="card radius-15 bg-voilet home-card">
					<div class="card-body text-white text-center">
						<i class="bx bx-user-check"></i>
						<h4 class="text-white mt-2">Attendance</h4>
						<p class="mb-0">Staff, shift times and daily punch-in / punch-out logs.</p>
						<a href="{{ route('attendance.index') }}" class="btn btn-light mt-3 px-4">View <i class="lni lni-arrow-right"></i></a>
					</div>
				</div>
			</div>
			<div class="col-12 col-lg-4 mb-3">
				<div class="card radius-15 bg-rose home-card">
					<div class="card-body text-white text-center">
						<i class="bx bx-coin-stack"></i>
						<h4 class="text-white mt-2">Chit Fund</h4>
						<p class="mb-0">Savings plans, members, monthly dues and invoices.</p>
						<a href="{{ route('chitfundIndex') }}" class="btn btn-light mt-3 px-4">View <i class="lni lni-arrow-right"></i></a>
					</div>
				</div>
			</div>
			<div class="col-12 col-lg-4 mb-3">
				<div class="card radius-15 bg-sunset home-card">
					<div class="card-body text-white text-center">
						<i class="bx bx-gift"></i>
						<h4 class="text-white mt-2">Spin Wheel</h4>
						<p class="mb-0">Customer invoice offers with spin-to-win discounts.</p>
						<a href="{{ route('showForm') }}" class="btn btn-light mt-3 px-4">View <i class="lni lni-arrow-right"></i></a>
					</div>
				</div>
			</div>
		</div>
	</div>
<!--end page-content-wrapper-->

@endsection
