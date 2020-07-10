function makeRequest (method, url, data) {
    return new Promise(function (resolve, reject) {
      var xhr = new XMLHttpRequest();
      xhr.open(method, url, true);
      xhr.onload = function () {
        if (this.status >= 200 && this.status < 300) {
          resolve(xhr.response);
        } else {
          reject({
            status: this.status,
            statusText: xhr.statusText
          });
        }
      };
      xhr.onerror = function () {
        reject({
          status: this.status,
          statusText: xhr.statusText
        });
      };
      xhr.send(data);
    });
}
window.addEventListener("load", () => {
    var serverInfo = JSON.parse(await makeRequest("GET", "server-info.php", null));
    /*
      Whatever you want to do with it
    */
})


// COD color codes parsing

function parseCODColorCodes(text) {
    text = '^7' + text;
    var regexp = /[\^][0-9]/g;
    var regexpRainbow = /[\^][:]/g;
    var letters = [];
    var colorCodes = {
        '^1' : '#FF3131',
        '^2' : '#86C000',
        '^3' : '#FFAD22',
        '^4' : '#0082BA',
        '^5' : '#25BDF1',
        '^6' : '#9750DD',
        '^7' : '#FFFFFF',
        '^8' : '#000000',
        '^9' : '#99A3B0',
        '^0' : '#000000',
        '^:' : 'rainbow'
    }
    // Puts each letter in a span element with the parsed color
    var nextColor = '#FFFFFF'
    for (var i = 0; i < text.length; i++) {
        if (i < text.length && ((text[i] + text[i + 1]).match(regexp) != null || (text[i] + text[i + 1]).match(regexpRainbow) != null)) {
            nextColor = colorCodes[text[i] + text[i + 1]];
        } else if (i > 1 && (text[i - 1] + text[i]).match(regexp) == null && (text[i - 1] + text[i]).match(regexpRainbow) == null) {
            var color = nextColor == 'rainbow' ? 'data-rainbow-text' : `style='color:${nextColor}'`;
            var letter = createElementFromHTML(`<span ${color}></span>`)
            letter.textContent = text[i];
            letters.push(letter);
        }
    }
    var text = createElementFromHTML(`<span></span>`);
    for (var i = 0; i < letters.length; i++) {
        text.appendChild(letters[i]);
    }
    return text;
    /*
        Example output:
         <span>
            <span style='color:#FF3131'>H</span>
            <span style='color:#FFAD22'>e</span>
            <span style='color:#25BDF1'>l</span>
            <span style='color:#99A3B0'>l</span>
            <span style='color:#9750DD'>o</span>
         </span>
    */
}
var rainbowColorIndex = 0;
setInterval(() => {
    // Cycles rainbow colors in elements with data-rainbowtext
    rainbowColorIndex > 360 ? rainbowColorIndex = 0 : ++rainbowColorIndex;
    document.querySelectorAll("*[data-rainbow-text]").forEach((r) => {
        var nextColor = hslToHex(rainbowColorIndex, 100, 50);
        r.style.color = nextColor;
    })
}, 100)
function hslToHex(h, s, l) {
    h /= 360;
    s /= 100;
    l /= 100;
    let r, g, b;
    if (s === 0) {
        r = g = b = l;
    } else {
        const hue2rgb = (p, q, t) => {
            if (t < 0) t += 1;
            if (t > 1) t -= 1;
            if (t < 1 / 6) return p + (q - p) * 6 * t;
            if (t < 1 / 2) return q;
            if (t < 2 / 3) return p + (q - p) * (2 / 3 - t) * 6;
            return p;
        };
        const q = l < 0.5 ? l * (1 + s) : l + s - l * s;
        const p = 2 * l - q;
        r = hue2rgb(p, q, h + 1 / 3);
        g = hue2rgb(p, q, h);
        b = hue2rgb(p, q, h - 1 / 3);
    }
    const toHex = x => {
        const hex = Math.round(x * 255).toString(16);
        return hex.length === 1 ? '0' + hex : hex;
    };
    return `#${toHex(r)}${toHex(g)}${toHex(b)}`;
}
function createElementFromHTML(htmlString) {
    var div = document.createElement('div');
    div.innerHTML = htmlString.trim();
    return div.firstChild; 
}
