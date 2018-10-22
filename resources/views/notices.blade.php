<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,height=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title>注意事项</title>
    <link href="../css/reset.css" rel="stylesheet">
    <link href="../css/common.css" rel="stylesheet">
    <link href="../css/iconfont.css" rel="stylesheet">
    <link href="../css/swiper.min.css" rel="stylesheet">
</head>
<body>

    <div class="swiper-container">
    <div class="swiper-wrapper">
        <div class="swiper-slide"><img src="../images/n01.jpg"></div>
        <div class="swiper-slide"><img src="../images/n02.jpg"></div>
        <div class="swiper-slide"><img src="../images/n03.jpg"></div>
        <div class="swiper-slide"><img src="../images/n04.jpg"></div>
        <div class="swiper-slide"><img src="../images/n05.jpg"></div>
        <div class="swiper-slide"><img src="../images/n06.jpg"></div>
        <div class="swiper-slide"><img src="../images/n07.jpg"></div>
    </div>
    </div>
        <a class="finger"><i class="iconfont">&#xe74b;</i></a>
    <script src="../js/swiper.min.js"></script>
    <script type="text/javascript">
        var mySwiper = new Swiper ('.swiper-container', {
            direction: 'vertical'
        });
        var w=document.documentElement.clientWidth||document.body.clientWidth;
        var h=document.documentElement.clientHeight||document.body.clientHeight;
        var aImgs=document.getElementsByTagName('img');
        aImgs.clientHeight=h;
        console.log(aImgs.clientHeight);
    </script>

</body>

</html>