<div id="footer">
    <div class="container">
        <p class="text-muted text-left">Power By YXZ</p>

    </div>
</div>
<script>
    $(document).ready(function(){
        $('.modal').modal();
        $(".button-collapse").sideNav();
    });
    if(<?php echo $isLogin?"false":"true";?>)
    {
        delayJump("<?php echo _HTTP;?>user/goLogin/",100);
    }
</script>
</body>
</html>