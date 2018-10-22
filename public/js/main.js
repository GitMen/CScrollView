/**
 * Created by liyi on 16/7/8.
 */
/*登录*/
var role;
var admin_id;
var staff_id;
var username;
var module = {
    framework: true,
    users: true,
    reserves: true,
    numbers: true,
    faqs: true,
    report: true,
    suggestion: true,
    options: true
};
getUrlPara();
function getUrlPara() {
    var url = location.search;
    var theRequest = new Object();
    if (url.indexOf("?") != -1) {
        var str = url.substr(1);
        strs = str.split("&");
        for (var i = 0; i < strs.length; i++) {
            theRequest[strs[i].split("=")[0]] = decodeURI(strs[i].split("=")[1]);
        }
    }
    role = theRequest.role;
    if (theRequest.admin_id) {
        admin_id = theRequest.admin_id;
    } else if (theRequest.staff_id) {
        staff_id = theRequest.staff_id;
    }
    username = theRequest.username;
}

role_login();
function role_login() {
    if (role == 0) {
        $("#user_info .username").html(username);
        module.numbers = false;
        backEnd(module);
    } else if (role == 1) {
        $("#user_info .username").html(username);
        module.numbers = false;
        module.framework = false;
        module.users = false;
        module.reserves = false;
        module.faqs = false;
        module.suggestion = false;
        module.options = false;
        backEnd(module);
    } else if (role == 2) {
        $("#user_info .username").html(username);
        module.numbers = false;
        module.framework = false;
        module.users = false;
        module.reserves = false;
        module.faqs = false;
        module.suggestion = false;
        module.options = false;
        backEnd(module);
    } else if (role == 3) {
        $("#user_info .username").html(username);
        module.numbers = false;
        module.faqs = false;
        module.suggestion = false;
        module.options = false;
        module.report = false;
        backEnd(module);
    } else if (role == 4) {
        $("#user_info .username").html(username);
        module.numbers = false;
        module.users = false;
        module.reserves = false;
        module.faqs = false;
        module.suggestion = false;
        module.report = false;
        backEnd(module);
    } else if (role == 6) {
        $("#user_info .username").html(username);
        module.framework = false;
        module.users = false;
        module.reserves = false;
        module.faqs = false;
        module.suggestion = false;
        module.report = false;
        module.options = false;
        backEnd(module);
    } else if (role == 5) {
        $("#user_info .username").html(username);
        module.framework = false;
        module.numbers = false;
        module.faqs = false;
        module.suggestion = false;
        module.report = false;
        module.options = false;
        backEnd(module);
    } else if (role == 7) {
        $("#user_info .username").html(username);
        module.framework = false;
        module.users = false;
        module.faqs = false;
        module.suggestion = false;
        module.report = false;
        module.options = false;
        backEnd(module);
    }
}

function role_framework() {
    if (role == 0) {
        $("#framework .secondary_menu").append("<li data-url='framework/adminlist.html'><i class='iconfont'>&#xe609;</i>管理员列表</li><li data-url='framework/staffslist.html'><i class='iconfont'>&#xe609;</i>员工列表</li><li data-url='framework/projectlist.html'><i class='iconfont'>&#xe609;</i>项目列表</li>");
    } else if (role == 3) {
        $("#framework .secondary_menu").append("<li data-url='framework/staffslist.html'><i class='iconfont'>&#xe609;</i>员工列表</li>");
    } else if (role == 4) {
        $("#framework .secondary_menu").append("<li data-url='framework/staffslist.html'><i class='iconfont'>&#xe609;</i>员工列表</li>");
    }
}

function role_() {

}

function backEnd(module) {
    if (module.framework) {
        $(".side_nav").append("<li id='framework'><p></p><i class='iconfont'>&#xe605;</i><span>组织架构</span><i class='iconfont arrow'>&#xe607;</i><ul class='list-unstyled secondary_menu'></ul></li>");
        role_framework();
    }
    if (module.users) {
        $(".side_nav").append("<li id='users'><p></p><i class='iconfont'>&#xe603;</i><span>客户管理</span><i class='iconfont arrow'>&#xe607;</i><ul class='list-unstyled secondary_menu'><li data-url='users/enterprise.html'><i class='iconfont'>&#xe609;</i>新增客户</li><li data-url='users/userlist.html'><i class='iconfont'>&#xe609;</i>客户列表</li></ul></li>");
    }
    if (module.reserves) {
        $(".side_nav").append("<li data-url='reserves/reserves.html'><p></p><i class='iconfont'>&#xe601;</i><span>预约管理</span></li>");
    }
    if (module.numbers) {
        $(".side_nav").append("<li data-url='numbers.html'><p></p><i class='iconfont'>&#xe606;</i><span>叫号系统</span></li>");
    }
     if (module.faqs) {
        $(".side_nav").append("<li data-url='interlocution/interlocution.html'><p></p><i class='iconfont'>&#xe60f;</i><span>问答系统</span></li>");
    }
    if (module.report) {
        $(".side_nav").append("<li><p></p><i class='iconfont'>&#xe604;</i><span>报表</span><i class='iconfont arrow'>&#xe607;</i><ul class='list-unstyled secondary_menu'><li data-url='addUser.html'><i class='iconfont'>&#xe609;</i>预约报表</li><li data-url='report/details.html'><i class='iconfont'>&#xe609;</i>签约完成情况明细</li><li data-url='userlist.html'><i class='iconfont'>&#xe609;</i>预约完成数据统计</li><li data-url='userlist.html'><i class='iconfont'>&#xe609;</i>合同领取数量</li></ul></li>");
    }
    if (module.suggestion) {
        $(".side_nav").append("<li  data-url='suggestions/suggestions.html'><p></p><i class='iconfont'>&#xe613;</i><span>投诉建议</span></li>");
    }
    if (module.options) {
        $(".side_nav").append("<li><p></p><i class='iconfont'>&#xe600;</i><span>设置</span><i class='iconfont arrow'>&#xe607;</i><ul class='list-unstyled secondary_menu'><li data-url='option/set.html'><i class='iconfont'>&#xe609;</i>签约时段设置</li><li data-url='option/besadmin.html'><i class='iconfont'>&#xe609;</i>预约后台账号设置</li></ul></li>");
    }
}

/*下拉菜单*/
//TODO 完成
function select_event() {
    $('.add_form>ul>li p').click(function () {
        if ($(this).next().is(":visible")) {
            $(this).next().hide();
        } else {
            $(this).next().show();
        }
    });
    $('.add_form>ul>li>ul>li').click(function () {
        var $text = $(this).text();
        var $data_int = $(this).attr('data-int');
        $('.add_form>ul>li>ul').not(".checkbox_style").hide();
        $('.add_form>ul>li>p>span').html($text);
        $('.add_form>ul>li>p>span').attr("data-int", $data_int);
        if ($data_int == 3 || $data_int == 4) {
            fun_projects();
        } else {
            $("#projects").hide();
        }
    });
}

//TODO 完成
function select_event1() {
    $('.add_form>ul>li p').click(function () {
        if ($(this).next().is(":visible")) {
            $(this).next().hide();
        } else {
            $(this).next().show();
        }
    });
    $('.add_form>ul>li>ul>li').click(function () {
        var $text = $(this).text();
        var $data_int = $(this).attr('data-int');
        $('.add_form>ul>li>ul').hide();
        $(this).parent().prev().children("span").html($text);
        $(this).parent().prev().children("span").attr("data-int", $data_int);
        if ($data_int == 5) {
            fun_project();
        } else {
            $("#project").hide();
        }
    });
}

function select_event2() {
    $('#project>ul>li').click(function () {
        var $text = $(this).text();
        var $data_in = $(this).attr('data-in');
        $('.add_form>ul>li>ul').hide();
        $(this).parent().prev().children("span").html($text);
        $(this).parent().prev().children("span").attr("data-in", $data_in);
    });
}

function select_reserves() {
    $('.add_form>ul>li p').click(function () {
        if ($(this).next().is(":visible")) {
            $(this).next().hide();
        } else {
            $(this).next().show();
        }
    });
    $('.add_form>ul>li>ul>li').click(function () {
        var $text = $(this).text();
        var $data_int = $(this).attr('data-int');
        $('.add_form>ul>li>ul').hide();
        $(this).parent().prev().children("span").html($text);
        $(this).parent().prev().children("span").attr("data-int", $data_int);
        if ($("#payfor>p span").attr("data-int") == 1) {
            $("#total_money").show();
            $("#first_money,#loan_money,#pay_bank").hide();
        } else {
            $("#total_money,#first_money,#loan_money,#pay_bank").show();
        }

    });
}

//TODO 完成
/*关联项目*/
function fun_project() {
    $.getJSON("http://zzo0jd72rt.proxy.qqbrowser.cc/api/staff/selproject/" + admin_id, function (json) {
        console.log(json);
        if (json.code == 200) {
            $("#project").show();
            $("#project ul").html("");
            for (var i = 0; i < json.data.length; i++) {
                $("#project ul").append("<li data-in=" + json.data[i].project_id + ">" + json.data[i].project_name + "</li>");
            }
            select_event2();
        }
    });
}

//TODO 完成
/*管理员关联项目*/
function fun_projects(arr) {
    $.getJSON("http://zzo0jd72rt.proxy.qqbrowser.cc/api/admin/selproject", function (json) {
        if (json.code == 200) {
            $("#projects").show();
            $("#projects ul").html("");
            for (var i = 0; i < json.data.length; i++) {
                $("#projects ul").append("<li><lable><input type='checkbox' name='project' value=" + json.data[i].id + ">" + json.data[i].name + "</lable></li>");
            }
            if (arr) {
                for (var i = 0; i < arr.length; i++) {
                    $("#projects input[value=" + arr[i].id + "]").attr("checked", true);
                }
            }
        }
    });
}

/*组织架构*/
//TODO 完成
/*检查账号*/
function getadminid(id) {
    $.getJSON("http://zzo0jd72rt.proxy.qqbrowser.cc/api/admin/getadminid/" + id, function (json) {
        if (json.code == 200) {
            $("#id_success").show();
            admin_btn_save(id);
        } else {
            $("#id_error").show();
            $('.a_save').unbind();
        }
    });
}

function getstaffid(id) {
    $.getJSON("http://zzo0jd72rt.proxy.qqbrowser.cc/api/staff/getstaffid/" + id, function (json) {
        if (json.code == 200) {
            $("#id_success").show();
            staff_btn_save(id);
        } else {
            $("#id_error").show();
            $('.s_save').unbind();
        }
    });
}

/*新增按钮*/
function addBtn() {
    $(".btn_add a").click(function () {
        var data_url = $(this).attr('data-url');
        var className = $(this).attr('class');
        $.ajax({
            url: data_url,
            type: 'post',
            dataType: 'html',
            success: function (data) {
                $(".system_page .content").html(data);
                if (className == "addAdmin") {
                    addAdmin();
                } else if (className == "addStaffs") {
                    addStaffs();
                } else if (className == "addProject") {
                    addProject();
                }
            }
        });
    });
}

//TODO 完成
/*删除按钮*/
function btn_del(url) {
    $('.delete').click(function () {
        var $id = $(this).children('span').text();
        $.getJSON(url + $id, function (data) {
            if (data.code == 200 && data.message == "success") {
                alert(data.data.status);
            } else {
                alert("无法删除!");
            }
        });
        $(this).parent().remove();
    });
}

//TODO 测试
/*保存按钮*/
function admin_btn_save(aid, id) {
    $('.a_save').click(function () {
        if (id) {
            var projects = [];
            $('#projects input[name="project"]:checked').each(function () {
                projects.push($(this).val());
            });
            var admin = {
                'id': id,
                'adminid': aid,
                'name': $('[name="name"]').val(),
                'mobile': $('[name="mobile"]').val(),
                'role': $('.role span').attr("data-int"),
                'projects': projects
            };
            $.post('http://zzo0jd72rt.proxy.qqbrowser.cc/api/admin/edit/' + id, admin, function (data) {
                var data = JSON.parse(data);
                if (data.code == 200) {
                    alert("添加成功!");
                    $.ajax({
                        url: 'framework/adminlist.html',
                        type: 'post',
                        dataType: 'html',
                        success: function (data) {
                            $(".system_page .content").html(data);
                        }
                    });
                } else {
                    alert("修改失败!");
                }
            });
        } else {
            var projects = [];
            $('#projects input[name="project"]:checked').each(function () {
                projects.push($(this).val());
            });
            var admin = {
                'adminid': aid,
                'password': $('[name="psd"]').val(),
                'name': $('[name="name"]').val(),
                'mobile': $('[name="mobile"]').val(),
                'role': $('.role span').attr("data-int"),
                'projects': projects
            };
            $.post('http://zzo0jd72rt.proxy.qqbrowser.cc/api/admin/add', admin, function (data) {
                var data = JSON.parse(data);
                if (data.code == 200) {
                    alert("添加成功!");
                    $.ajax({
                        url: 'framework/addAdmin.html',
                        type: 'post',
                        dataType: 'html',
                        success: function (data) {
                            $(".system_page .content").html(data);
                            addAdmin();
                        }
                    });
                } else {
                    alert("保存失败!");
                }
            });
        }
    });
}

function staff_btn_save(aid, id) {
    $('.s_save').click(function () {
        if (id) {
            var projects = [];
            $('#projects input[name="project"]:checked').each(function () {
                projects.push($(this).val());
            });
            var admin = {
                'id': id,
                'adminid': aid,
                'name': $('[name="name"]').val(),
                'mobile': $('[name="mobile"]').val(),
                'role': $('.role span').attr("data-int"),
                'projects': projects
            };
            $.post('http://zzo0jd72rt.proxy.qqbrowser.cc/api/admin/edit/' + id, admin, function (data) {
                var data = JSON.parse(data);
                if (data.code == 200) {
                    alert("添加成功!");
                    $.ajax({
                        url: 'framework/adminlist.html',
                        type: 'post',
                        dataType: 'html',
                        success: function (data) {
                            $(".system_page .content").html(data);
                        }
                    });
                } else {
                    alert("修改失败!");
                }
            });
        } else {
            var staff = {
                'admin_id': admin_id,
                'staffid': aid,
                'password': $('[name="psd"]').val(),
                'name': $('[name="name"]').val(),
                'mobile': $('[name="mobile"]').val(),
                'role': $('.role span').attr("data-int"),
                'project_id': $('#project p span').attr("data-in")
            };
            console.log(staff);
            $.post('http://zzo0jd72rt.proxy.qqbrowser.cc/api/staff/add', staff, function (data) {
                console.log(data);
                var data = JSON.parse(data);
                if (data.code == 200) {
                    alert("添加成功!");
                    $.ajax({
                        url: 'framework/addAdmin.html',
                        type: 'post',
                        dataType: 'html',
                        success: function (data) {
                            $(".system_page .content").html(data);
                            addAdmin();
                        }
                    });
                } else {
                    alert("保存失败!");
                }
            });
        }
    });
}

//TODO 分页
/*管理员列表*/
function adminlist() {
    addBtn();
    $.getJSON('http://zzo0jd72rt.proxy.qqbrowser.cc/api/admin/list/1', function (json) {
        console.log(json);
        var page = json.data[1];
        for (var i = 0; i < json.data[2].length; i++) {
            var id = json.data[2][i].id;
            var name = json.data[2][i].name;
            var mobile = json.data[2][i].mobile;
            // var adminid=json.data[2][i].adminid;
            var role = json.data[2][i].role;
            var projects = [];

            switch (role) {
                case 1:
                    role = "总部领导";
                    break;
                case 2:
                    role = "项目领导";
                    break;
                case 3:
                    role = "顾问管理员";
                    break;
                case 4:
                    role = "签约管理员";
                    break;
            }

            $("#admin_list tbody").append('<tr><td>' + name + '</td><td>' + mobile + '</td><td>' + role + '</td><td class=apartment' + i + '></td><td class="delete"><span>' + id + '</span><i class="iconfont">&#xe60d;</i></td><td class="edit"><i class="iconfont">&#xe60c;</i></td></tr>');
            for (var j = 0; j < json.data[2][i].projects.length; j++) {
                projects.push(json.data[2][i].projects[j].name);
            }
            for (var s = 0; s < projects.length; s++) {
                $("#admin_list tbody .apartment" + i).append(projects[s] + ";");
            }
        }
        var url = "http://zzo0jd72rt.proxy.qqbrowser.cc/api/admin/del/";
        btn_del(url);

        $('.edit').click(function () {
            var $id = $(this).prev().children('span').text();
            $.ajax({
                url: 'framework/addAdmin.html',
                type: 'post',
                dataType: 'html',
                success: function (data) {
                    $(".system_page .content").html(data);
                    edit_admin($id);
                }
            });
        });
    });
}

//TODO  完成
/*新增管理员*/
function addAdmin() {
    var admin_id;
    $("input[name=adminid]").focus(function () {
        $(".add_form>p").hide();
    });
    $("input[name=adminid]").blur(function () {
        $(".add_form>p").hide();
        admin_id = $('[name="adminid"]').val();
        if (admin_id) {
            getadminid(admin_id);
        } else if (admin_id == "") {
            $("#id_null").show();
            $('.a_save').unbind();
        }
    });
    select_event();
}

//TODO 完成
/*修改管理员信息*/
function edit_admin(id) {
    select_event();
    $.getJSON("http://zzo0jd72rt.proxy.qqbrowser.cc/api/admin/sel/" + id, function (json) {
        console.log(json);
        var id = json.data.id;
        var name = json.data.name;
        var mobile = json.data.mobile;
        var adminid = json.data.adminid;
        var projects = [];
        var role_id = json.data.role;
        var role;
        switch (role_id) {
            case 1:
                role = "总部领导";
                break;
            case 2:
                role = "项目领导";
                break;
            case 3:
                role = "顾问管理员";
                break;
            case 4:
                role = "签约管理员";
                break;
        }

        $('[name="adminid"]').val(adminid);
        $('[name="adminid"]').attr("readOnly", true);
        $('[name="psd"]').parent().hide();
        $('[name="name"]').val(name);
        $('[name="mobile"]').val(mobile);
        $('.role span').text(role);
        $('.role span').attr("data-int", role_id);

        if (role_id == 2 || role_id == 3) {
            fun_projects(json.data.projects);
        } else {
            $("#projects").hide();
        }
        admin_btn_save(adminid, id);
    });
}

/*员工列表*/
function staffslist() {
    addBtn();
    $.getJSON('http://zzo0jd72rt.proxy.qqbrowser.cc/api/staff/list/' + 1, function (json) {
        console.log(json);
        var page = json.data[1];
        for (var i = 0; i < json.data[2].length; i++) {
            var id = json.data[2][i].id;
            var name = json.data[2][i].name;
            var mobile = json.data[2][i].mobile;
            // var staffid=json.data[2][i].staffid;
            var role_id = json.data[2][i].role;
            var role;
            var project_id = json.data[2][i].project.id;
            var project_name = json.data[2][i].project.name;

            switch (role_id) {
                case "6":
                    role = "签约专员";
                    break;
                case "5":
                    role = "销售顾问";
                    break;
                case "7":
                    role = "档案专员";
                    break;
            }

            $("#staffs_list tbody").append('<tr><td>' + name + '</td><td>' + mobile + '</td><td>' + role + '</td><td>' + project_name + '</td><td class="delete"><span>' + id + '</span><i class="iconfont">&#xe60d;</i></td><td class="edit"><i class="iconfont">&#xe60c;</i></td></tr>');
        }
        var url = "http://zzo0jd72rt.proxy.qqbrowser.cc/api/staff/del/";
        btn_del(url);

        $('.edit').click(function () {
            var $id = $(this).prev().children('span').text();
            $.ajax({
                url: 'framework/addStaffs.html',
                type: 'post',
                dataType: 'html',
                success: function (data) {
                    $(".system_page .content").html(data);
                    edit_staff($id);
                }
            });
        });
    });
}

/*新增员工*/
function addStaffs() {
    var staffid;
    $("input[name=staffid]").focus(function () {
        $(".add_form>p").hide();
    });
    $("input[name=staffid]").blur(function () {
        $(".add_form>p").hide();
        staffid = $('[name="staffid"]').val();
        if (staffid) {
            getstaffid(staffid);
        } else if (staffid == "") {
            $("#id_null").show();
            $('.s_save').unbind();
        }
    });
    select_event1();
}

/*修改员工信息*/
function edit_staff(sid) {
    $.getJSON('http://zzo0jd72rt.proxy.qqbrowser.cc/api/staff/sel/'+sid, function (data) {
        console.log(data);
        var id=data.data.id;
        var mobile=data.data.mobile;
        var name= data.data.name;
        var project_id=data.data.project.id;
        var project_name=data.data.project.name;
        var role_id=data.data.role;
        var role;
        var staffid=data.data.staffid;

        switch (role_id) {
            case "6":
                role = "签约专员";
                break;
            case "5":
                role = "销售顾问";
                break;
            case "7":
                role = "档案专员";
                break;
        }

        $("[name='staffid']").val(staffid);
        $("[name='staffid']").attr("readOnly", true);
        $("[name='name']").val(name);
        $("[name='mobile']").val(mobile);
        $(".role span").text(role);
        $(".role span").attr("data-int",role_id);
        $("#project>p span").text(project_name);

        $('.s_save').click(function () {
            var staff = {
                'name': $('[name="name"]').val(),
                'mobile': $('[name="mobile"]').val(),
                'id': id,
                'password': $('[name="psd"]').val()
            };
            console.log(staff);
            $.post('http://zzo0jd72rt.proxy.qqbrowser.cc/api/staff/edit',staff, function (data) {
                var data = JSON.parse(data);
                if (data.code == 200 && data.message == "success") {
                    alert(data.data.status);
                    $.ajax({
                        url: 'framework/staffslist.html',
                        type: 'post',
                        dataType: 'html',
                        success: function (data) {
                            $(".system_page .content").html(data);
                            addStaffs();
                        }
                    });
                } else {
                    alert("提交失败");
                }
            });
        });
    });
    select_event1();

}

//TODO 完成
/*项目列表*/
function projectlist() {
    addBtn();
    $.getJSON('http://zzo0jd72rt.proxy.qqbrowser.cc/api/project/list/1', function (json) {
        console.log(json);
        var page = json.data[1];
        for (var i = 0; i < json.data[2].length; i++) {
            var id = json.data[2][i].id;
            var name = json.data[2][i].name;
            var bank = json.data[2][i].bank;
            var bank_name;

            $("#project_list tbody").append('<tr><td>' + name + '</td><td class=bank' + i + '></td><td class="edit"><span>' + id + '</span><i class="iconfont">&#xe60c;</i></td></tr>');

            for (var j = 0; j < bank.length; j++) {
                switch (bank[j]) {
                    case 1:
                        bank_name = "中国工商银行";
                        break;
                    case 2:
                        bank_name = "中国农业银行";
                        break;
                    case 3:
                        bank_name = "光大银行";
                        break;
                    case 4:
                        bank_name = "建设银行";
                        break;
                    case 5:
                        bank_name = "中国银行";
                        break;
                    case 6:
                        bank_name = "招商银行";
                        break;
                    case 7:
                        bank_name = "北京银行";
                        break;
                    case 8:
                        bank_name = "兴业银行";
                        break;
                    case 9:
                        bank_name = "交通银行";
                        break;
                    case 10:
                        bank_name = "民生银行";
                        break;
                    case 11:
                        bank_name = "平安银行";
                        break;
                    case 12:
                        bank_name = "广发银行";
                        break;
                    case 13:
                        bank_name = "邮政储蓄银行";
                        break;
                    case 14:
                        bank_name = "渤海银行";
                        break;
                    case 15:
                        bank_name = "北京农商银行";
                        break;
                }
                $(".bank" + i).append(bank_name + ";");
            }
        }
        $('.edit').click(function () {
            var $id = $(this).children('span').text();
            $.ajax({
                url: 'framework/addProject.html',
                type: 'post',
                dataType: 'html',
                success: function (data) {
                    $(".system_page .content").html(data);
                    edit_project($id);
                }
            });
        });

    });
}

//TODO 完成
/*新增项目*/
function addProject() {
    $(".p_save").click(function () {
        var name = $("input[name=project_name]").val();
        var banks = [];
        $('#banks input[name="banks"]:checked').each(function () {
            banks.push($(this).val());
        });
        $.post("http://zzo0jd72rt.proxy.qqbrowser.cc/api/project/add", {name: name, banks: banks}, function (data) {
            var data = JSON.parse(data);
            if (data.code == 200) {
                alert("添加成功!");
                $.ajax({
                    url: 'framework/addProject.html',
                    type: 'post',
                    dataType: 'html',
                    success: function (data) {
                        $(".system_page .content").html(data);
                        addProject();
                    }
                });
            } else {
                alert("保存失败!");
            }
        });
    });
}

//TODO 完成
/*修改项目信息*/
function edit_project(id) {
    $.getJSON("http://zzo0jd72rt.proxy.qqbrowser.cc/api/project/sel/" + id, function (json) {
        var name = json.data.name;
        var bank = json.data.bank;
        for (var i = 0; i < bank.length; i++) {
            $("#banks input[value=" + bank[i][0] + "]").attr("checked", true);
        }
        $("input[name=project_name]").val(name);
    });
    $('.p_save').click(function () {
        var name = $("input[name=project_name]").val();
        var banks = [];
        $('#banks input[name="banks"]:checked').each(function () {
            banks.push($(this).val());
        });

        $.getJSON('http://zzo0jd72rt.proxy.qqbrowser.cc/api/project/edit/' + id, {name: name, banks: banks}, function (data) {
            console.log(data);
            if (data.code == 200 && data.message == "success") {
                alert(data.data.status);
                $.ajax({
                    url: 'framework/projectlist.html',
                    type: 'post',
                    dataType: 'html',
                    success: function (data) {
                        $(".system_page .content").html(data);
                    }
                });
            } else {
                alert("修改失败");
            }
        });
    });
}

/*叫号系统*/
function numbers() {

}

/*问答系统*/
function faqs() {

}

/*报表*/
function report(arrive, finish) {
    if (arrive) {
        aaa
    }
    if (finish) {
        aaa
    }
}

/*投诉建议*/
function suggestion() {

}

/*设置*/
function options(time, account) {
    if (time) {
        aaa
    }
    if (account) {
        aaa
    }
}

//TODO 完成
/*新增客户*/
function adduser(classid) {
    if (classid) {
        $("#enterprise_p").click(function () {
            $.ajax({
                url: 'users/enterprise.html',
                type: 'post',
                dataType: 'html',
                success: function (data) {
                    $(".system_page .content").html(data);
                }
            });
        });
    } else {
        $("#user_p").click(function () {
            $.ajax({
                url: 'users/addUser.html',
                type: 'post',
                dataType: 'html',
                success: function (data) {
                    $(".system_page .content").html(data);
                }
            });
        });
    }
    $('.u_save').click(function () {
        var user = {
            'staff_id': staff_id,
            'username': $('[name="username"]').val(),
            'mobile': $('[name="mobile"]').val(),
            'card_id': $('[name="card_id"]').val()
        };
        console.log(user);
        if (classid) {
            $.post('http://zzo0jd72rt.proxy.qqbrowser.cc/api/userp/add', user, function (data) {
                var data = JSON.parse(data);
                if (data.code == 200) {
                    alert(data.data.status);
                    $.ajax({
                        url: 'users/addUser.html',
                        type: 'post',
                        dataType: 'html',
                        success: function (data) {
                            $(".system_page .content").html(data);
                        }
                    });
                } else {
                    alert("提交失败");
                }
            });
        } else {
            $.post('http://zzo0jd72rt.proxy.qqbrowser.cc/api/userc/add', user, function (data) {
                var data = JSON.parse(data);
                if (data.code == 200) {
                    alert(data.data.status);
                    $.ajax({
                        url: 'users/enterprise.html',
                        type: 'post',
                        dataType: 'html',
                        success: function (data) {
                            $(".system_page .content").html(data);
                        }
                    });
                } else {
                    alert("提交失败");
                }
            });
        }
    });
}

/*客户列表*/
function userlist() {
    $.getJSON('http://zzo0jd72rt.proxy.qqbrowser.cc/api/user/list/' + staff_id, function (json) {
        console.log(staff_id);
        console.log(json);
        console.log(json.data);
        for (var i = 0; i < json.data.data.length; i++) {
            var id = json.data.data[i].user_id;
            var staff_id = json.data.data[i].staff_id;
            var username = json.data.data[i].username;
            var mobile = json.data.data[i].mobile;
            var card_id = json.data.data[i].card_id;
            var class_id = json.data.data[i].class;
            var className;

            switch (class_id) {
                case 1:
                    className = "个人";
                    break;
                case 2:
                    className = "企业";
                    break;
            }
            $("#user_list tbody").append('<tr><td>' + username + '</td><td>' + mobile + '</td><td>' + card_id + '</td><td>' + className + '</td><td class="delete"><span>' + id + '</span><i class="iconfont">&#xe60d;</i></td><td class="edit"><i class="iconfont">&#xe60c;</i></td><td id="reserver_btn"><span>' + staff_id + '</span><button>发起预约</button></td></tr>');
        }

        $('.delete').click(function () {
            var $id = $(this).children('span').text();
            $.getJSON('http://zzo0jd72rt.proxy.qqbrowser.cc/api/user/del/' + $id, function (data) {
                console.log(data);
                if (data.code == 200 && data.message == "success") {
                    alert(data.data.status);
                } else {
                    alert("无法删除!");
                }
            });
            $(this).parent().remove();
        });

        $('.edit').click(function () {
            var $id = $(this).prev().children('span').text();
            $.ajax({
                url: 'users/edituser.html',
                type: 'post',
                dataType: 'html',
                success: function (data) {
                    $(".system_page .content").html(data);
                    uquery($id);
                }
            });
        });

        $('#reserver_btn button').click(function () {
            var $uid = $(this).parent().prev().prev().children("span").text();
            var $id = $(this).prev("span").text();
            console.log($uid);
            $.ajax({
                url: 'users/userreserves.html',
                type: 'post',
                dataType: 'html',
                success: function (data) {
                    $(".system_page .content").html(data);
                    sub_btn($uid, $id);
                }
            });
        });
    });
}

//TODO 完成
/*客户查询*/
function uquery(uid) {
    $.getJSON('http://zzo0jd72rt.proxy.qqbrowser.cc/api/user/sel/' + uid, function (json) {
        console.log(json);
        var id = json.data.id;
        var uname = json.data.username;
        var mobile = json.data.mobile;
        var card_id = json.data.card_id;
        var class_id = json.data.class;

        $('[name="username"]').val(uname);
        $('[name="mobile"]').val(mobile);
        $('[name="card_id"]').val(card_id);

        btn_usersave(id);
    });
}

//TODO 完成
/*发起预约提交*/
function sub_btn(uid, id) {
    $.getJSON('http://zzo0jd72rt.proxy.qqbrowser.cc/api/user/reserve/sel/' + id, function (json) {
        console.log(json);
        for (var i = 0; i < json.data.banks.length; i++) {
            var bank_id = json.data.banks[i];
            var bank;
            switch (bank_id) {
                case 1:
                    bank = "中国工商银行";
                    break;
                case 2:
                    bank = "中国农业银行";
                    break;
                case 3:
                    bank = "光大银行";
                    break;
                case 4:
                    bank = "建设银行";
                    break;
                case 5:
                    bank = "中国银行";
                    break;
                case 6:
                    bank = "招商银行";
                    break;
                case 7:
                    bank = "北京银行";
                    break;
                case 8:
                    bank = "兴业银行";
                    break;
                case 9:
                    bank = "交通银行";
                    break;
                case 10:
                    bank = "民生银行";
                    break;
                case 11:
                    bank = "平安银行";
                    break;
                case 12:
                    bank = "广发银行";
                    break;
                case 13:
                    bank = "邮政储蓄银行";
                    break;
                case 14:
                    bank = "渤海银行";
                    break;
                case 15:
                    bank = "北京农商银行";
                    break;
            }
            $("#pay_bank ul").append("<li data-int=" + bank_id + ">" + bank + "</li>");
        }
        for (var j = 0; j < json.data.timeslot.length; j++) {
            var started = json.data.timeslot[j].started;
            var ended = json.data.timeslot[j].ended;
            var time_id = json.data.timeslot[j].id;
            $("#reserve_date ul").append("<li data-int=" + time_id + ">" + started + "-" + ended + "</li>");
        }
        $("input[name='houses']").val(json.data.project_name);
        $("input[name='houses']").attr("readOnly", true);
        select_reserves();
    });
    $.getJSON('http://zzo0jd72rt.proxy.qqbrowser.cc/api/user/sel/' + id, function (json) {
        $("#btn_sub button").click(function () {
            var reserves_data = user_reserver_info(uid, id);
            console.log(reserves_data);
            $.post("http://zzo0jd72rt.proxy.qqbrowser.cc/api/user/reserve/add", reserves_data, function (data) {
                console.log(data);
                $.ajax({
                    url: 'users/userlist.html',
                    type: 'post',
                    dataType: 'html',
                    success: function (data) {
                        $(".system_page .content").html(data);
                    }
                });
            })
        });
    });
}

//TODO 完成
/*保存预约信息*/
function user_reserver_info(uid, id) {
    var houses = $("[name='houses']").val();
    var unit = $("[name='unit']").val();
    var number = $("[name='number']").val();
    var payfor = $("#payfor>p span").attr("data-int");
    var total_money = $("[name='total_money']").val();
    var first_money = $("[name='first_money']").val();
    var loan_money = $("[name='loan_money']").val();
    var pay_bank = $("#pay_bank>p span").attr("data-int");
    var reserve_date = $("[name='reserve_date']").val();
    var reserve_time = $("[name='reserve_time']").val();
    var timeslot = $("#reserve_date p span").attr("data-int");
    var discount = $("#discount>p span").attr("data-int");
    var pay_status = $("#pay_status>p span").attr("data-int");
    var sign_zip = $("#sign_zip>p span").attr("data-int");
    var reserve_class = $("#reserve_class>p span").attr("data-int");
    var special = $("#special>p span").attr("data-int");
    var notes = $("[name='notes']").val();

    var reserveinfo = {
        "user_id": uid,
        "staff_id": id,
        // "houses":houses,
        "unit": unit,
        "number": number,
        "payfor": payfor,
        "total_money": total_money,
        "first_money": first_money,
        "loan_money": loan_money,
        "pay_bank": pay_bank,
        "date": reserve_date,
        "timeslot_id": timeslot,
        "discount": discount,
        "pay_status": pay_status,
        "sign_zip": sign_zip,
        "reserve_class": reserve_class,
        "special": special,
        "notes": notes
    };
    return reserveinfo;
}

//TODO 完成
/*客户信息保存按钮*/
function btn_usersave(uid) {
    $('.u_save').click(function () {
        var username = $('[name="username"]').val();
        var mobile = $('[name="mobile"]').val();
        var card_id = $('[name="card_id"]').val();
        $.post('http://zzo0jd72rt.proxy.qqbrowser.cc/api/user/edit/' + uid, {
            username: username,
            mobile: mobile,
            card_id: card_id
        }, function (data) {
            var data = JSON.parse(data);
            if (data.code == 200) {
                alert(data.data.status);
                $.ajax({
                    url: 'users/userlist.html',
                    type: 'post',
                    dataType: 'html',
                    success: function (data) {
                        $(".system_page .content").html(data);
                    }
                });
            } else {
                alert("提交失败");
            }
        });
    });
}

/*预约管理*/
function reserves() {
    $.getJSON('http://zzo0jd72rt.proxy.qqbrowser.cc/api/reserve/list/' + staff_id, function (json) {
        console.log(json);
        for (var i = 0; i < json.data.data.length; i++) {
            var reserve_id = json.data.data[i].reserve_id;
            var username = json.data.data[i].username;
            var mobile = json.data.data[i].mobile;
            var date = json.data.data[i].date;
            var project = json.data.data[i].project;
            var unit = json.data.data[i].unit;
            var number = json.data.data[i].number;
            var reserve_etime = json.data.data[i].reserve_etime;
            var reserve_stime = json.data.data[i].reserve_stime;
            var status_id = json.data.data[i].status;
            var status;
            var progress_id = json.data.data[i].progress;
            var progress;

            switch (progress_id) {
                case 1:
                    progress = "已确认";
                    break;
                case 2:
                    progress = "已过号";
                    break;
                case 3:
                    progress = "等待办理中";
                    break;
                case 4:
                    progress = "已完成";
                    break;
            }

            switch (status_id) {
                case 1:
                    status = "已确认";
                    break;
                case 2:
                    status = "已取消";
                    break;
            }

            $("#reserves_list tbody").append("<tr><td>" + (i + 1) + "</td><td><a>" + username + "</a></td><td>" + mobile + "</td><td>" + date + "<br/>" + "(" + reserve_stime + "-" + reserve_etime + ")" + "</td><td>" + project + "</td><td>" + unit + "</td><td>" + number + "</td><td>" + progress + "</td><td>" + status + "</td><td class=edit><span>" + reserve_id + "</span><i class=iconfont>&#xe60c;</i></td><td id='info'><button>查看详情</button></td></tr>");
        }
        $(".edit").click(function () {
            var reserve_id = $(this).children("span").text();
            $.ajax({
                url: "reserves/editreserve.html",
                type: 'post',
                dataType: 'html',
                success: function (data) {
                    $(".system_page .content").html(data);
                    reserves_edit(reserve_id);
                }
            });
        });

        $("#info button").click(function () {
            var reserve_id = $(this).parent().prev().children("span").text();
            $.ajax({
                url: "reserves/managepage.html",
                type: 'post',
                dataType: 'html',
                success: function (data) {
                    $(".system_page .content").html(data);
                    reserves_edit1(reserve_id);
                    remove_reserve(reserve_id);
                    again_reserve(reserve_id);
                }
            });
        });
    });
}

/*预约编辑*/
function reserves_edit(reserve_id) {
    $.getJSON("http://zzo0jd72rt.proxy.qqbrowser.cc/api/reserve/sel/" + reserve_id, function (json) {
        console.log(json);
        console.log(reserve_id);
        var bank;
        for (var i = 0; i < json.data.banks.length; i++) {
            switch (json.data.banks[i]) {
                case 0:
                    bank = "请选择贷款银行";
                    break;
                case 1:
                    bank = "中国工商银行";
                    break;
                case 2:
                    bank = "中国农业银行";
                    break;
                case 3:
                    bank = "光大银行";
                    break;
                case 4:
                    bank = "建设银行";
                    break;
                case 5:
                    bank = "中国银行";
                    break;
                case 6:
                    bank = "招商银行";
                    break;
                case 7:
                    bank = "北京银行";
                    break;
                case 8:
                    bank = "兴业银行";
                    break;
                case 9:
                    bank = "交通银行";
                    break;
                case 10:
                    bank = "民生银行";
                    break;
                case 11:
                    bank = "平安银行";
                    break;
                case 12:
                    bank = "广发银行";
                    break;
                case 13:
                    bank = "邮政储蓄银行";
                    break;
                case 14:
                    bank = "渤海银行";
                    break;
                case 15:
                    bank = "北京农商银行";
                    break;
            }
            $("#pay_bank>ul").append("<li data-int=" + json.data.banks[i] + ">" + bank + "</li>");
        }
        receive_data(json);
        select_reserves();
        editreserve_btn(reserve_id);
    })
}

function reserves_edit1(reserve_id) {
    $.getJSON("http://zzo0jd72rt.proxy.qqbrowser.cc/api/reserve/sel/" + reserve_id, function (json) {
        console.log(json);
        console.log(reserve_id);
        var bank;
        for (var i = 0; i < json.data.banks.length; i++) {
            switch (json.data.banks[i]) {
                case 0:
                    bank = "请选择贷款银行";
                    break;
                case 1:
                    bank = "中国工商银行";
                    break;
                case 2:
                    bank = "中国农业银行";
                    break;
                case 3:
                    bank = "光大银行";
                    break;
                case 4:
                    bank = "建设银行";
                    break;
                case 5:
                    bank = "中国银行";
                    break;
                case 6:
                    bank = "招商银行";
                    break;
                case 7:
                    bank = "北京银行";
                    break;
                case 8:
                    bank = "兴业银行";
                    break;
                case 9:
                    bank = "交通银行";
                    break;
                case 10:
                    bank = "民生银行";
                    break;
                case 11:
                    bank = "平安银行";
                    break;
                case 12:
                    bank = "广发银行";
                    break;
                case 13:
                    bank = "邮政储蓄银行";
                    break;
                case 14:
                    bank = "渤海银行";
                    break;
                case 15:
                    bank = "北京农商银行";
                    break;
            }
            $("#pay_bank>ul").append("<li data-int=" + json.data.banks[i] + ">" + bank + "</li>");
        }
        receive_data(json);
        // editreserve_btn(reserve_id);
    })
}

/*修改按钮*/
function editreserve_btn(reserve_id) {
    $("#editreserve_btn button").click(function () {
        var reservedata={
            "payfor":$("#payfor>p span").attr("data-int"),
            "total_money":$("[name='total_money']").val(),
            "first_money":$("[name='first_money']").val(),
            "loan_money":$("[name='loan_money']").val(),
            "pay_bank":$("#pay_bank>p span").attr("data-int"),
            "discount":$("#discount>p span").attr("data-int"),
            "pay_status":$("#pay_status>p span").attr("data-int"),
            "sign_zip":$("#sign_zip>p span").attr("data-int"),
            "reserve_class":$("#reserve_class>p span").attr("data-int"),
            "special":$("[name='special']").val(),
            "notes":$("[name='notes']").val()
        }
        console.log(reservedata);
        $.post("http://zzo0jd72rt.proxy.qqbrowser.cc/api/reserve/edit/"+reserve_id,reservedata,function (json) {
            console.log(json);
            var json = JSON.parse(json);
            if (json.code == 200) {
                alert("修改成功!");
                $.ajax({
                    url: 'reserves/reserves.html',
                    type: 'post',
                    dataType: 'html',
                    success: function (data) {
                        $(".system_page .content").html(data);
                    }
                });
            } else {
                alert("修改失败!");
            }
        });
    });
}

/*开始办理按钮*/
function manage(id) {
    $.getJSON("http://zzo0jd72rt.proxy.qqbrowser.cc/api/reserve/sel/" + id, function (json) {
        console.log(json);

    });
}

/*接收预约信息*/
function receive_data(json) {
    var date = json.data.data.date;
    var discount_id = json.data.data.discount;
    var discount;
    var first_money = json.data.data.first_money;
    var loan_money = json.data.data.loan_money;
    var mobile = json.data.data.mobile;
    var notes = json.data.data.notes;
    var number = json.data.data.number;
    var pay_bank_id = json.data.data.pay_bank;
    var pay_bank;
    var pay_status_id = json.data.data.pay_status;
    var pay_status;
    var payfor_id = json.data.data.payfor;
    var progress = json.data.data.progress;
    var project = json.data.data.project;
    var reserve_class_id = json.data.data.reserve_class;
    var reserve_class;
    var reserve_class_notes = json.data.data.reserve_class_notes;
    var reserve_etime = json.data.data.reserve_etime;
    var reserve_stime = json.data.data.reserve_stime;
    var sign_zip_id = json.data.data.sign_zip;
    var sign_zip;
    var special = json.data.data.special;
    var status = json.data.data.status;
    var total_money = json.data.data.total_money;
    var unit = json.data.data.unit;
    var user_id = json.data.data.user_id;
    var username = json.data.data.username;
    var payfor;

    switch (payfor_id) {
        case 1:
            payfor = "一次性付款";
            break;
        case 2:
            payfor = "银行按揭";
            break;
        case 3:
            payfor = "公积金";
            break;
        case 4:
            payfor = "组合贷";
            break;
    }
    switch (reserve_class_id) {
        case 1:
            reserve_class = "草签";
            break;
        case 2:
            reserve_class = "正签";
            break;
        case 3:
            reserve_class = "其他";
            break;
    }
    switch (discount_id) {
        case 1:
            discount = "已完成";
            break;
        case 0:
            discount = "未完成";
            break;
    }
    switch (pay_status_id) {
        case 1:
            pay_status = "已完成";
            break;
        case 0:
            pay_status = "未完成";
            break;
    }
    switch (sign_zip_id) {
        case 1:
            sign_zip = "已完成";
            break;
        case 0:
            sign_zip = "未完成";
            break;
    }
    switch (pay_bank_id) {
        case 0:
            pay_bank = "请选择贷款银行";
            break;
        case 1:
            pay_bank = "中国工商银行";
            break;
        case 2:
            pay_bank = "中国农业银行";
            break;
        case 3:
            pay_bank = "光大银行";
            break;
        case 4:
            pay_bank = "建设银行";
            break;
        case 5:
            pay_bank = "中国银行";
            break;
        case 6:
            pay_bank = "招商银行";
            break;
        case 7:
            pay_bank = "北京银行";
            break;
        case 8:
            pay_bank = "兴业银行";
            break;
        case 9:
            pay_bank = "交通银行";
            break;
        case 10:
            pay_bank = "民生银行";
            break;
        case 11:
            pay_bank = "平安银行";
            break;
        case 12:
            pay_bank = "广发银行";
            break;
        case 13:
            pay_bank = "邮政储蓄银行";
            break;
        case 14:
            pay_bank = "渤海银行";
            break;
        case 15:
            pay_bank = "北京农商银行";
            break;
    }

    $("[name='username']").val(username);
    $("[name='username']").attr("readOnly", true);
    $("[name='mobile']").val(mobile);
    $("[name='mobile']").attr("readOnly", true);
    $("[name='date']").val(date);
    $("[name='date']").attr("readOnly", true);
    $("[name='reserve']").val(reserve_stime + "-" + reserve_etime);
    $("[name='reserve']").attr("readOnly", true);
    $("[name='houses']").val(project);
    $("[name='houses']").attr("readOnly", true);
    $("[name='unit']").val(unit);
    $("[name='number']").val(number);
    $("[name='unit']").attr("readOnly", true);
    $("[name='number']").attr("readOnly", true);
    $("#payfor>p span").html(payfor);
    $("#payfor>p span").attr("data-int", payfor_id);
    $("[name='total_money']").val(total_money);
    $("[name='first_money']").val(first_money);
    $("[name='loan_money']").val(loan_money);
    $("#pay_bank>p span").html(pay_bank);
    $("#pay_bank>p span").attr("data-int", pay_bank);
    $("#discount>p span").html(discount);
    $("#discount>p span").attr(discount_id);
    $("#pay_status>p span").html(pay_status);
    $("#pay_status>p span").attr(pay_status_id);
    $("#sign_zip>p span").html(sign_zip);
    $("#sign_zip>p span").attr(sign_zip_id);
    $("#reserve_class>p span").html(reserve_class);
    $("#reserve_class>p span").attr(reserve_class_id);
    $("[name='special']").val(special);
    $("[name='notes']").val(notes);
}

/*取消预约*/
function remove_reserve(reserve_id) {
    $("#remove_reserve").click(function () {
        $.getJSON("http://zzo0jd72rt.proxy.qqbrowser.cc/api/reserve/remove/"+reserve_id,function (data) {
            $.ajax({
                url: 'reserves/reserves.html',
                type: 'post',
                dataType: 'html',
                success: function (data) {
                    $(".system_page .content").html(data);
                }
            });
        });
    });
}

/*重新预约*/
function again_reserve(reserve_id) {
    $("#again").click(function () {
        $.ajax({
            url: 'reserves/again.html',
            type: 'post',
            dataType: 'html',
            success: function (data) {
                $(".system_page .content").html(data);
                $.getJSON("http://zzo0jd72rt.proxy.qqbrowser.cc/api/reserve/restartsel/"+reserve_id,function (data) {
                    console.log(data);
                    for(var i=0;i<data.data.timeslot.length;i++){
                        var started=data.data.timeslot[i].started;
                        var ended=data.data.timeslot[i].ended;
                        var id=data.data.timeslot[i].id;

                        $("#reserve_date>ul").append("<li data-int="+id+">"+started+"-"+ended+"</li>");
                    }
                    select_again();
                    btn_again(reserve_id);
                });
            }
        });
    });
}

function select_again() {
    $('.add_form>ul>li p').click(function () {
        if ($(this).next().is(":visible")) {
            $(this).next().hide();
        } else {
            $(this).next().show();
        }
    });
    $('.add_form>ul>li>ul>li').click(function () {
        var $text = $(this).text();
        var $data_int = $(this).attr('data-int');
        $('.add_form>ul>li>ul').not(".checkbox_style").hide();
        $('.add_form>ul>li>p>span').html($text);
        $('.add_form>ul>li>p>span').attr("data-int", $data_int);
    });
}

function btn_again(reserve_id) {
    $("#btn_again button").click(function () {
        var date=$(".fqyy>ul>li [name='reserve_date']").val();
        var timeslot_id=$("#reserve_date>p>span").attr("data-int");
        console.log(timeslot_id);
        console.log(date);
        console.log(reserve_id);
        $.post("http://zzo0jd72rt.proxy.qqbrowser.cc/api/reserve/restart",{date:date,timeslot_id:timeslot_id,id:reserve_id},function (json) {
            console.log(json);
            $.ajax({
                url: 'reserves/reserves.html',
                type: 'post',
                dataType: 'html',
                success: function (data) {
                    $(".system_page .content").html(data);
                }
            });
        });
    });
}

$("#exit").click(function () {
    window.location.href ="../index.html";
});

function receive_data1() {
    var date;
    var discount;
    var first_money;
    var loan_money;
    var mobile;
    var notes;
    var number;
    var pay_bank;
    var pay_status;
    var payfor;
    var progress;
    var project;
    var reserve_class;
    var reserve_class_notes;
    var reserve_etime;
    var reserve_stime;
    var sign_zip;
    var special;
    var status;
    var total_money;
    var unit;
    var user_id;
    var username;

}