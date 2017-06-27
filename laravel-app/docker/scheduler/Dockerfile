FROM php:7.1.6-cli

ENV DISPLAY=:99.0

# Google Chrome
ARG CHROME_VERSION="google-chrome-stable"
RUN apt-get update -qqy \
  && apt-get -qqy --no-install-recommends install ca-certificates wget unzip xvfb zlib1g-dev \
  && wget -q -O - https://dl-ssl.google.com/linux/linux_signing_key.pub | apt-key add - \
  && echo "deb http://dl.google.com/linux/chrome/deb/ stable main" >> /etc/apt/sources.list.d/google-chrome.list \
  && apt-get update -qqy \
  && apt-get -qqy install \
    ${CHROME_VERSION:-google-chrome-stable} \
  && rm /etc/apt/sources.list.d/google-chrome.list \
  && rm -rf /var/lib/apt/lists/* /var/cache/apt/*

# Chrome Webdriver
ARG CHROME_DRIVER_VERSION=2.30
RUN wget --no-verbose -O /tmp/chromedriver_linux64.zip https://chromedriver.storage.googleapis.com/$CHROME_DRIVER_VERSION/chromedriver_linux64.zip \
  && rm -rf /opt/selenium/chromedriver \
  && unzip /tmp/chromedriver_linux64.zip -d /opt/selenium \
  && rm /tmp/chromedriver_linux64.zip \
  && mv /opt/selenium/chromedriver /opt/selenium/chromedriver-$CHROME_DRIVER_VERSION \
  && chmod 755 /opt/selenium/chromedriver-$CHROME_DRIVER_VERSION \
  && ln -fs /opt/selenium/chromedriver-$CHROME_DRIVER_VERSION /usr/bin/chromedriver

# PHP Extensions
RUN docker-php-ext-install mbstring pdo pdo_mysql zip

COPY docker/scheduler/chrome-launcher.sh /opt/google/chrome/google-chrome
COPY docker/scheduler/entrypoint.sh /opt/bin/entrypoint.sh
RUN chmod +x /opt/google/chrome/google-chrome \
  && chmod +x /opt/bin/entrypoint.sh

COPY . /srv/app
WORKDIR /srv/app

RUN mkdir -p /srv/app/bootstrap/cache \
  && mkdir -p /srv/app/storage/app/public \
  && mkdir -p /srv/app/storage/app/reports \
  && mkdir -p /srv/app/storage/framework/cache \
  && mkdir -p /srv/app/storage/framework/sessions \
  && mkdir -p /srv/app/storage/framework/views \
  && mkdir -p /srv/app/storage/logs

CMD ["/opt/bin/entrypoint.sh"]
