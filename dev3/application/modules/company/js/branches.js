$(function () {
	/* Code to show the target if the url have #target appended. */
	if("#target"==window.location.hash) {
		$("#showiftarget a").click();
	}
		
	/* Code for the Data Table of logs table. */ 
	$('.log-list-table tfoot th, .branch-list-table tfoot th').each( function () {
        var title = $(this).text();
        $(this).html( '<input type="text" style="width:100%" placeholder="Search '+title+'" />' );
    } );
	
	/* Code for the Data Table of logs table. */ 
	var table = $(".log-list-table, .branch-list-table").DataTable({"order": [[ 0, "desc" ]],
		dom: 'Bfrtip',
		buttons: [
			'copy', 'csv', 'excel', 'pdf', 'print'
		]
	});
	
	/* Code for applying the search. */  
    table.columns().every( function () {
        var that = this;
        $( 'input', this.footer() ).on( 'keyup change', function () {
            if ( that.search() !== this.value ) {
                that
                    .search( this.value )
                    .draw();
            }
        } );
    } );
	
	/* Code for the Data Table of users table. */ 
	$('.users-list-table tfoot th').each( function () {
        var title = $(this).text();
        $(this).html( '<input type="text" style="width:100%" placeholder="Search '+title+'" />' );
    } );
	
	/* Code for the Data Table of logs table. */ 
//	var table = $(".users-list-table").DataTable({"order": [[ 2, "asc" ], [ 1, "asc" ]]});
				
	var table = $(".users-list-table").DataTable({"order": [[ 2, "asc" ], [ 1, "asc" ]],
				dom: 'Bfrtip',
        buttons: [
            'copy', 'csv', 'excel', 'pdf', 'print'
      ]});
	
	/* Code for applying the search. */  
    table.columns().every( function () {
        var that = this;
        $( 'input', this.footer() ).on( 'keyup change', function () {
            if ( that.search() !== this.value ) {
                that
                    .search( this.value )
                    .draw();
            }
        } );
    } );
		
		$("#branch").blur(function(){
			var branchid = $(this).val();
			check_unique_branch_id(branchid);			
		});
});

/* function to show the edit boxes */
function showEditBox(obj, yearmonth) {
	if(isAdmin || id==loggedinuserid) {
		var text = obj.text();
		obj.addClass("hidden");
		obj.next().removeClass("hidden");
		if(yearmonth) {
			year = obj.text().substr(0, 4);
			month = obj.text().substr(4, 2);
			obj.next().find('input.year').val(year);
			obj.next().find('input.month').val(month);
			obj.next().find('input.year').focus();
		} else {
			txt = obj.text();
			obj.next().find('input').val(txt);
			obj.next().find('input').focus();
		}
	}
}

/* Function to show the edit textbox on clicking the texts */	
$(".yearmonth_div, .salestarget_div, .marginok_div, .margingood_div").on('click', function(){
	showEditBox($(this), true);	
});

/* Global variable to store regular expression for non-blank */
var nonBlankRegexp = /[\S]+/;

/* Function to in-line-update the values */
function update(rawobj, id, yearmonth) {
	if(yearmonth) {
		var year = get_year(rawobj);
		var month = get_month(rawobj);
		var data = {year:year, month:month, yearmonth:true, id:id};
	} else {
		var value = get_value(rawobj);
		var fieldname = get_fieldname(rawobj);
		var data = {value:value, fieldname:fieldname, yearmonth:false, id:id};
		
		if(fieldname=="marginok" || fieldname=="margingood") {
			var marginokval = parseFloat($("#row_"+id).find("input.marginok").val());
			var margingoodval = parseFloat($("#row_"+id).find("input.margingood").val());
		
			if(margingoodval<marginokval) {
				alert("Margin ok can not be more than margin good!");
				return false;
			}
		}
	}
	
	save(data, rawobj);
}

/* Function to get Year */
function get_year(rawobj) {
	var parent = $(rawobj).parent();
	var year = parent.find('input.year').val();
	return year;
}

/* Function to get Month */
function get_month(rawobj) {
	var parent = $(rawobj).parent();
	var month = parent.find('input.month').val();
	return month;
}

/* Function to get value */
function get_value(rawobj) {
	var parent = $(rawobj).parent();
	var value = parent.find('input').val();
	return value;
}

/* Function to get field name */
function get_fieldname(rawobj) {
	var parent = $(rawobj).parent();
	var name = parent.find('input').attr('name');
	return name;
}

/* Function to call ajax and save the values */
function save(data, rawobj) {
	$.ajax({
		url: base_url+'branches/inlineupdate/', 
		data: data,
		type : 'POST',
		dataType : 'json',
		success: function(result){
			var type = '';
			if(data.yearmonth) {
				type = 'yearmonth';
			}
			
			if(result.success) {				
				closeE(rawobj, type, true);
			}	else {
				closeE(rawobj, type, false);
			}	
		}
	});
}

/* Function to open the form for adding a target */
function openAddTargetForm() {
	$(".overlay").fadeIn('fast', function() {
		$(".hidden-add-target-form").show();
	});
}

/* Function to close the form for adding a target */
function closeAddTargetForm() {
	$(".hidden-add-target-form").fadeOut('fast', function() {
		$(".overlay").hide();
	});
}

/* Function to close the editing of Target, year/month and target value */
function closeE(rawobj, type, saved) {	
	var obj = $(rawobj); 
	var objParent = obj.parent();
	var link_text = '';
	objParent.prev().removeClass("hidden");
	if("yearmonth"!=type) {
		if(!saved) {
			objParent.find('input').val(objParent.prev().text());
		} else {
			objParent.prev().text(objParent.find('input').val());
		}
	} else {
		if(!saved) {
			year = objParent.prev().text().substr(0, 4);
			month = objParent.prev().text().substr(4, 2);
			objParent.find('input.year').val(year);
			objParent.find('input.month').val(month);
		} else {
			objParent.prev().text(objParent.find('input.year').val()+objParent.find('input.month').val());
		}
	}
	objParent.addClass("hidden");
}


/* Function to check if the Year/Month values are valid */
function validateYearMonth(year, month) {
	var yearRegExp = /[\d]{4}/;
	var monthRegExp = /[\d]{1,2}/;
	var msg = "";
	var valid = true;
	if(!yearRegExp.test(year)) {
		msg += "Year is not valid\n";
		valid = false;
	}
	
	if(!monthRegExp.test(month)) {
		msg += "Month is not valid\n";
		valid = false;
	} else {
		if(month<=0 || month>12) {
			msg += "Please select a month between 1 to 12\n";
			valid = false;
		}
	}
	
	if(""!=msg) {
		alert(msg);
	}	
	return valid;
}

/* Function to check if the Sales target value is valid */
function validateSalesTarget(salestarget) {
	var salestargetRegExp = /[\d]+/;
	var msg = "";
	var valid = true;
	if(!salestargetRegExp.test(salestarget)) {
		msg += "Please enter a valid sales target\n";
		valid = false;
	}	
	if(""!=msg) {
		alert(msg);
	}	
	return valid;
}

/* Remove the error message if the email field is focused. */
$("#email").on("focus", function() {
	$("#email_err").remove(); 
});

/* Function to delete a target */
function deletetarget(id) {
	var confirmed = confirm('Are you sure to delete this target?');
	if(confirmed) {
		$.ajax({
			method: "DELETE",
			url: base_url+"branches/deletetarget/"+id,
			async : true
		}).done(function( response ) {
			if(response.deleteresult) {	
				alertHtml = '<div class="alert alert-danger">Target record deleted successfully!</div>';
				$("tr#row_"+id+" > td").removeClass("branch-link");
				$("tr#row_"+id+" > td").addClass("italicize");
				$("tr#row_"+id+" > td").text("deleted");
				$('.alert').remove();
				$("#alertmsg").html(alertHtml);
			}
		});
	}
}

/* for branch */
/* function to show the edit textbox on clicking the surname */	
$(".ulink").on('click', function(){
	var id = $(this).attr('for');
	if(isAdmin || id==loggedinuserid) {
		var text = $(this).text();
		$(this).addClass("hidden");
		$("#hidden_"+id).removeClass("hidden");
		$("#surname_"+id).val(text);
		$("#surname_"+id).focus();
	}
});

/* function to show the edit textbox on clicking the firstname */
$(".flink").on('click', function(){
	var id = $(this).attr('for');
	if(isAdmin || id==loggedinuserid) {
		var text = $(this).text();
		$(this).addClass("hidden");
		$("#fhidden_"+id).removeClass("hidden");
		$("#firstname_"+id).val(text);
		$("#firstname_"+id).focus();
	}
});

/* function to show the edit textbox on clicking the email */
$(".elink").on('click', function(){
	var id = $(this).attr('for');
	if(isAdmin || id==loggedinuserid) {
		var text = $(this).text();
		$(this).addClass("hidden");
		$("#ehidden_"+id).removeClass("hidden");
		$("#email_"+id).val(text);
		$("#email_"+id).focus();
	}
});

/* Function to close the editing of Target, year/month and target value */
function closeediting(id, type, saved) {	
	var ulink_text = '';
	if("target"==type) {
		$("#fhidden_"+id).addClass("hidden");
		if(!saved) {
			$("#salestarget_"+id).val($("#flink_"+id).text());
		}			
		$("#flink_"+id).removeClass("hidden");
	} else if("email"==type) {		
		$("#ehidden_"+id).addClass("hidden");
		if(!saved) {
			elink_text = $("#elink_"+id).text();				
			$("#email_"+id).val(elink_text);
		}
		$("#elink_"+id).removeClass("hidden");
	} else {
		$("#hidden_"+id).addClass("hidden");
		if(!saved) {
			ulink_text = $("#ulink_"+id).text();
			year = ulink_text.substr(0, 4);
			month = ulink_text.substr(4, 2);
			$("#year_"+id).val(year);
			$("#month_"+id).val(month);
		}
		$("#ulink_"+id).removeClass("hidden");
	}
}

/* Function to change the branch name */
function changeBranchName(id) {
	var name = $("#name_"+id).val();
	if(!nonBlankRegexp.test(name)) {
		alert("The Branch name is mandatory!");
		$("#hidden_"+id).addClass("hidden");
		$("#ulink_"+id).removeClass("hidden");
		return false;
	}
	$("#hidden_"+id).addClass("hidden");
	$("#ulink_"+id).removeClass("hidden");
	var parms = {name:name, id:id};
	$.ajax({
		url: base_url+'branches/savebranchname/', 
		data: parms,
		type : 'POST',
		success: function(result){
			if("notsaved"==result.value) {
				alert("OOps!! Branch name could not be update due to some technical issue.\nPlease try after sometime.");
			} else {
				$("#ulink_"+id).text(result.value);
			}
		}
	});
}

function check_unique_branch_id(branchid) {
	var parms = {branch:branchid};
	var isunique = false;
	$("#submitnewbranch").prop('disabled', true);
	$.ajax({
		url: base_url+'branches/checkuniquebranchid/', 
		data: parms,
		async : false,
		type : 'POST',
		success: function(result){
			unique = result.unique;
			$(".pass, .fail").hide();
			if(unique=="0notpossible") {
				$(".fail").fadeIn(function(){
					$(this).html("<strong>Oops!</strong> Invalid ID. Please enter a positive number, greater than 0.");
					$("#submitnewbranch").prop('disabled', true);
				});
			} else if(false==unique) {			
				$(".fail").fadeIn();
				$("#submitnewbranch").prop('disabled', true);
			} else {
				$(".pass").fadeIn(function() {
					$("#submitnewbranch").prop('disabled', false);
				});
			}
		}
	});
}