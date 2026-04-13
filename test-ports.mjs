import { chromium } from '@playwright/test';

async function checkPorts() {
    const browser = await chromium.launch({ headless: true });
    
    const ports = [
        { port: 5175, name: 'Vite Dev Server' },
        { port: 8081, name: 'Docker Nginx' }
    ];

    for (const { port, name } of ports) {
        console.log(`\n=== Checking ${name} (port ${port}) ===`);
        try {
            const context = await browser.newContext();
            const page = await context.newPage();
            
            const response = await page.goto(`http://localhost:${port}`, { 
                waitUntil: 'domcontentloaded',
                timeout: 10000 
            });
            
            console.log(`Status: ${response?.status()}`);
            console.log(`URL: ${page.url()}`);
            
            const title = await page.title();
            console.log(`Title: ${title}`);
            
            const h1 = await page.$eval('h1', el => el.textContent).catch(() => 'No H1 found');
            console.log(`H1: ${h1}`);
            
            // Check if it looks like Laravel
            const body = await page.content();
            const isLaravel = body.includes('Laravel') || body.includes('ArchitGrid');
            const isViteDefault = body.includes('VITE') && body.includes('Hot Module Replacement');
            
            console.log(`Is Laravel: ${isLaravel}`);
            console.log(`Is Vite Default Page: ${isViteDefault}`);
            
            await context.close();
        } catch (error) {
            console.log(`Error: ${error.message}`);
        }
    }
    
    await browser.close();
}

checkPorts();
