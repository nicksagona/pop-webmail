/**
 * pop.js
 */
var pop = {
    cookie : {
        /** Function to save a cookie */
        save : function(name, value, options) {
            if (typeof value != 'string') {
                if (typeof value == 'number') {
                    value = value.toString();
                } else {
                    value = JSON.stringify(value);
                }
            }
            var cookie = name + '=' + encodeURI(value);

            // Parse options
            if (options != undefined) {
                cookie += (options.domain != undefined) ? ';domain=' + options.domain : '';
                cookie += (options.path != undefined) ? ';path=' + options.path : '';
                if (options.expire != undefined) {
                    var expdate = new Date();
                    expdate.setDate(expdate.getDate() + options.expire);
                    cookie += ';expires=' + expdate.toGMTString();
                }
            }

            // Set the cookie.
            document.cookie = cookie;

            return this;
        },

        /** Function to load a cookie value */
        load : function(name) {
            var value = '';

            // If the cookie is set, parse the value.
            if (document.cookie.length > 0) {
                if (name == null) {
                    value = {};
                    var ary = document.cookie.split(';');
                    for (var i = 0; i < ary.length; i++) {
                        var a = ary[i].trim().split('=');
                        var n = a[0];
                        var v = decodeURI(a[1]);
                        if ((v.indexOf('{') != -1) || (v.indexOf('[') != -1)) {
                            v = JSON.parse(decodeURIComponent(v));
                        }
                        value[n] = v;
                    }
                } else {
                    var start = document.cookie.indexOf(name + '=');

                    if (start != -1) {
                        start = start + name.length + 1;
                        var end = document.cookie.indexOf(';', start);
                        if (end == -1) {
                            end = document.cookie.length;
                        }

                        value = decodeURI(document.cookie.substring(start, end));
                        if ((value.indexOf('{') != -1) || (value.indexOf('[') != -1)) {
                            value = JSON.parse(decodeURIComponent(value));
                        }
                    }
                }
            }

            return value;
        },

        /** Function to remove a cookie or all cookies */
        remove : function(name) {
            if (name == null) {
                var c = this.load();
                for (var n in c) {
                    this.save(n, '', {"expire" : -1});
                }
            } else {
                this.save(name, '', {"expire" : -1});
            }

            return this
        }
    },

    storage : {
        length : 0,

        /** Function to save a value to local storage */
        save   : function(name, value) {
            if (typeof value != 'string') {
                if (typeof value == 'number') {
                    value = value.toString();
                } else {
                    value = JSON.stringify(value);
                }
            }
            window.localStorage[name] = value;
            this.length = window.localStorage.length;
            return this;
        },

        /** Function to get a value from local storage */
        load : function(name) {
            if (name == null) {
                var value = {};
                for (var n in window.localStorage) {
                    var v = window.localStorage[n];
                    if ((v.indexOf('{') != -1) || (v.indexOf('[') != -1)) {
                        v = JSON.parse(decodeURIComponent(v));
                    }
                    value[n] = v;
                }
            } else {
                if (window.localStorage[name] != undefined) {
                    var value = window.localStorage[name];
                    if ((value.indexOf('{') != -1) || (value.indexOf('[') != -1)) {
                        value = JSON.parse(decodeURIComponent(value));
                    }
                } else {
                    var value = null;
                }
            }

            return value;
        },

        /** Function to remove a value or all values from local storage */
        remove : function(name) {
            if (name == null) {
                window.localStorage.clear();
            } else {
                window.localStorage.removeItem(name);
            }
            this.length = window.localStorage.length;
            return this;
        }
    },

    openWindow : function(href, name, opts) {
        if (opts == undefined) {
            opts = {};
        }

        var wid  = (opts.width != undefined)    ? opts.width    : 800;
        var hgt  = (opts.height != undefined)   ? opts.height   : 600;
        var scr  = (opts.scroll != undefined)   ? opts.scroll   : 'yes';
        var res  = (opts.resize != undefined)   ? opts.resize   : 'no';
        var stat = (opts.status != undefined)   ? opts.status   : 'no';
        var loc  = (opts.location != undefined) ? opts.location : 'no';
        var mnu  = (opts.menu != undefined)     ? opts.menu     : 'no';
        var tool = (opts.tool != undefined)     ? opts.tool     : 'no';
        var x    = (opts.x != undefined)        ? opts.x        : (screen.width / 2) - (wid / 2);
        var y    = (opts.y != undefined)        ? opts.y : (screen.height / 2) - (hgt / 2);

        var options = 'width=' + wid + ',height=' + hgt + ',scrollbars=' + scr + ',resizable=' + res +
            ',status=' + stat + ',location=' + loc + ',menubar=' + mnu + ',toolbar=' + tool +
            ',left=' + x + ',top=' + y;

        window.open(href, name, options);
    }
};

$(document).ready(function(){
    if ($('#checkAll')[0] != undefined) {
        $('#checkAll').click(function(){
            var checkName  = $('#checkAll').data('name');
            var checkBoxes = $("input[name='" + checkName + "[]']");
            for (var i = 0; i < checkBoxes.length; i++) {
                if ($(this).prop('checked')) {
                    $(checkBoxes[i]).prop('checked', true);
                } else {
                    $(checkBoxes[i]).prop('checked', false);
                }
            }
        });
    }

    if ($('#login-form')[0] != undefined) {
        $('#login-form').submit(function(){
            $('#loading').show();
            return true;
        });
    }

    if ($('form.pop-paginator-form')[0] != undefined) {
        $('form.pop-paginator-form').submit(function() {
            $('#loading').show();
            return true;
        });
    }

    if ($('div.page-links')[0] != undefined) {
        $('div.page-links > a').click(function() {
            $('#loading').show();
            return true;
        });
    }

    if ($('#delete-form')[0] != undefined) {
        $('#delete-form').submit(function(){
            return confirm('This action cannot be undone. Are you sure?');
        });
    }
    if ($('#saved').data('saved') == 1) {
        $('#saved').fadeIn({complete : function(){
            $('#saved').delay(2000).fadeOut();
        }});
    }
    if ($('#removed').data('removed') == 1) {
        $('#removed').fadeIn({complete : function(){
            $('#removed').delay(2000).fadeOut();
        }});
    }
});

module.exports = pop;