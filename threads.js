const puppeteer = require('puppeteer');
const fs = require('fs');
const path = require('path');

(async () => {
    const htmlFile = 'threads.html';

    // Check for the output image name as a command-line argument
    const args = process.argv.slice(2);
    if (args.length === 0) {
        console.error("Usage: node threads.js <output_image>");
        process.exit(1);
    }
    const outputImage = args[0];

    // Check if the HTML file exists
    if (!fs.existsSync(htmlFile)) {
        console.error(`File "${htmlFile}" not found!`);
        process.exit(1);
    }

    // Delete the existing image if it exists
    if (fs.existsSync(outputImage)) {
        fs.unlinkSync(outputImage);
    }

    // Read HTML file content
    const htmlContent = fs.readFileSync(htmlFile, 'utf8');

    // Launch Puppeteer with --no-sandbox
    const browser = await puppeteer.launch({
        args: ['--no-sandbox', '--disable-setuid-sandbox']
    });
    const page = await browser.newPage();

    // Load the HTML content first
    await page.setContent(htmlContent, { waitUntil: 'load' });

    // Get the full height of the page content
    const bodyHandle = await page.$('body');
    const { height } = await bodyHandle.boundingBox();
    await bodyHandle.dispose();

    // Set viewport to match the content height (width can remain fixed)
    await page.setViewport({
        width: 1080,
        height: 1080,
        deviceScaleFactor: 1
    });

    // Take a screenshot of the full page
    await page.screenshot({
        path: outputImage,
        type: 'jpeg',
        quality: 100, // High quality for HD
        fullPage: true // Capture the entire page content
    });

    console.log(`Image saved as "${outputImage}"`);

    // Close the browser
    await browser.close();
})();