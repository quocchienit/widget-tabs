
    var d = jQuery(".tabs-nav"),
        e = d.children("li");
    jQuery(".tab-content");
    d.each(function() {
        var b = jQuery(this);
        b.next().children(".tab-content").stop(!0, !0).hide().first().show(), b.children("li").first().addClass("active").stop(!0, !0).show()
    }), e.on("click", function(b) {
        var c = jQuery(this);
        c.siblings().removeClass("active").end().addClass("active"), c.parent().next().children(".tab-content").stop(!0, !0).hide().siblings(c.find("a").attr("href")).fadeIn(), b.preventDefault()
    }), jQuery(".entry-like a").on("click", function(b) {
        b.preventDefault();
        var c = jQuery(this),
            d = c.attr("id");
        return !c.hasClass("active") && void a.post(sitebox.ajaxurl, {
            action: "sitebox-likes",
            likes_id: d
        }, function(a) {
            c.html(a).addClass("active").attr("title", sitebox.rated)
        })
    })