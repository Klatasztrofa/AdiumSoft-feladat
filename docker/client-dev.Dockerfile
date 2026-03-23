FROM node:20-alpine

COPY client_start.sh /client_start.sh
RUN chmod +x /client_start.sh
RUN mkdir -p /usr/src/client

WORKDIR /usr/src/client

EXPOSE 5173

CMD ["/client_start.sh"]
