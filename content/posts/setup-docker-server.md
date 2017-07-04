---
title: "Setup Docker Server using RancherOS"
date: 2017-07-03T15:05:36+02:00
draft: false
image: "docker-server.jpg"
---

Docker is gaining more and more traction. If you wrapped your head around the basic idea of Docker, it is no wonder, as it is a very easy way of setting up an application or development environment. Especially if you can use the native Docker for Windows or Mac applications.

But when it comes to deployment using Docker, things are getting a bit more complicated. You have to prepare a server, that runs Docker, if it is a Linux Host you get into permission trouble with your shared files and how do you reliably run your application?

With this new portfolio site I wanted to use a Docker based setup and I'd like to take you through the steps, I have taken.

## Prerequisites

I'm using a VPS of the German hoster [netcup](https://netcup.de), but every root or virtual server, that you can boot from an ISO to install an operating system and has decent CPU performance and RAM, will suffice (I'm rocking 4GB of RAM and a Quad Core Virtual CPU).
You should also be comfortable with the idea of SSH keys and be able to access the host via some kind of VNC console.

## RancherOS

As a host system, there are many options. You could just go with your favorite Linux distribution, but this will introduce a large overhead and permission issues.

Instead you could grab a distribution that is specialised on hosting Docker. The most famous representative of this category must be CoreOS, but after reading an [interesting comparison](https://blog.codeship.com/container-os-comparison/) by Jonas Rosland on Codeship my interest was driven towards RancherOS.

[RancherOS](http://rancher.com/rancher-os/) is a super minimal Linux distribution. The downloaded ISO comes in at around 60 MB!
The only thing is does is launching one system docker process, that is responsible for running all system components in containers.
These components include a shell, SSH access and a user space docker process, that will be used to run your applications.

## Install RancherOS

To install RancherOS make your host boot from the ISO image. A live version of RancherOS will be booted. Although there is an SSH daemon running you won't be able to connect via SSH now, because the password of the standard user is randomized. Instead start a VNC session and you should see that you are already logged in.

The first step is to provide a `cloud-config.yml` file, that is used to configure the installation. You use it to supply public keys for SSH and could also define some initial bootstrapping.

Create a file named `cloud-config.yml` with the following contents:

```
hostname: <YOUR_HOSTNAME>
ssh_authorized_keys:
  - <YOUR_PUBLIC_KEY>
```

Replace the wildcards `<YOUR_HOSTNAME>` with a hostname, you'd like to use for the machine and `<YOUR_PUBLIC_KEY>` with the contents of your SSH public key file (include the *ssh-rsa* prefix!).

To upload this file to your server jump back into the VNC console and at first change the password of the main user called *rancher* by issuing the command

```
sudo passwd rancher
```

Follow the instructions to set a password, that you should remember at least for short.
Now you can use `scp` to upload the file into the home directory of the server:

```
scp /path/to/local/cloud-config.yml rancher@<YOUR_IP>:~/cloud-config.yml
```

Afterwards ssh into the server. You should see the uploaded file in the home directory.
RancherOS is now ready to install, thus issue the following command:

```
sudo ros install -c cloud-config.yml -d /dev/sda
```

**Attention**: This will clear the complete `/dev/sda` drive and install RancherOS.

This might take some time. Afterwards shut down the server, remove the ISO image and boot the server again.
Now try to connect to your server via SSH. This should succeed without a password prompt (the password would have been set back anyway!).

This is it! RancherOS is ready to run! Let's run our first container!

## Running containers

This is the best part: The docker command is completely available, so you could start a server quite easily:

```
docker run --name my-server -d -p 80:80 nginx:latest
```

No mounts yet, but that's up to your setup. That's all there is to it!

## What's next?

We are now able to run any docker container on our host without any problems. But there are some drawbacks. For example there is no docker-compose support (at least not the way, we are used to it).
Additionally we have no easy way to monitor our containers. So we should step up a notch and take a look at a full-fledged orchestration solution by the same guys, who also created RancherOS: Rancher.
With this tool we will be easily able to create complex docker setups and it integrates seamlessly in RancherOS.
In the next post, we will install Rancher and deploy a reverse http(s) proxy, that can be used to have multiple virtual hosts on the same machine. Stay tuned!