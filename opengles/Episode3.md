---
layout: page
title: "Episode 3: OpenGL en C/C++ pour Android"
description: ""
tags : [Android, C++, OpenGL]
---


Installation du NDK
-------------------

### Installation du NDK

Naturellement, point d'espoir de compiler du C/C++ avec le SDK Android, il est nécessaire d'installer **en supplément** le NDK. 

[Telechargez le NDK](http://developer.android.com/sdk/ndk/index.html)

Concernant la configuration du NDK, il suffit de placer l'archive décompressée dans un répertoire et d'ajouter le chemin du NDK décompressé dans le path. Pour tester le bon fonctionnement, il suffit alors de vérifier la présence de ndk-build:

    ellis$ ndk-build -h
    Elise:tutorial ellis$ ndk-build --h
    Usage: make [options] [target] ...
    Options:
    ...

### Test du NDK

Le NDK contient un ensemble d'exemples dont 3 relatifs aux NativeActivity. Il est important de les tester pour vérifier que votre téléphone est bien compatible avec. Ainsi, la cyanogen 7 RC1 ne l'est pas... Je vous recommande d'utiliser une nightly build mars 2011 minimum. Android 2.3.3 pour N1 l'est également.

Procédure de test:

    ellis$ cd chemin_du_ndk
    ellis$ cd samples/native-activity/
    ellis$ ndk-build
    
Ensuite, pour finaliser le packaging, il est nécessaire d'installer les fichiers suivants: build.xml, local.properties, default.properties. Vous pouvez utiliser les fichiers des épisodes 1 et 2. Ensuite, il faut créer le package:

    ellis$ ant install

Il ne reste plus qu'à tester l'application native-activity sur votre téléphone.

Native Activity
---------------

Accessible depuis Android 2.3, l'activité NativeActivity permet de créer des applications Android sans aucune ligne de C/C++ et donc d'éviter l'utilisation du JNI, fastidieuse pour les jeunes développeurs C/C++.
