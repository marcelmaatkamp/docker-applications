FROM fluent/fluentd
EXPOSE 24224

USER root
RUN adduser -D -g '' -u 999 -h /home/docker docker
RUN adduser fluent docker
RUN touch /var/log/fluentd-docker.pos && chown fluent /var/log/fluentd-docker.pos

# USER fluent
RUN gem install fluent-plugin-docker-tag-resolver
RUN gem install fluent-plugin-elasticsearch --no-rdoc --no-ri
RUN gem install fluent-plugin-record-reformer
