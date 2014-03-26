<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Untitled Document</title>
<link href="bin/css/main.css" rel="stylesheet" type="text/css">
<script src="bin/ckeditor/ckeditor.js"></script>
</head>

<body>
<div id="main">
Version 3
	<header><img src="bin/images/logo.png" width="750" height="375" alt=""/></header>
<form>
            <textarea name="editor1" id="editor1" rows="10" cols="80"></textarea>
<script>CKEDITOR.replace('editor1');</script>
            <input type="submit" formaction="ckeditor.php" formmethod="POST" value="Submit">
        </form>

</div><!--End Main Div -->
</body>
</html>
