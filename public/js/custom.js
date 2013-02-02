//$('#myForm .radio1:checked').val();
//status-section

$(window).load(function(){
    $("ol.progtrckr").each(function(){
        $(this).attr("data-progtrckr-steps", 
                     $(this).children("li").length);
    });
})

jQuery(function($) {
	
	$('.dropdown-toggle').dropdown()
	
    $('#reponame-section').on('change', 'input', function(event){
    	var rec = $('#readme-value').val();
        var formitem = $(this);
        var reponame = formitem.val();

        $.post("/creation/update-details", {
        	rid: rec,
        	reponame: reponame
        },function(data){
            if(data.response == false){
                // print error message
                console.log('could not update');
            }else{
            	$('#next').attr('href', "/creation/instructions/" + data.details.transid);
            }
        }, 'json');

    });
    
    $('#title-section').on('change', 'input', function(event){
    	var rec = $('#readme-value').val();
        var formitem = $(this);
        var title = formitem.val();

        $.post("/creation/update-details", {
        	rid: rec,
        	title: title
        },function(data){
            if(data.response == false){
                // print error message
                console.log('could not update');
            }
        }, 'json');

    });
    
    $('#description').change(function() {
    	var rec = $('#readme-value').val();
        var formitem = $(this);
        var description = formitem.val();

        $.post("/creation/update-details", {
        	rid: rec,
        	description: description
        },function(data){
            if(data.response == false){
                // print error message
                console.log('could not update');
            }
        }, 'json');
    	});
    
    $('#features-section').on('change', 'input', function(event){
    	var features_array = [];
    	var features_json = null;
    	 $("#features-section input[type=text]").each(function() {
             if(this.value != "") {
            	 features_array.push(this.value);
                 features_json = JSON.stringify(features_array, null, 2);
             }
         });
    	 
    	 alert(features_json);
    	 if(features_json != null){
    		 var rec = $('#readme-value').val();
    	     var $formitem = $(this);
	         $.post("/creation/update-details", {
	        	 rid: rec,
	             features: features_json
	         },function(data){
	             if(data.response == false){
	                 // print error message
	                 console.log('could not update');
	             }
	         }, 'json');
    	 }
    });
    
    $('#status-section').on('change', 'input', function(event){
    	var rec = $('#readme-value').val();
        var formitem = $(this);
        var type = formitem.attr('id');

        $.post("/creation/update-status", {
        	rid: rec,
            type: type
        },function(data){
            if(data.response == false){
                // print error message
                console.log('could not update');
            }
        }, 'json');

    });
    
    $('#requirements-section').on('change', 'input', function(event){
    	var rec = $('#readme-value').val();
    	alert(rec);
        var $formitem = $(this);
        var update_id = $formitem.attr('id');
        var update_content = $formitem.val();
        update_id = update_id.replace("requirements-","");

        $.post("/creation/add", {
        	rid: rec,
            id: update_id,
            requirements: update_content
        },function(data){
            if(data.response == false){
                // print error message
                console.log('could not update');
            }
        }, 'json');

    });
    
    $('#instructions-section').on('change', 'input', function(event){
    	var rec = $('#readme-value').val();
        var $formitem = $(this);
        var update_id = $formitem.attr('id');
        var update_content = $formitem.val();
        update_id = update_id.replace("instructions-","");

        $.post("/creation/add-instructions", {
        	rid: rec,
            id: update_id,
            instructions: update_content
        },function(data){
            if(data.response == false){
                // print error message
                console.log('could not update');
            }
        }, 'json');

    });
    
    $('#members-section').on('change', 'input', function(event){
    	var rec = $('#readme-value').val();
        var formitem = $(this);
        var update_id = formitem.attr('id');
        var update_content = formitem.val();
        update_id = update_id.replace("members-","");

        $.post("/creation/add-instructions", {
        	rid: rec,
            id: update_id,
            instructions: update_content
        },function(data){
            if(data.response == false){
                // print error message
                console.log('could not update');
            }
        }, 'json');

    });
});


function addFeatureAction() {
	var pendingval = $('#readme-count').val() + 1;
	
	var sectionrowid = "features-section-row-" + pendingval;
	var removeid = "remove-" + pendingval;
	var featuresid = "features-" + pendingval;
	$('#features-section').append('<div class="row-fluid" id="pending-section-row"><div class="span6" style="margin-top:6px; margin-bottom: 6px;"><input type="text" id="pending-id"></input></div><div class="span6" style="margin-top:6px; margin-bottom: 6px;"><span class="ui-icon ui-icon-closethick" id="pending-remove" onclick="removeFeatureAction(this);"></span></div>');
	$('#pending-section-row').attr('id', sectionrowid);
	$('#pending-id').attr('id', featuresid);
	$('#pending-remove').attr('id', removeid);
}


function addInstructionAction() {
	var pendingval = $('#readme-count').val() + 1;
	
	var sectionrowid = "instructions-section-row-" + pendingval;
	var removeid = "remove-" + pendingval;
	var requirementsid = "requirements-" + pendingval;
	$('#instructions-section').append('<div class="row-fluid" id="pending-section-row"><div class="span6" style="margin-top:6px; margin-bottom: 6px;"><input type="text" id="pending-id"></input></div><div class="span6" style="margin-top:6px; margin-bottom: 6px;"><span class="ui-icon ui-icon-closethick" id="pending-remove" onclick="removeInstructionAction(this);"></span></div>');
	$('#pending-section-row').attr('id', sectionrowid);
	$('#pending-id').attr('id', requirementsid);
	$('#pending-remove').attr('id', removeid);
}

function addRequirementAction() {
	var pendingval = $('#readme-count').val() + 1;
	
	var sectionrowid = "requirements-section-row-" + pendingval;
	var removeid = "remove-" + pendingval;
	var requirementsid = "requirements-" + pendingval;
	$('#requirements-section').append('<div class="row-fluid" id="pending-section-row"><div class="span6" style="margin-top:6px; margin-bottom: 6px;"><input type="text" id="pending-id"></input></div><div class="span6" style="margin-top:6px; margin-bottom: 6px;"><span class="ui-icon ui-icon-closethick" id="pending-remove" onclick="removeRequirementAction(this);"></span></div>');
	$('#pending-section-row').attr('id', sectionrowid);
	$('#pending-id').attr('id', requirementsid);
	$('#pending-remove').attr('id', removeid);
}

function removeFeatureAction(el) {
	alert("test");
	if (el.id.indexOf("remove-") >= 0){
		var remove_id = el.id.replace("remove-","");
		var rec = $('#readme-value').val();
        var update_content = $('#features-' + remove_id).val();

        //$.post("/creation/remove2", {
        //	rid: rec,
        //    id: remove_id,
        //    requirements: update_content
        //},function(data){
        //    if(data.response == false){
        //        // print error message
        //        console.log('could not update');
        //    }
        //}, 'json');
        
		$('#features-section-row-' + remove_id).remove();
	}else{
		
	}
}

function removeRequirementAction(el) {
	alert("test");
	if (el.id.indexOf("remove-") >= 0){
		var remove_id = el.id.replace("remove-","");
		var rec = $('#readme-value').val();
        var update_content = $('#requirements-' + remove_id).val();

        $.post("/creation/remove", {
        	rid: rec,
            id: remove_id,
            requirements: update_content
        },function(data){
            if(data.response == false){
                // print error message
                console.log('could not update');
            }
        }, 'json');
        
		$('#requirements-section-row-' + remove_id).remove();
	}else{
		
	}
}