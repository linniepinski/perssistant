tinymce.PluginManager.add("wordcount", function (a) {
    function b() {
        a.theme.panel.find("#wordcount").text(["Symbols: {0}", e.getCount()])
    }
    var c, d, e = this;
    c = a.getParam("wordcount_countregex", /./g), d = a.getParam("wordcount_cleanregex", /(<br[^>]*>\s*)+/i), a.on("init", function () {
        var c = a.theme.panel && a.theme.panel.find("#statusbar")[0];
        c && window.setTimeout(function () {
            c.insert({type: "label", name: "wordcount", text: ["Symbols: {0}", e.getCount()], classes: "wordcount", disabled: a.settings.readonly}, 0), a.on("setcontent beforeaddundo", b), a.on("keyup", function (a) {
                32 == a.keyCode && b()
            })
        }, 0)
    }), e.getCount = function () {
        var b = a.getContent({format: "raw"}), e = 0;
        if (b) {
            b = b.replace(/\.\.\./g, " "), b = b.replace(/<.[^<>]*?>/g, "").replace(/&nbsp;|&#160;/gi, " "), b = b.replace(/(\w+)(&#?[a-z0-9]+;)+(\w+)/i, "$1$3").replace(/&.+?;/g, " "), b = b.replace(d, "");
            var f = b.match(c);
            f && (e = f.length)
        }
        return e
    }
});