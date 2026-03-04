<?php
$pageTitle = 'Bulk QR Generator';
require_once __DIR__ . '/includes/header.php';
requireLogin();
$types = getQRTypes();
?>
<div class="max-w-5xl mx-auto px-4 sm:px-6 py-10">
    <div class="mb-8 animate-fade-in-up"><h1 class="font-display text-3xl font-bold mb-1 text-slate-900">Bulk QR Generator</h1><p class="text-slate-500">Generate up to 100 QR codes at once from a list or CSV</p></div>
    <div class="grid lg:grid-cols-5 gap-8">
        <div class="lg:col-span-3 space-y-6">
            <div class="bg-white rounded-2xl p-6 border border-slate-100 shadow-soft animate-fade-in-up delay-100">
                <h3 class="font-display text-lg font-bold mb-4 flex items-center gap-2 text-slate-800"><i data-lucide="settings" class="w-5 h-5 text-brand-500"></i> Configuration</h3>
                <div class="grid sm:grid-cols-2 gap-4">
                    <div><label class="block text-sm font-semibold text-slate-600 mb-2">QR Type</label><select id="bulk-type" class="w-full px-4 py-3 input-field"><option value="url">URL / Website</option><option value="text">Plain Text</option><option value="phone">Phone Number</option><option value="email">Email Address</option></select></div>
                    <div><label class="block text-sm font-semibold text-slate-600 mb-2">Size</label><select id="bulk-size" class="w-full px-4 py-3 input-field"><option value="128">128×128</option><option value="256" selected>256×256</option><option value="512">512×512</option></select></div>
                    <div><label class="block text-sm font-semibold text-slate-600 mb-2">Foreground</label><input type="color" id="bulk-fg" value="#000000" class="w-full h-12 rounded-xl cursor-pointer border border-slate-200 bg-white p-1"></div>
                    <div><label class="block text-sm font-semibold text-slate-600 mb-2">Background</label><input type="color" id="bulk-bg" value="#ffffff" class="w-full h-12 rounded-xl cursor-pointer border border-slate-200 bg-white p-1"></div>
                </div>
            </div>
            <div class="bg-white rounded-2xl p-6 border border-slate-100 shadow-soft animate-fade-in-up delay-200">
                <div class="flex items-center gap-4 mb-4">
                    <button onclick="switchTab('manual')" id="tab-manual" class="px-4 py-2 rounded-xl text-sm font-semibold bg-brand-50 text-brand-700">Manual Entry</button>
                    <button onclick="switchTab('csv')" id="tab-csv" class="px-4 py-2 rounded-xl text-sm font-semibold text-slate-500 hover:bg-slate-50">CSV Upload</button>
                </div>
                <div id="panel-manual"><label class="block text-sm font-semibold text-slate-600 mb-2">Enter one item per line (max 100)</label>
                <textarea id="bulk-input" rows="12" class="w-full px-4 py-3 input-field font-mono text-sm" placeholder="https://example.com&#10;https://google.com&#10;...one per line"></textarea>
                <p class="text-xs text-slate-400 mt-2" id="line-count">0 items</p></div>
                <div id="panel-csv" class="hidden"><div class="border-2 border-dashed border-slate-200 rounded-xl p-8 text-center hover:border-brand-300 transition-colors cursor-pointer" onclick="document.getElementById('csv-file').click()"><i data-lucide="upload" class="w-10 h-10 text-slate-300 mx-auto mb-3"></i><p class="text-sm text-slate-500 mb-1">Drop your CSV file here or click to browse</p><input type="file" id="csv-file" accept=".csv,.txt" class="hidden" onchange="handleCSV(this)"></div><div id="csv-status" class="mt-3 text-xs"></div></div>
            </div>
            <button onclick="generateBulk()" class="w-full py-4 rounded-xl font-display font-bold btn-primary flex items-center justify-center gap-2 text-base animate-fade-in-up delay-300"><i data-lucide="zap" class="w-5 h-5"></i> Generate All QR Codes</button>
        </div>
        <div class="lg:col-span-2"><div class="sticky top-24 space-y-6">
            <div class="bg-white rounded-2xl p-6 border border-slate-100 shadow-soft">
                <h3 class="font-display text-xs font-bold text-slate-400 uppercase tracking-wider mb-4">Output</h3>
                <div id="bulk-status" class="text-center py-8"><div class="w-16 h-16 rounded-2xl bg-slate-50 flex items-center justify-center mx-auto mb-3"><i data-lucide="layers" class="w-8 h-8 text-slate-300"></i></div><p class="text-sm text-slate-400">QR codes will appear here</p></div>
                <div id="bulk-progress" class="hidden"><div class="flex items-center justify-between mb-2"><span class="text-sm text-slate-600 font-medium">Generating...</span><span id="progress-text" class="text-sm font-mono text-brand-600">0/0</span></div><div class="w-full h-2.5 rounded-full bg-slate-100 overflow-hidden"><div id="progress-bar" class="h-full rounded-full bg-gradient-to-r from-brand-400 to-brand-600 transition-all duration-300" style="width:0%"></div></div></div>
                <div id="bulk-complete" class="hidden text-center py-4"><div class="w-14 h-14 rounded-full bg-mint-50 flex items-center justify-center mx-auto mb-3"><i data-lucide="check-circle" class="w-7 h-7 text-mint-500"></i></div><p class="text-sm font-bold text-mint-600 mb-1" id="complete-text">Done!</p><p class="text-xs text-slate-400 mb-4">All QR codes generated</p><button onclick="downloadAllBulk()" class="w-full py-3 rounded-xl font-semibold btn-primary flex items-center justify-center gap-2"><i data-lucide="download" class="w-4 h-4"></i> Download All (ZIP)</button></div>
            </div>
            <div id="bulk-thumbnails" class="bg-white rounded-2xl p-4 border border-slate-100 shadow-soft hidden"><h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-3">Preview</h4><div id="thumb-grid" class="grid grid-cols-3 gap-2 max-h-64 overflow-y-auto"></div></div>
        </div></div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/jszip@3.10.1/dist/jszip.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/file-saver@2.0.5/dist/FileSaver.min.js"></script>
<script>
let generatedQRs=[];
function switchTab(t){document.getElementById('panel-manual').classList.toggle('hidden',t!=='manual');document.getElementById('panel-csv').classList.toggle('hidden',t!=='csv');document.getElementById('tab-manual').className=t==='manual'?'px-4 py-2 rounded-xl text-sm font-semibold bg-brand-50 text-brand-700':'px-4 py-2 rounded-xl text-sm font-semibold text-slate-500 hover:bg-slate-50';document.getElementById('tab-csv').className=t==='csv'?'px-4 py-2 rounded-xl text-sm font-semibold bg-brand-50 text-brand-700':'px-4 py-2 rounded-xl text-sm font-semibold text-slate-500 hover:bg-slate-50';}
document.getElementById('bulk-input')?.addEventListener('input',function(){const l=this.value.trim().split('\n').filter(l=>l.trim());document.getElementById('line-count').textContent=`${l.length} item${l.length!==1?'s':''}`;});
function handleCSV(i){const f=i.files[0];if(!f)return;const r=new FileReader();r.onload=function(e){const l=e.target.result.split('\n').map(l=>l.trim()).filter(l=>l&&!l.startsWith('#'));if(l.length>1&&/^[a-zA-Z]/.test(l[0])&&/^https?:\/\//.test(l[1]))l.shift();document.getElementById('bulk-input').value=l.join('\n');document.getElementById('csv-status').innerHTML=`<span class="text-mint-500 font-medium">✓ Loaded ${l.length} items</span>`;document.getElementById('line-count').textContent=`${l.length} items`;switchTab('manual');};r.readAsText(f);}
async function generateBulk(){const input=document.getElementById('bulk-input').value.trim();if(!input)return alert('Please enter data');const items=input.split('\n').map(l=>l.trim()).filter(l=>l);if(items.length>100)return alert('Maximum 100 items');if(!items.length)return alert('No valid items');const type=document.getElementById('bulk-type').value,size=parseInt(document.getElementById('bulk-size').value),fg=document.getElementById('bulk-fg').value,bg=document.getElementById('bulk-bg').value;document.getElementById('bulk-status').classList.add('hidden');document.getElementById('bulk-progress').classList.remove('hidden');document.getElementById('bulk-complete').classList.add('hidden');document.getElementById('bulk-thumbnails').classList.remove('hidden');document.getElementById('thumb-grid').innerHTML='';generatedQRs=[];for(let i=0;i<items.length;i++){const content=type==='url'?(items[i].startsWith('http')?items[i]:'https://'+items[i]):type==='phone'?'tel:'+items[i]:type==='email'?'mailto:'+items[i]:items[i];document.getElementById('progress-bar').style.width=((i+1)/items.length*100)+'%';document.getElementById('progress-text').textContent=`${i+1}/${items.length}`;const canvas=await genQR(content,size,fg,bg);generatedQRs.push({name:`qr_${String(i+1).padStart(3,'0')}_${items[i].replace(/[^a-zA-Z0-9]/g,'_').substring(0,30)}.png`,canvas,content:items[i]});const t=document.createElement('div');t.className='bg-slate-50 rounded-lg p-1';const img=document.createElement('img');img.src=canvas.toDataURL();img.className='w-full h-auto rounded';t.appendChild(img);document.getElementById('thumb-grid').appendChild(t);await new Promise(r=>setTimeout(r,50));}document.getElementById('bulk-progress').classList.add('hidden');document.getElementById('bulk-complete').classList.remove('hidden');document.getElementById('complete-text').textContent=`${items.length} QR codes generated!`;}
function genQR(c,s,fg,bg){return new Promise(r=>{const d=document.createElement('div');d.style.cssText='position:absolute;left:-9999px';document.body.appendChild(d);new QRCode(d,{text:c,width:s,height:s,colorDark:fg,colorLight:bg,correctLevel:QRCode.CorrectLevel.H});setTimeout(()=>{const cv=d.querySelector('canvas');const f=document.createElement('canvas');f.width=s;f.height=s;const x=f.getContext('2d');x.imageSmoothingEnabled=false;x.drawImage(cv,0,0,s,s);document.body.removeChild(d);r(f);},100);});}
async function downloadAllBulk(){if(!generatedQRs.length)return;const z=new JSZip(),f=z.folder('qr_codes');for(const q of generatedQRs)f.file(q.name,q.canvas.toDataURL('image/png').split(',')[1],{base64:true});f.file('manifest.txt',generatedQRs.map((q,i)=>`${i+1}. ${q.name} → ${q.content}`).join('\n'));const b=await z.generateAsync({type:'blob'});saveAs(b,`qr_codes_bulk_${Date.now()}.zip`);}
document.addEventListener('DOMContentLoaded',()=>lucide.createIcons());
</script>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
