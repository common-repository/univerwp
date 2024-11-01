function ufw_notify(type, message){

    if (type == true || type == "success"){
        _type = "success";
    }
    if (type == false || type == "error") {
        _type = "error";
    }
    if (type == "warning") {
        _type = "warning";
    }

    new Notify({
        status: _type,
        title: 'UniverWP',
        text: message,
        effect: 'fade',
        speed: 300,
        customClass: '',
        customIcon: '',
        showIcon: true,
        showCloseButton: true,
        autoclose: true,
        autotimeout: 3000,
        gap: 20,
        distance: 20,
        type: 1,
        position: 'right bottom'
    })
}