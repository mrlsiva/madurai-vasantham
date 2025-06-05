<html lang="en">
  <head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Spin Wheel App</title>
    <link rel="stylesheet" href="https://s3-us-west-2.amazonaws.com/s.cdpn.io/172203/smart-forms.css" />
    <link rel="stylesheet" href="https://s3-us-west-2.amazonaws.com/s.cdpn.io/172203/font-awesome.min.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" >
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" >
    
    
<style>
    @font-face { font-family: 'FontAwesome'; src: url('https://s3-us-west-2.amazonaws.com/s.cdpn.io/172203/fontawesome-webfontba72.eot?#iefix') format('embedded-opentype'), url('https://s3-us-west-2.amazonaws.com/s.cdpn.io/172203/fontawesome-webfontba72.woff') format('woff'), url('https://s3-us-west-2.amazonaws.com/s.cdpn.io/172203/fontawesome-webfontba72.ttf') format('truetype'), url('https://s3-us-west-2.amazonaws.com/s.cdpn.io/172203/fontawesome-webfontba72.svg#FontAwesome') format('svg'); font-weight: normal; font-style: normal; }
    .smart-forms .append-icon .field-icon, .smart-forms .prepend-icon .field-icon {
    top: 13px !important;
    }
    .help-block {
      margin-top: 5px;
    }
    .spin-logo-div {
	width: 100%;
	text-align: center;
}
.spin-logo {
	margin: 0 auto;
}
.smart-container {
    margin: 0px auto;
    box-shadow: none;
    background: transparent;
}
.smart-forms .form-body {
    padding: 0px 30px;
    padding-bottom: 0px;
}
.smart-forms .form-footer {
    background: none;
    padding: 0px 25px;
    padding-top: 10px;
}
.smart-forms .tagline span {
  background-color: #ff0000;
  color: #fff;
}
.smart-forms .tagline {
    height: 0;
    border-top: 1px solid #ff0000;
    text-align: center;
}
.smart-forms .btn-primary {
    background-color: #ff0000 !important;
}
.smart-forms .btn-primary:hover, .smart-forms .btn-primary:focus {
    background-color: #ff00008c;
}
.smart-forms .gui-input:focus, .smart-forms .gui-textarea:focus, .smart-forms .select>select:focus, .smart-forms .select-multiple select:focus {
    border-color: transparent;
}
.smart-forms .gui-input:focus ~ .field-icon i, .smart-forms .gui-textarea:focus ~ .field-icon i {
    color: #B71C1C;
}
.smart-forms .button {
    background: #ffffff;
}
.form-control:focus {
    
    box-shadow: 0;
}
body {
      height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
      /* background-color: #111; */
      overflow: scroll;
    }

    .diwali-background {
      position: relative;
      width: 100%;
      height: 100%;
      background: linear-gradient(180deg, #ffde59, #ffde59);	  
	  display: table;
	  padding-bottom: 30px;
    }


    /* Firework Explosion */
    .firework-explosion {
      position: absolute;
      top: 10%;
      left: calc(50% - 75px);
      transform: translateX(-50%);
      width: 150px;
      height: 150px;
      background-color: transparent;
      display: none;
      animation: boom 1s ease-out infinite;
    }

    .firework-explosion.active {
      display: block;
    }

    /* Sparks */
    .spark {
      position: absolute;
      width: 4px;
      height: 4px;
      background-color: #ffcc00;
      border-radius: 50%;
      box-shadow: 0 0 5px #ffcc00;
    }

    .spark1 { top: 50%; left: 50%; animation: spark1 1s ease-out infinite; }
    .spark2 { top: 50%; left: 50%; animation: spark2 1s ease-out infinite; }
    .spark3 { top: 50%; left: 50%; animation: spark3 1s ease-out infinite; }
    .spark4 { top: 50%; left: 50%; animation: spark4 1s ease-out infinite; }
    .spark5 { top: 50%; left: 50%; animation: spark5 1s ease-out infinite; }
    .spark6 { top: 50%; left: 50%; animation: spark6 1s ease-out infinite; }
    .spark7 { top: 50%; left: 50%; animation: spark7 1s ease-out infinite; }
    .spark8 { top: 50%; left: 50%; animation: spark8 1s ease-out infinite; }

    /* Boom Animation */
    @keyframes boom {
      0% {
        display: block;
        transform: scale(0);
        opacity: 1;
      }
      50% {
        transform: scale(1);
        opacity: 0.8;
      }
      100% {
        transform: scale(2);
        opacity: 0;
      }
    }

    /* Spark Animations (Shooting out in different directions) */
    @keyframes spark1 {
      0% {
        transform: translate(0, 0);
      }
      100% {
        transform: translate(-50px, -50px);
        opacity: 0;
      }
    }

    @keyframes spark2 {
      0% {
        transform: translate(0, 0);
      }
      100% {
        transform: translate(50px, -50px);
        opacity: 0;
      }
    }

    @keyframes spark3 {
      0% {
        transform: translate(0, 0);
      }
      100% {
        transform: translate(-50px, 50px);
        opacity: 0;
      }
    }

    @keyframes spark4 {
      0% {
        transform: translate(0, 0);
      }
      100% {
        transform: translate(50px, 50px);
        opacity: 0;
      }
    }

    @keyframes spark5 {
      0% {
        transform: translate(0, 0);
      }
      100% {
        transform: translate(-75px, -25px);
        opacity: 0;
      }
    }

    @keyframes spark6 {
      0% {
        transform: translate(0, 0);
      }
      100% {
        transform: translate(75px, -25px);
        opacity: 0;
      }
    }

    @keyframes spark7 {
      0% {
        transform: translate(0, 0);
      }
      100% {
        transform: translate(-75px, 25px);
        opacity: 0;
      }
    }

    @keyframes spark8 {
      0% {
        transform: translate(0, 0);
      }
      100% {
        transform: translate(75px, 25px);
        opacity: 0;
      }
    }
    </style>
    </head>
    <body>
    <div class="diwali-background">
    <div class="rocket-container">
      <div class="rocket"></div>
    </div>
    <div class="firework-explosion">
      <div class="spark spark1"></div>
      <div class="spark spark2"></div>
      <div class="spark spark3"></div>
      <div class="spark spark4"></div>
      <div class="spark spark5"></div>
      <div class="spark spark6"></div>
      <div class="spark spark7"></div>
      <div class="spark spark8"></div>
    </div>
<div class="pt-3">
	@if($errors->any())
		<div class="alert alert-danger alert-dismissible" role="alert">
			<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
			@foreach($errors->all() as $error)
				{{ $error }} <br/>
			@endforeach
		</div>
	@endif

	@if(session()->has('success'))
		<div class="alert alert-success alert-dismissible" role="alert">
			<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
			{{ session()->get('success') }}
		</div>
	@endif

  <div class="smart-forms smart-container wrap-2">

    <div class="spin-logo-div">
      <!-- <h4 style="text-align: center;">Spin & Win</h4> -->
      <img class="spin-logo" src="{{ asset('resources/images/spin-logo.png') }}" height="200" alt="spinner arrow" />

    </div><!-- end .form-header section -->
    <form method="post" id="new_post" name="new_post" action="{{ route('saveInvoiceForm') }}" class="wpcf7-form" enctype="multipart/form-data">
      @csrf
      <div class="form-body">
        <div class="spacer-b30">
            <div class="tagline"><span>Customer Details</span></div><!-- .tagline -->
        </div>
        <div class="frm-row">
          <div class="section colm colm12 {{ $errors->has('username') ? 'has-error' : ''}}">
            <label for="username" class="field prepend-icon">
              <input type="text" name="username" id="username" class="gui-input form-control @error('username') is-invalid @enderror" placeholder="Name" value="{{ old('username') }}">
              <label for="username" class="field-icon"><i class="fa fa-user"></i></label>
            </label>
            {!! $errors->first('username', '<p class="help-block">:message</p>') !!}
          </div><!-- end section -->

        </div><!-- end .frm-row section -->

        <!--<div class="section {{ $errors->has('email') ? 'has-error' : ''}}">
            <label for="email" class="field prepend-icon">
              <input type="email" name="email" id="email" class="gui-input @error('email') is-invalid @enderror" placeholder="Email address" value="{{ old('email') }}">
              <label for="email" class="field-icon"><i class="fa fa-envelope"></i></label>
            </label>
            {!! $errors->first('email', '<p class="help-block">:message</p>') !!}
          </div><!-- end section -->
          <div class="section colm colm6 {{ $errors->has('mobile') ? 'has-error' : ''}}">
            <label for="mobile" class="field prepend-icon">
              <input type="tel" name="mobile" id="mobile" class="gui-input phone-group @error('mobile') is-invalid @enderror" placeholder="Mobile number" value="{{ old('mobile') }}">
              <label for="mobile" class="field-icon"><i class="fa fa-mobile-phone"></i></label>
            </label>
            {!! $errors->first('mobile', '<p class="help-block">:message</p>') !!}
          </div><!-- end section -->



        <div class="spacer-t40 spacer-b40">
          <div class="tagline"><span> Product Purchased Details </span></div><!-- .tagline -->
        </div>

        <div class="section {{ $errors->has('branch') ? 'has-error' : ''}}">
            <label class="field select">
              <select id="branch" name="branch" class="@error('branch') is-invalid @enderror">
                <option value="">Select branch...</option>
                <option value="Alanganallur">Alanganallur</option>
                <option value="Valasai">Valasai</option>
                <option value="Iyerbungalow">Iyer Bungalow</option>
                <option value="Palamedu">Palamedu</option>
              </select>
              <i class="arrow double"></i>
            </label>
            {!! $errors->first('branch', '<p class="help-block">:message</p>') !!}
          </div><!-- end section -->
  
          <!--<div class="section {{ $errors->has('invoice_copy') ? 'has-error' : ''}}">
            <label for="file1" class="field file">
              <span class="button btn-primary"> Choose invoice </span>
              <input type="file" class="gui-file @error('invoice_copy') is-invalid @enderror" name="invoice_copy" id="invoice_copy" onChange="document.getElementById('invoice_copy_name').value = this.value;">
              <input type="text" class="gui-input" id="invoice_copy_name" placeholder="No file selected" readonly>
            </label>
            {!! $errors->first('invoice_copy', '<p class="help-block">:message</p>') !!}
          </div><!-- end  section UPLOAD-->
  
          <div class="section colm colm6 {{ $errors->has('invoice_number') ? 'has-error' : ''}}">
            <label for="invoice_number" class="field prepend-icon">
              <input type="text" name="invoice_number" id="invoice_number" class="gui-input @error('invoice_number') is-invalid @enderror" placeholder="Invoice number" value="{{ old('invoice_number') }}">
              <label for="invoice_number" class="field-icon"><i class="fa fa-credit-card"></i></label>
            </label>
            {!! $errors->first('invoice_number', '<p class="help-block">:message</p>') !!}
          </div><!-- end section -->

      </div><!-- end .form-body section -->
      <div class="form-footer">
        <button type="submit" class="button btn-primary"> Submit </button>
        <button type="reset" class="button"> Cancel </button>
      </div><!-- end .form-footer section -->
    </form>

  </div><!-- end .smart-forms section -->
</div><!-- end .smart-wrap section -->

</div>
<script>
    document.addEventListener("DOMContentLoaded", function() {
      setInterval(function() {
        document.querySelector('.firework-explosion').classList.add('active');
        setTimeout(function() {
          document.querySelector('.firework-explosion').classList.remove('active');
        }, 1000); // 1-second explosion duration
      }, 3000); // Repeat every 3 seconds
    });
  </script>
</body>
</html>