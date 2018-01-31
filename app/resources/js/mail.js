/**
 * mail.js
 */
pop.changeMailbox = function() {
    window.location.href = '/mail/box/' + $('#accounts_select').val();
};

pop.changeFolder = function() {
    if ($('#folder-select').val() != '#') {
        window.location.href = $('#folder-select').val();
    }
};

pop.toggleMailFolder = function(a) {
    if ($(a).next().css('display') == 'none') {
        $(a).next().show();
        $(a)[0].innerHTML = '<i class="material-icons icon-sm">expand_less</i>'
    } else {
        $(a).next().hide();
        $(a)[0].innerHTML = '<i class="material-icons icon-sm">expand_more</i>'
    }
    return false;
};

pop.closeMail = function() {
    if ($('#dropzone')[0] != undefined) {
        var user = pop.cookie.load('user');
        $.ajax('/api/mail/clean', {
            "method"  : "POST",
            "headers" : {"Authorization": "Bearer " + user.token},
            "data"    : {
                "folder" : $('#dropzone').data('folder')
            },
            "success" : function(data) {
                window.close();
            }
        });
    } else {
        window.close();
    }

    return false;
};

Dropzone.autoDiscover = false;

$(document).ready(function(){
    if ($('#accounts_select')[0] != undefined) {
        $('#accounts_select').change(pop.changeMailbox);
    }

    if ($('#folder-select')[0] != undefined) {
        $('#folder-select').change(pop.changeFolder);
    }

    if ($('a.current-folder')[0] != undefined) {
        $('a.current-folder').parent().parent().show();
    }

    if ($('#dropzone')[0] != undefined) {
        var user = pab.cookie.load('user');
        $('#dropzone').dropzone({
            url     : "/api/mail/upload",
            headers : {"Authorization" : "Bearer " + user.token},
            init    : function() {
                this.on('sending', function(file, xhr, formData) {
                    formData.append("folder", $('#dropzone').data('folder'));
                })
            },
            dictDefaultMessage : '<h3><i class="material-icons">attach_file</i> Drag and Drop Attachments Here.</h3>'
        });
    }
});