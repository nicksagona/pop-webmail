/**
 * mail.js
 */
pop.changeMailbox = function() {
    window.location.href = '/mail/box/' + $('#accounts_select').val();
};

pop.changeFolder = function() {
    if (($('#folder-select').val() != '#') && ($('#folder-select').val() != '----')) {
        window.location.href = '/mail?folder=' + $('#folder-select').val();
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
        $.ajax('/mail/clean', {
            "method"  : "POST",
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

pop.openFolderForm = function(form) {
    $('div.folder-form-div').css('display', 'none');
    $(form).fadeIn();
    return false;
};

pop.closeFolderForm = function(a) {
    $($(a).parent().parent()).fadeOut();
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

    if ($('#move-folder-select')[0] != undefined) {
        $('#mail_process_action').change(function() {
            if ($('#mail_process_action').val() >= 2) {
                $('#move-folder-select').css('display', 'inline-block');
            } else {
                $('#move-folder-select').css('display', 'none');
            }
        });
    }

    if ($('#mail-process-form')[0] != undefined) {
        $('#mail-process-form').submit(function(){
            if ($('#mail_process_action').val() == -2) {
                return confirm('This action cannot be undone. Are you sure?');
            }
        });
    }

    if ($('#remove-folder-form-div')[0] != undefined) {
        $('#remove-folder-form-div').submit(function(){
            return confirm('This action cannot be undone. Are you sure?');
        });
    }


    if ($('#dropzone')[0] != undefined) {
        $('#dropzone').dropzone({
            url     : "/mail/upload",
            init    : function() {
                this.on('sending', function(file, xhr, formData) {
                    formData.append("folder", $('#dropzone').data('folder'));
                })
            },
            dictDefaultMessage : '<h3><i class="material-icons">attach_file</i> Drag and Drop Attachments Here.</h3>'
        });
    }
});