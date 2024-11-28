const { execSync } = require('child_process');

try {
    console.log('Checking and installing system dependencies...');

    // Ensure Python is installed (specifically Python 3.x)
    execSync('python3 --version', { stdio: 'ignore' });
    console.log('Python 3 is already installed.');
} catch {
    console.log('Python 3 is not installed. Installing...');
    try {
        execSync('apt-get update && apt-get install -y python3', { stdio: 'inherit' });
    } catch (err) {
        console.error('Failed to install Python 3. Please install it manually.');
        process.exit(1);
    }
}

try {
    // Ensure build-essential (make and compiler) is installed
    execSync('make --version', { stdio: 'ignore' });
    console.log('Build essentials are already installed.');
} catch {
    console.log('Build essentials (make, gcc, etc.) are not installed. Installing...');
    try {
        execSync('apt-get update && apt-get install -y build-essential', { stdio: 'inherit' });
    } catch (err) {
        console.error('Failed to install build essentials. Please install them manually.');
        process.exit(1);
    }
}

console.log('System dependencies are installed.');
