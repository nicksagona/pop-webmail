/**
 * mail.js
 */
pop.expressSettings = {
    "gmail" : {
        "imap_host"     : "imap.gmail.com",
        "imap_port"     : 993,
        "imap_flags"    : "/ssl",
        "smtp_host"     : "smtp.gmail.com",
        "smtp_port"     : 587,
        "smtp_security" : "tls"
    },
    "office-365" : {
        "imap_host"     : "outlook.office365.com",
        "imap_port"     : 993,
        "imap_flags"    : "/ssl",
        "smtp_host"     : "smtp.office365.com",
        "smtp_port"     : 587,
        "smtp_security" : "tls"
    }
};

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
    if ($('#env-icon-' + id).prop('class') != 'gray-link fa fa-envelope-open-o') {
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
    }

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

pop.expressSetup = function() {
    var account = $('#express_setup').val();
    if (account != '----') {
        if (pop.expressSettings[account] != undefined) {
            $('#imap_host').val(pop.expressSettings[account]["imap_host"]);
            $('#imap_port').val(pop.expressSettings[account]["imap_port"]);
            $('#imap_flags').val(pop.expressSettings[account]["imap_flags"]);
            $('#smtp_host').val(pop.expressSettings[account]["smtp_host"]);
            $('#smtp_port').val(pop.expressSettings[account]["smtp_port"]);
            $('#smtp_security').val(pop.expressSettings[account]["smtp_security"]);
        }
    } else {
        $('#imap_host').val('');
        $('#imap_port').val('');
        $('#imap_flags').val('');
        $('#smtp_host').val('');
        $('#smtp_port').val('');
        $('#smtp_security').val('');

    }
};

Dropzone.autoDiscover = false;

$(document).ready(function(){
    if ($('#accounts_select')[0] != undefined) {
        $('#accounts_select').change(pop.changeMailbox);
    }

    if ($('#folder-select')[0] != undefined) {
        $('#folder-select').change(function(){
            $('#loading').show();
            pop.changeFolder();
        });
    }

    if ($('#mail-search-form')[0] != undefined) {
        $('#mail-search-form').submit(function() {
            $('#loading').show();
            return true;
        });
    }

    if ($('#advanced-search-form')[0] != undefined) {
        $('#advanced-search-form').submit(function() {
            $('#loading').show();
            return true;
        });
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

    if ($('#mail-forward-attachments')[0] != undefined) {
        $('#mail-compose-form-fieldset-2 > dl > dd:nth-child(8)').append($('#mail-forward-attachments')[0].innerHTML);
        var spans = $('#mail-forward-attachments > span');
        var aids  = [];
        for (var i = 0; i < spans.length; i++) {
            aids.push($(spans[i]).data('aid'));
        }
        if (aids.length > 0) {
            $('#attachments').val(aids.join(','));
        }
    }

    if ($('#express_setup')[0] != undefined) {
        $('#express_setup').change(pop.expressSetup);
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