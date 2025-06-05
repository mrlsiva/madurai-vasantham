<!DOCTYPE html>
<html>
<head>
<title>Vasantham Happy Customer</title>
<meta name="csrf-token" content="{{ csrf_token() }}" />
<link href="https://fonts.googleapis.com/css?family=Open+Sans:300" rel="stylesheet">
<link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous">
<style>
    body {
  background: whitesmoke;
  font-family: 'Open Sans', sans-serif;
  text-align: center;
}
.container {
  max-width: 960px;
  margin: 30px auto;
  padding: 20px;
}
h1 {
  font-size: 20px;
  text-align: center;
  margin: 20px 0 20px;
}
h1 small {
  display: block;
  font-size: 15px;
  padding-top: 8px;
  color: gray;
}
.avatar-upload {
  position: relative;
  width:100%;
  margin: 50px auto;
}
.avatar-upload .avatar-edit {
  position: absolute;
  right: 12px;
  z-index: 9;
  top: 10px;
}
.avatar-upload .avatar-edit input {
  display: none;
}
.avatar-upload .avatar-edit input + label {
  display: inline-block;
  width: 34px;
  height: 34px;
  margin-bottom: 0;
  border-radius: 100%;
  background: #FFFFFF;
  border: 1px solid transparent;
  box-shadow: 0px 2px 4px 0px rgba(0, 0, 0, 0.12);
  cursor: pointer;
  font-weight: normal;
  transition: all 0.2s ease-in-out;
}
.avatar-upload .avatar-edit input + label:hover {
  background: #f1f1f1;
  border-color: #d6d6d6;
}
.avatar-upload .avatar-edit input + label:after {
  content: "\f040";
  font-family: 'FontAwesome';
  color: #757575;
  position: absolute;
  top: 10px;
  left: 0;
  right: 0;
  text-align: center;
  margin: auto;
}

.avatar-upload .avatar-preview > div {
  width: 100%;
  height: 100%;
  border-radius: 100%;
  background-size: cover;
  background-repeat: no-repeat;
  background-position: center;
}
.frame {
    position: relative;
    z-index: 1;
}
button {
    margin: 0 auto;
    width: 190px;
    background-color: green;
    border-radius: 4px;
    height: 36px;
    line-height: 36px;
    font-size: 16px;
    font-weight: 500;
    text-align: center;
    color: #fff;
    outline: none;
    border: 0;
    cursor: pointer;
}
.frame, .avatar-upload, .photo {
    max-width: 300px;

}
.photo {
  width: 178px !important;
    height: 245px !important;
    overflow: hidden;
    position: absolute;
    left: 61px;
    top: 73px;
    border-radius: 0 !important;
}
.photo img {
    min-width: 100%;
    max-width: 100%;
    min-height: 100%;
}

#canvas{
	height: 0;
	opacity: 0;
	visibility: hidden;
}
button:disabled,
button[disabled]{
	opacity: 0.5;
	pointer-events: none; 
}
</style>
</head>
<body>
    <div class="container">
        <h1> Happy Customer
            <small>Upload image and get Frame</small>
        </h1>
        <div class="avatar-upload">
            <div class="avatar-edit">
                <input type='file' id="imageUpload" accept=".png, .jpg, .jpeg" />
                <label for="imageUpload"></label>
            </div>
            <div id="avatar-preview" class="avatar-preview">
                <img src="{{ asset('resources/images/frame.png') }} " class="frame">
                <!-- <div id="imagePreview" style="background-image: url(http://i.pravatar.cc/500?img=7);">
                </div> -->
               <div class="photo">
                <img src="" id="imagePreview">
               </div>
            </div>

			<!-- Div to Img converted Preview  -->
			<div id="canvas"></div>			
        </div>

		<!-- Download Image Form -->
		<form action="{{ route('downloadFrame') }}" method="POST">			
			<input type="hidden" name="_token" value="{{ csrf_token() }}">
			<input type="hidden" name="image_url" id="image_url" value="" />
			<button id="download-frame" type="submit">Download</button>
		</form>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.1.1/jquery.min.js"> </script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/0.4.1/html2canvas.min.js"> </script>

<script>
//Selecting the Form Element
const form = document.querySelector('form');
//Selecting the Form Download button
const DownloadButton = form.querySelector('button[type="submit"]');
//Disabling the download button
DownloadButton.setAttribute('disabled','');

function readURL(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
			
            document.getElementById('imagePreview').src = e.target.result;

            // $('#imagePreview').css('background-image', 'url('+e.target.result +')');
            $('#imagePreview').hide();
            $('#imagePreview').fadeIn(650);
			setTimeout(function () {
				html2canvas([document.getElementById('avatar-preview')], {
					onrendered: function (canvas) {
						document.getElementById('canvas').appendChild(canvas);
						var data = canvas.toDataURL('image/png');
						// AJAX call to send `data` to a PHP file that creates an image from the dataURI string and saves it to a directory on the server
						save_img(data);	
					}
				});
			}, 1000);
        }
        reader.readAsDataURL(input.files[0]);
    }
}
//to save the canvas image
function save_img(data){
	//ajax method.
	$.post("{{ route('saveFrame') }}", 
	   {image_url: data, "_token": "{{ csrf_token() }}" }, function(res){
		if(res != ''){
			//var uploadedImgUrl = document.URL+'download/'+res+'.jpg';
			var uploadedImgUrl = res;
			document.getElementById('image_url').value = uploadedImgUrl;
			if(DownloadButton.hasAttribute('disabled')) {
				DownloadButton.removeAttribute('disabled');
			}			
		}
		else{
			return false;
		}
	});
}

$("#imageUpload").change(function() {
	DownloadButton.setAttribute('disabled','');
    readURL(this);
});
</script>
</body>
</html>