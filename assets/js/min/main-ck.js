function notify(t,i){i="undefined"!=typeof i?i:"success";var a=$(".notification");switch(a.show(),a.css("top","-"+a.outerHeight(!0)+"px"),a.attr("class","notification "+i),a.css("left",Math.max(0,($(window).width()-a.outerWidth())/2+$(window).scrollLeft())+"px"),i){case"success":icon="check";break;case"error":icon="exclamation";break;default:icon="question"}$(".notification").find("i").attr("class","fa fa-"+icon),$(".notification").find("span").html(t),a.animate({top:"0"},500),a.delay(2e3).fadeOut(1e3)}$(document).ready(function(){$(".pages li").each(function(t,i){var a=baseURL+$(this).find("a").attr("href");document.location==a&&$(this).addClass("active")})});