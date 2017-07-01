// Add some self-start things...
$(function () {
    $(window).scroll(gotopanywhere);
    $('input').iCheck({
        checkboxClass: 'icheckbox_flat-red',
        radioClass: 'iradio_flat-red'
    });
    $(".form-input").on("focus",function(){
        $(this).next().addClass("form-input-on");
    }).on("blur",function(){
        if(!$(this).val()) {
            $(this).next().removeClass("form-input-on");
        }
    });
});

function shake(filter, intShakes, intDistance, intDuration) {
    elem = $(filter);
    for (var x = 1; x <= intShakes; x++) {
        elem.animate({
            left: (intDistance * -1)
        }, (((intDuration / intShakes) / 4))).animate({
            left: intDistance
        }, ((intDuration / intShakes) / 2)).animate({
            left: 0
        }, (((intDuration / intShakes) / 4)));
    }
}

function register() {

}

// User...
function login() {
    email = $("#ajax-email").val();
    password = $("#ajax-password").val();
    $.ajax({
        url: '/user',
        data: {
            email: email,
            password: password,
            forgetmenot: remember
        },
        type: 'post',
        cache: false,
        success: function (data) {
            if (data.auth) {
                side_avatar = $("#side-avatar");
                side_avatar.attr("src", data.avatar);
                side_avatar.attr("alt", "Avatar");
                $("#side-username").text("Hi, " + data.nickname);
                $("#side-userinfo").html('<a href="/logout"><span class="glyphicon glyphicon-log-out" aria-hidden="true"></span></a>');
            } else {
                //shake shake-horizontal shake-constant
            }
        },
        error: function () {
            alert("与服务器通信出现错误，请刷新页面重试。");
        }
    });
}

var query_data_now = null;
var querying = false;
var query_skip = 0;
function query() {
    $("#info").hide();
    $("#query-result-panel").html("");
    $(".progress-bar").css("width", "0").text("").show().first().css('min-width', '0.764em;').
            next().css('min-width', '1.236em').
            next().css('min-width', '2em').text("0%").css("float", "left");
    var query_str = $("#search").val();
    $("#query-str").text(query_str);
    $("#hits-count").text("0");
    query_skip = 0;
    query_data_now = {query_str: query_str, skip: 0};
    if (querying) {
        return;
    }
    query_ajax(query_data_now);
    open_result();
}
function query_ajax(query_data) {
    querying = true;
    query_data.skip = query_skip;
    query_post(query_data,
        function (data) {

            console.log(data);

            var status = data.status;
            if (status < 0) {
                fail_query(data.info);
                return;
            } else if (status == 0) {
                setTimeout("query_ajax(query_data_now)", 200);
                return;
            }
            var total = data.total;
            var done = data.done;
            var base_percent = done / total * 100;
            var progress2 = base_percent * 0.5;
            var progress1 = progress2 * 0.618;
            var progress0 = base_percent - progress2 - progress1;
            var progress_bar = $(".progress-bar").first();
            progress_bar.css('width', progress0 + "%").
                    next().css('width', progress1 + "%").
                    next().css('width', progress2 + "%").
                            text(Math.ceil(base_percent) + "%");
            $("#hits-count").text(data.hits);
            query_skip = done;
            var lib_count = data.results.length, i;
            for (i = 0; i < lib_count; ++i) {
                show_result(data.results[i]);
            }
            if (done == total) {
                setTimeout("finish_query("+data.run+")", 1000);
            } else {
                setTimeout("query_ajax(query_data_now)", 500);
            }
        },
        function (data) {
            show_info('danger', '服务器故障！请稍后再试。');
            fail_query();
        });
}
function show_result(result) {

    console.log(result);

    if (result.hits_count < 1) {
        return;
    }

    var start = (new Date()).valueOf();

    var output = [];
    output.push('\
    <div class="panel panel-default">\
        <div class="panel-heading">\
            <span class="glyphicon glyphicon-file"></span>\
            <strong>');
    output.push(result.name);
    output.push('</strong>\
            <span class="badge">');
    output.push(result.hits_count);
    output.push('</span>\
        </div>\
        <div class="table-responsive">\
            <table class="table table-bordered table-hover">\
            <thead>\
            <tr>');
    var fields_count, i, j;
    fields_count = result.fields.length;
    for (i = 0; i < fields_count; ++i) {
        output.push('<th>' + result.fields[i] + '</th>');
    }
    output.push('\
        </tr>\
        </thead>\
        <tbody>');
    for (i = 0; i < result.hits_count; ++i) {
        output.push('<tr>');
        for (j = 0; j < fields_count; ++j) {
            output.push('<td>'+result.hits[i][result.fields[j]]+'</td>');
        }
        output.push('</tr>');
    }
    output.push('\
        </tbody>\
    </table>\
    </div>\
    </div>');

    var end1 = (new Date()).valueOf();
    console.log("Step 1: "+(end1-start)+"ms");

    $("#query-result-panel").append(output.join(''));

    var end2 = (new Date()).valueOf();
    console.log("Step 2: "+(end2 - end1)+" ms");
}
function finish_query(time) {
    querying = false;
    console.log(querying);
    var time_f = Number(time).toFixed(3);
    var progress_bar = $(".progress-bar").first();
    progress_bar.css('width', "0").fadeOut().
        next().css('width', "0").fadeOut().
        next().css('width', "100%").css('float', "right").text("Success in "+time_f+" ms");
}
function fail_query(error_info) {
    querying = false;
    switch (error_info) {
        case 'failed':
        case 'server':
            show_info('danger', '服务器故障！请稍后再试。');
            break;
        case 'format':
            show_info('danger', '查询字符串格式错误！');
            break;
        default:
            show_info('danger', '未知错误！请稍后再试。');
            break;
    }
    var progress_bar = $(".progress-bar").first();
    progress_bar.css('width', "100%").text("Failed!").
        next().css('width', "0").fadeOut().
        next().css('width', "0").fadeOut().text("");
}
function query_post(data, success, error) {
    $.ajax({
        url: '/query',
        data: data,
        type: 'post',
        cache: false,
        success: success,
        error: error
    });
}
function show_info(type, msg) {
    $("#info").text(msg).addClass('alert-'+type).show();
}
function open_result() {
    $("#query-result").show();
}

// Go top
var goingtop = false;
function gotop() {
    goingtop = true;
    $("html,body").animate(
        {scrollTop: "0px"},
        200,
        function(){
            goingtop = false;
            gotopanywhere();
        }
    );
}
function gotopanywhere() {
    if (goingtop) {
        return;
    }
    var scrollt = $("body").scrollTop();
    if (scrollt > 200) {
        $(".gotop").addClass("gotop-show");
    } else if (scrollt < 200) {
        $(".gotop").removeClass("gotop-show");
    }
}

// Rotate a card
function toggleCard(frontID, backID) {
    var front = $('#' + frontID);
    var back = $('#' + backID);
    if (front.hasClass('front-hidden')) {
        back.addClass('back-hidden');
        setTimeout("$('#" + backID + "').hide()", 400);
        setTimeout("$('#" + frontID + "').show()", 400);
        setTimeout("$('#" + frontID + "').removeClass('front-hidden')", 450);
    } else {
        front.addClass('front-hidden');
        setTimeout("$('#" + frontID + "').hide()", 400);
        setTimeout("$('#" + backID + "').show()", 400);
        setTimeout("$('#" + backID + "').removeClass('back-hidden')", 450);
    }
}
