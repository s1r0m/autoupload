const puppeteer = require('puppeteer-extra');
const StealthPlugin = require('puppeteer-extra-plugin-stealth');

// Use the stealth plugin to avoid detection
puppeteer.use(StealthPlugin());

(async () => {
    // Launch Puppeteer in headless mode with root-compatible configurations
    const browser = await puppeteer.launch({
        headless: false, // Runs in headless mode
        args: [
            '--no-sandbox',
            '--disable-setuid-sandbox', 
            '--disable-blink-features=AutomationControlled',
            '--disable-features=SameSiteByDefaultCookies',
            '--disable-dev-shm-usage',
            '--disable-web-security',
            '--disable-features=IsolateOrigins',
            '--disable-site-isolation-trials',
            '--remote-debugging-port=9222'
        ]
    });

    try {
        const page = await browser.newPage();

        const url = 'https://instagram.com/accounts/login';

        // Define mobile viewport settings
        const mobileViewport = {
            width: 393, // iPhone X width
            height: 876, // iPhone X height
            deviceScaleFactor: 3, // Retina display
            isMobile: true,
            hasTouch: true,
            isLandscape: false,
        };

        // Set user-agent to mimic a mobile browser
        const mobileUserAgent =
            'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Mobile Safari/537.36';

        console.log('Configuring viewport for mobile view...');
        await page.setViewport(mobileViewport);
        await page.setUserAgent(mobileUserAgent);
        
        await page.evaluateOnNewDocument(() => {
            Object.defineProperty(navigator, 'deviceMemory', {
                get: () => 8, // 8GB
            });
            Object.defineProperty(navigator, 'userAgent', {
                get: () =>
                    'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Mobile Safari/537.36',
            });
        });

        // Handle JavaScript alert by dismissing it
        page.on('dialog', async (dialog) => {
            console.log(`Alert detected: "${dialog.message()}"`);
            await dialog.dismiss();
        });

        console.log(`Navigating to ${url}...`);
        await page.goto(url, { waitUntil: 'domcontentloaded' });

        // Wait for the page to fully load (adjust selector as needed)
        console.log('Waiting for page to load...');

        try {
            await page.waitForSelector('input', { timeout: 8000 });

            await page.type('input[name=username]', 'sub4subtoday247', { delay: 29 });
            await page.type('input[name=password]', 'okokokoko', { delay: 19 });

            await page.click('button[type="submit"]');

            await page.waitForNavigation({ waitUntil: 'networkidle2' });
            await page.waitForSelector('button', { timeout: 8000 });

        await page.waitForSelector('iframe');
        
        console.log(await page.content());
        } catch (e) {
            console.log(e);
            console.log("Error SS Saved error.png");
            await page.screenshot({ path: "error.png" });
        }

        const outputPath = 'detect-headless-screenshot.png';
        console.log(`Taking screenshot and saving to ${outputPath}...`);

        // Take screenshot and save it
        await page.screenshot({ path: outputPath });

        console.log('Screenshot captured successfully!');
    } catch (error) {
        console.error('An error occurred:', error);
    } 
})();
