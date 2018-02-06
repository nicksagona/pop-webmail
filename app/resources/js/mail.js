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

pop.openMailWindow = function(href, name, opts, id) {
    $('#subject-span-' + id).prop('class', 'responsive-lg');
    $('#env-icon-' + id).prop('class', 'gray-link fa fa-envelope-open-o');

    var htmlTitle = $('title')[0].innerHTML;
    var number     = htmlTitle.substring(htmlTitle.indexOf('(') + 1);
    number         = parseInt(number.substring(0, number.indexOf(')')));
    var htmlTitle1 = htmlTitle.substring(0, htmlTitle.indexOf('('));
    var htmlTitle2 = htmlTitle.substring(htmlTitle.indexOf(')') + 1);

    if (number > 1) {
        number--;
        var newTitle = htmlTitle1 + ' (' + number.toString() + ')';
    } else {
        var newTitle = htmlTitle1;
    }

    $('title')[0].innerHTML = newTitle + ' ' + htmlTitle2;
    $('h1.title-header')[0].innerHTML = newTitle;

    return pop.openWindow(href, name, opts);
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

pop.openMailForm = function(form) {
    $('div.mail-form-div').css('display', 'none');
    $(form).fadeIn();
    return false;
};

pop.closeMailForm = function(a) {
    $($(a).parent().parent()).fadeOut();
    return false;
};

pop.showPassword = function(a, type) {
    if ($('#' + type + '_password').prop('type') == 'password') {
        $('#' + type + '_password').prop('type', 'text');
        $(a)[0].innerHTML = 'Hide';
    } else {
        $('#' + type + '_password').prop('type', 'password');
        $(a)[0].innerHTML = 'Show';
    }
    return false;
};

pop.showContent = function(a, type) {
    if (type == 'html') {
        $('#html-link').prop('class', 'nav-link active');
        $('#text-link').prop('class', 'nav-link');
        $('#html-content').show();
        $('#text-content').hide();
    } else {
        $('#html-link').prop('class', 'nav-link');
        $('#text-link').prop('class', 'nav-link active');
        $('#html-content').hide();
        $('#text-content').show();
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
        $('#dropzone').on('dragenter', function(e) {
            $(this).css('color', '#3daacc')
                .css('border', 'dashed 5px #3daacc')
                .css('background-color', '#ccf3ff');
            e.stopPropagation();
            e.preventDefault();
        });
        $('#dropzone').on('dragleave', function(e) {
            $(this).css('color', '#aaa')
                .css('border', 'dashed 5px #aaa')
                .css('background-color', '#eee');
            e.stopPropagation();
            e.preventDefault();
        });
    }
});