<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title>我的预约</title>
    <link href="/css/reset.css" rel="stylesheet">
    <link href="/css/common.css" rel="stylesheet">
</head>
<body>
    @if(empty($data))
        <div class="meg_num"></div>
        <div class="status status1">预约信息</div>
        <ul class="content">
            <li><span>预约房源：</span><span class="txt" id="home">当前您没有预约信息</span></li>
        </ul>
    @else
    @foreach($data as $v)
    <div class="meg_num"></div>
    <div class="status status2">{{$v['reserve_progress']}}</div>
    <ul class="content">
        <li><span>预约房源：</span><span class="txt" id="home">{{$v['reserve_house']}}</span></li>
        <li><span>预约时间：</span><span class="txt" id="time">{{$v['reserve_date']}}</span></li>
        <li><span>预约类型：</span><span class="txt" id="type">{{$v['reserve_class']}}</span></li>
        <li><span>销售专员：</span><span class="txt" id="person">{{$v['reserve_staff']}}</span></li>
        <li><span>销售专员联系方式：</span><a class="txt" id="person" href="tel:{{$v['reserve_staff_mobile']}}">{{$v['reserve_staff_mobile']}}</a></li>
        <li><span>付款方式：</span><span class="txt" id="paytype">{{$v['reserve_payfor']}}</span></li>
        <li><span>贷款银行：</span><span class="txt" id="bank">{{$v['reserve_bank']}}</span></li>
        <li><span>备注：</span><span class="txt" id="bank">{{$v['reserve_notes']}}</span></li>
    </ul>
    @endforeach
    @endif
    {{--<div class="status status2">办理等待中</div>--}}
    {{--<ul class="content">--}}
        {{--<li><span>预约房源：</span><span class="txt" id="home">首创·爱这城 三号楼2单元301室</span></spa></li>--}}
        {{--<li><span>预约时间：</span><span class="txt" id="time"></span></li>--}}
        {{--<li><span>预约类型：</span><span class="txt" id="type"></span></li>--}}
        {{--<li><span>销售专员：</span><span class="txt" id="person"></span></li>--}}
        {{--<li><span>付款方式：</span><span class="txt" id="paytype"></span></li>--}}
        {{--<li><span>贷款银行：</span><span class="txt" id="bank"></span></li>--}}
    {{--</ul>--}}
    {{--<div class="status status3">办理等待中（已暂停）</div>--}}
    {{--<ul class="content">--}}
        {{--<li><span>预约房源：</span><span class="txt" id="home">首创·爱这城 三号楼2单元301室</span></spa></li>--}}
        {{--<li><span>预约时间：</span><span class="txt" id="time"></span></li>--}}
        {{--<li><span>预约类型：</span><span class="txt" id="type"></span></li>--}}
        {{--<li><span>销售专员：</span><span class="txt" id="person"></span></li>--}}
        {{--<li><span>付款方式：</span><span class="txt" id="paytype"></span></li>--}}
        {{--<li><span>贷款银行：</span><span class="txt" id="bank"></span></li>--}}
        {{--<li><span>备注：</span><span class="txt" id="remark"></span></li>--}}
    {{--</ul>--}}
    {{--<div class="status status4">办理完成</div>--}}
    {{--<ul class="content">--}}
        {{--<li><span>预约房源：</span><span class="txt" id="home">首创·爱这城 三号楼2单元301室</span></spa></li>--}}
        {{--<li><span>预约时间：</span><span class="txt" id="time"></span></li>--}}
        {{--<li><span>预约类型：</span><span class="txt" id="type"></span></li>--}}
        {{--<li><span>销售专员：</span><span class="txt" id="person"></span></li>--}}
        {{--<li><span>付款方式：</span><span class="txt" id="paytype"></span></li>--}}
        {{--<li><span>贷款银行：</span><span class="txt" id="bank"></span></li>--}}
        {{--<li><span>房产证：</span><span class="txt" id="deed"></span></li>--}}
        {{--<li><span>发票：</span><span class="txt" id="invoice"></span></li>--}}
    {{--</ul>--}}

</body>
<style>
    .content li a {
        color: #00a0e9;
    }
</style>
<script src="/js/jquery-1.9.1.min.js"></script>
<script src="/js/style.js"></script>
</html>