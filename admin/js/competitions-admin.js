jQuery(function( $ ) {
	'use strict';

	$("select#project_competition").select2({
		placeholder: "Select a competition"
	});

	function uploadAvatar() {
		var imgfile, selectedFile;
		// If the frame already exists, re-open it.
		if (imgfile) {
			imgfile.open();
			return;
		}
		//Extend the wp.media object
		imgfile = wp.media.frames.file_frame = wp.media({
			title: 'Choose Avatar',
			button: {
				text: 'Select'
			},
			multiple: false
		});

		//When a file is selected, grab the URL and set it as the text field's value
		imgfile.on('select', function () {
			selectedFile = imgfile.state().get('selection').first().toJSON();
			
			let elem = `<div class="avatar">
				<span class="removeAvatar">+</span>
				<img src="${selectedFile.url}" alt="avatar">
				<input type="hidden" name="image_urls[]" value="${selectedFile.url}">
			</div>`;

			$(".avatars").append(elem);
		});

		//Open the uploader dialog
		imgfile.open();
	}

	$("#addAvatar").on("click", function (e) {
		e.preventDefault();
		uploadAvatar();
	});

	$(document).on("click", "span.removeAvatar", function(){
		$(this).parent().remove();
	});

	$("#add_remark").on("click", function(e){
		e.preventDefault();
		let remark = $("#remark_input").val();

		if(remark !== ""){
			let remarkEl = `<div class="remark"><span>${remark}</span><input type="hidden" name="comp_remarks[]" value="${remark}"><span class="remarkremove">+</span></div>`
			$(".remarks_list").append(remarkEl);
			$("#remark_input").val("");
		}
		
	});

	$(document).on("click", ".remarkremove", function () {
		$(this).parents(".remark").remove();
	});

	$(document).on("change", "#user_remark", function(){
		let userid = $(this).data("user");
		let remark = $(this).val();
		$.ajax({
			type: "post",
			url: compajax.ajaxurl,
			data: {
				action: "update_user_remark",
				user: userid,
				remark: remark
			},
			dataType: "json",
			success: function (response) {
				
			}
		});
	})

});
