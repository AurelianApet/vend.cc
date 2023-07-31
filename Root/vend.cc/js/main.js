
var ready = true;
function checkAll( id, name )
{
    var check = false;
    if($('#' + id).is(':checked')) {
        check = true;
    }
   $('input[name="'+name+'"]').prop("checked", check);
}
function confirmDeleteInvalid()
{
	if(confirm("This will remove all invalid cards."))
	{
		return true;
	}
	return false;
}
function checkCard(card_id) {
	if (ready) {
	$.ajax({
	type: "GET",
	url: './checker.php?card_id='+card_id,
	beforeSend: function(){
	ready = false;
	$("#check_"+card_id).html("<img src=\"./images/loading.gif\" height=\"30px\" width=\"30px\" />");
	},
	success: function(msg){
	ready = true;
	$("#check_"+card_id).html(msg).show("slow");
	},
	error: function(msg){
	ready = true;
	$("#check_"+card_id).html("<span class=\"error\">Loading error.</span>");
	}
	});
	} else {
		alert('Please wait until current checking complete.');
	}
}
function checkDump(dump_id) {
	if (ready) {
	$.ajax({
	type: "GET",
	url: './checker.php?dump_id='+dump_id,
	beforeSend: function(){
	ready = false;
	$("#check_"+dump_id).html("<img src=\"./images/loading.gif\" height=\"30px\" width=\"30px\" />");
	},
	success: function(msg){
	ready = true;
	$("#check_"+dump_id).html(msg).show("slow");
	},
	error: function(msg){
	ready = true;
	$("#check_"+dump_id).html("<span class=\"error\">Loading error.</span>");
	}
	});
	} else {
		alert('Please wait until current checking complete.');
	}
}
function change_city_select_mode(auto) {
	switch ($("[name='city_select_mode']").val()) {
		case '0':
			$("#card_city").removeClass('bold');
			$("#card_state").removeClass('bold');
			$("#card_zip").removeClass('bold');
			if (auto == true) {
				$("[name='card_city']").removeAttr("readonly");
				$("[name='card_state']").removeAttr("readonly");
				$("[name='card_zip']").removeAttr("readonly");
			} else {
				$("[name='card_city']").val('').removeAttr("readonly");
				$("[name='card_state']").val('').removeAttr("readonly");
				$("[name='card_zip']").val('').removeAttr("readonly");
			}
			break;
		case '1':
			$("#card_city").removeClass('bold');
			$("#card_state").removeClass('bold');
			$("#card_zip").addClass('bold');
			$("[name='card_city']").val('AUTO BY ZIP').attr("readonly", true);
			$("[name='card_state']").val('AUTO BY ZIP').attr("readonly", true);
			if (auto == true) {
				$("[name='card_zip']").removeAttr("readonly");
			} else {
				$("[name='card_zip']").val('').removeAttr("readonly");
			}
			break;
		case '2':
			$("#card_city").addClass('bold');
			$("#card_state").addClass('bold');
			$("#card_zip").removeClass('bold');
			if (auto == true) {
				$("[name='card_city']").removeAttr("readonly");
				$("[name='card_state']").removeAttr("readonly");
			} else {
				$("[name='card_city']").val('').removeAttr("readonly");
				$("[name='card_state']").val('').removeAttr("readonly");
			}
			$("[name='card_zip']").val('AUTO BY CITY').attr("readonly", true);
			break;
	}
	return false;
}
function change_country_select_mode(auto)
{
	switch ($("[name='country_select_mode']").val()) {
		case '0':
			//$("[name='card_country']").parent().html('<input name="card_country" type="text" size="11" value="" readonly="">');
			$("[name='card_country']").val('AUTO BY BIN').attr("readonly", true);
			break;
		case '1':
			if (auto == true) {
				$("[name='card_country']").removeAttr("readonly");
			} else {
				$("[name='card_country']").val('').removeAttr("readonly");
				//$("[name='card_country']").parent().html(select_country);
			}
			break;
	}
	return false;
}
function strip_off_string(str)
{
    str = str.toLowerCase();
    return str.replace(/[^a-z0-9]/g, "")
}


function unhide(id)
{
	if (confirm("You will lose credits. Are you sure ?"))
    {
		if (ready) {
		$("#i"+id+'ssn').html("<img src=\"images/loading.gif\" height=\"15px\" width=\"15px\" />");
		$("#i"+id+'dob').html("<img src=\"images/loading.gif\" height=\"15px\" width=\"15px\" />");
		$.ajax({
		type: "GET",
		url: './ssndobsearcher.php?id='+id,
		beforeSend: function(){
			ready = false;
			$("#i"+id+'ssn').html("<img src=\"images/loading.gif\" height=\"15px\" width=\"15px\" />");
			$("#i"+id+'dob').html("<img src=\"images/loading.gif\" height=\"15px\" width=\"15px\" />");
		},
		success: function(msg){
			ready = true;
			var tmp = msg.split("|");
			if (tmp.length==2){
			
			$("#i"+id+'ssn').html(tmp[0]).show("slow");
			$("#i"+id+'dob').html(tmp[1]).show("slow");
			}
			else{
			$("#i"+id+'ssn').html(msg).show("slow");
			$("#i"+id+'dob').html(msg).show("slow");}
		},
		error: function(msg){
			ready = true;
			$("#i"+id+'ssn').html("<span class=\"red\">Error</span>");
			$("#i"+id+'dob').html("<span class=\"red\">Error</span>");
			}
		});
		} else {
			alert('Please wait until current job complete.');
		}
    }
}
/*
function unhide(id,param)
{
    if (confirm("You will lose credits. Are you sure ?"))
    {
		if (ready) {
		$.ajax({
		type: "GET",
		url: './ssndobsearcher.php?id='+id+'&param='+param,
		beforeSend: function(){
		ready = false;
		$("#"+strip_off_string("item"+id+param)).html("<img src=\"./images/loading.gif\" height=\"15px\" width=\"15px\" />");
		},
		success: function(msg){
		ready = true;
		$("#"+strip_off_string("item"+id+param)).html(msg).show("slow");
		},
		error: function(msg){
		ready = true;
		$("#"+strip_off_string("item"+id+param)).html("<span class=\"red\">Loading error.</span>");
		}
		});
		} else {
			alert('Please wait until current job complete.');
		}
    }
}
*/
function ukdob(id)
{
    if (confirm("You will lose credits. Are you sure ?"))
    {
		if (ready) {
		$.ajax({
		type: "GET",
		url: './ukdobsearcher.php?id='+id,
		beforeSend: function(){
		ready = false;
		$("#"+strip_off_string("item"+id)).html("<img src=\"./images/loading.gif\" height=\"15px\" width=\"15px\" />");
		},
		success: function(msg){
		ready = true;
		$("#"+strip_off_string("item"+id)).html(msg).show("slow");
		},
		error: function(msg){
		ready = true;
		$("#"+strip_off_string("item"+id)).html("<span class=\"red\">Loading error.</span>");
		}
		});
		} else {
			alert('Please wait until current job complete.');
		}
    }
}

(function($) {
    "use strict";

    $.fn.tree = function() {

        return this.each(function() {
            var btn = $(this).children("a").first();
            var menu = $(this).children(".treeview-menu").first();
            //var isActive = $(this).hasClass('active');

            //initialize already active menus
            /*if (isActive) {
                menu.show();
                btn.children(".fa-angle-left").first().removeClass("fa-angle-left").addClass("fa-angle-down");
            }*/
            //Slide open or close the menu on link click
            btn.click(function(e) {
            	var isActive = btn.parent().hasClass('active');
            	console.log(isActive);
                e.preventDefault();
                if (isActive) {
                	console.log("here");
                    //Slide up to close menu
                    menu.slideUp();
                    btn.children(".fa-angle-down").first().removeClass("fa-angle-down").addClass("fa-angle-left");
                    btn.parent("li").removeClass("active");
                } else {
                	console.log("or here");
                    //Slide down to open menu
                    $(".treeview-menu").slideUp();
                    menu.slideDown();
                    
                    $(".treeview a").children(".fa-angle-down").first().removeClass("fa-angle-down").addClass("fa-angle-left");
                    $(".treeview a").parent("li").removeClass("active");
                    btn.children(".fa-angle-left").first().removeClass("fa-angle-left").addClass("fa-angle-down");
                    btn.parent("li").addClass("active");
                }
            });

            /* Add margins to submenu elements to give it a tree look */
            menu.find("li > a").each(function() {
                var pad = parseInt($(this).css("margin-left")) + 10;

                $(this).css({"margin-left": pad + "px"});
            });

        });

    };
		
}(jQuery));


$(document).ready(function() {
	change_city_select_mode(true);
	change_country_select_mode(true);
	$('.viewcard').popupWindow({height:300, width:900, });
	
	/*Added by SM */
	$('.sidebar-menu .treeview').tree();
	
	$('.left-toggler').click(function(){
			if ($('.sidebar-offcanvas').hasClass('collapse-left')) {
					$('.sidebar-offcanvas').removeClass('collapse-left');
					$('.right-side').removeClass('strech');
			} else {
					$('.sidebar-offcanvas').addClass('collapse-left');
					$('.right-side').addClass('strech');
			}
	});
	
});