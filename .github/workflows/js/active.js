'use strict';

$(function() {
    var url = window.location.href.substr(window.location.href.lastIndexOf("/")+1);
    $("#headerNav ul li a").each(function(){
        if($(this).attr("href") == url || $(this).attr("href") == '')
            $(this).addClass("active");
    });
    $(".ipNav").each(function(){
        if($(this).attr("href") == url || $(this).attr("href") == '')
            $('#ipNav').addClass("active");
    });
    $(".apNav").each(function(){
        if($(this).attr("href") == url || $(this).attr("href") == '')
            $('#apNav').addClass("active");
    });
});