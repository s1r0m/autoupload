# Use an Ubuntu base image
FROM ubuntu:22.04

# Set environment variables to prevent interactive prompts during installations
ENV DEBIAN_FRONTEND=noninteractive

# Update package list and install required base packages
RUN apt-get update && apt-get install -y \
    curl \
    gnupg2 \
    software-properties-common \
    php-cli \
    php-mbstring \
    php-xml \
    php-zip \
    zip \
    unzip \
    python3 \
    python3-pip \
    wget \
    cmake \
    git \
    libc6-i386 \
    lib32z1 \
    lib32stdc++6 \
    snapd \
    sudo \
    ufw \
    net-tools \
    iproute2 \
    openjdk-11-jdk-headless \
    xz-utils \
    --no-install-recommends

# Install FFmpeg
RUN apt-get install -y ffmpeg

# Install Node.js (LTS version) and Yarn
RUN curl -fsSL https://deb.nodesource.com/setup_18.x | bash - && apt-get install -y nodejs
RUN npm install -g yarn

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Install Puppeteer dependencies
RUN apt-get install -y \
    libnss3 \
    libatk1.0-0 \
    libatk-bridge2.0-0 \
    libcups2 \
    libxcomposite1 \
    libxrandr2 \
    libxdamage1 \
    libgbm1 \
    libasound2 \
    libpangocairo-1.0-0 \
    libgtk-3-0 \
    fonts-liberation \
    --no-install-recommends

# Install Puppeteer globally
RUN npm install -g puppeteer

# Install Selenium dependencies
RUN apt-get install -y \
    chromium-browser \
    chromium-chromedriver

# Install ngrok
RUN curl -sSL https://ngrok-agent.s3.amazonaws.com/ngrok.asc \
    | tee /etc/apt/trusted.gpg.d/ngrok.asc >/dev/null \
    && echo "deb https://ngrok-agent.s3.amazonaws.com buster main" \
    | tee /etc/apt/sources.list.d/ngrok.list \
    && apt-get update \
    && apt-get install -y ngrok

RUN ngrok config add-authtoken 2npNmt4lYhrnAI7CYppyGF2ZAIU_7c92JzSWMhcyJHck59ZoR

# Install Android NDK
ENV ANDROID_NDK_VERSION=r25c
ENV ANDROID_NDK_HOME=/opt/android-ndk
RUN wget https://dl.google.com/android/repository/android-ndk-${ANDROID_NDK_VERSION}-linux.zip -P /tmp && \
    unzip /tmp/android-ndk-${ANDROID_NDK_VERSION}-linux.zip -d /opt && \
    mv /opt/android-ndk-${ANDROID_NDK_VERSION} ${ANDROID_NDK_HOME} && \
    rm /tmp/android-ndk-${ANDROID_NDK_VERSION}-linux.zip
ENV PATH="${ANDROID_NDK_HOME}:${PATH}"

# Ensure 'python' command runs 'python3'
RUN ln -s /usr/bin/python3 /usr/bin/python

# Set the working directory
WORKDIR /

# Copy application files to the working directory
COPY . .

# Install Node.js dependencies
RUN yarn install

# Install Apktool
ENV APKTOOL_VERSION="2.9.0"
ENV ANDROID_HOME="/android-sdk-linux"
ENV BUILD_TOOLS_VERSION="34.0.0"
ENV PATH="${PATH}:${ANDROID_HOME}/tools/bin:${ANDROID_HOME}/cmdline-tools/bin:${ANDROID_HOME}/build-tools/${BUILD_TOOLS_VERSION}"
RUN mkdir -p "${ANDROID_HOME}" && \
    wget -q "https://dl.google.com/android/repository/commandlinetools-linux-7583922_latest.zip" -O android-sdk.zip && \
    unzip -q android-sdk.zip -d "${ANDROID_HOME}" && \
    rm android-sdk.zip
RUN yes | sdkmanager --sdk_root="${ANDROID_HOME}" "build-tools;${BUILD_TOOLS_VERSION}"
RUN wget -q "https://raw.githubusercontent.com/iBotPeaches/Apktool/master/scripts/linux/apktool" -O /usr/local/bin/apktool && \
    chmod a+x /usr/local/bin/apktool && \
    wget -q "https://bitbucket.org/iBotPeaches/apktool/downloads/apktool_${APKTOOL_VERSION}.jar" -O /usr/local/bin/apktool.jar && \
    chmod a+x /usr/local/bin/apktool.jar

# Install BundleDecompiler
RUN wget -q "https://raw.githubusercontent.com/TamilanPeriyasamy/BundleDecompiler/master/build/libs/BundleDecompiler-0.0.2.jar" -O /usr/local/bin/BundleDecompiler.jar && \
    chmod a+x /usr/local/bin/BundleDecompiler.jar

# Expose necessary ports
EXPOSE 10000
EXPOSE 8000

# Start PHP server
#RUN nohup php -S 0.0.0.0:8000 -c /php.ini > server.log 2>&1 &
RUN npm install puppeteer
RUN npm install puppeteer-core
RUN npm install puppeteer-extra puppeteer-extra-plugin-stealth

# Default command
CMD ["php", "-S", "0.0.0.0:10000", "-c", "/php.ini"]