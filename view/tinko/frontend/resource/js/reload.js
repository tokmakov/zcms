if (window.location.hash.substr(0, 2) === '#!') {
    var pathname;
    if (/^\/catalog\/category\/[0-9]+/.test(window.location.pathname)) {
        pathname = window.location.pathname.match(/^\/catalog\/category\/[0-9]+/i)[0] + window.location.hash.slice(2);
        window.location.replace(pathname);
    } else if (/^\/catalog\/maker\/[0-9]+/.test(window.location.pathname)) {
        pathname = window.location.pathname.match(/^\/catalog\/maker\/[0-9]+/i)[0] + window.location.hash.slice(2);
        window.location.replace(pathname);
    } else if (/^\/catalog\/group\/[0-9]+/.test(window.location.pathname)) {
        pathname = window.location.pathname.match(/^\/catalog\/group\/[0-9]+/i)[0] + window.location.hash.slice(2);
        window.location.replace(pathname);   
    }
}
