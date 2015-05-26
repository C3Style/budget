<?php 
	session_start();
	
	header('Content-Type: text/html; charset=utf-8');
	
	include_once 'classes/User.class.php';
	include_once 'classes/Texte.class.php';
	
	unset($_SESSION['user']);
	
	if (isset($_POST['username'])) {
	
		$user = User::identify($_POST['username'], $_POST['password']);
	
		if (is_object($user)) {
			$_SESSION['user'] = $user->getLogin();
			header('Location: index.php');
		} 
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
<head>
<title>Login</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="stylesheet" type="text/css" href="styles/login.css">
</head>
<body>
	<div id="page-content">
		<div id="login-page">
			<div id="logo">
				<img src="images/gold_login.png">
			</div>
			<form action="login.php" method="post">
				<div id="normal-login">
					<p><label for="user_session_login"><?php echo Texte::getText($_SESSION['lang'], 'login'); ?></label><br>
					<input id="user_session_login" name="username" size="30" type="text"></p>
					<p><label for="user_session_password"><?php echo Texte::getText($_SESSION['lang'], 'password'); ?></label><br>
					<input id="user_session_password" name="password" size="30" type="password"></p>
				</div>
				<p align='right'><input class="button" name="commit" value="<?php echo Texte::getText($_SESSION['lang'], 'login'); ?>" type="submit"></p>
			</form>  
		</div>
	</div>
</body>
</html>