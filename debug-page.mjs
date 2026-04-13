import{chromium}from'@playwright/test';
(async()=>{
  const b=await chromium.launch();
  const p=await b.newPage();
  await p.goto('http://localhost:5175/login',{timeout:15000,waitUntil:'networkidle'});
  
  // Check if bg-card element exists
  const hasCard=await p.evaluate(()=>!!document.querySelector('.bg-card'));
  console.log('Has .bg-card element:',hasCard);
  
  // Get full HTML structure around the form
  const formHtml=await p.evaluate(()=>{
    const form=document.querySelector('form');
    return form?form.outerHTML.substring(0,500):'Form not found';
  });
  console.log('Form HTML:',formHtml);
  
  // Check CSS variables on root
  const rootStyles=await p.evaluate(()=>{
    const root=document.documentElement;
    const styles=getComputedStyle(root);
    return {
      card:styles.getPropertyValue('--card'),
      background:styles.getPropertyValue('--background'),
      primary:styles.getPropertyValue('--primary')
    };
  });
  console.log('Root CSS vars:',rootStyles);
  
  // Check all loaded stylesheets
  const stylesheets=await p.evaluate(()=>{
    return Array.from(document.styleSheets).map(s=>{
      try{return s.href||'inline'}catch(e){return'inline'}
    });
  });
  console.log('Stylesheets:',stylesheets);
  
  // Check body background
  const bodyBg=await p.evaluate(()=>window.getComputedStyle(document.body).backgroundColor);
  console.log('Body background:',bodyBg);
  
  await b.close();
})().catch(console.error);
