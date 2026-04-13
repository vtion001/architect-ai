import{chromium}from'@playwright/test';
(async()=>{
  const b=await chromium.launch();
  const p=await b.newPage();
  await p.goto('http://localhost:5175/login',{timeout:15000,waitUntil:'networkidle'});
  const card=await p.evaluate(()=>{
    const el=document.querySelector('.bg-card');
    if(!el)return'NOT FOUND';
    return window.getComputedStyle(el).backgroundColor;
  });
  console.log('Card background:',card);
  const cardVal=await p.evaluate(()=>getComputedStyle(document.documentElement).getPropertyValue('--card').trim());
  console.log('--card CSS var:',cardVal);
  const links=await p.evaluate(()=>Array.from(document.querySelectorAll('link')).map(l=>l.href));
  console.log('Tailwind CDN links:',links.filter(l=>l.includes('tailwind')));
  await b.close();
  console.log('Expected: white background (rgb(255,255,255) or #FFFFFF)');
})().catch(console.error);
