! function (t) {
    t((function () {
        var e, c, n, o = "",
            _ = window.location.href,
            a = void 0 !== document.title ? document.title : "",
            r = {};

        function i(t) {
            return !!r[t] && r[t]
        }

        function s(t, e) {
            r[t] = e;
            var c = JSON.stringify(r);
            localStorage.setItem("ht_ctc_storage", c)
        }
        if (localStorage.getItem("ht_ctc_storage") && (r = localStorage.getItem("ht_ctc_storage"), r = JSON.parse(r)), "undefined" != typeof ht_ctc_chat_var) o = ht_ctc_chat_var;
        else try {
            if (document.querySelector(".ht_ctc_chat_data")) {
                var d = t(".ht_ctc_chat_data").attr("data-settings");
                o = JSON.parse(d)
            }
        } catch (t) {
            o = {}
        }

        function l(e) {
            var c = "",
                n = e + "_data";
            if (void 0 !== window[e]) c = window[e];
            else try {
                if (document.querySelector("." + n)) {
                    var o = t("." + n).attr("data-settings");
                    c = JSON.parse(o)
                }
            } catch (t) {
                c = {}
            }
            return t("." + n).remove(), c
        }

        function u(e) {
            o = e.detail.ctc, display_chat = e.detail.display_chat, ht_ctc_chat = e.detail.ht_ctc_chat;
            var c = "",
                n = o.bh,
                _ = (f.getDay(), f.getHours()),
                a = f.getMinutes();
            if ("always" == n) c = "yes";
            else if ("timebase" == n) {
                var r = f.getDay();
                "0" == r && (r = 7);
                var i = "d" + r;
                if (o[i]) {
                    var s = i + "_et";
                    if (o[d = i + "_st"] && o[s]) {
                        var d = o[d],
                            l = (s = o[s], d.split(":")),
                            u = s.split(":"),
                            g = l[0],
                            m = l[1],
                            h = u[0],
                            p = u[1];
                        g <= _ && _ <= h ? (c = "yes", g == _ ? m > a && (c = "no") : _ == h && a > p && (c = "no")) : c = "no"
                    } else c = "yes"
                } else c = "no"
            }

            function v() {
                if ("" !== o.timedelay || "" !== o.scroll) {
                    if ("" !== o.timedelay && setTimeout((() => {
                        display_chat(ht_ctc_chat)
                    }), 1e3 * o.timedelay), "" !== o.scroll) {
                        window.addEventListener("scroll", (function t() {
                            var e = document.documentElement,
                                c = document.body,
                                n = "scrollTop",
                                _ = "scrollHeight",
                                a = (e[n] || c[n]) / ((e[_] || c[_]) - e.clientHeight) * 100;
                            a >= o.scroll && (display_chat(ht_ctc_chat), window.removeEventListener("scroll", t))
                        }), !1)
                    }
                } else display_chat(ht_ctc_chat)
            }

            function y() {
                if (document.querySelector(".ctc_woo_schedule")) {
                    var e = t(".ctc_woo_schedule").attr("data-dt");
                    t(".ctc_woo_schedule").css("display", e)
                }
            }
            "yes" == c ? (v(), y()) : "no" == c && function () {
                o.off_cta && (t(".ht-ctc-chat .ctc_cta").text(o.off_cta), t(".ctc_chat.ctc_woo_schedule .ctc_cta").text(o.off_cta));
                o.off_num && (o.number = o.off_num, o.r_nums = "");
                o.off_hide || (v(), y())
            }()
        }

        function g() {
            var t = new Date,
                e = t.getTime() + 6e4 * t.getTimezoneOffset(),
                c = parseFloat(o.tz);
            return new Date(e + 36e5 * c)
        }
        e = t(".ht_ctc_multi_agent"), c = l("ht_ctc_multi_agent_main"), n = "yes", e.each((function (e, o) {
            var _ = l(t(o).attr("data-key")),
                a = g().getDay();
            "0" == a && (a = 7);
            var r = [];
            for (e = a; e <= 7; e++) r.push(e);
            if (7 !== r.length) {
                var i = 7 - r.length;
                for (e = 1; e <= i; e++) r.push(e)
            }
            var s = "yes";
            if (_.timings && "always" == _.timings) s = "yes";
            else if (_.timings && "set" == _.timings)
                if (_.time_sets) {
                    s = "no";
                    var d = _.time_sets,
                        u = m(),
                        f = 1;
                    d.forEach((t => {
                        t.stm <= u && u <= t.etm && 1 == f && (s = "yes", f++)
                    }))
                } else s = "no", n = "no";

            function h(e) {
                var a = t(o);
                t(o).css({
                    order: "1"
                }), a.find(".g_multi_box").css({
                    opacity: "0.5"
                }), a.find(".ctc_g_agent_tags").css({
                    color: "unset"
                }), "nochat" == e && t(o).css({
                    "pointer-events": "none"
                });
                var r = function () {
                    var t = "";
                    if (_.time_sets) {
                        var e = m(),
                            n = _.time_sets,
                            o = "",
                            a = 1;
                        if (n.forEach((t => {
                            e <= t.stm && 1 == a && (o = t.stm, a++)
                        })), "" == o) {
                            var r = 10080 - e;
                            next_week_start_minute = n[0].stm, i = r + next_week_start_minute
                        } else var i = o - e;
                        (t = i) < 60 ? t = (t = Math.round(t)) + " " + (t < 2 ? c.ctc_minute : c.ctc_minutes) : 24 > (t /= 60) ? t = (t = Math.round(t)) + " " + (t < 2 ? c.ctc_hour : c.ctc_hours) : (t /= 24, t = (t = Math.round(t)) + " " + (t < 2 ? c.ctc_day : c.ctc_days))
                    }
                    return t
                }();
                "no" !== n && (a.find(".ctc_g_agent_tags .ctc_agent_next_time").append(r), a.find(".ctc_g_agent_tags .ctc_agent_next_time").css({
                    display: "flex"
                }))
            }
            "no" == s && c.agent_offline && ("chat" == c.agent_offline ? h("chat") : "nochat" == c.agent_offline ? h("nochat") : o.remove())
        })), g_hook_v = o.hook_v ? o.hook_v : "", g_hook_url = o.hook_url ? o.hook_url : "", document.addEventListener("ht_ctc_event_number", (function (t) {
            if ((o = t.detail.ctc).r_nums && o.r_nums[0]) {
                var e = o.r_nums;
                o.number = e[Math.floor(Math.random() * e.length)]
            }
        })), t(document).on("click", ".ht_ctc_chat_greetings_for_forum_link", (function (e) {
            t(".ht_ctc_chat_greetings_forum_link").trigger("click");
            var c = document.getElementById("ctc_pro_greetings_form");
            c && !1 === c.checkValidity() && t("#ctc_pro_greetings_form [type=checkbox][required]:not(:checked)").closest("div").fadeOut("1").fadeIn("1")
        })), o.form_no_duplicates && t(document).on("input", ".ctc_pro_greetings_form", (function (t) {
            s("form_data_change", (new Date).getTime())
        })), document.querySelector("#ctc_opt_g_form") && (t("#ctc_opt_g_form").on("change", (function (e) {
            t("#ctc_opt_g_form").is(":checked") && (t(".ctc_opt_g_form").hide(500), s("g_optin", "y"))
        })), i("g_optin") ? (t("#ctc_opt_g_form").prop("checked", !0), t(".ctc_opt_g_form").hide()) : t("#ctc_opt_g_form").prop("required", !0)), t(document).on("click", ".ht_ctc_chat_greetings_box_link_multi", (function (e) {
            function c() {
                var e = t(".ctc_g_content").attr("data-agentstyle");
                if ("card-1" == e ? t(".ctc_g_content").css({
                    padding: "4px 0px",
                    "background-color": "#f8f8f8"
                }) : "7_1" == e && t(".ctc_g_content").css({
                    padding: "4px 0px 8px 0px"
                }), t(".ctc_g_sentbutton").hide(), "card-1" == e) {
                    var c = t(".ctc_g_message_box");
                    t(c).removeClass("ctc_g_message_box"), t(c).css({
                        "background-color": "",
                        "text-align": "inherit",
                        padding: "4px 0px"
                    }), t(".ctc_g_heading_for_main_content").append(c)
                } else "7_1" == e && t(".ctc_g_message_box").css({
                    "background-color": "",
                    "text-align": "center",
                    padding: "4px 0px"
                });
                t(".ctc_g_agents").slideToggle("slow", (function () { }))
            }
            e.preventDefault(), document.querySelector("#ctc_opt_multi") ? t("#ctc_opt_multi").is(":checked") || i("g_optin") ? c() : t(".ctc_opt_in").show(400).fadeOut("1").fadeIn("1") : c(), document.querySelector("#ctc_opt_multi") && t("#ctc_opt_multi").on("change", (function (e) {
                t("#ctc_opt_multi").is(":checked") && (t(".ctc_opt_in").hide(100), s("g_optin", "y"), setTimeout((() => {
                    c()
                }), 500))
            }))
        })), document.querySelector(".add_ctc_chat") && ((t("#ctc_opt_multi").is(":checked") || i("g_optin")) && t(".add_ctc_chat").addClass("ctc_chat").removeClass("add_ctc_chat"), t(document).on("click", ".add_ctc_chat", (function (e) {
            t(".ctc_opt_in").show(400).fadeOut("1").fadeIn("1")
        })), t("#ctc_opt_multi").on("change", (function (e) {
            t("#ctc_opt_multi").is(":checked") && (t(".ctc_opt_in").hide(100), s("g_optin", "y"), t(".add_ctc_chat").addClass("ctc_chat").removeClass("add_ctc_chat"))
        }))), t(document).on("submit", ".ctc_pro_greetings_form", (function (e) {
            e.preventDefault();
            try {
                t(".ctc_g_its_checkbox").each((function () {
                    t(this).is(":checked") && t(this).siblings(['type="hidden"']).prop("disabled", !0)
                }))
            } catch (e) { }
            var c = t(".ctc_pro_greetings_form").find(".ht_ctc_g_form_field").serializeArray();
            if (document.querySelector(".ctc_g_field_add_to_prefilled")) {
                var n = o.pre_filled,
                    a = o.pre_filled,
                    r = t(".ctc_pro_greetings_form").find(".ctc_g_field_add_to_prefilled").serializeArray(),
                    d = "\n";
                for (var l of r.values()) {
                    var u = ".ctc_pro_greetings_form [name='" + (S = l.name) + "']",
                        f = S;
                    t(u)[0].hasAttribute("data-name") && (f = t(u).attr("data-name")), "" !== (E = l.value) && (d += f + ": " + E + "\n")
                }
                a += d, o.pre_filled = a
            }
            t(".ht_ctc_chat_greetings_forum_link").addClass("ht_ctc_chat_greetings_box_link"), setTimeout((() => {
                t(".ht_ctc_chat_greetings_box_link").trigger("click")
            }), 20), setTimeout((() => {
                t(".ht_ctc_chat_greetings_forum_link").removeClass("ht_ctc_chat_greetings_box_link"), o.pre_filled && n && (o.pre_filled = n)
            }), 500);
            var m = t(".ctc_g_form_keys #ht_ctc_pro_greetings_nonce").val(),
                p = "no";
            try {
                if (o.form_no_duplicates) {
                    var v = (new Date).getTime(),
                        y = i("form_data_sent"),
                        b = Number.isInteger(y) ? Math.abs(v - y) : 60001,
                        k = i("form_data_change");
                    b < 6e4 ? Number.isInteger(k) && y >= k && (p = "y") : Number.isInteger(k) || s("form_data_change", v)
                }
            } catch (e) { }
            if ("y" == p);
            else if (o.ajaxurl && o.g1_form_email && t.ajax({
                url: o.ajaxurl,
                type: "POST",
                data: {
                    action: "ctc_pro_greetings_form",
                    form_data: c,
                    nonce: m
                },
                success: function (t) {
                    s("form_data_sent", (new Date).getTime())
                },
                error: function (t) { }
            }), o.g1_form_webhook) {
                var w = o.g1_form_webhook,
                    x = {};
                for (var l of c.values()) {
                    var S = l.name,
                        E = l.value;
                    x[S] = E
                }
                decoded_url = h(_), x.ctc_page_url = decoded_url;
                var T = g();
                if (x.ctc_wp_date = T.toDateString(), x.ctc_wp_time = T.getHours() + ":" + T.getMinutes() + ":" + T.getSeconds(), o.webhook_format && "json" == o.webhook_format) var I = x;
                else I = JSON.stringify(x);
                t.ajax({
                    url: w,
                    type: "POST",
                    mode: "no-cors",
                    data: I,
                    success: function (t) {
                        s("form_data_sent", (new Date).getTime())
                    },
                    error: function (t) { }
                })
            }
            try {
                t(".ctc_g_hidden_for_checkbox").each((function () {
                    t(this).prop("disabled", !1)
                }))
            } catch (e) { }
        })), document.addEventListener("ht_ctc_event_hook", (function (t) {
            o = t.detail.ctc, number = t.detail.number;
            var e = g(),
                c = e.toDateString() + ", " + e.getHours() + ":" + e.getMinutes() + ":" + e.getSeconds();
            if (o.hook_url) {
                var n = o.hook_url ? o.hook_url : "";
                if (decoded_url = h(_), n = (n = (n = n.replace(/\{url}/gi, decoded_url)).replace(/\{number}/gi, number)).replace(/\{time}/gi, c), o.hook_v) {
                    hook_values = g_hook_v;
                    var a = {},
                        r = 1;
                    hook_values.forEach((t => {
                        t = (t = (t = t.replace(/\{url}/gi, decoded_url)).replace(/\{number}/gi, number)).replace(/\{time}/gi, c), a["value" + r] = t, r++
                    })), o.hook_url = n, o.hook_v = a
                }
            }
        })), document.addEventListener("ht_ctc_event_display", (function (e) {
            (o = e.detail.ctc).display_user_base ? function (e) {
                var c = (o = e.detail.ctc).display_user_base ? o.display_user_base : "",
                    n = "logged_out";
                document.querySelector("body").classList.contains("logged-in") && (n = "logged_in"), "logged_in" == c ? "logged_in" == n ? u(e) : o.ajaxurl && t.ajax({
                    url: o.ajaxurl,
                    data: {
                        action: "ctc_pro_is_user_logged_in"
                    },
                    success: function (t) {
                        "yes" == t.data && u(e)
                    }
                }) : "logged_out" == c && "logged_out" == n && u(e)
            }(e) : u(e)
        })), document.addEventListener("ht_ctc_event_after_chat_displayed", (function (t) {
            if (o = t.detail.ctc, greetings_open = t.detail.greetings_open, greetings_close = t.detail.greetings_close, o.g_no_reopen && "user_closed" == i("g_user_action"));
            else if (o.g_time_action && setTimeout((() => {
                greetings_open("time_action")
            }), 1e3 * o.g_time_action), o.g_scroll_action) {
                window.addEventListener("scroll", (function t() {
                    var e = document.documentElement,
                        c = document.body,
                        n = "scrollTop",
                        _ = "scrollHeight";
                    (e[n] || c[n]) / ((e[_] || c[_]) - e.clientHeight) * 100 >= o.g_scroll_action && (greetings_open("scroll_action"), window.removeEventListener("scroll", t))
                }), !1)
            }
            if (document.querySelector(".ctc_greetings_now") && "IntersectionObserver" in window) {
                var e = window.innerHeight / 4 * -1;
                e = parseInt(e), rm = e + "px", rm = String(rm);
                var c = new IntersectionObserver((function (t) {
                    t.forEach((t => {
                        t.isIntersecting && (greetings_open("now"), c.unobserve(t.target))
                    }))
                }), {
                    rootMargin: rm
                });
                document.querySelectorAll(".ctc_greetings_now").forEach((t => {
                    c.observe(t, {
                        subtree: !0,
                        childList: !0
                    })
                }))
            }
        })), document.addEventListener("ht_ctc_event_analytics", (function (e) {
            if (o.gads_conversation && "undefined" != typeof gtag) {
                var c = o.gads_conversation;
                gtag("event", "conversion", {
                    send_to: c
                })
            }
            var n = o.nonce ? o.nonce : "";
            o.fb_conversion && t.ajax({
                url: o.ajaxurl,
                data: {
                    action: "ctc_pro_capi",
                    url: _,
                    title: a,
                    number: o.number,
                    nonce: n
                },
                type: "POST",
                success: function (t) { },
                error: function (t) { }
            })
        })), document.addEventListener("ht_ctc_event_number", (function (t) { })), document.querySelector(".ht_ctc_chat_greetings_box_layout") && t(".ht_ctc_chat_greetings_box_layout").css("background-color", "unset");
        var f = g();

        function m() {
            var t = g(),
                e = t.getDay();
            return "0" == e && (e = 7), 60 * (24 * (e - 1) + t.getHours()) + t.getMinutes()
        }

        function h(t) {
            new_value = t;
            try {
                new_value = decodeURI(t)
            } catch (t) { }
            return new_value
        }
        m()
    }))
}(jQuery);