define(['uiComponent', 'jquery'],
    function (Component, $) {
        'use strict';
        return Component.extend({
            initialize: function (config, node) {
                window.cookieconsent.initialise({
                    'palette': config['palette'],
                    'type': config['type'],
                    'content': config['content'],
                    window: '<div role="dialog" aria-live="polite" aria-label="cookieconsent" aria-describedby="cookieconsent:desc" class="cc-window {{classes}}"><div class="content">\x3c!--googleoff: all--\x3e{{children}}\x3c!--googleon: all--\x3e</div></div>',
                    cookie_group: config['cookie_group'],
                    cookie_tags: [],
                    cookie_setter: null,
                    cookie_getter: null,
                    old_cookie: document.cookie,
                    consisted: false,
                    cookies_overriden: false,
                    cookieDesc: null,
                    onPopupOpen: function () {
                        this.options.consisted = false;
                        this.options.overrideCookies();
                    },

                    getNew: function () {
                        var self = this;
                        var checkedCookies = $('input[name="coockie"]:checked');
                        checkedCookies.each(function (index, element) {
                            for (const group in self.cookie_group) {
                                if (self.cookie_group[group].name.toLowerCase() === element.value) {
                                    for (const cookie in self.cookie_group[group].cookies) {
                                        self.cookie_tags.push(self.cookie_group[group].cookies[cookie].name)
                                    }
                                }
                            }
                        });
                    },
                    overrideCookies: function () {
                        var self = this;

                        this.getNew();
                        if (self.consisted) {
                            let cookies = document.cookie.split(';');
                            for (let i = 0; i < cookies.length; i++) {
                                let name = self.getCookieName(cookies[i]);
                                if (self.cookie_tags.includes(name)) {
                                    document.cookie = self.deleteCookie(name);
                                }
                            }
                        }
                        if (self.cookies_overriden) {
                            return;
                        }
                        let classes = document.getElementsByClassName(
                            'cc-details-show');
                        classes[0].addEventListener('click', function () {
                            if (this.className === "cc-details-show show") {
                                this.className = "cc-details-show hide";
                                document.getElementsByClassName(
                                    'cc-details')[0].className = 'cc-details';
                            } else {
                                this.className = "cc-details-show show";
                                document.getElementsByClassName(
                                    'cc-details')[0].className = 'cc-details hidden';
                            }
                        }, false);

                        self.cookieDesc = Object.getOwnPropertyDescriptor(
                                Document.prototype, 'cookie') ||
                            Object.getOwnPropertyDescriptor(
                                HTMLDocument.prototype, 'cookie');
                        if (self.cookieDesc && self.cookieDesc.configurable) {
                            Object.defineProperty(document, 'cookie', {
                                get: function () {
                                    return self.cookieDesc.get.call(document);
                                },
                                set: function (val) {
                                    // if (val) {
                                        let name = self.getCookieName(val);
                                        if (self.cookie_tags.includes(name) &&
                                            !self.consisted) {
                                            val = self.getClearCookieData(val);
                                        // }
                                        self.cookieDesc.set.call(document, val);
                                    }
                                },
                            });
                        }
                        self.cookies_overriden = true;
                    },
                    deleteCookie: function (name) {
                        var whost = window.location.hostname.split('.');
                        while (whost.length >= 2) {
                            document.cookie = name + '=; path=/; expires=Thu, 01 Jan 1970 00:00:00 GMT; domain=' + whost.join('.') + ';';
                            document.cookie = name + '=; path=/; expires=Thu, 01 Jan 1970 00:00:00 GMT; domain=.' + whost.join('.') + ';';
                            whost = whost.slice(1)
                        }
                    },
                    getClearCookieData: function (val) {
                        let data = val.split(';');
                        var exst = false;
                        data.each(function (v, k) {
                            let expArray = v.split('=');
                            if (expArray.length === 2) {
                                if (expArray[0].trim().toLowerCase() === 'expires') {
                                    data[k] = 'expires=Thu, 01 Jan 1970 00:00:00 GMT';
                                    exst = true;
                                }
                            }
                        });
                        if (!exst) {
                            data.push('expires=Thu, 01 Jan 1970 00:00:00 GMT');
                        }
                        return data.join(';');
                    },
                    getCookieName: function (val) {
                        let eqPos = val.indexOf('=');
                        let name = eqPos > -1 ? val.substr(0,
                            eqPos) : val;
                        return name.trim();
                    },
                    onInitialise: function (status) {
                        this.options.consisted = status === 'allow';
                    },
                    onStatusChange: function (status, chosenBefore) {
                        this.options.consisted = status === 'allow';
                        this.options.overrideCookies();
                    },
                    onRevokeChoice: function () {
                        this.options.consisted = false;
                        this.options.overrideCookies();
                    },
                    afterInit: function (status) {
                        this.options.consisted = status === 'allow';
                        this.options.overrideCookies();
                    }
                });
            },
        });
    });
