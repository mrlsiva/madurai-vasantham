<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thank You</title>
    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@500;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            height: 100vh;
            background: #FFDE59;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .thank-you-wrapper {
    text-align: center;
    background-color: #e91414;
    padding: 2em;
    border-radius: 1em;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
    color: white;
    max-width: 80%;
    margin: 0 auto;
}
        .strech {
        height: 100%;
        flex-direction: column;
        box-sizing: border-box;
        display: flex;
        place-content: stretch flex-start;
        align-items: stretch;
        max-width: 100%;
      }
      .logo {
        width: 100%;
        text-align: center;
        margin-top: 30px;
      }
      .logo img {
        /* max-height:120px; */
        max-width: 80%;
        margin: 0 auto;
      }
      .social {
        max-width: 300px;
    width: 300px;
    flex-direction: row;
    box-sizing: border-box;
    display: flex;
    place-content: center space-between;
    align-items: center;
	margin: 0 auto;
    padding: 30px 15px;
      }
      .social a {
        color:#222;
      }
	  .prize-img img {
		width: 150px;
		height: auto;
		border-radius: 10px;
	  }
    </style>
</head>
<body>
<div class="strech">
      <div class="logo">
        <img src="{{ asset('resources/images/logo-2.png') }}" style="max-height:200px;" />
      </div>
      <div class="logo" style="margin-top: 0px">
        <img src="{{ asset('resources/images/spin-logo.png') }}" height="200px" />
      </div>
    <div class="thank-you-wrapper">
        <h2><strong>Dear {{ ucfirst($spindata['name']) }}</strong></h2>
		<h2>Thank You for Your Participation!</h2>
        <p>Your spin has been completed.</p>
        <p>You won: <strong>{{ $prize }}</strong></p> <!-- Display the prize here -->
		
		<div class="prize-img" style="margin-top: 0px;">
			<img src="{{ asset('resources/images/spin/'.$spindata['discount'].'.png') }}" />
		</div>
		
		<p style="margin-top: 15px;">Mobile: <strong>{{ $spindata['mobile'] }}</strong></p> 
		<p>Branch: <strong>{{ $spindata['branch'] }}</strong></p> 
		<p>Invoice: <strong>{{ $spindata['invoice_number'] }}</strong></p> 
		
    </div>
    <div class="social">
      <a href="https://www.facebook.com/VasanthamHomeAppliances" target="_blank"> Facebook </a>
      <a href="https://www.youtube.com/@vasanthamhomeappliances" target="_blank"> Youtube </a>
      <a href="https://www.instagram.com/vasantham_furnitures" target="_blank"> Instagram </a>
    </div>
    </div>

</body>
</html>
