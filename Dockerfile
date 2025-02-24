# Start from scratch
FROM ubuntu:bionic

# Label this image
# LABEL name="registry.arpa.local/servizi/rasdaman"
LABEL name="arpasmr:rasdaman"
LABEL version="1.1"
LABEL maintainer="Luca Paganotti <luca.paganotti@gmail.com>"
LABEL description="image for rasdaman container with Ubuntu 18.04"

# To run dpkg (behind other tools like Apt) without interactive dialogue, you can set one environment variable as
ENV DEBIAN_FRONTEND noninteractive

# Update system
RUN apt-get -y update --fix-missing && \
    apt-get -y upgrade --fix-missing && \
    apt-get -y autoremove && \
    apt-get -y install apt-utils wget unzip vim openssh-client apache2 php iproute2 gnupg nano mlocate ntp net-tools && \
    wget -qO - http://download.rasdaman.org/packages/rasdaman.gpg | apt-key add - && \
    echo "deb [arch=amd64] http://download.rasdaman.org/packages/deb bionic stable" | tee /etc/apt/sources.list.d/rasdaman.list && \
    apt-get -y update --fix-missing && \
    apt-get -y install rasdaman tomcat9-admin && \
    apt-get -y upgrade --fix-missing && \
    apt-get -y autoremove && \
    apt-get -y install apt-utils wget unzip && \
    apt-get -y install vim && \
    apt-get -y install s3cmd && \
    apt-get -y install python3-gdal && \
    #wget -O - http://download.rasdaman.org/packages/rasdaman.gpg | apt-key add - && \
    # echo "deb [arch=amd64] http://download.rasdaman.org/packages/deb jessie stable" | tee /etc/apt/sources.list.d/rasdaman.list && \
    rm -rf /var/lib/apt/lists/* && \
    mkdir /opt/rasdaman/log && \
    /opt/rasdaman/bin/create_db.sh && \
    /opt/rasdaman/bin/update_db.sh && \
    # /opt/rasdaman/bin/rasdaman_insertdemo.sh && \
    chmod 777 -R /opt/rasdaman/log

ENV RASMGR_PORT 7001
ENV RASLOGIN rasadmin:d293a15562d3e70b6fdc5ee452eaed40
ENV RMANHOME /opt/rasdaman
ENV RMANDATA $RMANHOME/data
ENV RMANBIN $RMANHOME/bin
ENV RMANETC $RMANHOME/etc
ENV RASMGR_CONF_FILE $RMANETC/rasmgr.conf

EXPOSE 7001-7010

# Update rasmgr.conf
# commented by Roberto GTER, because we try to have a fixed configuration using the rasmgr.conf file in Sinergico03
# COPY rasmgr.conf.in /rasmgr.conf.in

# Naive check runs checks once a minute to see if either of the processes exited.
# If you wanna be elegant use supervisord
COPY entrypoint.sh /entrypoint.sh

RUN pip install glob2 jsonschema

# tomcat9 setup
COPY server.xml /etc/tomcat9/server.xml

#copy the startup sh script (starting from tomcat9) 
COPY tomcat9 /etc/init.d/tomcat9
#RUN chmod +x /etc/init.d/tomcat9 && \
#    update-rc.d /etc/init.d/tomcat9 defaults


RUN mkdir -p /var/lib/tomcat9/shared/classes && \
      mkdir -p /var/lib/tomcat9/common/classes && \
      mkdir -p /var/lib/tomcat9/server/classes && \
      mkdir -p /usr/share/tomcat9/temp && \
      mkdir -p /usr/share/tomcat9/common && \
      chown -R tomcat:tomcat /usr/share/tomcat9/ && \
      chown -R tomcat:tomcat /var/lib/tomcat9/ && \
      chown -R tomcat:tomcat /var/lib/tomcat9/webapps && \
      chown -R tomcat:tomcat /var/lib/tomcat9/webapps/* && \
      mkdir -p /opt/rasdaman/scripts


# Expose tomcat port
EXPOSE 8080

# Expose apache2 port
EXPOSE 80

CMD ./entrypoint.sh

