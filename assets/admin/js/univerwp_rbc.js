jQuery(document).ready(function ($) {
    $('.action').click(function (e) {
        e.preventDefault();
        
        [field, state] = this.id.split("_");
        field_data = jQuery.parseJSON(atob($("#" + field + "_data").val()));

        if (field_data.value_type == "int") {
            if (state == "on"){
                value = $("#" + field + "_field_value").val();

                if (!(value.match(/^[0-9x]+$/))) {
                    ufw_notify(false, 'Please enter a number.');
                    return false;
                }
            }else{
                value = "x";
            }
        }

        if (field_data.value_type == "boolean") {
            if (state == "on") {
                value = "true";
            }else{
                value = "false";
            }
        }

        var data = {
            action: 'rbc_update',
            field: field,
            value: value,
            state: state,
            nonce: rbcXHR.nonce,
        };             

        $.post(rbcXHR.url, data, function (response) {
            ufw_notify(response.success, response.data);
            if (data.state == "off") {
                $("."+ data.field + "_off").addClass('uwp_hide');
                $("#revision_number").val('');
            }
            if (data.state == "on") {
                $("." + data.field + "_off").removeClass('uwp_hide');
                $("#" + data.field + "_field_value").val(data.value);
                $("#" + data.field + "_actual_value").html(data.value);
            }
        });
    });        
});