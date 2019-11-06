 /*
  * @package Unilab JS Scripts 
  * @author  Jerick. Y. DUguran - Movent, INC. -  jerick.duguran@gmail.com
  * @date    November 06, 2013
  *
**/

/******************************************************************************/

/*
 * Prescription Switcher 
 * @date November 07, 2013
 */

PrescriptionSwitcher = Class.create();
PrescriptionSwitcher.prototype = {
	initialize: function (wrapperEl,selElm)
	{ 
		try {	 		
			this.wrapper = $(wrapperEl); 	
			this.types   = $$("input."+selElm);		 
			this.types.each(function(element){
				Event.observe(element, 'click', this.update.bind(this))
            }.bind(this));		
		}catch(e){
            //console.log(e);		
		}
	},
	update: function(event){
		var _obj = this;
        var element = Event.element(event); 	
        	  
		this.types.each(function(elm){
			var prescription_details = $(elm).up().down('div.prescription_details');
			if(element.value == elm.value){
                if(prescription_details){
					if(prescription_details.select("input").length > 0){
						prescription_details.select("input").each(function(currentElement){ 
								Validation.reset(currentElement);	 
								if(currentElement.hasClassName("required")){
									currentElement.addClassName('required-entry');
								}else if(currentElement.hasClassName("required-file-u")){ 
									//_obj.resortClass(currentElement,'required-file-rx');
									Event.observe(currentElement,'change', _obj.upload.bind(_obj));  
								}else if(currentElement.hasClassName("required-one")){
									currentElement.addClassName('validate-one-required-by-name');							
								}else{
									//console.log('..');
								}
						}); 	
					} 
					prescription_details.show();
				}
			}else{
				if(prescription_details){
					if(prescription_details.select("input").length > 0){
						prescription_details.select("input").each(function(currentElement){
								Validation.reset(currentElement);	 
								if(currentElement.hasClassName("required")){
									currentElement.removeClassName('required-entry');
								}else if(currentElement.hasClassName("required-file-u")){
									//currentElement.removeClassName('required-file-rx');							
								}else if(currentElement.hasClassName("required-one")){
									currentElement.removeClassName('validate-one-required-by-name');							
								}else{
									//console.log('..');
								}
						}); 	
						prescription_details.hide();		
					} 			
				}
			}
		});  
	},
	upload: function(event)
	{  
		var element  = Event.element(event); 
		Validation.validate(element); 	
	},
	resortClass: function(elm,priority_class)
	{	
		var old_class_name = elm.className; 		
		elm.className 	   = ''; 
		elm.addClassName(priority_class); 
		elm.addClassName(old_class_name); 
	}
}

/*
 * Auto Updater revision 
 * @date November 26, 2013
 * $desc fix core issue on cleaning up whitespacewith invalid referenced element
 */
Autocompleter.Base.prototype.updateChoices = function(choices){
    if(!this.changed && this.hasFocus) {
      this.update.innerHTML = choices; 
      if(this.update.firstChild && this.update.down().childNodes) { 
		//actual changes
		Element.cleanWhitespace(this.update);
		Element.cleanWhitespace(this.update.down());
		
        this.entryCount =
          this.update.down().childNodes.length;
        for (var i = 0; i < this.entryCount; i++) {
          var entry = this.getEntry(i);
          entry.autocompleteIndex = i;
          this.addObservers(entry);
        }
      } else {
        this.entryCount = 0;
      }

      this.stopIndicator();
      this.index = 0;

      if(this.entryCount==1 && this.options.autoSelect) {
        this.selectEntry();
        this.hide();
      } else {
        this.render();
      }
    }
  },

/*
 * Custom Validators 
 * @date 	November 07, 2013
 */
Validation.addAllThese([
		['validate-file-type', 'Please select valid file type. [jpg,png,gif]', function (v,element) {  
			if(element.hasClassName('rx-type-error')){
				return false;
			}
			return true;
						
		}],
		['validate-file-size', 'Maximum File Size is 2MB', function (v,element) {
			if(element.hasClassName('rx-size-error')){
				return false;
			}
			return true;
		}],
		['validate-terms-condition', 'Please agree on our terms and condition', function (v,element) {
	
				if(element.checked == true) return true;
				
				return false;
		}],
		['validate-no-existing', 'No Existing Prescription.', function (v,element) { 
				return false; 
		}],		
		['validate-contactnumber', 'Should not exceed 13 characters.',function (v,element) {
			return v.length > 13 ? false : true;
		}],
		['validate-contactnumber-require-one', 'At least one contact number is required.',function (v,e) {	 
			var valid = true;
			if(e.name == 'telephone'){
				valid = ((!Validation.get('IsEmpty').test($('mobile').value) && ($('mobile').value != "+63" )  || (!Validation.get('IsEmpty').test(v))));
			}			
			else if(e.name == 'mobile'){
				valid = ((!Validation.get('IsEmpty').test($('telephone').value)) || (!Validation.get('IsEmpty').test(v)) && e.value != "+63");
			}else{
				valid = false;
			}
			return valid;
		}],
		['required-ph-mobile', 'Mobile number is required.',function (v,e) {	 
			if(v=="+63" || v == ""){
				return false;
			}
		}],
		['required-file-rx', 'No prescription attached', function(v, elm) { 
			 var result = !Validation.get('IsEmpty').test(v);
			 if (result === false) {
				 ovId = elm.id + '_value';
				 if ($(ovId)) {
					 result = !Validation.get('IsEmpty').test($(ovId).value);
				 }
			 }
			 return result;
		}],
		['validate-existing-prescriptions', 'No Prescription is Selected.', function(v, elm) {
			return false;
		}],
		['validate-filetype', 'Invalid File Type.', function(v, elm) {  
			if(elm == 'justuploaded'){
				alert('Invalid File Type');
				return false;
			}
			return true;
		}], 
		['validate-filesize', 'File size must be 2MB and below.', function(v, elm) {
			if(elm == 'justuploaded'){ 
				debugger;
				return false;
			}
			return true;
		}],
		['unknownerror-upload', 'You may have uploaded a large file.', function(v, elm) { 
			return false; 
		}],
		['validatefiles', 'Please attach your prescription', function(v, elm) {
			if(jQuery("#main_presc_scanned").find("input").length < 1){
				return false;
			}
			
			return true;
		}]
	]); 
	
/*********************************************
 * JQUERY SCRIPTS
 *********************************************/ 
 
 /* @date November 22, 2013
  * @description Override accordion to handle anchor links
  */
  
var UnilabAccord = function(){ddaccordion.call(this);};

jQuery.extend(UnilabAccord,ddaccordion,{
	init:function(config){
			document.write('<style type="text/css">\n')
			document.write('.'+config.contentclass+'{display: none}\n') //generate CSS to hide contents
			document.write('a.hiddenajaxlink{display: none}\n') //CSS class to hide ajax link
			document.write('<\/style>')
			jQuery(document).ready(function($){
				UnilabAccord.urlparamselect(config.headerclass)
				var persistedheaders=UnilabAccord.getCookie(config.headerclass)
				UnilabAccord.headergroup[config.headerclass]=$('.'+config.headerclass) //remember header group for this accordion
				UnilabAccord.contentgroup[config.headerclass]=$('.'+config.contentclass) //remember content group for this accordion
				var $headers=UnilabAccord.headergroup[config.headerclass]
				var $subcontents=UnilabAccord.contentgroup[config.headerclass]
				config.cssclass={collapse: config.toggleclass[0], expand: config.toggleclass[1]} //store expand and contract CSS classes as object properties
				config.revealtype=config.revealtype || "click"
				config.revealtype=config.revealtype.replace(/mouseover/i, "mouseenter")
				if (config.revealtype=="clickgo"){
					config.postreveal="gotourl" //remember added action
					config.revealtype="click" //overwrite revealtype to "click" keyword
				}
				if (config.revealtype=="clickForce"){ 
					config.clickforce = true;
					config.revealtype="click";
				}				
				if (typeof config.clickforce =="undefined")
					config.clickforce=false;
					
				if (typeof config.togglehtml=="undefined")
					config.htmlsetting={location: "none"}
				else
					config.htmlsetting={location: config.togglehtml[0], collapse: config.togglehtml[1], expand: config.togglehtml[2]} //store HTML settings as object properties
				config.oninit=(typeof config.oninit=="undefined")? function(){} : config.oninit //attach custom "oninit" event handler
				config.onopenclose=(typeof config.onopenclose=="undefined")? function(){} : config.onopenclose //attach custom "onopenclose" event handler
				var lastexpanded={} //object to hold reference to last expanded header and content (jquery objects)
				var expandedindices=UnilabAccord.urlparamselect(config.headerclass) || ((config.persiststate && persistedheaders!=null)? persistedheaders : config.defaultexpanded)
				if (typeof expandedindices=='string') //test for string value (exception is config.defaultexpanded, which is an array)
					expandedindices=expandedindices.replace(/c/ig, '').split(',') //transform string value to an array (ie: "c1,c2,c3" becomes [1,2,3]
				if (expandedindices.length==1 && expandedindices[0]=="-1") //check for expandedindices value of [-1], indicating persistence is on and no content expanded
					expandedindices=[]
				if (config["collapseprev"] && expandedindices.length>1) //only allow one content open?
					expandedindices=[expandedindices.pop()] //return last array element as an array (for sake of jQuery.inArray())
				if (config["onemustopen"] && expandedindices.length==0) //if at least one content should be open at all times and none are, open 1st header
					expandedindices=[0]
				 
				$headers.each(function(index){ //loop through all headers 
					var $header=$(this)
					if (/(prefix)|(suffix)/i.test(config.htmlsetting.location) && $header.html()!=""){ //add a SPAN element to header depending on user setting and if header is a container tag
						$('<span class="accordprefix"></span>').prependTo(this)
						$('<span class="accordsuffix"></span>').appendTo(this)
					}
					$header.attr('headerindex', index+'h') //store position of this header relative to its peers
					$subcontents.eq(index).attr('contentindex', index+'c') //store position of this content relative to its peers
					var $subcontent=$subcontents.eq(index)
					var $hiddenajaxlink=$subcontent.find('a.hiddenajaxlink:eq(0)') //see if this content should be loaded via ajax
					if ($hiddenajaxlink.length==1){
						$header.data('ajaxinfo', {url:$hiddenajaxlink.attr('href'), cacheddata:null, status:'none'}) //store info about this ajax content inside header
					}
					var needle=(typeof expandedindices[0]=="number")? index : index+'' //check for data type within expandedindices array- index should match that type
					if (jQuery.inArray(needle, expandedindices)!=-1 || $header.hasClass('expandme')){ //check for headers that should be expanded automatically (convert index to string first)
						UnilabAccord.expandit($header, $subcontent, config, false, false, !config.animatedefault) //3rd last param sets 'isuseractivated' parameter, 2nd last sets isdirectclick, last sets skipanimation param
						lastexpanded={$header:$header, $content:$subcontent}
					}  //end check
					else{
						$subcontent.hide()
						config.onopenclose($header.get(0), parseInt($header.attr('headerindex')), $subcontent.css('display'), false) //Last Boolean value sets 'isuseractivated' parameter
						UnilabAccord.transformHeader($header, config, "collapse")
					}
				})
				$headers.bind("evt_accordion", function(e, isdirectclick){ //assign CUSTOM event handler that expands/ contacts a header
						var $subcontent=$subcontents.eq(parseInt($(this).attr('headerindex'))) //get subcontent that should be expanded/collapsed
						if ($subcontent.css('display')=="none"){
							UnilabAccord.expandit($(this), $subcontent, config, true, isdirectclick) //2nd last param sets 'isuseractivated' parameter
							if (config["collapseprev"] && lastexpanded.$header && $(this).get(0)!=lastexpanded.$header.get(0)){ //collapse previous content?
								UnilabAccord.collapseit(lastexpanded.$header, lastexpanded.$content, config, true) //Last Boolean value sets 'isuseractivated' parameter
							}
							lastexpanded={$header:$(this), $content:$subcontent}
						}
						else if (!config["onemustopen"] || config["onemustopen"] && lastexpanded.$header && $(this).get(0)!=lastexpanded.$header.get(0)){
							UnilabAccord.collapseit($(this), $subcontent, config, true) //Last Boolean value sets 'isuseractivated' parameter
						}
				})
				$headers.bind(config.revealtype, function(e){
					if (config.revealtype=="mouseenter"){
						clearTimeout(config.revealdelay)
						var headerindex=parseInt($(this).attr("headerindex"))
						config.revealdelay=setTimeout(function(){UnilabAccord.expandone(config["headerclass"], headerindex)}, config.mouseoverdelay || 0)
					} 
					else if(config.revealtype == "click" && config.clickforce){
						if(e.target.tagName == "A"){  
							return true;
						}else{ 
							$(this).trigger("evt_accordion", [true]) ;  
							return false  
						}
					}
					else{
						$(this).trigger("evt_accordion", [true]) //last parameter indicates this is a direct click on the header
						return false //cancel default click behavior
					}
				}) 
				$headers.bind("mouseleave", function(){
					clearTimeout(config.revealdelay)
				})
				config.oninit($headers.get(), expandedindices)
				$(window).bind('unload', function(){ //clean up and persist on page unload
					$headers.unbind()
					var expandedindices=[]
					$subcontents.filter(':visible').each(function(index){ //get indices of expanded headers
						expandedindices.push($(this).attr('contentindex'))
					})
					if (config.persiststate==true && $headers.length>0){ //persist state?
						expandedindices=(expandedindices.length==0)? '-1c' : expandedindices //No contents expanded, indicate that with dummy '-1c' value?
						UnilabAccord.setCookie(config.headerclass, expandedindices)
					}
				})
			})  
		}
	})
	
var showDialog = function(_content)
{	
	jQuery.fancybox({content: _content,
					 closeBtn: false,
					 closeClick: false,
					 helpers: { overlay : {closeClick : false,
										  locked:	   true}}
					}); 
} 

/* ### LIGHT CHECKOUT  
 * Movent, INC.
 * Jerick Y. Duguran - jerick.duguran@gmail.com
 * January 17, 2013 
 */

 if(typeof Lightcheckout != "undefined"){
Lightcheckout.prototype.submitForReValidation = function(params, action){ 
		this.showLoadinfoForShipping();
		
		params.action = action;

		var request = new Ajax.Request(this.url,
		  {
		    method:'post',
		    parameters:params,
		    onSuccess: function(transport){

		    eval('var response = '+transport.responseText);

		    if(response.messages_block){
		    	var gcheckout_onepage_wrap = $$('div.gcheckout-onepage-wrap')[0];
		    	if (gcheckout_onepage_wrap){
		    		new Insertion.Before(gcheckout_onepage_wrap, response.messages_block);
		    	}
		    	this.disable_place_order = true;
		    }else{
		    	this.disable_place_order = false;
		    }

		    if(response.url){

		    	this.existsreview = false;
		    	setLocation(response.url);

		    }else{

		    if(response.error){
				if(response.message){
					alert(response.message);
				}
				this.existsreview = false;
				this.hideLoadinfo();
			}else{ 
				var process_save_order = false;
				
				if(response.methods){
					// Quote isVirtual
					this.innerHTMLwithScripts($('gcheckout-onepage-methods'), response.methods);
					var wrap = $$('div.gcheckout-onepage-wrap')[0];
					if (wrap && !wrap.hasClassName('not_shipping_mode')){
						wrap.addClassName('not_shipping_mode');	
					}
					if ($('billing_use_for_shipping_yes') && $('billing_use_for_shipping_yes').up('li.control')){
						$('billing_use_for_shipping_yes').up('li.control').remove();
					}
					if ($('gcheckout-shipping-address')){
						$('gcheckout-shipping-address').remove();
					}
					payment.init();
					this.observeMethods();
				}
				
				if(response.shippings){
					if(shipping_rates_block = $('gcheckout-shipping-method-available')){
						this.innerHTMLwithScripts(shipping_rates_block, response.shippings);
						this.observeShippingMethods();
					}
				}

				if(response.payments){
					this.innerHTMLwithScripts($('gcheckout-payment-methods-available'), response.payments);
					payment.init();
					this.observePaymentMethods();
				}

				if (response.gift_message){
					if(giftmessage_block = $('gomage-lightcheckout-giftmessage')){
						this.innerHTMLwithScripts(giftmessage_block, response.gift_message);
					}
				}

				if(response.toplinks){						
					this.replaceTopLinks(response.toplinks);						
				}

                if(response.cart_sidebar && typeof(GomageProcartConfig) != 'undefined'){
                    GomageProcartConfig._replaceEnterpriseTopCart(response.cart_sidebar, ($('topCartContent') && $('topCartContent').visible()));
                }

				if(response.review){
					this.innerHTMLwithScripts($$('#gcheckout-onepage-review div.totals')[0], response.review);
				}

				if (response.content_billing){
					var div_billing = document.createElement('div');
					div_billing.innerHTML = response.content_billing;
					$('gcheckout-onepage-address').replaceChild(div_billing.firstChild, $('gcheckout-billing-address'));
				}

				if (response.content_shipping && $('gcheckout-shipping-address')){
					var div_shipping = document.createElement('div');
					div_shipping.innerHTML = response.content_shipping;
					$('gcheckout-onepage-address').replaceChild(div_shipping.firstChild, $('gcheckout-shipping-address'));
				}

				if (response.content_billing || response.content_shipping){
					initAddresses();
				}

				if(response.section == 'varify_taxvat'){
										
					if($('billing_taxvat_verified')){
						$('billing_taxvat_verified').remove();
					}

					checkout.billing_taxvat_verified_flag = response.verify_result;

					if(response.verify_result){
						if(label = $('billing_taxvat').parentNode.parentNode.getElementsByTagName('label')[0]){
							label.innerHTML += '<strong id="billing_taxvat_verified" style="margin-left:5px;">(<span style="color:green;">Verified</span>)</strong>';
						}
					}else if($('billing_taxvat') && $('billing_taxvat').value){
						if(label = $('billing_taxvat').parentNode.parentNode.getElementsByTagName('label')[0]){
							label.innerHTML += '<strong id="billing_taxvat_verified" style="margin-left:5px;">(<span style="color:red;">Not Verified</span>)</strong>';
						}
					}					

				}

				if (response.section == 'centinel'){

					if (response.centinel){
						this.showCentinel(response.centinel);
					}else{
						process_save_order = true;
						if((payment.currentMethod == 'authorizenet_directpost') && ((typeof directPostModel != 'undefined'))){
							directPostModel.saveOnepageOrder();
						}else{
							this.saveorder();
						}
					}
				}

                this.setBlocksNumber();

				if (this.existsreview)
				{
					this.existsreview = false;
					review.save();
				}
				else
				{
					if (!process_save_order){
						this.hideLoadinfo();
					}
				}

			}

			}

		    }.bind(this),
		    onFailure: function(){
		    	this.existsreview = false;
		    }
		  });
}

Lightcheckout.prototype.reloadShippingMethods= function()
{
	this.submitForReValidation(this.getFormData(), 'get_shipping_methods');
}

Lightcheckout.prototype.loadAddressForValidation = function(type, id, url){ 
    	if(id){
			this.showLoadinfoForShipping(); 

			var use_for_shipping = ($('billing_use_for_shipping_yes') && $('billing_use_for_shipping_yes').checked);
			
			var request = new Ajax.Request(url,
			  {
			    method:'post',
			    parameters:{'id':id,
				            'type':type,
				            'use_for_shipping': use_for_shipping
				           },
			    onSuccess: function(transport){

			    eval('var response = '+transport.responseText);

			    if(response.error){



				}else{					
					
					if(response.shippings){
						if ($('gcheckout-shipping-method-available')){
							this.innerHTMLwithScripts($('gcheckout-shipping-method-available'), response.shippings);
						}
					}
					if(response.payments){
						this.innerHTMLwithScripts($('gcheckout-payment-methods-available'), response.payments);
						payment.init();
					}

					if (response.content_billing){
						var div_billing = document.createElement('div');
						div_billing.innerHTML = response.content_billing;
						$('gcheckout-onepage-address').replaceChild(div_billing.firstChild, $('gcheckout-billing-address'));
					}

					if (response.content_shipping && $('gcheckout-shipping-address')){
						var div_shipping = document.createElement('div');
						div_shipping.innerHTML = response.content_shipping;
						$('gcheckout-onepage-address').replaceChild(div_shipping.firstChild, $('gcheckout-shipping-address'));
					}

					if(response.review){
						this.innerHTMLwithScripts($$('#gcheckout-onepage-review div.totals')[0], response.review);
					}

					initAddresses();

					checkout.initialize();
				}
				this.hideLoadinfo();

			    }.bind(this),
			    onFailure: function(){
			    	// ...
			    }
			  });

    	}else{

	    	$(type+'-new-address-form').select('input[type=text], select, textarea').each(function(e){

	    		e.value = '';

	    	});

    	}

    }
	
Lightcheckout.prototype.loadAddress = function(type, id, url){ 
    	if(id){
			this.showLoadinfo();

			var use_for_shipping = ($('billing_use_for_shipping_yes') && $('billing_use_for_shipping_yes').checked);
			
			var request = new Ajax.Request(url,
			  {
			    method:'post',
			    parameters:{'id':id,
				            'type':type,
				            'use_for_shipping': use_for_shipping
				           },
			    onSuccess: function(transport){

			    eval('var response = '+transport.responseText);

			    if(response.error){



				}else{
					
					//CHECK FOR REVALIDATING of Shipping Method
					if(response.shipping_recall){
						this.loadAddressForValidation(type, id, url);
						this.hideLoadinfo();
						return;
					}
					
					
					if(response.shippings){
						if ($('gcheckout-shipping-method-available')){
							this.innerHTMLwithScripts($('gcheckout-shipping-method-available'), response.shippings);
						}
					}
					if(response.payments){
						this.innerHTMLwithScripts($('gcheckout-payment-methods-available'), response.payments);
						payment.init();
					}

					if (response.content_billing){
						var div_billing = document.createElement('div');
						div_billing.innerHTML = response.content_billing;
						$('gcheckout-onepage-address').replaceChild(div_billing.firstChild, $('gcheckout-billing-address'));
					}

					if (response.content_shipping && $('gcheckout-shipping-address')){
						var div_shipping = document.createElement('div');
						div_shipping.innerHTML = response.content_shipping;
						$('gcheckout-onepage-address').replaceChild(div_shipping.firstChild, $('gcheckout-shipping-address'));
					}

					if(response.review){
						this.innerHTMLwithScripts($$('#gcheckout-onepage-review div.totals')[0], response.review);
					}

					initAddresses();

					checkout.initialize();
				}
				this.hideLoadinfo();

			    }.bind(this),
			    onFailure: function(){
			    	// ...
			    }
			  });

    	}else{

	    	$(type+'-new-address-form').select('input[type=text], select, textarea').each(function(e){

	    		e.value = '';

	    	});

    	}

    }

Lightcheckout.prototype.submit = function(params, action){   
		this.showLoadinfo();
		
		params.action = action;

		var request = new Ajax.Request(this.url,
		  {
		    method:'post',
		    parameters:params,
		    onSuccess: function(transport){ 

		    eval('var response = '+transport.responseText);

		    if(response.messages_block){
		    	var gcheckout_onepage_wrap = $$('div.gcheckout-onepage-wrap')[0];
		    	if (gcheckout_onepage_wrap){
		    		new Insertion.Before(gcheckout_onepage_wrap, response.messages_block);
		    	}
		    	this.disable_place_order = true;
		    }else{
		    	this.disable_place_order = false;
		    }

		    if(response.url){

		    	this.existsreview = false;
		    	setLocation(response.url);

		    }else{

		    if(response.error){
				if(response.message){
					alert(response.message);
				}
				this.existsreview = false;
				this.hideLoadinfo();
			}else{
			
				//CHECK FOR REVALIDATING of Shipping Method
				if(response.shipping_recall){
					this.submitForReValidation(params, action);
					this.hideLoadinfo();
					return;
				}
				
				var process_save_order = false;
				
				if(response.methods){
					// Quote isVirtual
					this.innerHTMLwithScripts($('gcheckout-onepage-methods'), response.methods);
					var wrap = $$('div.gcheckout-onepage-wrap')[0];
					if (wrap && !wrap.hasClassName('not_shipping_mode')){
						wrap.addClassName('not_shipping_mode');	
					}
					if ($('billing_use_for_shipping_yes') && $('billing_use_for_shipping_yes').up('li.control')){
						$('billing_use_for_shipping_yes').up('li.control').remove();
					}
					if ($('gcheckout-shipping-address')){
						$('gcheckout-shipping-address').remove();
					}
					payment.init();
					this.observeMethods();
				}
				
				if(response.shippings){
					if(shipping_rates_block = $('gcheckout-shipping-method-available')){
						this.innerHTMLwithScripts(shipping_rates_block, response.shippings);
						this.observeShippingMethods();
					}
				}

				if(response.payments){
					this.innerHTMLwithScripts($('gcheckout-payment-methods-available'), response.payments);
					payment.init();
					this.observePaymentMethods();
				}

				if (response.gift_message){
					if(giftmessage_block = $('gomage-lightcheckout-giftmessage')){
						this.innerHTMLwithScripts(giftmessage_block, response.gift_message);
					}
				}

				if(response.toplinks){						
					this.replaceTopLinks(response.toplinks);						
				}

                if(response.cart_sidebar && typeof(GomageProcartConfig) != 'undefined'){
                    GomageProcartConfig._replaceEnterpriseTopCart(response.cart_sidebar, ($('topCartContent') && $('topCartContent').visible()));
                }

				if(response.review){
					this.innerHTMLwithScripts($$('#gcheckout-onepage-review div.totals')[0], response.review);
				}

				if (response.content_billing){
					var div_billing = document.createElement('div');
					div_billing.innerHTML = response.content_billing;
					$('gcheckout-onepage-address').replaceChild(div_billing.firstChild, $('gcheckout-billing-address'));
				}

				if (response.content_shipping && $('gcheckout-shipping-address')){
					var div_shipping = document.createElement('div');
					div_shipping.innerHTML = response.content_shipping;
					$('gcheckout-onepage-address').replaceChild(div_shipping.firstChild, $('gcheckout-shipping-address'));
				}

				if (response.content_billing || response.content_shipping){
					initAddresses();
				}

				if(response.section == 'varify_taxvat'){
										
					if($('billing_taxvat_verified')){
						$('billing_taxvat_verified').remove();
					}

					checkout.billing_taxvat_verified_flag = response.verify_result;

					if(response.verify_result){
						if(label = $('billing_taxvat').parentNode.parentNode.getElementsByTagName('label')[0]){
							label.innerHTML += '<strong id="billing_taxvat_verified" style="margin-left:5px;">(<span style="color:green;">Verified</span>)</strong>';
						}
					}else if($('billing_taxvat') && $('billing_taxvat').value){
						if(label = $('billing_taxvat').parentNode.parentNode.getElementsByTagName('label')[0]){
							label.innerHTML += '<strong id="billing_taxvat_verified" style="margin-left:5px;">(<span style="color:red;">Not Verified</span>)</strong>';
						}
					}					

				}

				if (response.section == 'centinel'){

					if (response.centinel){
						this.showCentinel(response.centinel);
					}else{
						process_save_order = true;
						if((payment.currentMethod == 'authorizenet_directpost') && ((typeof directPostModel != 'undefined'))){
							directPostModel.saveOnepageOrder();
						}else{
							this.saveorder();
						}
					}
				}

                this.setBlocksNumber();

				if (this.existsreview)
				{
					this.existsreview = false;
					review.save();
				}
				else
				{
					if (!process_save_order){
						this.hideLoadinfo();
					}
				}

			}

			}

		    }.bind(this),
		    onFailure: function(){
		    	this.existsreview = false;
		    }
		  });
	}
	

	
Lightcheckout.prototype.showLoadinfoForShipping = function(){
		$('submit-btn').disabled = 'disabled';
		$('submit-btn').addClassName('disabled');
		$$('.validation-advice').each(function(e){e.remove()});
		msgs = $$('ul.messages');
		if(msgs.length){
			for(i = 0; i < msgs.length;i++){
				msgs[i].remove();
			}
		} 
		$$('div.gcheckout-onepage-wrap')[0].insert('<div class="loadinfo">'+loadshippingmethod_text+'</div>');
	}
}