// Build a small, self-hosted Lucide subset from the icon names used in the project.
// The upstream library and license header are retained in tools/vendor/lucide.min.js.
const fs = require('fs'), path = require('path'), vm = require('vm');
const root = path.resolve(__dirname, '..');
const upstream = fs.readFileSync(path.join(__dirname, 'vendor/lucide.min.js'), 'utf8');
const context = {}; vm.createContext(context); vm.runInContext(upstream, context);
const available = {};
Object.entries(context.lucide.icons).forEach(([name, nodes]) => {
    available[name.replace(/([a-z0-9])([A-Z])/g, '$1-$2').toLowerCase()] = nodes;
});
const names = new Set(['circle', 'arrow-up-right', 'play', 'x', 'sparkles']);
function scan(folder) {
    for (const file of fs.readdirSync(folder, { withFileTypes: true })) {
        const full = path.join(folder, file.name);
        if (file.isDirectory()) scan(full);
        else if (/\.(php|js)$/.test(full)) {
            for (const match of fs.readFileSync(full, 'utf8').matchAll(/["']([a-z][a-z0-9-]+)["']/g)) if (available[match[1]]) names.add(match[1]);
        }
    }
}
['resources/views', 'config', 'app', 'database/seeders'].forEach(folder => scan(path.join(root, folder)));
const icons = Object.fromEntries([...names].sort().map(name => [name, available[name]]));
const runtime = `\nwindow.lucide = { createIcons() {\n  document.querySelectorAll('[data-lucide]').forEach(old => {\n    if (old.tagName.toLowerCase() === 'svg') return;\n    const nodes = icons[old.dataset.lucide] || icons.circle;\n    const svg = document.createElementNS('http://www.w3.org/2000/svg', 'svg');\n    for (const [key, value] of Object.entries({width:'24',height:'24',viewBox:'0 0 24 24',fill:'none',stroke:'currentColor','stroke-width':'2','stroke-linecap':'round','stroke-linejoin':'round','aria-hidden':'true',focusable:'false'})) svg.setAttribute(key,value);\n    for (const attribute of old.attributes) svg.setAttribute(attribute.name,attribute.value);\n    for (const [tag, attrs] of nodes) { const child=document.createElementNS('http://www.w3.org/2000/svg',tag); for (const [key,value] of Object.entries(attrs)) child.setAttribute(key,value); svg.append(child); }\n    old.replaceWith(svg);\n  });\n}};\n})();\n`;
const header = upstream.match(/\/\*![\s\S]*?\*\//)?.[0] || '// Lucide icons — ISC license; see tools/vendor/lucide.min.js.';
const result = header + '\n(() => { const icons = ' + JSON.stringify(icons) + ';' + runtime;
fs.writeFileSync(path.join(root, 'public/js/icons.js'), result);
console.log(`${names.size} icons, ${Buffer.byteLength(result)} bytes (upstream ${Buffer.byteLength(upstream)} bytes)`);
