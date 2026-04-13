import{chromium}from'@playwright/test';
(async()=>{
  const b=await chromium.launch();
  const p=await b.newPage();
  
  // Intercept the CSS request
  let appCssContent='NOT LOADED';
  p.on('response',async response=>{
    if(response.url().includes('app.css')){
      appCssContent=await response.text();
    }
  });
  
  await p.goto('http://localhost:5175/auth/login',{timeout:20000,waitUntil:'networkidle'});
  
  console.log('app.css content (first 1500 chars):');
  console.log(appCssContent.substring(0,1500));
  
  console.log('\n\n=== Checking for CSS variables in app.css ===');
  console.log('Has --card:',appCssContent.includes('--card'));
  console.log('Has --background:',appCssContent.includes('--background'));
  console.log('Has :root:',appCssContent.includes(':root'));
  
  await b.close();
})().catch(console.error);
