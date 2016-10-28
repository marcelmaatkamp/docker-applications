FROM node:4.4.1

# Create a source folder for the app
RUN mkdir /src
WORKDIR /src

# Get latest version of the Livemap app
# --strip 1 removes the container folder
RUN curl -sSL https://github.com/edenb/livemap/tarball/master | tar -xvz --strip 1

# Install dependencies
RUN npm install

# Make port available from outside the container
EXPOSE 3000

# Start app
CMD ["node", "app.js"]