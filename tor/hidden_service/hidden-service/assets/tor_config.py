#!/usr/bin/python3

import os
from docker_links import DockerLinks
from subprocess import call

# Generate conf for tor hidden service
def set_conf():
    rtn = []
    links = DockerLinks()
    with open("/etc/tor/torrc", "a") as conf:
        for link in links.links():
            name=links.links()[link]['names'][1]
            path = "/var/lib/tor/hidden_service/{service}".format(service=name)
#           if ( name == 'bro' or name == 'bro-xinetd' ):
#              continue
            # Test if link has ports
            if len(links.links()[link]['ports']) == 0:
                print("{link} has no port")
                continue
            conf.write('HiddenServiceDir {path}\n'.format(path=path))
            rtn.append(name)
            for port in links.links()[link]['ports']:
                if links.links()[link]['ports'][port]['protocol'] == 'UDP':
                    continue
                service = '{port} {ip}:{port}'.format(
                    port=port, ip=link
                )
                conf.write('HiddenServicePort {service}\n'.format(
                    service=service
                ))
        # set relay if enabled in env (not so secure)
        if 'RELAY' in os.environ:
            conf.write("ORPort 9001\n")
        # Disable local socket
        conf.write("SocksPort 0\n")
    return rtn

def gen_host(services):
    # Run tor to generate keys if they doesn't exist
    call(["sh", "-c", "timeout 3s tor > /dev/null"])
    for service in services:
        filename = "/var/lib/tor/hidden_service/{service}/hostname".format(
            service=service
        )
        with open(filename, 'r') as hostfile:
            onion=hostfile.read()[:22]
            print('{onion}'.format(
                onion=onion
            ))

if __name__ == '__main__':
    filename = "/var/lib/tor/hidden_service/.configured"

    if not os.path.isfile(filename) : 
        open(filename, 'a').close()
        services = set_conf()
        gen_host(services)
