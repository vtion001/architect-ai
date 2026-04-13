import{chromium}from'@playwright/test';
(async()=>{
  const b=await chromium.launch();
  const p=await b.newPage();
  
  // Track all requests
  const requests=[];
  p.on('request',req=>{
    if(req.url().includes('app.css')||req.url().includes('.css')){
      requests.push({url:req.url(),method:req.method()});
    }
  });
  
  p.on('response',async res=>{
    if(res.url().includes('app.css')||res.url().includes('.css')){
      console.log('CSS Response:',res.url(),res.status());
    }
  });
  
  await p.goto('http://localhost:5175/auth/login',{timeout:20000,waitUntil:'networkidle'});
  
  console.log('CSS Requests:',JSON.stringify(requests,null,2));
  
  // Try fetching app.css directly
  console.log('\n=== Direct fetch of app.css ===');
  const cssPage=await p.goto('http://localhost:5175/resources/css/app.css',{timeout:10000});
  console.log('Status:',cssPage.status());
  const content=await p.evaluate(()=>document.body.innerHTML.substring(0,500));
  console.log('Content:',content);
  
  await b.close();
})().catch(console.error);
