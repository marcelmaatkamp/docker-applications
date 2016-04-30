FROM ubuntu:14.04

# python && pip

RUN apt-get update && apt-get install -y \
  python-pip python-dev build-essential git telnet amqp-tools wget axel &&\
  pip install --upgrade pip &&\
  pip install --upgrade virtualenv

# GNURadio

WORKDIR /projects
RUN apt-get install git python-pip
RUN pip install PyBOMBS
RUN pybombs prefix init /usr/local -a default_prx
RUN pybombs config default_prefix default_prx
RUN pybombs recipes add gr-recipes git+https://github.com/gnuradio/gr-recipes.git
RUN pybombs recipes add gr-etcetera git+https://github.com/gnuradio/gr-etcetera.git

RUN apt-get install -y \
     autoconf automake \
     libfftw3-3 \
     libasound2 libasound2-data \
     libcppunit-dev \
     libgsl0ldbl libgsl0-dev \
     libbz2-dev \
     libboost-dev libboost-date-time-dev libboost-serialization-dev \
     libboost-filesystem-dev libboost-system-dev libboost-program-options-dev \
     libboost-regex-dev libboost-atomic-dev libboost-chrono-dev libboost-thread-dev \
     libboost-test-dev \
     cmake cmake-data \
     pkg-config \
     libbison-dev \
     libssl-dev libssl-doc zlib1g-dev \
     libevent-dev \
     libtool \
     python-crypto python-openssl python-pam python-pyasn1 python-serial \
     python-twisted python-twisted-bin python-twisted-conch python-twisted-core \
     python-twisted-lore python-twisted-mail python-twisted-names python-twisted-news \
     python-twisted-runner python-twisted-web python-twisted-words python-zope.interface \
     flex \
     python-cheetah \
     wget \
     liblog4cpp5-dev \
     libzmq3-dev \
     python-sip-dev \
     fontconfig fontconfig-config fonts-dejavu-core iso-codes libaudio2 \
     libavahi-client3 libavahi-common-data libavahi-common3 libcups2 \
     libdrm-amdgpu1 libdrm-dev libdrm-intel1 libdrm-nouveau2 libdrm-radeon1 \
     libelf1 libfontconfig1 libfreetype6 libgl1-mesa-dev libgl1-mesa-dri \
     libgl1-mesa-glx libglapi-mesa libglu1-mesa libglu1-mesa-dev \
     libgstreamer-plugins-base1.0-0 libgstreamer1.0-0 libice6 libjbig0 \
     libjpeg-turbo8 libjpeg8 libllvm3.4 libmysqlclient18 liborc-0.4-0 \
     libpciaccess0 libpthread-stubs0-dev libqt4-dbus libqt4-declarative \
     libqt4-designer libqt4-dev libqt4-dev-bin libqt4-help libqt4-network \
     libqt4-opengl libqt4-opengl-dev libqt4-qt3support libqt4-script \
     libqt4-scripttools libqt4-sql libqt4-sql-mysql libqt4-svg libqt4-test \
     libqt4-xml libqt4-xmlpatterns libqtcore4 libqtdbus4 libqtgui4 \
     libqtwebkit-dev libqtwebkit4 libsm6 libtiff5 libtxc-dxtn-s2tc0 libx11-dev \
     libx11-doc libx11-xcb-dev libx11-xcb1 libxau-dev libxcb-dri2-0 \
     libxcb-dri2-0-dev libxcb-dri3-0 libxcb-dri3-dev libxcb-glx0 libxcb-glx0-dev \
     libxcb-present-dev libxcb-present0 libxcb-randr0 libxcb-randr0-dev \
     libxcb-render0 libxcb-render0-dev libxcb-shape0 libxcb-shape0-dev \
     libxcb-sync-dev libxcb-sync1 libxcb-xfixes0 libxcb-xfixes0-dev libxcb1-dev \
     libxdamage-dev libxdamage1 libxdmcp-dev libxext-dev \
     libxi6 libxrender1 libxshmfence-dev libxshmfence1 libxslt1.1 libxt6 \
     libxxf86vm-dev libxxf86vm1 mesa-common-dev mysql-common qdbus \
     qt4-linguist-tools qt4-qmake qtchooser qtcore4-l10n x11-common \
     x11proto-core-dev x11proto-damage-dev x11proto-dri2-dev x11proto-fixes-dev \
     x11proto-gl-dev x11proto-input-dev x11proto-kb-dev x11proto-xext-dev \
     x11proto-xf86vidmode-dev xorg-sgml-doctools xtrans-dev \
     python-qt4 pyqt4-dev-tools \
     libqwt5-qt4 \
     libqwt-dev libqwt6 \
     python-numpy python-qwt5-qt4 \
     libfreetype6-dev libpng12-dev \
     libfontconfig1-dev \
     libpixman-1-0 \
     libxrender-dev x11proto-render-dev \
     libcairo2 \
     libdatrie1 libgraphite2-3 libharfbuzz0b libpango-1.0-0 libpango1.0-0 \
     libpangocairo-1.0-0 libpangoft2-1.0-0 libpangox-1.0-0 libpangoxft-1.0-0 \
     libthai-data libthai0 libxft2 \
     libglib2.0-dev \
     libjpeg-turbo8-dev \
     libtiff5-dev \
     libgdk-pixbuf2.0-dev \
     libatk1.0-0 \
     bsdmainutils debhelper dh-apparmor gettext gettext-base gir1.2-atk-1.0 \
     gir1.2-freedesktop gir1.2-gtk-2.0 gir1.2-pango-1.0 groff-base \
     hicolor-icon-theme intltool-debian libasprintf-dev libasprintf0c2 \
     libatk1.0-dev libcairo-gobject2 libcairo-script-interpreter2 libcairo2-dev \
     libcroco3 libgettextpo-dev libgettextpo0 libgtk2.0-0 libgtk2.0-bin \
     libgtk2.0-common libgtk2.0-dev libharfbuzz-dev libharfbuzz-gobject0 \
     libharfbuzz-icu0 libice-dev libmail-sendmail-perl libpango1.0-dev \
     libpipeline1 libpixman-1-dev libsm-dev libsys-hostname-long-perl \
     libunistring0 libxcb-shm0-dev libxcomposite-dev libxcomposite1 \ 
     libxcursor-dev libxcursor1 libxft-dev libxi-dev libxinerama-dev libxinerama1 \
     libxml2-utils libxrandr-dev libxrandr2 man-db po-debconf \
     x11proto-composite-dev x11proto-randr-dev x11proto-xinerama-dev \
     swig2.0 \
     python-wxgtk2.8 \
     python-cairo-dev \
     gobject-introspection \
     python-gobject-2-dev \
     python-gtk2 \
     libfftw3-dev \
     libxml2-dev \
     libxslt1-dev \
     python-lxml \
     libusb-1.0-0-dev \
     amqp-tools \
     wget axel telnet \
     nodejs npm 
     
RUN npm install -g amqp-ts
     
     
