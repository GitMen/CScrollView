<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title>企业用户绑定</title>
    <link href="/css/reset.css" rel="stylesheet">
    <link href="/css/common.css" rel="stylesheet">
</head>
<body>
    <div class="select_btn">
         <a href="/wx/card/get/p"><button >个人用户</button></a><a href="/wx/card/get/c"><button class="bussiness_btn">企业用户</button></a>
    </div>
    <form action="/wx/card/get/c" method="get">
    <div class="wrap">
        <input type="text" name="card" placeholder="请输入企业注册号" id="bunssiness_number">
        <button id="bussiness_btn">立刻绑定</button>
        <div id="hehe">
            <div class="s">绑定成功!</div>
        </div>
    </div>
    </form>
    
</body>
<script src="/js/jquery-1.9.1.min.js"></script>
<script>
    $(function() {
        function success() {
            $('#hehe').show();
            window.location.href = '/wx/get/my/reserve';
        }
        function nopreson() {
            $('#hehe').show();
            $('#hehe .s').html('企业号不存在!');
            setTimeout(function () {
                window.location.reload();
            }, 600);

        }
        $('#bussiness_btn').click(function() {
            var url = $('form').attr('action') + '?card=' + $('#bunssiness_number').val();

            $.ajax({
                type: 'get',
                url: url,
                dataType: 'json',
                success: function(res) {
                    if(res.code == '402') {
                        nopreson()
                    } else if(res.code == '200') {
                        success()
                    }

                },
            });
            return false;
        });
    });
</script>

</body>
</html>