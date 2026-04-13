import{chromium}from'@playwright/test';
(async()=>{
  const b=await chromium.launch();
  const p=await b.newPage();
  
  console.log('=== Port 8081 (Docker) ===');
  const r1=await p.goto('http://localhost:8081/login',{timeout:15000,waitUntil:'domcontentloaded'});
  console.log('Status:',r1.status());
  const title1=await p.title();
  console.log('Title:',title1);
  
  const p2=await b.newPage();
  console.log('\n=== Port 5175 (Vite) ===');
  const r2=await p2.goto('http://localhost:5175/login',{timeout:15000,waitUntil:'domcontentloaded'});
  console.log('Status:',r2.status());
  const title2=await p2.title();
  console.log('Title:',title2);
  
  await b.close();
})().catch(console.error);
