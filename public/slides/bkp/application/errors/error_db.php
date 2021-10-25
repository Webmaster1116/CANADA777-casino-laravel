<!DOCTYPE html>
<html lang="en">
<head>
<title>Database Error</title>
<style type="text/css">

::selection{ background-color: #E13300; color: white; }
::moz-selection{ background-color: #E13300; color: white; }
::webkit-selection{ background-color: #E13300; color: white; }

body {
	background-color: #fff;
	margin: 40px;
	font: 13px/20px normal Helvetica, Arial, sans-serif;
	color: #4F5155;
}

a {
	color: #003399;
	background-color: transparent;
	font-weight: normal;
}

h1 {
    color: #fff;
    background-color: #e74c3c;
    border-bottom: 1px solid #D0D0D0;
    font-size: 19px;
    font-weight: normal;
    margin: 0 0 14px 0;
    padding: 14px 15px 10px 15px;
}
h2 {
    font-size: 19px;
    font-weight: normal;
	margin: 14px 0;
    padding: 12px 15px 12px 15px;
}
h3 {
    font-size: 16px;
    font-weight: normal;
	margin: 12px 0;
}

code {
	font-family: Consolas, Monaco, Courier New, Courier, monospace;
	font-size: 12px;
	background-color: #f9f9f9;
	border: 1px solid #D0D0D0;
	color: #002166;
	display: block;
	margin: 14px 0 14px 0;
	padding: 12px 10px 12px 10px;
}

#container {
	margin: 10px;
	border: 1px solid #D0D0D0;
	-webkit-box-shadow: 0 0 8px #D0D0D0;
}

p {
	margin: 12px 15px 12px 15px;
}

ul {
	margin-bottom: 30px;
}
li {
	margin-bottom: 20px;
}

</style>
</head>
<body>
	<div id="container">
		<h1><?php echo $heading; ?></h1>
		<?php echo $message; ?>
		<h2>How to fix it?</h2>
		<ul>
			<li>
				<h3>Check server status</h3>
				<p>Check if MySQL server is up and running. You can do it in cPanel/WHM of your server or by executing "<strong>mysqladmin status</strong>" command via SSH.</p>
			</li>
			<li>
				<h3>Check that user is correct</h3>
				<p>
					Double check thay all database settings (hostname, database, user, password) are spcified correctly. Make sure database user have appropriate access privelegies for your database.
					You can check and modify this settings in Database section of your servers cPanel/WHM.
				</p>
			</li>
			<li>
				<h3>Try to remove config files and run reinstall</h3>
				<p>
					Locate and remove following configuration files on your server:<br />
					"<strong>./application/config/config.php</strong>"<br />
					"<strong>./application/config/database.php</strong>"<br />
					Open url of your Slider Editor in browser to start over installation process.
				</p>
			</li>
		</ul>
	</div>
</body>
</html>