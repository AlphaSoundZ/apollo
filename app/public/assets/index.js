const XdynamicContent = new dynamicContent();

function handleClick(path) {
    const PAGES = JSON.parse(XdynamicContent.loadFile("../pages.txt"));
    var UrlPath = path;
    if (PAGES[path][3])
        UrlPath = PAGES[path][3];
    XdynamicContent.loadContent(PAGES[path][0], PAGES[path][1], PAGES[path][2], UrlPath); // 1. param: page-file-name, 2. param: json-file-name, 3. param: document Title, 4. param: url path
}

function loadStatic(id) {
    const el = document.getElementById(id);
    if (XdynamicContent.info[2] != "Login" && XdynamicContent.info[2] != "404") {
        XdynamicContent.loadStaticContent(el.dataset.page, el.dataset.js, id);
    }
    else {
        el.innerHTML = "";
    }
}