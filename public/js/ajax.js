
function ajax(form_id, btn_id, method, csrf_token, act_url, errmsg, timeout){
    if( timeout != undefined ){
        timeout = 10000;
    }

    if( method != undefined ){
        method = 'post';
    }

    $("#" + btn_id).on("click", function(event){
        event.preventDefault();
        $.ajax({
            url:$("#" + form_id).prop('action'),
            type:method,
            timeout:timeout,
            data:$("#" + form_id).serialize(),
            headers:{'X-CSRF-TOKEN':csrf_token},
            success:function(result_data) {
                $('[name=sendid]').val(result_data);
                $('#' + form_id).attr('action', act_url);
                $('#' + form_id).submit();
            },
            error:function(error) {
                $('#errmsg').text(errmsg);
            }
        });
        return false;
    });
}