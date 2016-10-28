FROM postgres:9.4
MAINTAINER edenb

# Create additional initialization queries
COPY init-user-db.sql /docker-entrypoint-initdb.d/
