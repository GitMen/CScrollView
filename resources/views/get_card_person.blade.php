<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title>个人用户绑定</title>
    <link href="/css/reset.css" rel="stylesheet">
    <link href="/css/common.css" rel="stylesheet">
</head>
<body>
    <div class="select_btn">
          <a href="/wx/card/get/p"><button class="bussiness_btn">个人用户</button></a><a href="/wx/card/get/c"><button >企业用户</button></a>
    </div>
    <form action="/wx/card/get/p" method="get">
   <div class="wrap">
        <input type="text" name="mobile" placeholder="请输入手机号" id="name">
        <input type="text" name="card" placeholder="请输入身份证号后4位" id="number">
        <button id="person_btn">立刻绑定</button>
        <div id="hehe">
           <div class="s">绑定成功!</div>
        </div>
    </div>
    </form>
    
</body>
</div>
<script src="/js/jquery-1.9.1.min.js"></script>
<script src="/js/jquery.js"></script>
<script>

$(function() {
    function success() {
        $('#hehe').show();
        window.location.href = '/wx/get/my/reserve';
    }
    function nopreson() {
        $('#hehe').show();
        $('#hehe .s').html('没有此人!');
        setTimeout(function () {
            window.location.reload();
        }, 600);

    }
    function havepperson() {
        $('#hehe').show();
        $('#hehe .s').html('用户已绑定过微信!');
        setTimeout(function () {
            window.location.reload();
        }, 600);


    }
    $('#person_btn').click(function() {
        var url = $('form').attr('action') + '?mobile=' + $('#name').val() + '&card=' + $('#number').val();

        $.ajax({
            type: 'get',
            url: url,
            dataType: 'json',
            success: function(res) {
                if(res.code == '402') {
                    nopreson();
                } else if(res.code == '200') {
                    //alert('绑定成功!');
                    success();


                } else if(res.code == '404') {
                    havepperson();
                }

            },
        });
        return false;
    });
});
</script>
</body>
</html>