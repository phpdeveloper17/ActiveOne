	/**
		-> capture image using camera
		
		-> Author 	: Richel R. Amante
		
		-> Email	: richelramante@gmail.compile
		
		-> Date		: October 5, 2014
		
		-> Filename	: capture_image.js
	
	**/
	
	var imgeWsize = 475;
	
	var imgHsize= 355;
	
	var img_id = 0;
	
	var loadingurl = 'images/searching.gif';
	
	var bseUrl = location.protocol + '//' + location.hostname;
	
	var dataURL ='';
	
	var date = new Date();
	
	var settemp_data = '';
	
	var imagefilename = '';
	
	var imagetype = 'png';
	
	var temp_file ='';
	
	var action_delurl 	=	jQuery('#base_url').val()	+	'imagecapture/index/deletecaptured?_=/&___SID=' + date.getTime();
	var action_saveurl 	=	jQuery('#base_url').val()	+	'imagecapture/index/presavecapturerx?_=/&___SID=' + date.getTime();
	var prod_id 		= 	jQuery('[name=product]').val();
	
	
	//Preparing Camera
	
	
	jQuery(document).ready(function() {
		
			jQuery('#status').css('width',imgeWsize);		
			
			
			jQuery("#capture_button").on('click', function(e) {
			
				stop_cap();
				
				e.preventDefault();
				
				
				canvas.setAttribute('width', imgeWsize);
				
				canvas.setAttribute('height', imgHsize);
				
				ctx.drawImage(video, 0, 0, imgeWsize, imgHsize);
				
				jQuery('#controls > img').fadeIn();	
				
				jQuery('#status').html('<span class="success_msg">Verifiying your image. Please wait...</span>');	
				
				save_image();
				
			});
			
			// Get the dimensions and scale the video when stream is ready to play,		
			jQuery("video").on('canplay', function(e) {
			
				if (!streaming) {
				
					jQuery('#status').html('');
					
					jQuery('#controls > img').fadeOut();
					
					//imgHsize = video.videoHeight / (video.videoWidth / imgeWsize);
					
					video.setAttribute('width', imgeWsize);
					
					video.setAttribute('height', imgHsize);
					
					jQuery('#status').attr('width', imgeWsize);
					
					jQuery('#status').attr('height', imgHsize);
					
					streaming = true;
				}
			});
			
	});	

	//Saving image to media folder @ tmp->prescriptions

	function save_image()
		{	
			
				imagefilename = 'captured_prescription_'	+	prod_id	+ '_' + img_id + '_' + date.getTime() + '.' + imagetype;
	
				temp_file = 'captured_prescription_'	+	prod_id + '_' + img_id + '_' +  date.getTime() + '.php';
				
				product_id = prod_id;
			
				setTimeout(function (){
				
					var imgdata = jQuery('#result_image > img').attr('src'); // canvas.toDataURL('image/png');
					
					var newdata = imgdata.replace(/^data:image\/png/,'data:application/octet-stream');	
					
					dataURL = jQuery('#result_image > img').attr('src');	

				//Posting Image to presavescannedrx controller
				
				jQuery.post( action_saveurl,
					{ 
						imgdata: dataURL, prodid: product_id, imagefile_name: imagefilename, img_type: imagetype, tempname: temp_file, camera_file: true
					},
					function(xhr){
						
						var getresponse_data = xhr;  
						
						if(getresponse_data.success == true){
						
								 jQuery(".footer .control .cancel").attr("onclick","remove_Rx(\'"+getresponse_data.temp_file+"\')");
								 jQuery('#file_attacher_capture_path').val(getresponse_data.file_path);
								 jQuery('.cancel_capture').attr('data-url',getresponse_data.file_path);
								 //jQuery('#grp-btn').show();
								 jQuery('.webcam_upload').show();
								 jQuery('.webcam_cancel').show();
								 generate_rx();
							}
							
						else{
						
							jQuery('#status').html('<span class="critical_msg">Error in saving your image. <br/>Please try again or refresh your Browser.</span>');	
						}
					});
                	
		},500);		
		

		img_id++;
	}
	
	function generate_rx(){   
	
		//jQuery('#scanned_prescription_lists').append(settemp_data);
		
		//jQuery("#main_presc_scanned").append(settemp_data);
		
		jQuery('#status').html('');	
		jQuery("#loader_rx").hide();
		jQuery(".footer .control .hide").show();		
		jQuery('#controls > img').fadeOut();		
		jQuery('.cancel_capture').show();	
		jQuery('#camera-control').fadeIn();	
		jQuery('#preloader').hide();
	

	}
	
	function remove_Rx(ref_id){
	
		jQuery('#controls > img').fadeIn();
		jQuery('#status').html('<span class="critical_msg">Deleting image. please wait...</span>');
		
		//Posting to delete selected image
		jQuery.post( action_delurl,
			{ 
				img_filename: ref_id+'.'+imagetype
			},
			function(xhr)
			{
				var getresponse_data = xhr; 
				
				if (getresponse_data.success == true)
				{
					jQuery("."+ref_id).remove();					
					jQuery('#status').html('<span class="success_msg">Successfully deleted.</span>');
					jQuery("#capture_button").show();
					jQuery('#controls > img').fadeOut();
					jQuery('#camera-control').fadeOut();					
					jQuery("#canvas").css('display','none');
					jQuery("#video").css('display','block');
					jQuery('.webcam_upload').hide();
					jQuery('.webcam_cancel').hide();
				}
			});
	}
	
	jQuery('body').on('click', '.capimage', function (){	
		jQuery('.previmage').attr('src',jQuery(this).attr('src'));
		jQuery('.image-wrapper').fadeIn();
	});
	
	jQuery('body').on('click', '.previmage', function (){	
		hide_image();
	});	
	
	jQuery('body').on('click', '#close_me', function (){	
		hide_image();
	});	
	
	function hide_image()
	{
		jQuery('.image-wrapper').fadeOut();
	}