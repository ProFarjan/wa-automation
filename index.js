const puppeteer = require('puppeteer-extra');
const http = require('http');
const { exec } = require('child_process');

const RUNNING_PORT = 3333;


function randomSleep(min, max) {
    return new Promise(resolve => setTimeout(resolve, Math.floor(Math.random() * (max - min) + min)));
}

const handleTask = async (data) => {
    const userDataDir = `/home/Farjan/.config/google-chrome/Default`;
    const remoteDebuggingPort = 9222;
    let browser = null;
    let isWhatsappNumber = 2;

    try {
        exec(`google-chrome-stable --headless --remote-debugging-port=${remoteDebuggingPort} --user-data-dir=${userDataDir} --profile-directory=Default --start-maximized`, (err, stdout, stderr) => {
            if (err) {
            console.error(`Error launching Chrome: ${err.message}`);
            return;
            }
        });

        await randomSleep(3000, 5000);

        puppeteer.connect({
            browserURL: 'http://localhost:' + remoteDebuggingPort,
            defaultViewport: null,
            timeout: 5000
        }).then(async (connectedBrowser) => {
            browser = connectedBrowser;

            const pages = await browser.pages();
            const page = pages[0];
            page.setDefaultTimeout(15000);

            await page.goto(`https://web.whatsapp.com/send?phone=${data.phone}&text=${encodeURIComponent(data.message)}`);
            await randomSleep(10000, 15000);

            const errorMessageSelector = 'div[data-animate-modal-body="true"]';
            const errorMessage = await page.$(errorMessageSelector);
            if (errorMessage) {
                console.log('Invalid phone number or whatsapp not register:', data.phone);
                const errorButtonSelector = 'div[data-animate-modal-body="true"] button';
                const errorButton = await page.$(errorButtonSelector);
                if (errorButton) {
                    await errorButton.click();
                }
                await randomSleep(1000, 3000);
                return;
            }

            isWhatsappNumber = 1;

            await page.keyboard.press('Enter');
            await randomSleep(5000, 8000);

        }).catch((error) => {
            console.error('Failed to connect to browser:', error);
        }).finally(async () => {
            if (browser) {
                await browser.close();
            }
            await fetch(`https://api.farjan.dev/whatsapp.php?method=is_whatsapp&wa_status=${isWhatsappNumber}&id=${data.id}`, {
                method: 'GET'
            }).then(response => response.json()).then(result => {
                console.log('API call result:', result);
            }).catch(error => {
                console.error('API call error:', error);
            });
        });
    } catch (err) {
        console.log('try catch error: ' + err.message);
    }
};

const requestHandler = async (req, res) => {
    if (req.method === 'POST') {
        let body = '';
        req.on('data', chunk => {
            body += chunk.toString();
        });
        req.on('end', async () => {
            try {
                const postData = JSON.parse(body);
                if (postData) {
                    res.end('Request received, processing in background');
                    try {
                        setImmediate(() => {
                            handleTask(postData); 
                        });
                    } catch (err) {}
                } else {
                    res.end('No search text provided');
                }
            } catch (err) {
                res.end('error found');
            }
        });
    } else {
        res.end('Invalid request method');
    }
};

const server = http.createServer(requestHandler);

server.listen(RUNNING_PORT, () => {
    console.log(`Server is listening on port ${RUNNING_PORT}`);
});

const shutdown = () => {
    browser = null;
    server.close(() => {
        process.exit(0);
    });
};

process.on('SIGINT', shutdown);
process.on('SIGTERM', shutdown);

process.on('unhandledRejection', (reason, promise) => {
    console.error('Unhandled Rejection at:', promise, 'reason:', reason);
});