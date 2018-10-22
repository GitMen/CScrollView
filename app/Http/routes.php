<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', function () {
    return view('welcome');
});


Route::get('/userProgress', function () {
    return view('user_progress');
});

//===================================================微信接口位置
//微信后台
Route::any('/wx','WxController@index');
//自定义菜单
Route::any('/menu','WxController@setWechatMenu');
Route::any('/forget','WxController@forGetSession');
//菜单路由
//一键导航
Route::any('/wx/map','WxController@setWechatMap');
Route::any('/wx/suggesstion','WxController@suggestAdd');
Route::any('/wx/address','WxController@getAddress');
Route::any('/wx/notices','WxController@getNotices');
Route::any('/wx/progress','WxController@getProgress');


Route::any('/wx/sendExcle','WxController@sendExcle');
Route::any('/wx/getUserProgress','WxController@getUserProgress');


//个人绑定
Route::any('/wx/card/get/p','WxController@card_get_p');
//企业绑定
Route::any('/wx/card/get/c','WxController@card_get_c');
Route::any('/wx/get/my/reserve','WxController@get_my_reserve');


//oauth2
//建议授权
Route::any('/wx/sugg/login','IndexController@suggesstion_login');
//绑定授权
Route::any('/wx/bangding/login','IndexController@bangding_login');
//查看详情授权
Route::any('/wx/reserve/login','IndexController@reserve_login');

Route::any('/wx/logout','IndexController@logout');
Route::any('/wx/index','IndexController@index');

//模版消息
Route::any('/message/{reserve_id}','WxController@noticeMB');
//循环领导openid
Route::any('/sel/leader/fa','WxController@selLeader');
//fasong
Route::any('/message/leader/fa','WxController@noticeLeader');

//================================================微信接口结束============

//=====================通用接口未知
//查找项目列表200
Route::any('api/admin/selproject','AdminController@selProject');
//项目列表带银行id的OK200
Route::any('api/project/list/{page}','ProjectController@projectList');
//项目列表
//Route::any('/api/project/list','ReserveController@projectList');

//=====================================管理员后台接口位置
//超级管理员一键更改 预约进度adminEditReserver
Route::any('api/admin/edit/reserve/{reserve_id}/{id}','WxController@adminEditReserver');
//超级管理员 作废预约
Route::any('api/admin/abolish/{reserve_id}','WxController@adminAbolishReserver');
//检查工号200
Route::any('api/admin/getadminid/{adminid}','AdminController@getAdminid');
//管理员和员工检查手机号是否唯一    ---->重要
Route::any('api/get/mobile/all/admin','AdminController@getMobileAdmin');
//用户检查手机号是否唯一   ---->重要
Route::any('api/get/mobile/all/user','AdminController@getMobileUser');

//用户检查身份证号是否唯一   ---->重要
Route::any('api/get/card/all/user','UserController@getCardUser');
//企业检查注册号是否唯一   ---->重要
Route::any('api/get/card/all/company','UserController@getCardCompany');

//用户检查身份证 多个
Route::any('api/get/card/all/users','UserController@getCardUsers');
Route::any('api/get/phone/all/users','UserController@getCardUsersPhone');
Route::any('api/user/addJ','UserController@usercJAdd');


//添加管理员200
Route::any('api/admin/add','AdminController@adminAdd');
//删除管理员200
Route::any('api/admin/del/{id}','AdminController@adminDelete');
//查找管理员200
Route::any('api/admin/sel/{id}','AdminController@adminSelect');
//修改管理员200
Route::any('api/admin/edit/{id}','AdminController@adminEdit');
//管理员信息列表200
    Route::any('api/admin/list/{page}','AdminController@adminList');

//=====================================项目接口位置
//添加项目200
Route::any('api/project/add','ProjectController@projectAdd');
//查找项目200
Route::any('api/project/sel/{id}','ProjectController@projectSelect');
//修改项目200
Route::any('api/project/edit/{id}','ProjectController@projectEdit');


//=====================================成员接口位置
//返回该管理的项目200
Route::any('/api/staff/selproject/{admin_id}','ApiController@sysSelProject');
////检查工号200
//Route::any('/api/staff/getstaffid/{staffid}','ApiController@getStaffid');
//销售员工添加200
Route::any('/api/staff/add','ApiController@systemAdd');
//其他员工添加200
Route::any('/api/staffother/add','ApiController@systemOtherAdd');
//员工删除200
Route::any('/api/staff/del/{id}','ApiController@systemDel');
//员工修改200
Route::any('/api/staff/edit','ApiController@systemEdit');
//员工查找200
Route::any('/api/staff/sel/{id}','ApiController@systemSelect');
//其他员工查找200
Route::any('/api/staffother/sel/{id}','ApiController@systemOtherSelect');
//其他员工修改200
Route::any('/api/staffother/edit','ApiController@systemOtherEdit');
//全部员工列表200
Route::any('/api/staff/list/{page?}','ApiController@systemlist');
//管理员自己的员工列表200
Route::any('/api/staff/admin/list/{admin_id}/{page?}','ApiController@systemAdminlist');
//员工冻结
Route::any('/api/stafrozen/{id}','ApiController@staFrozen');


//=====================================登录退出接口位置
//管理员登录
Route::any('api/admin/login','StaffController@adminlogin');
Route::any('api/admin/logout','StaffController@adminlogout');

//员工登录退出
Route::any('api/qianyue/login','StaffController@login');
Route::any('api/staff/logout','StaffController@logout');
//===============================所有人改密
Route::any('api/allperson/editpsd','StaffController@editPsd');
Route::any('api/allperson/select/{id}','StaffController@staffsel');
//员工找回密码
Route::any('api/staff/stafffind/{mobile}','StaffController@stafffind');
Route::any('api/staff/findpsd/{mobile}/{psd}/{code}','StaffController@findPsd');

//=====================================所有客户管理接口位置===========
//新增个人用户
Route::any('/api/userp/add','UserController@userpAdd');
//新增企业用户
Route::any('/api/userc/add','UserController@usercAdd');
//成员登录显示自己的用户列表
Route::any('/api/user/list/{staff_id}/{page?}','UserController@userList');
//超级管理员访问的所有用户列表
Route::any('/api/user/admin/list/{page?}','UserController@userAdminList');
//顾问管理员访问自己成员所属的用户列表
Route::any('/api/user/admins/list/{admin_id}/{page?}','UserController@userAdminsList');

//查找用户200
Route::any('/api/user/sel/{id}','UserController@userSelect');
//修改用户200
Route::any('/api/user/edit/{id}','UserController@userEdit');
Route::any('/api/user/editJuser','UserController@editJuser');
//删除用户200
Route::any('/api/user/del/{id}','UserController@userDel');
//关键字查找用户信息
Route::any('/api/list/key/user/{key}','UserController@userListKey');
//用户检测房源是否被预约
Route::any('/api/project/reserve/get','ReserveController@getReserveOnly');
//用户建立预约信息
Route::any('/api/user/reserve/add','UserController@userDate');

Route::any('/api/user/reserve/sel/{user_id}','UserController@userDateSel');


Route::any('/api/user/findClass','UserController@findClass');

Route::any('/api/user/finJUser','UserController@finJUser');
//检测预约时段的人数是否达到上限
Route::any('/api/user/reserve/gettime/{date}/{id}','UserController@userDateTimeslots');
Route::any('/api/user/reserve/setRTime','UserController@setRTime');
Route::any('/api/user/reserve/getRTime','UserController@getRTime');

//==============================用户结束===========================//




//=====================================web预约管理
//一键过号
Route::any('/one/key/guohao','ReserveController@keyGuohao');
//顾问成员查看自己所属用户的预约列表200
Route::any('/api/reserve/list/{staff_id}/{page?}','ReserveController@reserveList');
//顾问成员查看自己所属用户的项目预约列表200
Route::any('/api/reserve/project/list/{staff_id}/{project_id}/{page?}','ReserveController@reserveStarProjectList');


//超级管理员访问的预约列表200
Route::any('/api/reserve/admin/list/{page?}','ReserveController@reserveAdminList');
//超级管理员访问的项目预约列表200
Route::any('/api/reserve/project/admin/list/{project_id}/{page?}','ReserveController@reserveStarAdminList');

//顾问管理员查看的预约列表OK
Route::any('/api/reserve/admins/list/{admin_id}/{page?}','ReserveController@reserveAdminsList');
//顾问管理员查看的项目预约列表OK
Route::any('/api/reserve/project/admins/list/{admin_id}/{project_id}/{page?}','ReserveController@reserveStarAdminsList');
//超管可以修改的预约接口
Route::any('/api/reserve/admin/edit','ReserveController@reserveAdminEdit');

//超管 和 项目管理员 可以删除的预约接口
Route::any('/api/reserve/admin/del/{reserve_id}','ReserveController@reserveAdminDel');

//======================档案管理员看的列表
//预约发送领取合同通知项目查找列表OK
Route::any('/api/reserve/project/progress/list/{project_id}/{page?}','ReserveController@reserveProjectList');
Route::any('/api/reserve/projectNoCam/progress/list/{project_id}/{page?}','ReserveController@reserveProjectListNoCam');
//预约全部已完成查找列表OK
Route::any('/api/reserve/progress/end/list/{page?}','ReserveController@reserveProgressEndList');
//预约完成发送领取合同的消息(可取合同按钮)
Route::any('/api/compact/get/ids','ReserveController@reserveGetCompact');

//预约完成未领取合同消息列表OK(所有可发合同列表)
Route::any('/api/compact/end/list/{page?}','ReserveController@reserveProgressGetCompactList');


Route::any('/api/uncompact/end/list/{page?}','ReserveController@reserveProgressGetUnCompactList');

Route::any('/api/reserve/project/updateStatusAll','ReserveController@reserveProjectUpdateStatus');

//关键字预约全部已完成查找列表OK
//Route::any('/api/reserve/progress/end/list/{key}', 'ReserveController@reserveProgressEndListKey');


//预约开始办理
Route::any('/api/reserve/start/{id}','ReserveController@reserveStart');
//预约叫号开始挂起
Route::any('/api/reserve/haltstart/{id}','ReserveController@reserveHaltStart');
//预约叫号结束挂起
Route::any('/api/reserve/haltend/{id}','ReserveController@reserveHaltEnd');
//预约叫号办理完成
Route::any('/api/reserve/end/{id}/{staff_id}','ReserveController@reserveEnd');

//今日预约自动过号
Route::any('/api/reserve/key/guohao','ReserveController@keyGuohao');


//预约中心 查找预约信息
Route::any('/api/reserve/sel/{id}','ReserveController@reserveSelect');
//预约中心 修改预约信息
Route::any('/api/reserve/edit/{id}','ReserveController@reserveEdit');
//预约中心 新建正签预约
Route::any('/api/user/reserve/zheng','UserController@userReserveClassDate');
//预约中心 重新预约查询200
Route::any('/api/reserve/restartsel/{id}','ReserveController@reserveRestartSel');
//预约中心 重新预约200
Route::any('/api/reserve/restart','ReserveController@reserveRestart');
//取消预约200
Route::any('/api/reserve/remove/{id}','ReserveController@reserveRemove');

// 预约时段200
//建立预约的时候判断的
Route::any('/api/timeslot/readTimeslot','TimeslotController@readTimeslot');
//预约时段列表200
Route::any('/api/timeslot/threadTimeslot','TimeslotController@threadTimeslot');
//新建客户和修改客户查询剩余人数200
Route::any('/api/timeslot/shengyu','TimeslotController@shengyuTimeslot');

//保存的预约时段列表 插入 排队列表200
Route::any('/api/timeslot/saveTimeslot','TimeslotController@saveTimeslot');

//预约关键字查找200
Route::any('/api/reserveselall/{key}/{progress?}','ReserveController@reserveSelectAll');
//预约关键字查找200
Route::any('/api/reserveselNoCom/{key}','ReserveController@reserveselNoCom');
//Route::any('/api/reserveseltime/{order}','ReserveController@reserveSelectTime');//(已经ok)
//预约 确认预约取消预约查询
Route::any('/api/reserveselstatus/{status}','ReserveController@reserveSelectStatus');//(已经ok)
//progress状态排序
Route::any('/api/reserveselprogerss/{progress}','ReserveController@reserveSelectProgress');//(已经ok)
//Route::any('/api/reservesel/{class}/{info}/{order}','ReserveController@reserveSelectUser');//weiceshi
//Route::any('/api/reservesel/{houses}/{unit}/{number}/{order}','ReserveController@reserveSelectHouses');//weiceshi

//web叫号系统
Route::any('api/numberlist','NumberController@numberList');
//输入叫号查找预约信息
Route::any('api/numbersel/{number}','NumberController@numberSelect');
//输入叫号查找取证信息
Route::any('api/numbersel/compact/{number}','NumberController@numberCompactSelect');

//取证完成
Route::any('/api/compact/end/ids','ReserveController@reserveCompactEnd');

//=====================================投诉建议接口位置
//投诉全部信息接口
Route::any('api/suggest/list/{page?}','FunController@suggestList');
//投诉 项目分组 接口
Route::any('api/suggest/project/list/{project_id}/{page?}','FunController@suggestProjectList');
//添加自定义回复等信息
Route::any('api/wx/answer/add','FunController@wxAnswersAdd');
//自定义回复列表
Route::any('api/wx/answer/list/{page?}','FunController@wxAnswersList');
//查找自定义回复
Route::any('api/wx/answer/sel/{id}','FunController@wxAnswersSelect');
//修改自定义回复消息
Route::any('api/wx/answer/edit/{id}','FunController@wxAnswersEdit');
//删除自定义回复消息
Route::any('api/wx/answer/del/{id}','FunController@wxAnswersDelete');


//=====================================预约报表excel接口位置
//预约项目时间段下载报表
Route::any('api/down/reserve/project/fanwei/excel/{reserve_stime}/{reserve_etime}/{project_id}','FunController@DownReserveProjectFanweiExcel');
//预约项目时间段列表报表
Route::any('api/get/reserve/project/fanwei/excel/{reserve_stime}/{reserve_etime}/{project_id}/{page?}','FunController@getReserveProjectFanweiExcel');
//预约项目下载报表
Route::any('api/down/reserve/project/excel/{project_id}','FunController@DownReserveProjectExcel');
//预约项目列表报表
Route::any('api/get/reserve/project/excel/{project_id}/{page?}','FunController@getReserveProjectExcel');

//预约时间段下载报表
Route::any('api/down/reserve/excel/{reserve_stime}/{reserve_etime}','FunController@DownReserveFanweiExcel');
//预约时间段列表报表
Route::any('api/get/reserve/excel/{reserve_stime}/{reserve_etime}/{page?}','FunController@getReserveFanweiExcel');

//预约全部下载报表
Route::any('api/down/excel/reserve','FunController@DownReserveExcel');
//预约全部报表{10 18添加了page分页}
Route::any('api/get/reserve/excel/{page?}','FunController@getReserveExcel');


//======================================预约完成
//预约完成项目时间段下载报表
Route::any('api/down/reserveend/project/fanwei/excel/{reserve_stime}/{reserve_etime}/{project_id}','FunController@DownReserveendProjectFanweiExcel');
//预约完成项目时间段列表报表
Route::any('api/get/reserveend/project/fanwei/excel/{reserve_stime}/{reserve_etime}/{project_id}/{page?}','FunController@getReserveendProjectFanweiExcel');

//预约完成项目下载报表
Route::any('api/down/reserveend/project/excel/{project_id}','FunController@DownReserveendProjectExcel');
//预约完成项目列表报表
Route::any('api/get/reserveend/project/excel/{project_id}/{page}','FunController@getReserveendProjectExcel');

//预约完成时间段下载报表
Route::any('api/down/reserveend/excel/{reserve_stime}/{reserve_etime}','FunController@DownReserveendFanweiExcel');
//预约完成时间段列表报表
Route::any('api/get/reserveend/excel/{reserve_stime}/{reserve_etime}/{page}','FunController@getReserveendFanweiExcel');

//预约完成全部下载报表
Route::any('api/down/reserveend/excel','FunController@DownReserveendExcel');
//预约完成全部报表
Route::any('api/get/reserveend/excel/{page}','FunController@getReserveendExcel');


//======================================预约完成数据分析
//预约完成      项目时间段下载报表
Route::any('api/down/endinfo/project/fanwei/excel/{reserve_stime}/{reserve_etime}/{project_id}','FunController@DownEndinfoProjectFanweiExcel');
//预约完成      项目时间段列表报表
Route::any('api/get/endinfo/project/fanwei/excel/{reserve_stime}/{reserve_etime}/{project_id}','FunController@getEndinfoProjectFanweiExcel');

//预约完成时间段下载报表
Route::any('api/down/endinfo/excel/{reserve_stime}/{reserve_etime}','FunController@DownEndinfoFanweiExcel');
//预约完成时间段列表报表
Route::any('api/get/endinfo/excel/{reserve_stime}/{reserve_etime}','FunController@getEndinfoFanweiExcel');

//======================================领取合同完成详情
//合同完成      项目时间段下载报表
Route::any('api/down/compact/project/fanwei/excel/{reserve_stime}/{reserve_etime}/{project_id}','FunController@DownCompactProjectFanweiExcel');
//合同完成      项目时间段列表报表
Route::any('api/get/compact/project/fanwei/excel/{reserve_stime}/{reserve_etime}/{project_id}','FunController@getCompactProjectFanweiExcel');

//合同完成时间段下载报表
Route::any('api/down/compact/excel/{reserve_stime}/{reserve_etime}','FunController@DownCompactFanweiExcel');
//合同完成时间段列表报表
Route::any('api/get/compact/excel/{reserve_stime}/{reserve_etime}','FunController@getCompactFanweiExcel');

//======================================预约类型管理
//添加预约类型
Route::any('api/type/add','AppointmentTypeController@addType');
Route::any('api/type/get','AppointmentTypeController@getTypeList');
Route::any('api/type/delete','AppointmentTypeController@deleteType');

Route::any('api/bank/add','AppointmentTypeController@addBank');
Route::any('api/bank/get','AppointmentTypeController@getBankList');
Route::any('api/bank/delete','AppointmentTypeController@deleteBank');

////预约到达率报表
//Route::any('api/get/reserve/excel/{reserve_stime?}/{reserve_etime?}/{project_id?}','FunController@getReserveExcel');
//
////预约完成明细率下载报表
//Route::any('api/down/reservefind/excel/{reserve_stime?}/{reserve_etime?}/{project_id?}','FunController@DownReserveFindExcel');
////预约完成明细报表 
//Route::any('api/get/reservefind/excel/{reserve_stime?}/{reserve_etime?}/{project_id?}','FunController@getReserveFindExcel');
//
////预约完成率报表
//Route::any('api/endreserve/excel/{reserve_stime}/{reserve_etime}','FunController@endReserveExcel');
////短信api
//Route::any('api/getcode','FunController@getCode');





////=====================================测试路由位置
////测试路由
//Route::any('api/testpostttt','TestController@testPost');
////测试路由
Route::get('/','TestController@index');
Route::get('/test','TestController@test');
//Route::get('/get/{id}','TestController@get');
////Route::get('/api/{format}','TestController@res');
//Route::get('/api/getsession','StaffController@ddSession');
//Route::get('/api/admin/{adminid}/{psd}','StaffController@adminlogin');




//=====================================叫号机接口位置
//叫号机api路由
//获取普通用户预约信息
Route::any('api/getreserve/{mobile}/{card_four}/{card}','ReserveController@getReserve');
//获取公司用户预约信息
Route::any('api/getcomreserve/{regnum}','ReserveController@getComReserve');
//获取普通用户取合同信息
Route::any('api/getcompact/{mobile}/{card_four}/{card}','ReserveController@getComPact');
//获取企业用户取合同信息
Route::any('api/getbuscompact/{regnum}','ReserveController@getBusComPact');
//绑定号码
Route::any('api/getreservenum/{id}/{num}','ReserveController@getReserveNum');
//过号
Route::any('api/overreservenum/{number}','ReserveController@overReserveNum');