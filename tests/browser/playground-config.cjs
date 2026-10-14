const {chromium}=require('playwright');
const {spawn}=require('node:child_process');
const net=require('node:net');
const assert=require('node:assert/strict');
(async()=>{
 const socket=net.createServer();
 await new Promise(r=>socket.listen(0,'127.0.0.1',r));
 const port=socket.address().port;
 await new Promise(r=>socket.close(r));
 const server=spawn('php',['-S','127.0.0.1:'+port,'-t','.','../vendor/laravel/framework/src/Illuminate/Foundation/resources/server.php'],{cwd:process.cwd()+'/public',stdio:['ignore','ignore','pipe']});
 server.stderr.resume();
 console.log('Owned preview PID: '+server.pid+'; port: '+port);
 let browser;
 try{
  for(let i=0;i<50;i++){try{if((await fetch('http://127.0.0.1:'+port+'/playground/text-input')).ok)break;}catch{} await new Promise(r=>setTimeout(r,100));}
  browser=await chromium.launch({headless:true});
  const page=await browser.newPage();
  let navigations=0;
  page.on('request',r=>{if(r.isNavigationRequest() && r.frame()===page.mainFrame())navigations++;});
  const errors=[]; page.on('pageerror',e=>errors.push(e.message));
  await page.goto('http://127.0.0.1:'+port+'/playground/text-input');
  await page.waitForFunction(()=>Boolean(window.Livewire));
  await page.evaluate(()=>{window.browserTestMarker='same-document';});
  const editor=page.locator('[data-playground-config]');
  const node=JSON.parse(await editor.inputValue());
  node.defaults.placeholder='Browser verified edit';
  await editor.fill(JSON.stringify(node,null,2));
  await page.waitForFunction(()=>document.querySelector('[data-playground-preview] input')?.placeholder==='Browser verified edit');
  await page.waitForFunction(()=>new URL(location.href).searchParams.get("state")?.includes("Browser verified edit"));
  const sharedUrl=page.url();
  const sharedState=JSON.parse(new URL(sharedUrl).searchParams.get("state"));
  assert.deepEqual(sharedState,node.defaults);
  assert.equal(await page.locator("[data-playground-share-link]").getAttribute("href"),new URL(sharedUrl).pathname+new URL(sharedUrl).search);
  const recipient=await browser.newPage();
  await recipient.goto(sharedUrl);
  assert.equal(await recipient.locator("[data-playground-preview] input").getAttribute("placeholder"),"Browser verified edit");
  assert.deepEqual(JSON.parse(await recipient.locator("[data-playground-config]").inputValue()),node);
  await recipient.close();
  assert.equal(navigations,1);
  assert.equal(await page.evaluate(()=>window.browserTestMarker),'same-document');
  await editor.fill('{');
  await page.locator('[data-playground-errors]').waitFor();
  assert.match(await page.locator('[data-playground-errors]').innerText(),/Invalid JSON/);
  node.defaults.unknownBrowserKey=true;
  await editor.fill(JSON.stringify(node));
  await page.waitForFunction(()=>document.querySelector('[data-playground-errors]')?.textContent.includes('unknownBrowserKey'));
  assert.equal(await page.locator('[data-playground-preview] input').getAttribute('placeholder'),'Browser verified edit');
  delete node.defaults.unknownBrowserKey;
  node.defaults.placeholder='Recovered valid edit';
  await editor.fill(JSON.stringify(node));
  await page.waitForFunction(()=>document.querySelector('[data-playground-preview] input')?.placeholder==='Recovered valid edit');
  assert.equal(await page.locator('[data-playground-errors]').count(),0);
  assert.equal(navigations,1);
  assert.deepEqual(errors,[]);
  console.log('PASS: share URL update, recipient state and preview, valid edit, no reload, malformed JSON, unknown keys, retained preview, recovery, no browser errors');
 }finally{
  if(browser)await browser.close();
  if(server.exitCode === null && server.signalCode === null){
   const stopped=new Promise(r=>server.once('exit',r));
   server.kill('SIGTERM');
   await stopped;
  }
  console.log('Stopped owned preview PID: '+server.pid);
 }
})().catch(e=>{console.error(e);process.exitCode=1;});
