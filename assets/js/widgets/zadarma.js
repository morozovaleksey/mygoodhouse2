var ZCallbackWidgetLinkId = '94865d45b52ee01b9388000e832ccea2';
var ZCallbackWidgetDomain = 'ss.zadarma.com';

if (typeof window.ZCallbackWidget === "undefined") {
    window.ZCallbackWidget = {
        options: {
            cookieName: "ZCallbackWidget",
            cookieNameTmpSid: "ZCallbackWidgetTmpId",
            showOnRate: 30,
            showOnRateAndExit: 20
        },
        clientCountry: "",
        clientCountryPrefix: "",
        isActive: true,
        events: [],
        tmpSessionId: "",
        sessionInfo: {sId: "", ref: "", start: null, show: 1, rate: 0, pages: []},
        javascriptFiles: [],
        javascriptFilesCounter: 0,
        formData: function (b) {
            var a = {};
            jQuery(b + " input").each(function (e, f) {
                var c = jQuery(f);
                var g = c.prop("name");
                var d = c.val();
                if (g != "") {
                    a[g] = d
                }
            });
            jQuery(b + " select").each(function (e, f) {
                var c = jQuery(f);
                var g = c.prop("name");
                var d = c.find("option:selected").val();
                if (g != "") {
                    a[g] = d
                }
            });
            return a
        },
        preload: function () {
            if (typeof ZCallbackWidgetLinkId === "undefined") {
                return
            }
            ZCallbackWidget.options.linkId = ZCallbackWidgetLinkId;
            var a = "https://" + ZCallbackWidgetDomain;
            ZCallbackWidget.options.baseUrl = a + "/callbackWidget/";
            ZCallbackWidget.options.apiUrl = a + "/callback/widget/";
            if (!window.hasOwnProperty("jQuery") || typeof jQuery === "undefined") {
                ZCallbackWidget.javascriptFiles.push("js/jquery-1.9.1.min.js");
                ZCallbackWidget.javascriptFiles.push("js/idle-timer.min.js")
            } else {
                if (!jQuery.hasOwnProperty("idleTimer")) {
                    ZCallbackWidget.javascriptFiles.push("js/idle-timer.min.js")
                }
            }
            ZCallbackWidget.javascriptFiles.push("js/jquery.cookie.min.js");
            ZCallbackWidget.javascriptFiles.push("js/template.min.js");
            ZCallbackWidget.javascriptFiles.push("js/raty/jquery.raty.min.js");
            ZCallbackWidget.loadNextJavascript()
        },
        loadNextJavascript: function () {
            if (ZCallbackWidget.javascriptFilesCounter >= ZCallbackWidget.javascriptFiles.length) {
                ZCallbackWidget.initialize()
            } else {
                ZCallbackWidget.loadJavascript(ZCallbackWidget.javascriptFiles[ZCallbackWidget.javascriptFilesCounter], function () {
                    ZCallbackWidget.javascriptFilesCounter++;
                    ZCallbackWidget.loadNextJavascript()
                })
            }
        },
        loadJavascript: function (d, a) {
            if (typeof d === "undefined") {
                a();
                return
            }
            var c = document.getElementsByTagName("head")[0];
            var b = document.createElement("script");
            b.type = "text/javascript";
            b.src = ZCallbackWidget.options.baseUrl + d + "?unq=" + Math.floor(Math.random(0, 1000) * 1000);
            b.onload = a;
            c.appendChild(b)
        },
        initialize: function () {
            if (typeof Prototype !== "undefined") {
                jQuery.noConflict()
            }
            var c = new Date(new Date().getFullYear(), 0, 1).getTimezoneOffset();
            var b = new Date(new Date().getFullYear(), 5, 1).getTimezoneOffset();
            b = (c != b) ? 1 : 0;
            ZCallbackWidget.tmpSessionId = jQuery.zcbwCookie(ZCallbackWidget.options.cookieNameTmpSid);
            var a = {
                linkId: ZCallbackWidget.options.linkId,
                timezoneOffset: c,
                isDst: b,
                tmpSessionId: ZCallbackWidget.tmpSessionId
            };
            jQuery.ajax({
                url: ZCallbackWidget.options.apiUrl + "initialize",
                cache: false,
                data: a,
                dataType: "jsonp",
                type: "GET",
                success: function (d) {
                    ZCallbackWidget.initializeReply(d)
                }
            })
        },
        initializeReply: function (b) {
            if (!b.hasOwnProperty("success")) {
                console.log("Zadarma Widget - wrong initialize reply");
                jQuery(document).trigger("ZadarmaWidget.error");
                return
            }
            if (!ZCallbackWidget.isActive) {
                return
            }
            console.log(b.text);
            if (b.success) {
                if (!ZCallbackWidget.tmpSessionId) {
                    ZCallbackWidget.tmpSessionId = b.tmpSessionId;
                    jQuery.zcbwCookie(ZCallbackWidget.options.cookieNameTmpSid, ZCallbackWidget.tmpSessionId, {
                        expires: 1,
                        path: "/"
                    })
                }
                ZCallbackWidget.clientCountry = b.clientCountry;
                ZCallbackWidget.clientCountryPrefix = b.clientCountryPrefix;
                for (var a in b.options) {
                    ZCallbackWidget.options[a] = b.options[a]
                }
                for (var a in ZCallbackWidget.options.pages) {
                    ZCallbackWidget.options.pages[a] = ZCallbackWidget.cleanUrl(ZCallbackWidget.options.pages[a])
                }
                if (ZCallbackWidget.options.layout["css"] == "custom") {
                    ZCallbackWidget.start()
                } else {
                    ZCallbackWidget.loadStyles()
                }
            }
        },
        loadStyles: function () {
            var a = "style.php?color=" + ZCallbackWidget.options.layout["color"] + "&radius=" + ZCallbackWidget.options.layout["radius"] + "&opacity=" + ZCallbackWidget.options.layout["opacity"] + "&position=" + ZCallbackWidget.options.layout["position"] + "&x_offset=" + ZCallbackWidget.options.layout["x_offset"] + "&y_offset=" + ZCallbackWidget.options.layout["y_offset"] + "&v=2";
            ZCallbackWidget.loadStyle(a, function () {
                ZCallbackWidget.start()
            })
        },
        loadStyle: function (d, b) {
            if (typeof d === "undefined") {
                b();
                return
            }
            var c = document.getElementsByTagName("head")[0];
            var a = document.createElement("link");
            a.rel = "stylesheet";
            a.type = "text/css";
            a.href = ZCallbackWidget.options.baseUrl + d;
            a.onload = b;
            c.appendChild(a)
        },
        start: function () {
            if (!ZCallbackWidget.isActive) {
                return
            }
            jQuery("body").append(ZCallbackWidgetTemplate.main());
            jQuery(".zcwPopup-bg, .zcwPopup-close").on("click", ZCallbackWidget.closeCallbackDialog);
            jQuery(document).on("keyup", function (a) {
                if (a.keyCode == 27 && ZCallbackWidget.isCallbackDialogActive) {
                    ZCallbackWidget.closeCallbackDialog()
                }
            });
            if (ZCallbackWidget.options.showButton == 1) {
                jQuery("body").append(ZCallbackWidgetTemplate.miniButton());
                jQuery("#zcwMiniButton").on("click", ZCallbackWidget.showCallback)
            }
            ZCallbackWidget.getSessionInfo();
            if (ZCallbackWidget.sessionInfo.show == 0) {
                return
            }
            ZCallbackWidget.catchActivity();
            ZCallbackWidget.catchPageVisit();
            ZCallbackWidget.catchMouseActivity();
            ZCallbackWidget.catchScroll();
            ZCallbackWidget.catchExit();
            jQuery(document).trigger("ZadarmaWidget.start")
        },
        stop: function () {
            ZCallbackWidget.isActive = false;
            if (window.hasOwnProperty("jQuery") && typeof jQuery != "undefined") {
                jQuery(".zcwPopup").remove();
                jQuery(".zcwPopup-bg").remove();
                jQuery("#zcwMiniButton").remove()
            }
            console.info("Zadarma widget - disabled");
            jQuery(document).trigger("ZadarmaWidget.stop")
        },
        getSessionInfo: function () {
            var a = jQuery.zcbwCookie(ZCallbackWidget.options.cookieName);
            if (a) {
                ZCallbackWidget.sessionInfo = JSON.parse(a)
            }
            console.info("Zadarma Widget - current rate: " + ZCallbackWidget.sessionInfo.rate)
        },
        setSessionInfo: function () {
            jQuery.zcbwCookie(ZCallbackWidget.options.cookieName, JSON.stringify(ZCallbackWidget.sessionInfo), {
                expires: 1,
                path: "/"
            })
        },
        resetSessionInfo: function () {
            ZCallbackWidget.sessionInfo.sId = "";
            ZCallbackWidget.sessionInfo.start = null;
            ZCallbackWidget.sessionInfo.rate = 0;
            ZCallbackWidget.sessionInfo.pages = [];
            ZCallbackWidget.setSessionInfo();
            ZCallbackWidget.catchPageVisit()
        },
        isCallbackDialogActive: false,
        showCallback: function () {
            ZCallbackWidget.showCallbackDialogByType("byUser")
        },
        dialogType: "",
        showCallbackDialogByType: function (b) {
            ZCallbackWidget.dialogType = b;
            if (b == "byUser") {
                ZCallbackWidget.sessionInfo.show = 1
            }
            if (ZCallbackWidget.sessionInfo.show == 0 || ZCallbackWidget.isCallbackDialogActive) {
                return
            }
            var a = false;
            if (b == "byRate") {
                a = ZCallbackWidget.sessionInfo.rate >= ZCallbackWidget.options.showOnRate
            } else {
                if (b == "byRateAndExit") {
                    a = ZCallbackWidget.sessionInfo.rate >= ZCallbackWidget.options.showOnRateAndExit
                } else {
                    if (b == "byUser") {
                        a = true
                    }
                }
            }
            if (a) {
                ZCallbackWidget.isCallbackDialogActive = true;
                ZCallbackWidget.log("show", {
                    rate: ZCallbackWidget.sessionInfo.rate,
                    refererUrl: ZCallbackWidget.sessionInfo.ref,
                    start: ((new Date().getTime()) - ZCallbackWidget.sessionInfo.start) / 1000,
                    type: b
                });
                ZCallbackWidget.showCallbackDialog()
            }
        },
        setSessionId: function (a) {
            ZCallbackWidget.sessionInfo.sId = a
        },
        showCallbackDialog: function () {
            if (ZCallbackWidget.isWorkingHours()) {
                ZCallbackWidget.dialogActive(ZCallbackWidget.dialogType == "byRateAndExit")
            } else {
                ZCallbackWidget.dialogAfterHours(ZCallbackWidget.dialogType == "byRateAndExit")
            }
        },
        closeCallbackDialog: function () {
            if (ZCallbackWidget.options.showButton == 1) {
                jQuery("#zcwMiniButton").show()
            }
            jQuery(".zcwPopup-bg").hide();
            jQuery(".zcwPopup").hide();
            if (ZCallbackWidget.sessionInfo.show == 1) {
                ZCallbackWidget.log("close", {})
            }
            ZCallbackWidget.isCallbackDialogActive = false;
            ZCallbackWidget.sessionInfo.show = 0;
            ZCallbackWidget.setSessionInfo();
            jQuery(document).trigger("ZadarmaWidget.closed")
        },
        fireEvent: function (c, a, b) {
            if (!ZCallbackWidget.isActive) {
                return false
            }
            if (ZCallbackWidget.events.indexOf(c) === -1) {
                a = a || c;
                b = b || ZCallbackWidget.options[a];
                ZCallbackWidget.sessionInfo.rate = ZCallbackWidget.sessionInfo.rate * 1 + b * 1;
                ZCallbackWidget.setSessionInfo();
                ZCallbackWidget.events.push(c);
                console.info("Zadarma widget - increase rate: " + c + " (+" + b + "), current rate: " + ZCallbackWidget.sessionInfo.rate);
                ZCallbackWidget.showCallbackDialogByType("byRate");
                return true
            } else {
                return false
            }
        },
        activityTimer: null,
        activityTime: 0,
        activityStartTime: 0,
        activityCount30: 0,
        activityTimerCallback: function () {
            if (ZCallbackWidget.activityStartTime == 0) {
                return
            }
            ZCallbackWidget.activityTime += (new Date().getTime() - ZCallbackWidget.activityStartTime) / 1000;
            ZCallbackWidget.activityStartTime = new Date().getTime();
            if (ZCallbackWidget.activityTime >= 60) {
                ZCallbackWidget.fireEvent("activeDuringMinute")
            }
            if (Math.floor((ZCallbackWidget.activityTime - 60) / 30) > ZCallbackWidget.activityCount30) {
                ZCallbackWidget.activityCount30++;
                ZCallbackWidget.fireEvent("activeEach30SecondsOverMinute #" + ZCallbackWidget.activityCount30, "activeEach30SecondsOverMinute")
            }
        },
        catchActivity: function () {
            ZCallbackWidget.activityStartTime = new Date().getTime();
            ZCallbackWidget.activityTimer = window.setInterval(ZCallbackWidget.activityTimerCallback, 1000);
            jQuery("body").idleTimer(10000);
            jQuery("body").on("idle.idleTimer", function () {
                ZCallbackWidget.activityStartTime = 0;
                window.clearInterval(ZCallbackWidget.activityTimer);
                ZCallbackWidget.activityTimer = null
            });
            jQuery("body").on("active.idleTimer", function () {
                ZCallbackWidget.activityStartTime = new Date().getTime();
                ZCallbackWidget.activityTimer = window.setInterval(ZCallbackWidget.activityTimerCallback, 1000)
            })
        },
        cleanUrl: function (a) {
            if (typeof a == "string") {
                a = a.replace(/^([^#]+)#.*$/, "$1");
                a = a.replace(/^[^\/]+\/\/[^\/]+\/(.+)$/, "$1")
            }
            return a
        },
        catchPageVisit: function () {
            var a = ZCallbackWidget.cleanUrl(document.location.href);
            if (ZCallbackWidget.sessionInfo.pages.length == 0) {
                ZCallbackWidget.sessionInfo.ref = document.referrer;
                ZCallbackWidget.sessionInfo.start = new Date().getTime()
            }
            if (ZCallbackWidget.sessionInfo.pages.indexOf(a) != -1) {
                return
            }
            if (ZCallbackWidget.sessionInfo.pages.length == 5) {
                ZCallbackWidget.sessionInfo.pages.shift()
            }
            ZCallbackWidget.sessionInfo.pages.push(a);
            ZCallbackWidget.setSessionInfo();
            if (ZCallbackWidget.sessionInfo.pages.length == 2) {
                ZCallbackWidget.fireEvent("visitOtherPage")
            }
            if (ZCallbackWidget.sessionInfo.pages.length == 4) {
                ZCallbackWidget.fireEvent("visit3Pages")
            }
            if (ZCallbackWidget.options.pages.indexOf(a) != -1) {
                ZCallbackWidget.fireEvent("visitSpecialPage")
            }
        },
        mouseX: null,
        mouseY: null,
        mouseDistance: 0,
        catchMouseActivity: function () {
            jQuery("body").on("mousemove", function (c) {
                if (ZCallbackWidget.mouseX !== null) {
                    var b = Math.abs(ZCallbackWidget.mouseX - c.pageX);
                    var g = Math.abs(ZCallbackWidget.mouseY - c.pageY);
                    var f = Math.pow(Math.pow(b, 2) + Math.pow(g, 2), 0.5);
                    ZCallbackWidget.mouseDistance += f
                }
                ZCallbackWidget.mouseX = c.pageX;
                ZCallbackWidget.mouseY = c.pageY;
                var e = jQuery(document).width();
                var a = jQuery(document).height();
                if (ZCallbackWidget.mouseDistance > ZCallbackWidget.options.mouseDistance * (e + a) / 100) {
                    ZCallbackWidget.fireEvent("mouseActivity");
                    jQuery("body").off("mousemove")
                }
            })
        },
        catchScroll: function () {
            jQuery(document).on("scroll", function () {
                var c = jQuery(document).scrollTop();
                var a = jQuery(document).height() - jQuery(window).height();
                var b = (c * 100) / a;
                if (b >= 99) {
                    ZCallbackWidget.fireEvent("scroll25Percent 100%", "scroll25Percent");
                    jQuery(document).off("scroll")
                } else {
                    if (b >= 75) {
                        ZCallbackWidget.fireEvent("scroll25Percent 75%", "scroll25Percent")
                    } else {
                        if (b >= 50) {
                            ZCallbackWidget.fireEvent("scroll25Percent 50%", "scroll25Percent")
                        } else {
                            if (b >= 25) {
                                ZCallbackWidget.fireEvent("scroll25Percent 25%", "scroll25Percent")
                            }
                        }
                    }
                }
            })
        },
        isExit: false,
        catchExit: function () {
            jQuery("body").on("mouseout mouseleave", function (a) {
                if (!ZCallbackWidget.isExit && a.relatedTarget == null && (a.pageX < 10 || a.pageY < 10 || a.pageX > (jQuery(window).width() - 10) || a.pageY > (jQuery(window).height() - 10))) {
                    ZCallbackWidget.isExit = true;
                    ZCallbackWidget.showCallbackDialogByType("byRateAndExit")
                }
            });
            jQuery("body").on("mouseenter", function () {
                ZCallbackWidget.isExit = false
            })
        },
        log: function (e, f, c) {
            var a = {
                linkId: ZCallbackWidget.options.linkId,
                sessionId: ZCallbackWidget.sessionInfo.sId,
                tmpSessionId: ZCallbackWidget.tmpSessionId,
                url: document.location.href,
                action: e
            };
            if (typeof f == "object") {
                for (var b in f) {
                    a[b] = f[b]
                }
            }
            var d = ZCallbackWidget.sessionInfo.sId == "";
            jQuery.ajax({
                url: ZCallbackWidget.options.apiUrl + "log",
                cache: false,
                data: a,
                dataType: "jsonp",
                type: "GET",
                success: function (g) {
                    ZCallbackWidget.setSessionId(g.id);
                    if (d) {
                        ZCallbackWidget.setSessionInfo()
                    }
                    if (typeof c == "function") {
                        c()
                    }
                    if (g.onSuccess == "showCallbackDialog") {
                        ZCallbackWidget.showCallbackDialog()
                    }
                }
            })
        },
        initializeAndShowDialog: function (c, b, a) {
            jQuery(".zcwPopup-title").html(c);
            jQuery(".zcwPopup-description").html(b);
            jQuery(".zcwPopup-form").html(a);
            ZCallbackWidget.autoReplace8to7();
            if (ZCallbackWidget.options.showButton == 1) {
                jQuery("#zcwMiniButton").hide()
            }
            jQuery(".zcwPopup").show();
            jQuery(".zcwPopup-bg").show()
        },
        autoReplace8to7: function () {
            if (ZCallbackWidget.options.language != "ru" || ZCallbackWidget.clientCountry != "RU" || ZCallbackWidget.options.layout.auto8to7 != "y") {
                return false
            }
            var a = jQuery("input[name=n]", ".zcwPopup");
            if (a.length > 0) {
                a.off("keyup");
                a.on("keyup", function () {
                    var b = jQuery(this).val();
                    if (b.length == 11 && b.substring(0, 1) == "8") {
                        b = b.replace(/^8/, "7");
                        jQuery(this).val(b)
                    }
                })
            }
        },
        dialogActive: function (isOnExit) {
            var title = !isOnExit ? ZCallbackWidget.options.texts.title_on : ZCallbackWidget.options.texts.title_away;
            var description = !isOnExit ? ZCallbackWidget.options.texts.on : ZCallbackWidget.options.texts.away;
            description = description.replace(/#countdown#/, ZCallbackWidget.options.countdown);
            var form = ZCallbackWidgetTemplate.active();
            ZCallbackWidget.initializeAndShowDialog(title, description, form);
            jQuery(".zcwPopup-deferred").on("click", function () {
                ZCallbackWidget.dialogDeferred()
            });
            jQuery("form", ".zcwPopup").bind("submit", function () {
                jQuery.ajax({
                    url: jQuery("form", ".zcwPopup").attr("action"),
                    cache: false,
                    data: ZCallbackWidget.formData(".zcwPopup form"),
                    dataType: "jsonp",
                    type: "GET",
                    beforeSend: function (xhr) {
                        jQuery("input[name=n]", ".zcwPopup").removeClass("zcwInputError");
                        var n = jQuery("input[name=n]", ".zcwPopup").val();
                        n = n.replace(/[^0-9]/g, "");
                        if (n.length == 0) {
                            jQuery("input[name=n]", ".zcwPopup").addClass("zcwInputError");
                            return false
                        }
                        jQuery("input, button, select", ".zcwPopup").attr("disabled", "disabled");
                        jQuery(".zcwPopup-deferred").hide();
                        if (ZCallbackWidget.options.analytics_js != "") {
                            var jsEval = ZCallbackWidget.options.analytics_js;
                            jsEval = jsEval.replace(/PHONE_NUMBER/g, n);
                            jsEval = jsEval.replace(/PAGE_URL/g, window.location.href);
                            eval(jsEval)
                        }
                        jQuery(document).trigger("ZadarmaWidget.call")
                    },
                    success: function (reply) {
                        if (reply.success) {
                            ZCallbackWidget.countdown(reply.text)
                        } else {
                            ZCallbackWidget.error(reply.text);
                            if (reply.action == "deferred") {
                                window.setTimeout(function () {
                                    ZCallbackWidget.dialogDeferred()
                                }, 1000)
                            }
                        }
                    }
                });
                return false
            });
            jQuery(".zcwPopup-recall").on("click", function () {
                jQuery("input, button, select", ".zcwPopup").attr("disabled", false);
                jQuery(".zcwPopup-countdown").html(ZCallbackWidget.showCountdown(ZCallbackWidget.options.countdown));
                jQuery(".zcwPopup-countdown").hide();
                jQuery("#zcwPopup-callresult").hide();
                jQuery("#zcwPopup-busy").hide();
                jQuery(".zcwPopup-deferred").show();
                jQuery("form", ".zcwPopup").show()
            });
            jQuery.fn.raty.defaults.path = ZCallbackWidget.options.baseUrl + "js/raty/";
            jQuery("#zcwPopup-raty").raty({
                click: function (score, evt) {
                    jQuery("#zcwPopup-raty").raty("readOnly", true);
                    ZCallbackWidget.log("rate", {score: score, n: jQuery("input[name=n]", ".zcwPopup").val()})
                }
            });
            jQuery(document).trigger("ZadarmaWidget.opened")
        },
        error: function (a) {
            jQuery("#zcwPopup-error").html(a);
            jQuery("#zcwPopup-error").show();
            jQuery("#zcwPopup-error").delay(3000).hide("slow", function () {
                jQuery("input, button, select", ".zcwPopup").attr("disabled", false);
                jQuery(".zcwPopup-deferred").show()
            })
        },
        dialogDeferred: function () {
            var c = ZCallbackWidget.options.texts.title_deferred;
            var b = ZCallbackWidget.options.texts.deferred;
            var a = ZCallbackWidgetTemplate.deferred();
            ZCallbackWidget.initializeAndShowDialog(c, b, a);
            jQuery(".zcwPopup-active").on("click", function () {
                ZCallbackWidget.showCallbackDialog()
            });
            ZCallbackWidget.initializeCallRequestForm();
            jQuery(document).trigger("ZadarmaWidget.opened")
        },
        dialogAfterHours: function (d) {
            var c = !d ? ZCallbackWidget.options.texts.title_off : ZCallbackWidget.options.texts.title_away;
            var b = ZCallbackWidget.options.texts.off;
            var a = ZCallbackWidgetTemplate.afterHours();
            ZCallbackWidget.initializeAndShowDialog(c, b, a);
            ZCallbackWidget.initializeCallRequestForm();
            jQuery(document).trigger("ZadarmaWidget.opened")
        },
        lastSunOfMonth: function (c, d, e) {
            for (var b = e; b > 0; b--) {
                var a = new Date(c, d - 1, b);
                if (a.getDay() == 0) {
                    return a
                }
            }
        },
        isDstDate: function (a) {
            var c = ZCallbackWidget.lastSunOfMonth(a.getFullYear(), 3, 31);
            var b = ZCallbackWidget.lastSunOfMonth(a.getFullYear(), 10, 31);
            return a.getTime() > c.getTime() && a.getTime() < b.getTime()
        },
        initializeCallRequestForm: function () {
            jQuery("form", ".zcwPopup").bind("submit", function () {
                jQuery.ajax({
                    url: jQuery("form", ".zcwPopup").attr("action"),
                    cache: false,
                    data: ZCallbackWidget.formData(".zcwPopup form"),
                    dataType: "jsonp",
                    type: "GET",
                    beforeSend: function (xhr) {
                        jQuery("input[name=n]", ".zcwPopup").removeClass("zcwInputError");
                        var n = jQuery("input[name=n]", ".zcwPopup").val();
                        n = n.replace(/[^0-9]/g, "");
                        if (n.length == 0) {
                            jQuery("input[name=n]", ".zcwPopup").addClass("zcwInputError");
                            return false
                        }
                        jQuery("input, button, select", ".zcwPopup").attr("disabled", "disabled");
                        if (ZCallbackWidget.options.deferred_analytics_js != "") {
                            var jsEval = ZCallbackWidget.options.deferred_analytics_js;
                            jsEval = jsEval.replace(/PHONE_NUMBER/g, n);
                            jsEval = jsEval.replace(/PAGE_URL/g, window.location.href);
                            var timezoneOffset = jQuery("input[name=timezoneOffset]").val();
                            var isDst = jQuery("input[name=isDst]").val();
                            var day = jQuery("select[name=day] option:selected").val();
                            var hour = jQuery("select[name=hour] option:selected").val();
                            var minute = jQuery("select[name=minute] option:selected").val();
                            var match = day.match(/^(\d{4})-(\d{2})-(\d{2})$/);
                            if (match) {
                                var date = new Date(parseInt(match[1]), parseInt(match[2]) - 1, parseInt(match[3]), parseInt(hour), parseInt(minute), 0);
                                var time = date.getTime() / 1000;
                                if (parseInt(isDst) === 1 && ZCallbackWidget.isDstDate(date)) {
                                    time -= 60 * 60
                                }
                                if (ZCallbackWidget.options.isDst === 1 && ZCallbackWidget.isDstDate(date)) {
                                    time += 60 * 60
                                }
                                time += timezoneOffset * 60;
                                time -= ZCallbackWidget.options.timezoneOffset * 60;
                                jsEval = jsEval.replace(/TIMESTAMP/g, Math.round(time))
                            }
                            eval(jsEval)
                        }
                        jQuery(document).trigger("ZadarmaWidget.call_request")
                    },
                    success: function (reply) {
                        if (reply.success) {
                            ZCallbackWidget.callRequestFormAnswer()
                        } else {
                            ZCallbackWidget.error(reply.text)
                        }
                    }
                });
                return false
            })
        },
        callRequestFormAnswer: function () {
            var a = ZCallbackWidgetTemplate.request();
            jQuery(".zcwPopup-form").html(a)
        },
        isWorkingHours: function () {
            var c = new Date();
            var b = c.getDay();
            var e = c.getHours() * 60 + c.getMinutes();
            var a = ZCallbackWidget.options.working_hours[b];
            var h = false;
            var g = 0;
            var f = 0;
            for (var d = 0; d < a.length; d++) {
                g = a[d][0] * 60 + a[d][1];
                f = a[d][2] * 60 + a[d][3];
                if (e > g && f > e) {
                    h = true;
                    break
                }
            }
            return h
        },
        workingHours: {},
        getWorkingHours: function () {
            var c = new Date();
            var n = c.getDay();
            var b = (c.getHours() + 1) * 60;
            var a = null;
            var m = 0;
            var p = 0;
            var q = 0;
            ZCallbackWidget.workingHours = {};
            for (var g = 0; g < 7; g++) {
                var l = c.getDate();
                a = ZCallbackWidget.options.working_hours[n];
                for (var h = 0; h < a.length; h++) {
                    p = a[h][0] * 60 + a[h][1];
                    q = a[h][3] > 0 ? (a[h][2] * 60 + a[h][3]) : ((a[h][2] - 1) * 60);
                    if (q > b || g > 0) {
                        if (!ZCallbackWidget.workingHours.hasOwnProperty(l)) {
                            var f = c.getFullYear() + "-m-d";
                            var o = "d.m";
                            var k = l;
                            var e = c.getMonth() + 1;
                            if (k < 10) {
                                k = "0" + k
                            }
                            if (e < 10) {
                                e = "0" + e
                            }
                            f = f.replace(/d/, k);
                            o = o.replace(/d/, k);
                            f = f.replace(/m/, e);
                            o = o.replace(/m/, e);
                            ZCallbackWidget.workingHours[l] = {day: o, dayFull: f, hours: []};
                            m++
                        }
                        p = Math.max(b / 60, a[h][0]);
                        q = (a[h][3] > 0) ? a[h][2] : (a[h][2] - 1);
                        ZCallbackWidget.workingHours[l]["hours"].push([p, q])
                    }
                }
                if (m >= 2) {
                    break
                }
                c = new Date(c.valueOf());
                c.setDate(c.getDate() + 1);
                b = 0;
                n += 1;
                if (n >= 7) {
                    n = 0
                }
            }
            for (var r in ZCallbackWidget.workingHours) {
                ZCallbackWidget.workingHours[r]["hours"].sort(function (i, d) {
                    console.log(i, d);
                    if (i[0] > d[0]) {
                        return 1
                    } else {
                        if (i[0] < d[0]) {
                            return -1
                        }
                    }
                    return 0
                })
            }
            return ZCallbackWidget.workingHours
        },
        workingHoursOptions: function (a) {
            var b = "";
            for (var f in ZCallbackWidget.workingHours) {
                if (ZCallbackWidget.workingHours[f]["dayFull"] == a) {
                    for (var c in ZCallbackWidget.workingHours[f]["hours"]) {
                        for (var e = ZCallbackWidget.workingHours[f]["hours"][c][0]; e <= ZCallbackWidget.workingHours[f]["hours"][c][1]; e++) {
                            b += '<option value="' + e + '">' + ((e > 9) ? e : ("0" + e)) + "</option>"
                        }
                    }
                }
            }
            return b
        },
        onChangeWorkingHours: function () {
            var a = jQuery("select[name=day] option:selected", ".zcwPopup").val();
            jQuery("select[name=hour]", ".zcwPopup").html(ZCallbackWidget.workingHoursOptions(a))
        },
        countdownTimer: null,
        countdownStart: 0,
        showCountdown: function (d) {
            d = Math.round(d * 100) / 100;
            var a = Math.floor(d / 60);
            var c = Math.floor(d - a * 60);
            var b = Math.round((d - a * 60 - c) * 100);
            var e = "";
            if (a > 0) {
                e += (a > 9) ? a : ("0" + a)
            } else {
                e += "0"
            }
            e += ":";
            if (c > 0) {
                e += (c > 9) ? c : ("0" + c)
            } else {
                e += "00"
            }
            e += ".";
            if (b > 0) {
                e += (b > 9) ? b : ("0" + b)
            } else {
                e += "00"
            }
            return e
        },
        countdown: function () {
            jQuery(".zcwPopup-countdown").show();
            ZCallbackWidget.countdownStart = new Date().getTime();
            ZCallbackWidget.countdownTimer = window.setInterval(function () {
                var b = new Date().getTime() - ZCallbackWidget.countdownStart;
                b /= 1000;
                b = ZCallbackWidget.options.countdown - b;
                if (b <= 0) {
                    b = 0;
                    window.clearInterval(ZCallbackWidget.countdownTimer);
                    ZCallbackWidget.countdownTimer = null;
                    jQuery("form", ".zcwPopup").hide();
                    jQuery("#zcwPopup-callresult").show();
                    jQuery("input, button, select", ".zcwPopup").attr("disabled", false)
                }
                var a = ZCallbackWidget.showCountdown(b);
                jQuery(".zcwPopup-countdown").html(a)
            }, 100)
        },
        failCall: function () {
            jQuery("input, button, select", ".zcwPopup").attr("disabled", "disabled");
            jQuery("#zcwPopup-busy").show();
            ZCallbackWidget.log("fail", {n: jQuery("input[name=n]", ".zcwPopup").val()})
        },
        successCall: function () {
            jQuery("input, button, select", ".zcwPopup").attr("disabled", "disabled");
            if (ZCallbackWidget.options.redirect_url != "") {
                document.location.href = ZCallbackWidget.options.redirect_url
            } else {
                jQuery("#zcwPopup-rate").show()
            }
        }
    };
    ZCallbackWidget.preload()
} else {
    ZCallbackWidget.stop();
    console.error("Zadarma Widget - you try to install several widgets on one page!")
}
;
