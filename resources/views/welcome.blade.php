<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Laravel</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />

        <!-- Styles -->
        <style>
            body {
	background: #445;
	color: #FFF;
    margin:0;
}

#main {
	background: linear-gradient(to bottom, rgba(0,0,0,0.66) 100%, transparent), url('resources/images/home_bg.png');
	background-size: cover, cover;
	background-position: center, center;
	height: 100vh;
	width: 100%;
	display: flex;
	justify-content: center;
	align-items: center;
}

h1 {
	font-family:"ヒラギノ角ゴ Pro W3", "Hiragino Kaku Gothic Pro", Osaka, "メイリオ", Meiryo, "ＭＳ Ｐゴシック", "MS PGothic", sans-serif;
	font-size: 2rem;
	display: inline-block;
	padding: 1rem;
	font-weight: 400;
	position: relative;
	opacity: 1;
	transform: scale(1);
	transition: transform 0.5s ease, opacity 1s ease;
}

.is-loading h1 {
	transform: scale(0.9);
	opacity: 0;
}

h1:before,
h1:after {
	height: 2px;
	width: 100%;
	content: "";
	background: white;
	display: block;
	position: absolute;
	transition: width 0.4s ease;
	transition-delay: 0.8s;
}

h1:before {
	top: 0;
	left: 0;
}

h1:after {
	bottom: 0;
	right: 0;
}

.is-loading h1:before,
.is-loading h1:after {
	width: 0;
}
        </style>
    </head>
    <body class="antialiased">
    <div id="main" class="is-loading">
	<h1>
    Attendance App
	</h1>
</div>
<!-- https://cdnjs.cloudflare.com/ajax/libs/jquery/3.0.0-alpha1/jquery.min.js -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.0.0-alpha1/jquery.min.js"></script>
<script>
    $(document).ready(function() {
	setTimeout(function() {
		$("#main").removeClass("is-loading");
	}, 100)
});
</script>
    </body>
</html>
