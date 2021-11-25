Sign X Lossless Decoder(XLD) log
==

This is a 32bit app due to the nature of the signing algorithm
so you need 32bit libs

  - e.g. apt-get install libssl-dev:i386

Compile with -m32

Tested on Debian 64

Tools used:
  + Ollydbg
  + Wine
  + IDA Pro with Hex-Rays decompiler

Notes
--
  - First of the algo uses a custom IV for it's SHA256
  - Then lots of messing around
  - And jumping around a base64 table.

  - Most the code here is fixed up Hex-Rays dump from the Mac XLDLogChecker.bundle
  - The AKA sub_401AA0 refer to the win32 xldlogchecker.exe

Necessary apt-get installs on Debian 8:
```
sudo apt-get install libssl-dev:i386
sudo apt-get install clang++-3.5
sudo apt-get install gcc-multilib g++-multilib
sudo apt-get install libc++-dev
```
