/**
 * Created by liyi on 16/6/28.
 */


/*预约管理*/
function res_data_save(json) {
    var j=1;
    for (var i = 0; i < json.data.length; i++) {
        var id = json.data[i].id;
        var username = json.data[i].username;
        var mobile = json.data[i].mobile;
        var reserve_time = json.data[i].reserve_time;
        var houses = json.data[i].houses;
        var unit = json.data[i].unit;
        var number = json.data[i].number;
        var status = json.data[i].status;
        switch(status){
            case 1:status="已确认";
                break;
            case 2:status="已取消";
                break;
            case 3:status="待确认";
                break;
        }
        $("#reserves_list tbody").append('<tr><td><span>'+id+'</span>' + (j++) + '</td><td>' + username + '</td><td>' + mobile + '</td><td>' + reserve_time + '</td><td>' + houses + '</td><td>' + unit + '</td><td>' + number + '</td><td>预约成功</td><td>' + status + '</td><td class="edit"><i class="iconfont">&#xe60c;</i></td></tr>');
    }
}

function res_select() {
    $('.add_form>ul>li p').click(function () {
        if ($(this).next().is(":visible")) {
            $(this).next().hide();
        } else {
            $(this).next().show();
        }
    });
    $('.select+ul>li').click(function () {
        var $text = $(this).text();
        var $data_int=$(this).attr('data-int');
        $(this).parent().hide();
        $(this).parent().prev('p').children('span').html($text);
        $(this).parent().prev('p').children('span').attr("data-int",$data_int);
    });
    $('.select1+ul>li').click(function () {
        var $text = $(this).text();
        var $data_int=$(this).attr('data-int');
        $(this).parent().hide();
        $(this).parent().prev('p').children('span').html($text);
        $(this).parent().prev('p').children('span').attr("data-int",$data_int);
    });
}

function getDate(strDate) {
    var date = eval('new Date(' + strDate.replace(/\d+(?=-[^-]+$)/, function (a) { return parseInt(a, 10) - 1; }).match(/\d+/g) + ')');
    return (date.getTime()/1000);
}

function reserves() {
    $.getJSON('http://10.1.9.50/shou/public/api/reservelist', function (json) {
        console.log(json);
        res_data_save(json);

        $('.con_left>ul>li p').click(function () {
            if($(this).next().is(":hidden")){
                $(this).next().show();
            }else {
                $(this).next().hide();
            }
        });
        $('#status li').click(function () {
            $text = $(this).text();
            $('#status').hide();
            $('#status').prev().html('<span>' + $text + '</span>' + '<i class="iconfont">&#xe607;</i>');
            switch ($text){
                case "已确认":status=1;
                    break;
                case "已取消":status=2;
                    break;
                case "待确认":status=3;
                    break;
            }
            $("#reserves_list tbody tr").remove();
            $.getJSON('http://10.1.9.50/shou/public/api/reserveselstatus/'+status+'/desc', function (json){
                console.log(json);
                res_data_save(json);
            });
        });
        $('#handle_progress li').click(function () {
            $text = $(this).text();
            $('#handle_progress').hide();
            $('#handle_progress').prev().html('<span>' + $text + '</span>' + '<i class="iconfont">&#xe607;</i>');
        });
        $('.con_right button').click(function () {
            var $value=$('.con_right>input[type=search]').val();
            $("#reserves_list tbody tr").remove();
            $.getJSON('http://10.1.9.50/shou/public/api/reserveselall/'+$value, function (json){
                console.log(json);
                res_data_save(json);
            });
        });

        $('.edit').click(function () {
            var $id=$(this).parent().children('td').children('span').text();
            $.getJSON('http://10.1.9.50/shou/public/api/reservesel/' + $id, function (json) {
                console.log(json);
                var user_id = json.data.user_id;
                var username = json.data.username;
                var mobile = json.data.mobile;
                var reserve_time = json.data.reserve_time;
                var houses = json.data.houses;
                var unit = json.data.unit;
                var number = json.data.number;
                var payfor = json.data.payfor;
                var pay_bank = json.data.pay_bank;
                var discount = json.data.discount;
                var pay_status = json.data.pay_status;
                var sign_zip = json.data.sign_zip;
                var reserve_class = json.data.reserve_class;
                var status = json.data.status;
                var progress = json.data.progress;
                var special = json.data.special;
                var notes = json.data.notes;
                switch(payfor){
                    case 1:payfor="现金";
                        break;
                    case 2:payfor="银行卡";
                        break;
                }
                switch(pay_bank){
                    case 1:pay_bank="工商银行";
                        break;
                    case 2:pay_bank="建设银行";
                        break;
                    case 3:pay_bank="农业银行";
                        break;
                    case 4:pay_bank="中国银行";
                        break;
                }
                switch(discount){
                    case 0:discount="未完成";
                        break;
                    case 1:discount="已完成";
                        break;
                }
                switch(pay_status){
                    case 0:pay_status="未完成";
                        break;
                    case 1:pay_status="已完成";
                        break;
                }
                switch(sign_zip){
                    case 0:sign_zip="未完成";
                        break;
                    case 1:sign_zip="已完成";
                        break;
                }
                switch(reserve_class){
                    case 1:status="草签";
                        break;
                    case 2:status="正签";
                        break;
                    case 3:status="其他";
                        break;
                }
                switch(status){
                    case 1:status="确认预约";
                        break;
                    case 2:status="取消预约";
                        break;
                    case 3:status="修改预约";
                        break;
                }
                switch(progress){
                    case 1:progress="按揭办理等待中";
                        break;
                    case 2:progress="按揭办理中";
                        break;
                    case 3:progress="按揭办理完成";
                        break;
                    case 4:progress="签约办理等待中";
                        break;
                    case 5:progress="签约办理中";
                        break;
                    case 6:progress="交款中";
                        break;
                    case 7:progress="办理完成";
                        break;
                }

                $('[name="username"]').val(username);
                $('[name="mobile"]').val(mobile);
                $('[name="reserve_time"]').val(reserve_time);
                $('[name="houses"]').val(houses);
                $('[name="unit"]').val(unit);
                $('[name="number"]').val(number);
                $('#payof p span').text(payfor);
                $('#pay_bank p span').text(pay_bank);
                $('#discount p span').text(discount);
                $('#pay_status p span').text(pay_status);
                $('#sign_zip p span').text(sign_zip);
                $('#reserve_class p span').text(reserve_class);
                $('#res_status p span').text(status);
                $('[name="special"]').val(special);
                $('[name="notes"]').val(notes);
            });
            $.ajax({
                url:'editreserves.html',
                type:'post',
                dataType:'html',
                success:function(data){
                    $(".system_page .content").html(data);
                    res_select();
                    $('div.fqyy>ul>li>button').click(function () {
                        var reserve_time=$('[name="reserve_time"]').val();
                        var houses=$('[name="houses"]').val();
                        var unit=$('[name="unit"]').val();
                        var number=$('[name="number"]').val();
                        var payfor=$('#payfor p span').attr('data-int');
                        var pay_bank=$('#pay_bank p span').attr('data-int');
                        var discount=$('#discount p span').attr('data-int');
                        var pay_status=$('#pay_status p span').attr('data-int');
                        var sign_zip=$('#sign_zip p span').attr('data-int');
                        var reserve_class=$('#reserve_class p span').attr('data-int');
                        var status=$('#res_status p span').attr('data-int');
                        var special=$('[name="special"]').val();
                        var notes=$('[name="notes"]').val();

                        reserve_time=getDate(reserve_time);
                        console.log(reserve_time);
                        $.getJSON('http://10.1.9.50/shou/public/api/reserveedit/'+$id+'/'+houses+'/'+unit+'/'+number+'/'+payfor+'/'+pay_bank+'/'+reserve_time+'/'+discount+'/'+pay_status+'/'+sign_zip+'/'+reserve_class+'/'+status+'/'+1+'/'+special+'/'+notes, function (data){
                            console.log(data);
                        });
                    });
                }
            });
        });
    });
}

/*叫号系统*/
function numbers() {
    num_query();
    $.getJSON('http://10.1.9.50/shou/public/api/numberlist', function (json) {
        console.log(json);
        for (var i = 0; i < json.data.length; i++) {
            var id = json.data[i].id;
            var number = json.data[i].number;
            var username = json.data[i].username;
            var mobile = json.data[i].mobile;
            var houses = json.data[i].houses;
            var unit = json.data[i].unit;
            var num = json.data[i].num;
            var number_class = json.data[i].number_class;
            number_class==1?number_class="签约":number_class="取证";

            $('#numbers_list tbody').append('<tr><td>'+number+'</td><td>'+username+'</td><td>'+mobile+'</td><td>'+houses+'</td><td>'+unit+'</td><td>'+num+'</td><td>'+number_class+'</td><td id="handle"><span>'+id+'</span><button>开始办理</button></td></tr>');
        }
    });
}
function num_query() {
    $('#number_query').click(function () {
        var number = $(".con_right input").val();
        if (number){
            $.getJSON('http://10.1.9.50/shou/public/api/numbersel/'+number, function (data) {
                var id = data.data.id;
                var number = data.data.number;
                var username = data.data.username;
                var mobile = data.data.mobile;
                var houses = data.data.houses;
                var unit = data.data.unit;
                var num = data.data.num;
                var number_class = data.data.number_class;
                number_class==1?number_class="签约":number_class="取证";

                $('#numbers_list tbody').html('<tr><td>'+number+'</td><td>'+username+'</td><td>'+mobile+'</td><td>'+houses+'</td><td>'+unit+'</td><td>'+num+'</td><td>'+number_class+'</td><td id="handle"><span>'+id+'</span><button>开始办理</button></td></tr>');
            });
        }
    });
}