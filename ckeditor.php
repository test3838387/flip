<?php
$split = "<hr />
<hr />";
$linebreak = "
";
$side = explode($split, $_POST['editor1']);
$side = str_replace($linebreak, "", $side);
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Untitled Document</title>
<link href="bin/css/main.css" rel="stylesheet" type="text/css">
<style>
/* entire container, keeps perspective */
.flip-container {
	perspective: 1000;
}
	/* flip the pane when hovered */
	.flip-container:hover .flipper, .flip-container.hover .flipper {
		transform: rotateY(180deg);
	}

.flip-container, .front, .back {
	width: 500px;
	height: 300px;
}

/* flip speed goes here */
.flipper {
	transition: 0.6s;
	transform-style: preserve-3d;

	position: relative;
}

/* hide back of pane during swap */
.front, .back {
	backface-visibility: hidden;

	position: absolute;
	top: 0;
	left: 0;
}

/* front pane, placed above back */
.front {
	background-color:#FFF;
	z-index: 2;
}

/* back, initially hidden pane */
.back {
	background-color:#FFF;
	transform: rotateY(180deg);
}
</style>
</head>

<body style="margin:5px">
<div class="flip-container" ontouchstart="this.classList.toggle('hover');" id="canvas-border">
	<div class="flipper">
		<div class="front"><?php echo $side[0]; ?></div>
		<div class="back"><?php echo $side[1]; ?></div>
	</div>
</div>
</body>
</html>