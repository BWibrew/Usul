FROM node:10

# Copy Yarn install files
COPY --chown=node:node ./package.json /var/www/package.json
COPY --chown=node:node ./yarn.lock /var/www/yarn.lock

USER node

# Prep Yarn cache
RUN cd /var/www && yarn
