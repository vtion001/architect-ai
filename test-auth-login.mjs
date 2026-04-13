import{chromium}from'@playwright/test';
(async()=>{
  const b=await chromium.launch();
  
  console.log('=== Testing /auth/login ===\n');
  
  const p=await b.newPage();
  await p.goto('http://localhost:5175/auth/login',{timeout:20000,waitUntil:'networkidle'});
  
  const card=await p.evaluate(()=>{
    const el=document.querySelector('.bg-card');
    if(!el)return'NOT FOUND';
    return window.getComputedStyle(el).backgroundColor;
  });
  console.log('Port 5175 - Card background:',card);
  
  const cardVal=await p.evaluate(()=>getComputedStyle(document.documentElement).getPropertyValue('--card').trim());
  console.log('Port 5175 - --card CSS var:',cardVal);
  
  const hasTailwindCDN=await p.evaluate(()=>{
    return Array.from(document.querySelectorAll('script')).some(s=>s.src&&s.src.includes('tailwind'));
  });
  console.log('Port 5175 - Has Tailwind CDN:',hasTailwindCDN);
  
  await b.close();
  console.log('\nExpected: rgb(255, 255, 255) or #FFFFFF with NO Tailwind CDN');
})().catch(console.error);
