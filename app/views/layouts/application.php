<!DOCTYPE html>
<html>
<head>
	<title><?php echo $site_title; ?></title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	
    <?php
	HTMLHelper::css ( array (
			"bootstrap/bootstrap.css",
			"lib/font-awesome.css",
			"http://fonts.googleapis.com/css?family=Lato:300,400,700,900,300italic,400italic,700italic,900italic",
			"http://fonts.googleapis.com/css?family=Open+Sans:300italic,400italic,600italic,700italic,800italic,400,300,600,700,800" 
	) );
	?>
    <!--[if lt IE 9]>
      <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->
</head>
<body>
 {{ CONTENT }}
	<!-- scripts -->
<?php
	HTMLHelper::script ( array (
			'jquery-1.10.2.min.js',
			'bootstrap.min.js',
			'respond.min.js',
			'holder.js'
	) );
	?>
</body>
</html>