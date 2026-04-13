import{chromium}from'@playwright/test';
(async()=>{
  const b=await chromium.launch();
  const p=await b.newPage();
  
  // Get full page content
  const response=await p.goto('http://localhost:5175/login',{timeout:15000,waitUntil:'domcontentloaded'});
  console.log('Status:',response.status());
  console.log('URL:',p.url());
  
  const title=await p.title();
  console.log('Title:',title);
  
  // Get body content
  const bodyContent=await p.evaluate(()=>document.body.innerHTML.substring(0,1000));
  console.log('Body content:',bodyContent);
  
  await b.close();
})().catch(console.error);
