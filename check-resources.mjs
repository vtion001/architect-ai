import{chromium}from'@playwright/test';
(async()=>{
  const b=await chromium.launch();
  const p=await b.newPage();
  await p.goto('http://localhost:5175/auth/login',{timeout:20000,waitUntil:'networkidle'});
  
  console.log('=== CSS Links ===');
  const cssLinks=await p.evaluate(()=>{
    return Array.from(document.querySelectorAll('link[rel="stylesheet"]')).map(l=>({href:l.href,media:l.media}));
  });
  console.log(JSON.stringify(cssLinks,null,2));
  
  console.log('\n=== JS Scripts ===');
  const jsScripts=await p.evaluate(()=>{
    return Array.from(document.querySelectorAll('script')).map(s=>({src:s.src||'inline',type:s.type}));
  });
  console.log(JSON.stringify(jsScripts,null,2));
  
  console.log('\n=== Inline Styles ===');
  const inlineStyles=await p.evaluate(()=>{
    const el=document.querySelector('style');
    return el?el.innerHTML.substring(0,500):'none';
  });
  console.log(inlineStyles);
  
  await b.close();
})().catch(console.error);
