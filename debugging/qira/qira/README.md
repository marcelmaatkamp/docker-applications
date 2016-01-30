[Source](http://qira.me/ "Permalink to qira")

# qira
  
![][1]

## QIRA is a timeless debugger.

All state is tracked while a program is running, so you can debug in the past.

## Install it now.

Linux required, 64-bit Ubuntu recommended.

cd ~/ && wget -qO- qira.me/dl | unxz | tar x && cd qira && ./install.sh

## b *0x8048446

    Your breakpoint was hit 5 times, at change 90, 111, 128, 145, and 162.
    I drew red lines in the vtimeline for you to signify this.
    Would you like to see the memory at those times? Just click.
    Or navigate between them with j and k

## info registers; x/32wx 0xf6ffee80

![][2]

#### Instructions are red. Data is yellow. And registers are colorful.

## cat /proc/self/maps

![][3]

## watch *($esp+0x1c)

| ----- |
|  ![][4] |

####  Reads are dark yellow.

Writes are bright yellow.

The selected change is blue.

 |

## IDA Integration

![][5]

#### Just install the plugin in ~/qira/ida/bin

## qira -s ./a.out

# or if you like long commands  
socat tcp-l:4000,reuseaddr,fork exec:"qira ./a.out"

[1]: http://qira.me/img/first_splash.png
[2]: http://qira.me/img/hexdump.png
[3]: http://qira.me/img/haddrbar.png
[4]: http://qira.me/img/watch.png
[5]: http://qira.me/img/ida.png
  
