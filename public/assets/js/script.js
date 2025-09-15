(function (root, doc) {
    var
    select = doc.querySelector('select'),
    embedCode = doc.querySelector('#embedCode'),
    embed = doc.querySelector('#embed'),
    sample = doc.querySelector('#sample'),
    specimen = doc.querySelector('#specimen'),
    specimenTest = doc.querySelector('#specimen-test'),
    _ = {
        loc: doc.location,
        path: doc.location.pathname,
        host: doc.location.hostname
    };

    var host = ('https:' == _.loc.protocol ? 'https://' : 'http://') + _.host + ':' + _.loc.port + _.path.substr(0, _.path.length - _.path.split('/')[_.path.split('/').length - 1].length) + 'css';

    var link = doc.createElement('link');
    link.rel = 'stylesheet';

    select.onchange = function (e) {
        (e || root.event).preventDefault();
        var family = select.value.replace(/(%20|%2B| )/g, '+');
        select.selected = true;
        if (family) {
            embedCode.style.display = 'block';
            sample.style.display = 'block';
            specimen.style.display = 'block';

            specimenTest.style.fontFamily = family;
            embed.innerHTML = '&ltlink rel=&quot;stylesheet&quot; href=&quot;' + host + '?family=' + family + '&quot; /&gt;';
        } else {
            embedCode.style.display = 'none';
            sample.style.display = 'none';
            specimen.style.display = 'none';
        }
        link.href = host + '?family=' + family + '&display=swap';
        console.log(host+'?family='+family);
    };

    doc.querySelector('head').appendChild(link);


    function ready (callback) {
        if ( root.addEventListener ) {
            root.addEventListener('DOMContentLoaded', function (event) {
                if (callback) callback();
            }, false);
        } else {    
            root.attachEvent('DOMContentLoaded', function (event) {
                if (callback) callback();
            });
        }
    }


    ready(function () {
        var q = function (value) {
            return doc.querySelector(value);
        };
        var family = q('select').value.replace(/(%20|%2B| )/g, '+');
        q('#btnThin').onclick = function () {
            q('#specimen-test p').style.cssText = 'font-family: ' + family + ';font-weight: 100;font-style: normal;';
        };
        q('#btnLight').onclick = function () {
            q('#specimen-test p').style.cssText = 'font-family: ' + family + ';font-weight: 300;font-style: normal;';
        };
        q('#btnRegular').onclick = function () {
            q('#specimen-test p').style.cssText = 'font-family: ' + family + ';font-weight: 400;font-style: normal;';
        };
        q('#btnItalic').onclick = function () {
            q('#specimen-test p').style.cssText = 'font-family: ' + family + ';font-weight: 400;font-style: italic;';
        };
        q('#btnMedium').onclick = function () {
            q('#specimen-test p').style.cssText = 'font-family: ' + family + ';font-weight: 500;font-style: normal;';
        };
        q('#btnMediumItalic').onclick = function () {
            q('#specimen-test p').style.cssText = 'font-family: ' + family + ';font-weight: 500;font-style: italic;';
        };
        q('#btnBold').onclick = function () {
            q('#specimen-test p').style.cssText = 'font-family: ' + family + ';font-weight: 700;font-style: normal;';
        };
        q('#btnBoldItalic').onclick = function () {
            q('#specimen-test p').style.cssText = 'font-family: ' + family + ';font-weight: 700;font-style: italic;';
        };
    });

})(window, document);

