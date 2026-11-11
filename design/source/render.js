const { chromium } = require('playwright'); const fs=require('fs'); const path=require('path');
const D=__dirname; const man=JSON.parse(fs.readFileSync(D+'/manifest.json','utf8'));
const only=process.argv[2]; const OUT=process.argv[3]||D+'/out'; fs.mkdirSync(OUT,{recursive:true}); fs.mkdirSync(D+'/png',{recursive:true});
const cache={};
function dataUri(url){ if(cache[url]) return cache[url]; const f='/home/user/alena/'+url.replace('http://127.0.0.1:8766/',''); const b=fs.readFileSync(f); return cache[url]='data:image/jpeg;base64,'+b.toString('base64'); }
(async()=>{ const b=await chromium.launch(); const bundle=fs.readFileSync(D+'/bundle.js','utf8');
 for (const f of man) { if(only && only!=='all' && !f.name.startsWith(only)) continue;
  const p=await b.newPage({viewport:{width:f.w,height:f.h||900}});
  await p.goto('file://'+f.html,{waitUntil:'networkidle'}); await p.evaluate(()=>document.fonts.ready); await p.waitForTimeout(300);
  await p.screenshot({path:D+'/png/'+f.name.replace(/ /g,'_')+'.png',clip:await p.evaluate(()=>{const r=document.querySelector('#frame').getBoundingClientRect();return {x:0,y:0,width:r.width,height:r.height}}),fullPage:true});
  await p.addScriptTag({content:bundle});
  let svg=await p.evaluate(()=>window.__toSVG('#frame'));
  svg=svg.replace(/(xlink:href|href)="(http:\/\/127\.0\.0\.1:8766\/[^"]+)"/g,(m,a,u)=>`${a}="${dataUri(u)}"`);
  svg=svg.replace(/font-family="&quot;([^"&]+)&quot;(,[^"]*)?"/g,'font-family="$1"');
  svg=svg.replace(/ textLength="[^"]*"/g,'').replace(/ lengthAdjust="[^"]*"/g,'');
  svg=svg.replace(/(<mask id="mask-for-avw\d+"><rect width="([\d.]+)")/g,(m,all,w)=>all+` rx="${(+w/2)}"`);
  svg=svg.replace(/<image /g,'<image preserveAspectRatio="xMidYMid slice" ');
  fs.writeFileSync(OUT+'/'+f.name+'.svg',svg);
  console.log(f.name,(svg.length/1024|0)+' КБ', (svg.match(/<text/g)||[]).length+' text', (svg.match(/<image/g)||[]).length+' img');
  await p.close(); }
 await b.close(); })();
