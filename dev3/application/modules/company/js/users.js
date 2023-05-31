$(function () {
	branchCondition();
	k8RepCodeCondition();
	addRequiredOnKeyUp();
	
	/* Code to show the target if the url have #target appended. */
	if("#target"==window.location.hash) {
		$("#showiftarget a").click();
	}
		
	/* Code for the Data Table of logs table. */ 
	$('.log-list-table tfoot th').each( function () {
        var title = $(this).text();
        $(this).html( '<input type="text" style="width:100%" placeholder="Search '+title+'" />' );
    } );
	
	/* Code for the Data Table of logs table. */ 
	var table = $(".log-list-table").DataTable({"order": [[ 0, "desc" ]]});
	
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
	var table = $(".users-list-table").DataTable({"order": [[ 2, "asc" ], [ 1, "asc" ]]});
	
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
});

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

/* Global variable to store regular expression for non-blank */
var nonBlankRegexp = /[\S]+/;
/* Function to save the updated value of surname on blur. */
$("[name='surname']").on('blur', function(){
//	changeSurname(this);	/* not working on blur now */
});

/* Function to save the updated value of firstname on blur. */
$("[name='firstname']").on('blur', function(){
//	changeFirstname(this); 	/* not working on blur now */
});

/* Function to save the updated value of email on blur. */
$("[name='email']").on('blur', function(){
//	changeEmail(this); 	/* not working on blur now */
});

/* Function to call for changing the surname in inline editing */
function changeSurname(id) {
	if(isAdmin || id==loggedinuserid) {
		var surname = $("#surname_"+id).val();
		if(!nonBlankRegexp.test(surname)) {
			alert("The surname is mandatory!");
			$("#hidden_"+id).addClass("hidden");
			$("#ulink_"+id).removeClass("hidden");
			return false;
		}
		
		$("#hidden_"+id).addClass("hidden");
		$("#ulink_"+id).removeClass("hidden");
		var parms = {surname:surname, id:id};
		$.ajax({
			url: base_url+'users/savesurname/', 
			data: parms,
			type : 'POST',
			success: function(result){
				$("#ulink_"+id).text(result.value);
			}
		});
	}
}

/* Function to call for changing the firstname */
function changeFirstname(id) {
	if(isAdmin || id==loggedinuserid) {
		var firstname = $("#firstname_"+id).val();
		if(!nonBlankRegexp.test(firstname)) {
			alert("The First name is mandatory!");
			$("#fhidden_"+id).addClass("hidden");
			$("#flink_"+id).removeClass("hidden");
			return false;
		}
		$("#fhidden_"+id).addClass("hidden");
		$("#flink_"+id).removeClass("hidden");
		var parms = {firstname:firstname, id:id};
		$.ajax({
			url: base_url+'users/savefirstname/', 
			data: parms,
			type : 'POST',
			success: function(result){
				if("notsaved"==result.value) {
					alert("OOps!! First name could not be saved due to some technical issue.\nPlease try after sometime.");
				} else {
					$("#flink_"+id).text(result.value);
				}
				
			}
		});
	}
}

/* Function to call for changing the email address */
function changeEmail(id) {	
	if(isAdmin || id==loggedinuserid) {
		var email = $("#email_"+id).val();
		var isform = !$("#email_"+id).hasClass('input-sm');
		$("#ehidden_"+id).addClass("hidden");
		$("#elink_"+id).removeClass("hidden");
		var parms = {email:email, id:id};
		$.ajax({
			url: base_url+'users/saveemail/', 
			data: parms,
			type : 'POST',
			success: function(result){
				if("notunique"==result.value) {
					if(!isform) {
						alert("This email is already in use!");
						$("#elink_"+id).text($("#elink_"+id).text());
					}
					
				} else if("notsaved"==result.value) {
					alert("OOps!! Email could not be saved due to some technical issue.\nPlease try after sometime.");
					$("#elink_"+id).text($("#elink_"+id).text());
				} else if("wrongemail"==result.value) {
					alert("OOps!! we can not change the email. Please enter a valid email address.");
				} else {
					$("#elink_"+id).text(result.value);
				}
			}
		});
	}
}

/* Applying conditions on branch and k8 rep code on changing the value of user type. */
$("#usertype").on('change', function() {
	branchCondition();
	k8RepCodeCondition();
	addRequiredOnKeyUp();
});

$("#repcode, #repcode_2, #repcode_3, #repcode_4, #repcode_5").on('keyup', function(){
	var selected_value = document.getElementById("usertype").value;
	if($(this).val().trim()) {
		removeRequiredAllK8();
	} else {
		addRequiredOnKeyUp();
	}
});

/* Function to check if at-least one rep-code is non-empty. if the user type is (A)ll or (B)ranch, then at least one rep-code must be filled. */
function addRequiredOnKeyUp() {
	var anyone = false;
	var selected_value = "";
	var usertypeObj = document.getElementById("usertype");
	var repcodeObj = document.getElementById("repcode");
	var repcode_2Obj = document.getElementById("repcode_2");
	var repcode_3Obj = document.getElementById("repcode_3");
	var repcode_4Obj = document.getElementById("repcode_4");
	var repcode_5Obj = document.getElementById("repcode_5");
	if(usertypeObj!=null) {
		selected_value = document.getElementById("usertype").value;
	}
	
	if(repcodeObj!=null && repcodeObj.value.trim()) {
		anyone = true;
	}
	
	if(repcode_2Obj!=null && repcode_2Obj.value.trim()) {
		anyone = true;
	}
	
	if(repcode_3Obj!=null && repcode_3Obj.value.trim()) {
		anyone = true;
	}
	
	if(repcode_4Obj!=null && repcode_4Obj.value.trim()) {
		anyone = true;
	}
	
	if(repcode_5Obj!=null && repcode_5Obj.value.trim()) {
		anyone = true;
	}
	
	if(!anyone && "B"!=selected_value && "A"!=selected_value) {
		addRequiredAllK8();
	} else {
		removeRequiredAllK8();
	}	
}

/* Conditions to apply on branch field on the basis of selected user type */
function branchCondition() {
	var selected_value = "";
	var branch = document.getElementById("branch");
	if(document.getElementById("usertype")) {
		selected_value = document.getElementById("usertype").value;
	}
	
	if("R"==selected_value) {
		document.getElementById("branch").removeAttribute("required", true);
	} else {
		if("A"!=selected_value) {
			console.log(typeof branch);
			if(branch != null) {
				branch.setAttribute("required", true);
			}			
		} else {
			if(branch != null) {
				branch.removeAttribute("required", true);
			}
		}
	}
}

/* Conditions to apply on rep codes on the basis of selected user type */
function k8RepCodeCondition() {
	var usertypeobj = document.getElementById("usertype");
	var selected_value = '';
	if(usertypeobj!=null) {
		selected_value = document.getElementById("usertype").value;
	}
	
	if("R"==selected_value) {
		addRequiredAllK8();
	} else {
		if("A"!=selected_value) {
			addRequiredAllK8();
		} else {
			removeRequiredAllK8();
		}
	}
}

/* Function to add the required attributes to all k8 fields */
function addRequiredAllK8() {
	var repcodeObj = document.getElementById("repcode");
	var repcode_2Obj = document.getElementById("repcode_2");
	var repcode_3Obj = document.getElementById("repcode_3");
	var repcode_4Obj = document.getElementById("repcode_4");
	var repcode_5Obj = document.getElementById("repcode_5");
	if(repcodeObj!=null) {
		document.getElementById("repcode").setAttribute("required", true);
	}
	
	if(repcode_2Obj!=null) {
		document.getElementById("repcode_2").setAttribute("required", true);
	}
	
	if(repcode_3Obj!=null) {
		document.getElementById("repcode_3").setAttribute("required", true);
	}
	
	if(repcode_4Obj!=null) {
		document.getElementById("repcode_4").setAttribute("required", true);
	}
	
	if(repcode_5Obj!=null) {
		document.getElementById("repcode_5").setAttribute("required", true);
	}
}

/* Function to remove the required attributes from all k8 fields */
function removeRequiredAllK8() {
	var repcodeObj = document.getElementById("repcode");
	var repcode_2Obj = document.getElementById("repcode_2");
	var repcode_3Obj = document.getElementById("repcode_3");
	var repcode_4Obj = document.getElementById("repcode_4");
	var repcode_5Obj = document.getElementById("repcode_5");
	
	if(repcodeObj!=null) {
		document.getElementById("repcode").removeAttribute("required", true);
	}
	
	if(repcode_2Obj!=null) {
		document.getElementById("repcode_2").removeAttribute("required", true);
	}
	
	if(repcode_3Obj!=null) {
		document.getElementById("repcode_3").removeAttribute("required", true);
	}
	
	if(repcode_4Obj!=null) {
		document.getElementById("repcode_4").removeAttribute("required", true);
	}
	
	if(repcode_5Obj!=null) {
		document.getElementById("repcode_5").removeAttribute("required", true);
	}
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


/* Function to update Year/Month in user target listing with given target id */
function updateyearmonth(id) {
	var year = $("#year_"+id).val();
	var month = $("#month_"+id).val();
	var valid = validateYearMonth(year, month);
	if(valid) {
		var parms = {yearmonth:''+year+month, id:id};
		$.ajax({
			url: base_url+'users/updateyearmonth/', 
			data: parms,
			type : 'POST',
			success: function(result){
				if("success"==result.value){
					closeediting(id, "yearmonth", true);
					if(month<10) {
						month = '0'+month;
					}
					$("#ulink_"+id).text(''+year+month);
				}	
				
				if("duplicate"==result.value){
					closeediting(id, "yearmonth", false);
					alert("The target for this Year/Month already exists.");
				}	
				
				if("notsaved"==result.value){
					closeediting(id, "yearmonth", false);
					alert("The Year/Month for this target could not be updated due to some error.\nPlease try again later.");
				}			
			}
		});
	}
}

/* Function to update sales target in user target listing with given target id */
function updatesalestarget(id) {
	var salestarget = $("#salestarget_"+id).val();
	var valid = validateSalesTarget(salestarget);
	if(valid) {
		var parms = {salestarget:salestarget, id:id};
		$.ajax({
			url: base_url+'users/updatesalestarget/', 
			data: parms,
			type : 'POST',
			success: function(result){
				if("success"==result.value){
					closeediting(id, "target", true);
					$("#flink_"+id).text(salestarget);
				}
				
				if("notsaved"==result.value){
					closeediting(id, "target", false);
					alert("The target for this Year/Month could not be saved due to some error.\nPlease try again later.");
				}			
			}
		});
	}
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

/* Check if the email is unique. Called at the time of the user add/edit/copy form submit. */
function checkUnique() {
	var email = $('#email').val().replace('@', '--atrate--');
	email = email.replace(/\./g, '--dot--');
	var u = $('#userid');
	var userid = "";
	if(typeof u != 'undefined') {
		userid = u.val();
	}	
	var returnval = false;
	if(typeof userid == 'undefined') {
		userid = "";
	}
	$.ajax({
		method: "GET",
		url: base_url+"users/isUnique/"+email+'/'+userid,
		async : false
	}).done(function( response ) {
		returnval = response.unique;
	});
	
	if(!returnval) {
		var html = '<p id="email_err" class="email error alert-danger"><span>Please select a different email address, this email is already in use.</span></p>';
		$("#email_err").remove();
		$("#email").after(html);
		window.scrollTo(0, 0);
		$("#email_err").delay(5000).fadeOut("slow", function(){
			$(this).remove();
		});
	}	
	return returnval;
}

/* Remove the error message if the email field is focused. */
$("#email").on("focus", function() {
	$("#email_err").remove(); 
});

/* Function to show the selected file name below the upload button */
function showselectedfilename(obj) {
	var filearray = obj.value.split("\/");
	var far = [];
	var filename = '';
	var displayfilenamelocation = document.getElementById("selectedfile");
	if(filearray.length>1) {
		far = filearray;
	} else {
		far = obj.value.split("\\");
	}
	filename = far[far.length-1];
	
	if((filename!=null && ""!=filename) && displayfilenamelocation!=null) {
		$(displayfilenamelocation).text(filename);
	} else {
		$(displayfilenamelocation).text("No file selected");
	}
}

/* Function to check if the license exists */
function checklicense(ischecked) {
	if(ischecked) {
		$.ajax({
			method: "GET",
			url: base_url+"users/license",
			async : false
		}).done(function( response ) {
			if(response.limits_crossed) {
				document.getElementById("active").checked = false;
				alert("Can not make activate this user as all the licenses are already used.");
			}
		});
	}
}

/* Function to delete a target */
function deletetarget(id, userid) {
	var confirmed = confirm('Are you sure to delete this target?');
	if(confirmed) {
		$.ajax({
			method: "DELETE",
			url: base_url+"users/deletetarget/"+id+"/"+userid,
			async : true
		}).done(function( response ) {
			if(response.deleteresult) {	
				alertHtml = '<div class="alert alert-danger">Target record deleted successfully!</div>';
				$("#ulink_"+id+", #flink_"+id).removeClass("ulink flink");
				$("#ulink_"+id+", #flink_"+id+", #dlink_"+id).parent().addClass("italicize");
				$("#ulink_"+id+", #flink_"+id+", #dlink_"+id).parent().text("deleted");
				$('.alert').remove();
				$("#alertmsg").html(alertHtml);
			}
		});
	}
}