<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title>联系我们</title>
    <link href="/css/reset.css" rel="stylesheet">
    <link href="/css/common.css" rel="stylesheet">
</head>
</div>
<body>
<div class="wrap">
    <form action="/wx/suggesstion" method="post">
        <textarea placeholder="请输入您的宝贵建议" id="sugesstion" name="sugesstion"></textarea>
        <button id="submit">提交</button>
        <div id="hehe">
            <div class="s">您提交的建议成功, 我们会尽快处理!</div>
            <div><a href="javascript:void(0);" id="bok">好的</a></div>
        </div>
        <div id="dddsss">
            <div class="s">您还没有输入建议内容!</div>
            <div><a href="javascript:void(0);" id="bbok">好的</a></div>
        </div>
    </form>

</div>

</body>
<script src="/js/jquery-1.9.1.min.js"></script>
<script src="/js/jquery.js"></script>
<script>
$(function() {
    $('#bok').click(function() {
        $('#hehe').hide();
        window.location.reload();
        $('#sugesstion').val("");
    });
    $('#bbok').click(function() {
        //$('#dddsss').css('display', 'none');
        $('#dddsss').hide();


    });
    function chenggong() {
        //$('#hehe').css('display', 'block');
        $('#hehe').show();
    }
    function sdfsdf() {
        //$('#dddsss').css('display', 'block');
        $('#dddsss').show();
    }
    $('#submit').click(function() {
//        $('form').submit();
        if($('#sugesstion').val()==""){
            sdfsdf();
            return false;
        }

        var url = $('form').attr('action') + '?sugesstion=' + $('#sugesstion').val();;

        $.ajax({
            type: 'get',
            url: url,
//            data: {sugesstion:$('#sugesstion').val()},
            dataType: 'json',
            success: function(res) {
//                alert(res.msg);
                if(res.msg == 'ok') {
//                    return alert('您提交的建议成功, 我们会尽快处理!','');
                    chenggong();
                }
//                    chenggong();

            },
            error: function(res) {
                alert(res.statusText);
                alert(res.readyState);
                alert(res.status);
//                alert('---');
//                for(var i = 0; i <= res.length; i ++) {
//                    alert(res[i]);
//                }
            }
        });
        return false;
    });

});
</script>
</body>
</html>