class dynamicContent {
    loadContent(page, js, title, path, data = null) {
        if (!Array.isArray(js))
            js = [js];
        var xhttp = new XMLHttpRequest();
        var xpage = "../content/pages/" + page;
        xhttp.open("POST", xpage, true);
        var me = this;
        this.info = [page, js.slice(), title, path];
        loadStatic('navbar');
        if (js[0] != "") {
            js.forEach(function(jsfile) {
                var xjs = "../content/pages/assets/js/" + jsfile;
                me.loadJS(xjs, false);
            });
        }
        xhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                me.updateURL(path, title);
                document.getElementById("dynamic-content").innerHTML = this.responseText;
            }
            if (this.status == 404) {
                me.fileNotFound();
            }
        };

        xhttp.setRequestHeader("Content-Type", "application/json");

        if (data) xhttp.send(JSON.stringify(data));
        else xhttp.send();
    }
    loadStaticContent(file, js, div, data = null) {
        const page = '../static/' + file;
        if (!Array.isArray(js))
            js = [js];
        var xhttp = new XMLHttpRequest();
        var xpage = "../content/static/" + page;
        xhttp.open("POST", xpage, true);
        var me = this;
        xhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                document.getElementById(div).innerHTML = this.responseText;
                if (js[0] != "") {
                    js.forEach(function(jsfile) {
                        const xjs = '../content/static/js/' + jsfile;
                        me.loadJS(xjs, false);
                    });
                }
            }
            if (this.status == 404) {
                me.fileNotFound();
            }
        };

        xhttp.setRequestHeader("Content-Type", "application/json");

        if (data) xhttp.send(JSON.stringify(data));
        else xhttp.send();
    }
    loadJS(FILE_URL, async = true) {
        var scriptEleExists = document.querySelector('script[src="'+FILE_URL+'"]'); // Does the script tag already exist?
        let scriptEle = document.createElement("script");
        
        scriptEle.setAttribute("src", FILE_URL);
        scriptEle.setAttribute("type", "text/javascript");
        scriptEle.setAttribute("async", async);
        
        document.body.appendChild(scriptEle);
        
        // success event 
        scriptEle.addEventListener("load", () => {
            // File loaded
        });
            // error event
        scriptEle.addEventListener("error", (ev) => {
            // error on loading file
            this.fileNotFound();
        });
    }
    updateURL(urlPath, title) {
        window.history.replaceState({}, "", "/"+urlPath);

        document.title = title;
    }
    fileNotFound() {
        const PAGES = JSON.parse(this.loadFile("../pages.txt"));
        this.loadContent(PAGES[".404"][0], PAGES[".404"][1], PAGES[".404"][2], "404");
    }
    loadFile(filePath) {
        if (localStorage.getItem(filePath)) {
            console.log("saved time!");
            return localStorage.getItem(filePath);
        }
        var result = null;
        var xmlhttp = new XMLHttpRequest();
        xmlhttp.open("GET", filePath, false);
        xmlhttp.send();
        if (xmlhttp.status==200) {
          result = xmlhttp.responseText;
        }
        localStorage.setItem(filePath, result);
        return result;
    }
}