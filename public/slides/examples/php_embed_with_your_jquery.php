<?php include '../embed.php'; ?>
<!DOCTYPE html>
<html>
	<head>
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title>Revolution Slider Example - Embedding with PHP Method</title>
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
		<!----------------------------->
		<!-- here comes your scripts -->
		<!----------------------------->
		<!-- headIncludes(false) - false parameter disable revslider jquery library -->
		<?php RevSliderEmbedder::headIncludes(false); ?>
	</head>
	<body style="margin: 0; padding: 0">
		<?php RevSliderEmbedder::putRevSlider('example'); ?>
	</body>
</html>