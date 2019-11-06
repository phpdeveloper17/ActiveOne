define([
    'jquery',
    'jquery/ui',
    'jquery/validate',
    'mage/translate'
], function($){
    'use strict';
 
    return function() {
        $.validator.addMethod(
            "validate-file-type",
            function(v, element) {
                if(element.hasClassName('rx-type-error')){
                    return false;
                }
                return true;
            },
            $.mage.__("Please select valid file type. [jpg,png,gif]")
        );

        $.validator.addMethod(
            "validate-file-size",
            function(v, element) {
                if(element.hasClassName('rx-size-error')){
                    return false;
                }
                return true;
            },
            $.mage.__("Maximum File Size is 2MB")
        );

        $.validator.addMethod(
            "validate-terms-condition",
            function(v, element) {
                if(element.checked == true) return true;
				
				return false;
            },
            $.mage.__("Please agree on our terms and condition")
        );

        $.validator.addMethod(
            "validate-no-existing",
            function(v, element) {
                return false;
            },
            $.mage.__("No Existing Prescription.")
        );

        $.validator.addMethod(
            "validate-contactnumber",
            function(v, element) {
                return v.length > 13 ? false : true;
            },
            $.mage.__("Should not exceed 13 characters.")
        );

        $.validator.addMethod(
            "validate-contactnumber-require-one",
            function(v, e) {
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
            },
            $.mage.__("At least one contact number is required.")
        );

        $.validator.addMethod(
            "required-ph-mobile",
            function(v, e) {
                if(v=="+63" || v == ""){
                    return false;
                }
            },
            $.mage.__("Mobile number is required.")
        );

        $.validator.addMethod(
            "required-file-rx",
            function(v, elm) {
                var result = !Validation.get('IsEmpty').test(v);
                if (result === false) {
                    ovId = elm.id + '_value';
                    if ($(ovId)) {
                        result = !Validation.get('IsEmpty').test($(ovId).value);
                    }
                }
                return result;
            },
            $.mage.__("No prescription attached.")
        );

        $.validator.addMethod(
            "validate-existing-prescriptions",
            function(v, e) {
                return false;
            },
            $.mage.__("No Prescription is Selected.")
        );

        $.validator.addMethod(
            "validate-filetype",
            function(v, elm) {
                if(elm == 'justuploaded'){
                    alert('Invalid File Type');
                    return false;
                }
                return true;
            },
            $.mage.__("Invalid File Type.")
        );

        $.validator.addMethod(
            "validate-filesize",
            function(v, elm) {
                if(elm == 'justuploaded'){ 
                    debugger;
                    return false;
                }
                return true;
            },
            $.mage.__("File size must be 2MB and below.")
        );

        $.validator.addMethod(
            "unknownerror-upload",
            function(v, elm) {
                return false;
            },
            $.mage.__("You may have uploaded a large file.")
        );

        $.validator.addMethod(
            "validatefiles",
            function(v, elm) {
                if(jQuery("#main_presc_scanned").find("input").length < 1){
                    return false;
                }
                
                return true;
            },
            $.mage.__("Please attach your prescription.")
        );
    }
});