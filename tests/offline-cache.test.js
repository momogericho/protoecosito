const { spawn } = require('child_process');
const puppeteer = require('puppeteer');

async function delay(ms) {
  return new Promise(resolve => setTimeout(resolve, ms));
}

async function run() {
  // Start PHP built-in server
  const server = spawn('php', ['-S', 'localhost:8081', '-t', 'public'], {
    stdio: 'inherit'
  });

  // Give the server time to boot
  await delay(2000);

  const browser = await puppeteer.launch({ headless: 'new', args: ['--no-sandbox'] });
  const page = await browser.newPage();

  await page.goto('http://localhost:8081/offline.html', { waitUntil: 'networkidle0' });
  // Register the service worker manually for the static page
  await page.evaluate(() => navigator.serviceWorker.register('/sw.js'));
  await page.reload({ waitUntil: 'networkidle0' });
  // Wait for the service worker to control the page
  await page.waitForFunction(() => navigator.serviceWorker && navigator.serviceWorker.controller);

  // Simulate offline
  await page.setOfflineMode(true);

  const hasCaches = await page.evaluate(async () => {
    const keys = await caches.keys();
    return keys.length > 0;
  });

  await browser.close();
  server.kill();

  if (!hasCaches) {
    throw new Error('No caches found while offline');
  }

  console.log('Offline cache check passed');
}

run().catch(err => {
  console.error(err);
  process.exit(1);
});