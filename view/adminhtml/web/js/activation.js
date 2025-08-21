define([
    "jquery",
    "Magento_Ui/js/modal/alert",
    "mage/translate",
    "jquery/ui",
    'mage/validation'
], function ($, alert, $t) {
    "use strict";

    $.widget('ups.activation', {
        options: {
            ajaxUrl: '',
            activateBtn: '#ups_dashboard_general_activate',
            enable: '#ups_dashboard_general_enable',
            store_domain: '#ups_dashboard_general_store_domain',
            site_email: '#ups_dashboard_general_site_email',
            tc_checkbox_0: '#ups_dashboard_general_checkbox_tc_checkbox_0',
            tc_checkbox_1: '#ups_dashboard_general_checkbox_tc_checkbox_1',
            tc_checkbox_2: '#ups_dashboard_general_checkbox_tc_checkbox_2',
            hid_magento_url: '#magento_url',
            hid_magento_version: '#magento_version',
            hid_module_name: '#module_name',
            hid_module_version: '#module_version',
            hid_token: '#token',
            hid_consumer_key: '#consumer_key',
            hid_consumer_secret: '#consumer_secret',
            hid_apiurl: '#upsapiurl',
            ups_is_activate: '#ups_is_activate'
        },
        _create: function () {
            var self = this;

            $(this.options.activateBtn).click(function (e) {
                self._ajaxSubmit();
            });

            $('#removeIntegration').click(function (e) {
                if(confirm($.mage.__('Do you want to remove connection?'))) {
                    self._ajaxSubmitRmv();
                    return false;
                }
            });
        },

        _ajaxSubmitRmv: function () {
            $.ajax({
                url: this.options.ajaxUrl,
                data: {
                    integration_name: $('#integration_name').val(),
                    store_id: $('#store_id').val(),
                    integration_type: 'remove',
                },
                dataType: 'json',
                showLoader: true,
                success: function (result) {
                    // console.log(result);
                    var url = window.location.href;
                    var newUrl = url.split('?')[0] + '?ibtms=' + new Date().getTime();
                    window.location.href = newUrl;
                }
            });     
        },
        
        _ajaxSubmit: function () {

            var dataAll = {
                base_url: $(this.options.hid_magento_url).val() || '',
                admin_url: $("#admin_url").val() || '',
                version: $(this.options.hid_magento_version).val() || '',
                module: $(this.options.hid_module_name).val() || '',
                module_version: $(this.options.hid_module_version).val() || '',
                token: $(this.options.hid_token).val() || '',
                store_id: $('#store_id').val() || '',
                site_email: $(this.options.site_email).val() || ''
            };

            var ValidDataAll = {
                store_domain: '#ups_dashboard_general_store_domain',
                site_email: '#ups_dashboard_general_site_email',
                tc_checkbox_0: '#ups_dashboard_general_checkbox_tc_checkbox_0',
                tc_checkbox_1: '#ups_dashboard_general_checkbox_tc_checkbox_1',
                tc_checkbox_2: '#ups_dashboard_general_checkbox_tc_checkbox_2',
            };

            var allFieldsFilled = true;

            $.each(ValidDataAll, function(key, value) {
                var errorMessage = 'This is a required field.';
                var $element = $(value);
                $element.siblings('.mage-error').remove();
                $element.removeClass('mage-error');
                if ($element.attr('type') === 'text' || $element.attr('type') === 'email') {
                    if ($element.val() === '') {
                        allFieldsFilled = false;
                        $element.addClass('mage-error');
                        var $errorMessageContainer = $element.siblings('.mage-error');
                        if ($element.hasClass('validate-email')) {
                            var errorMessage = $.mage.__('Please enter a valid email address (Ex: johndoe@domain.com).');
                            $element.after('<label class="mage-error">' + errorMessage + '</label>');
                        }else{

                            if ($errorMessageContainer.length) {
                                $errorMessageContainer.text(errorMessage);
                            } else {

                                $element.after('<label class="mage-error">' + errorMessage + '</label>');
                            }
                        }  
                    }
                } else if ($element.attr('type') === 'checkbox') {
                    if (!$element.prop('checked')) {
                        allFieldsFilled = false;
                        $element.addClass('mage-error');
                        var $errorMessageContainer = $element.siblings('.mage-error');
                        if ($errorMessageContainer.length) {
                            $errorMessageContainer.text(errorMessage);
                        } else {
                            $element.after('<label class="mage-error">' + errorMessage + '</label>');
                        }
                    }
                }
            });

            if (allFieldsFilled) {

                var config_form = $("#config-edit-form");
                var attr_action = config_form.attr('action');
                $.ajax({
                  type:'POST',
                  data:config_form.serialize(),
                  url:attr_action,
                  success:function(data) {
                  }
                });

                if ($("#token").val() == "") {
                    alert({
                        title: $.mage.__('Something went wrong..!'),
                        content: "",
                        actions: {
                            always: function(){
                                location.reload();
                            }
                        }
                    });
                }else{

                    var form = $('<form method="post" action="' + $("#upsapiurl").val() + '" id="activateups_form">');
                    $.each(dataAll, function(key, value) {
                        console.log(value);
                        $('<input>').attr({
                            'type': 'hidden',
                            'name': key,
                            'value': value
                        }).appendTo(form);
                    });

                    $('<input>').attr({'type': 'hidden','name': 'option1','value': '1'}).appendTo(form);
                    $('<input>').attr({'type': 'hidden','name': 'option2','value': '1'}).appendTo(form);
                    $('<input>').attr({'type': 'hidden','name': 'option3','value': '1'}).appendTo(form);
                
                    $.ajax({
                        url: this.options.ajaxUrl,
                        data: {
                            integration_name: $('#integration_name').val(),
                            store_id: $('#store_id').val(),
                            integration_type: 'save',
                        },
                        dataType: 'json',
                        showLoader: true,
                        success: function (result) {
                        }
                    });
                    $('#activateups_form').remove();
                    form.appendTo('body').submit();
                }

            }
        }
    });
    
    $(document).ready(function () {
        var activeMessage = $.mage.__('Connected Successfully');
        var deactiveMessage = $.mage.__('Deactive Connection');
        if ($('#conn_status').val() == '1') {
            var DisabeledataAll = {
                store_domain: $('#ups_dashboard_general_store_domain'),
                site_email: $('#ups_dashboard_general_site_email'),
                tc_checkbox_0: $('#ups_dashboard_general_checkbox_tc_checkbox_0'),
                tc_checkbox_1: $('#ups_dashboard_general_checkbox_tc_checkbox_1'),
                tc_checkbox_2: $('#ups_dashboard_general_checkbox_tc_checkbox_2'),
                hid_magento_url: $('#magento_url'),
                hid_magento_version: $('#magento_version'),
                hid_module_name: $('#module_name'),
                hid_module_version: $('#module_version'),
                hid_token: $('#token'),
                hid_consumer_key: $('#consumer_key'),
                hid_consumer_secret: $('#consumer_secret'),
                hid_apiurl: $('#upsapiurl'),
                ups_is_activate: $('#ups_is_activate')
                
            };
            $.each(DisabeledataAll, function(key, value) {
                if ($('#conn_status').val() == '1') {
                    value.attr('disabled','disabled');
                    if (value.attr('type') == 'checkbox'){
                        value.attr('checked','checked');
                    }
                }
            });
            
            $('.msg_upsdashboard_active').remove();
            $('#ups_dashboard_general table').before('<div class="messages msg_upsdashboard_active"><div class="message message-success"><div data-ui-id="messages-message-success">'+activeMessage+'</div></div></div>');
            $('#row_ups_dashboard_general_activate').hide();
            $('#row_ups_dashboard_general_hiddenfield').hide();
    
            $('#row_ups_dashboard_general_activate').after('<tr><td></td><td><button class="action-default ui-button ui-corner-all ui-widget action- scalable action-secondary" id="removeIntegration">'+deactiveMessage+'</button></td></tr>');
        }else{
            $('.msg_upsdashboard_active').remove();
        }
    });
    

    return $.ups.activation;
});
