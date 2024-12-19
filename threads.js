const puppeteer = require('puppeteer');
const fs = require('fs');
const path = require('path');

(async () => {
    const htmlFile = 'threads.html';
    const width = 672;
    const height = 192;

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

    // Set viewport for 1080x1080 resolution
    await page.setViewport({ width, height });

    // Load the HTML content
    await page.setContent(htmlContent, { waitUntil: 'load' });

    // Take a screenshot of the page and save it as a JPEG
    await page.screenshot({
        path: outputImage,
        type: 'jpeg',
        quality: 100, // High quality for HD
        clip: { x: 0, y: 0, width, height } // Ensure the image is 1080x1080
    });

    console.log(`Image saved as "${outputImage}"`);

    // Close the browser
    await browser.close();
})();
