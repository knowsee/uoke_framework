
function getAdminAjax(model, idList, type, ehandle) {
    if (!idList || (typeof idList == 'string' && idList < 1)) {
        return;
    }
    if (!type)
        type = 'json';
    if (!ehandle)
        ehandle = fucntion(response,status,xhr);
    var ModelUri = baseUri + 'adminStatus.php?act=getInfo&model=' + model;
    if (typeof idList == 'string') {
        var data = {'ajaxId': idList};
    } else {
        var data = idList;
    }
    $.ajax({
        type: 'GET',
        url: ModelUri,
        data: data,
        dataType: type,
        beforeSend: function() {
            $('#loading').html('loading....');
        },
        complete: function() {
            $('#loading').html('');
        },
        success: ehandle
    });
}