<?php
session_start(); 
if(  trim($_SESSION["admin_pass"]) != "" && trim($_SESSION["admin_id"]) != "" && trim($_SESSION["admin_name"]) != "" 
	 && trim($_SESSION["admin_country"]) != "" && trim($_SESSION["admin_currency"]) != ""){

		header("Location: in/");

}

?>
<!DOCTYPE html>
<html lang="en">
<head>
	<title>FishPott Administrator</title>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
<!--===============================================================================================-->	
	<link rel="icon" type="image/png" href="images/favicon.ico"/>
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="vendor/bootstrap/css/bootstrap.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="fonts/font-awesome-4.7.0/css/font-awesome.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="vendor/animate/animate.css">
<!--===============================================================================================-->	
	<link rel="stylesheet" type="text/css" href="vendor/css-hamburgers/hamburgers.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="vendor/select2/select2.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="css/util.css">
	<link rel="stylesheet" type="text/css" href="css/main.css">
<!--===============================================================================================-->
</head>
<body>
	
	<div class="limiter">
		<div class="container-login100">
			<div class="wrap-login100">
				<div class="login100-pic js-tilt" data-tilt>
					<img src="images/black_fishpot.png" alt="IMG">
					<span class="login100-form-title">
					<br>
						Powerful Connections With A Pott
					</span>
				</div>

				<form method="post" action="../inc/admin/login.php" class="login100-form validate-form">
					<span class="login100-form-title">
						Administrator Login
						<?php if(isset($_SESSION["asem"]) && trim($_SESSION["asem"])){ ?>
							<a class="login100-form-title" style="font-size: small; margin-top: 5px; margin-bottom: 5px;">
								<?php echo $_SESSION["asem"]; ?>
							</a>
						<?php } ?>
					</span>

					<div class="wrap-input100 " data-validate = "Valid email is required: ex@abc.xyz">
						<input class="input100" type="text" name="phone" placeholder="Phone Number">
						<span class="focus-input100"></span>
						<span class="symbol-input100">
							<i class="fa fa-phone" aria-hidden="true"></i>
						</span>
					</div>

					<div class="wrap-input100 validate-input" data-validate = "Password is required">
						<input class="input100" type="password" name="password" placeholder="Password">
						<span class="focus-input100"></span>
						<span class="symbol-input100">
							<i class="fa fa-lock" aria-hidden="true"></i>
						</span>
					</div>
					
					<div class="container-login100-form-btn">
						<button class="login100-form-btn">
							Login
						</button>
					</div>

					<div class="text-center p-t-12">
						<span class="txt1">
							If you forgot your password, 
						</span>
						<a class="txt2" href="#">
							Contact your Super Administrator to start a reset
						</a>
					</div>
				</form>
			</div>
		</div>
	</div>
	
	

	
<!--===============================================================================================-->	
	<script src="vendor/jquery/jquery-3.2.1.min.js"></script>
<!--===============================================================================================-->
	<script src="vendor/bootstrap/js/popper.js"></script>
	<script src="vendor/bootstrap/js/bootstrap.min.js"></script>
<!--===============================================================================================-->
	<script src="vendor/select2/select2.min.js"></script>
<!--===============================================================================================-->
	<script src="vendor/tilt/tilt.jquery.min.js"></script>
	<script >
		$('.js-tilt').tilt({
			scale: 1.1
		})
	</script>
<!--===============================================================================================-->
	<script src="js/main.js"></script>

</body>
</html>
<?php //unset($_SESSION["asem"]); ?>