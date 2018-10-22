<html>
    <header>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
        <link href="/css/reset.css" rel="stylesheet">
        <title>进度查询</title>

        <style>
            body{
                background-color: rgb(245,245,245);
            }
            #search_div{
                float: left;
                width: 100%;
                height: 30px;
                margin-top: 20px;
            }
            #search_div input{
                text-align: center;
                float: left;
                margin-left: 5%;
                width: 60%;
                height: 30px;
                border: 1px solid rgb(197,197,197);
                background-color: white;
                font-size: 12px;
                color: #000;
                font-weight: 100;
            }
            #search_div button{
                float: left;
                margin-left: 5%;
                width: 25%;
                height: 30px;
                border: 1px solid rgb(214,214,214);
                background-color: #00a0e9;
                font-size: 12px;
                color: #fff;
                font-weight: 100;
            }
            #cell_list{
                float: left;
                width: 100%;
                height: auto;
                margin-top: 10px;
                font-weight: 200;
                background-color: rgb(245,245,245);
            }
            .cell {
                margin-top: 10px;
                width: 96%;
                margin-left: 2%;
                height: auto;
                float: left;
                background-color: white;
                border:  0.5px solid rgb(214, 214, 214);
                border-radius: 10px;
            }
            .time{
                display: block;
                float: left;
                width: 90%;
                margin-left: 5%;
                height: 15px;
                margin-top: 5px;
                font-size: 12px;
                line-height: 20px;
                color: rgb(81,81,81);
            }
            .oneCell{
                float: left;
                width: 90%;
                margin-left: 5%;
                margin-top: 5px;
                height: auto;
            }
            .proTitle{
                display: block;
                float: left;
                width: 30%;
                height: 20px;
                font-size: 14px;
                line-height: 20px;
                color: #00a0e9;
            }
            .proContent{
                display: block;
                float: left;
                width: 70%;
                height: auto;
                min-height: 20px;
                font-size: 14px;
                color: rgb(43,43,43);
            }
        </style>

    </header>

    <body>
        <div id="search_div">
            <input type="text" placeholder="请输入预留的手机号码" id="key">
            <button onclick="searchP()">立即查询</button>
        </div>
        <div id="cell_list">
            {{--<div class="cell_">--}}
                {{--<span class="time">更新时间:2017-12-10 23:12:12</span>--}}
                {{--<div class="oneCell">--}}
                    {{--<span class="proTitle">贷款进度：</span>--}}
                    {{--<span class="proContent">撒打算打算等撒打算的撒打算打算等123撒打算打算打算打算打算打算等</span>--}}
                {{--</div>--}}
            {{--</div>--}}
        </div>
    </body>
    <script src="/js/jquery-1.9.1.min.js"></script>
    <script>
        function searchP() {
            var phone = $('#key').val();
            if(phone == ''){
                alert('请先输入预留手机号码');
                return;
            }
            alert('正在查询请稍等');
            $.post('http://shouchuang.sinosspace.com/wx/getUserProgress',{phone:phone},function (result) {
                $('#cell_list').html('');
                var data = $.parseJSON(result);
                if(data.data.length == 0){
                    alert('未能查到任何进度记录');
                }else{
                    var list = data.data;
                    for(var i = 0 ; i < list.length ; i ++){
                        var content = list[i];
                        $('#cell_list').append(' <div class="cell cell_'+i+'">\n'+
                        '    <span class="time">更新时间:'+content.createAt+'</span>\n'+
                        '<div class="oneCell"></div></div>');


                        var progress = content.content;
                        var progressList = progress.split("+");
                        for(var j = 0 ; j < progressList.length ; j++){
                            var pTitle = progressList[j].split('&')[0];
                            var pContent = progressList[j].split('&')[1];
                            $('.cell_'+i).append('<div class="oneCell">\n'+
                            '    <span class="proTitle">'+pTitle+'：</span>\n'+
                            '<span class="proContent">'+pContent+'</span>\n'+
                            '   </div>');
                        }
                    }

                }
            })
        }
    </script>
</html>