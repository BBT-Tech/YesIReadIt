<html>
<head>
</head>
<body>
<p><?php echo $info;?></p>
<script>
    setTimeout(function(){window.location="<?php echo htmlspecialchars_decode($location);?>";},2000);

</script>
</body>
</html>
