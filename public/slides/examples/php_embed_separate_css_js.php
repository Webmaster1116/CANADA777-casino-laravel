<?php include '../embed.php'; ?>
<!DOCTYPE html>
<html>
	<head>
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title>Revolution Slider Example - Embedding with PHP Method</title>
		<!-- revslider css only -->
		<?php RevSliderEmbedder::cssIncludes(); ?>
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
	</head>
	<body style="margin: 0; padding: 0">
		<div class="content">
			<?php RevSliderEmbedder::putRevSlider('example'); ?>
		</div>

		<!----------------------------->
		<!-- here comes your scripts -->
		<!----------------------------->
		<!-- revslider javascript -->
		<!-- jsIncludes(false) - false parameter disable revslider jquery library -->
		<?php RevSliderEmbedder::jsIncludes(false); ?>
	</body>
</html>