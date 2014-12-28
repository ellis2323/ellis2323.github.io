---
layout: page
title: Sauver votre Macbook Pro 2011
tagline: Hackintosh
tags : [Hackintosh]
---

## Introduction

En 2013, mon macbook pro 2011 a commencé à montrer des signes de disfonctionnement. Comme
de très nombreux acheteurs de cette série, il s'agit de problèmes GPU et plus spécifiquement
de sa soudure. Au début, il s'agissait d'artefacts et puis ... mon gpu a rendu l'ame. L'astuce initiale, forcer le GPU intel après le démarrage ne fonctionnait plus [gfxCardStatus](https://gfx.io/).

Desperemment, j'ai tenté de forcer le gpu intel en supprimant les extensions kext suivantes:

	AMDRadeonAccelerator.kext
	AMDRadeonVADriver.bundle
	AMDRadeonX3000GLDriver.bundle
	AMDRadeonX4000GLDriver.bundle
	ATI2400Controller.kext
	ATI2600Controller.kext
	ATI3800Controller.kext
	ATI4600Controller.kext
	ATI4800Controller.kext
	ATI5000Controller.kext
	ATI6000Controller.kext
	ATI7000Controller.kext
	ATIFramebuffer.kext
	ATIRadeonX2000.kext
	ATIRadeonX2000GA.plugin
	ATIRadeonX2000GLDriver.bundle
	ATIRadeonX2000VADriver.bundle
	ATISupport.kext

Mais, cette solution n'est pas acceptable:

	Pas d'accélération OpenGL: Pas de Photoshop et une interface si lente Slow...
	Pas de réglage de la luminosité, pas de veille,

Mon mac était juste plus utilisable!!! j'ai tenté de travailler quelques heures et puis je me suis résolu à lui trouver un remplaçant: un mac mini 2012, 16Go ram et un ssd, que
j'ai installé par moi même. J'ai suivi les nombreux fils de discussions liés au sujet et
fut déçu par l'attitude d'Apple.

Depuis ce jour, j'ai installé quelques hackintoshs pour mes amis; Et en ce noel 2014, j'ai tenté l'installation de yosemite sur mon mbp 2011 comme s'il s'agissait d'un hackintosh. Et ça fonctionne!!! Le meilleur c'est qu'il avance encore plutot bien le bougre!
Si tu mbp 2011 est bloqué sur un écran noir et que tu veux l'utiliser, alors suit ce tutoriel. C'est si simple.

Je ne vais pas faire l'aumone, mais si tu trouves ce tuto et cette solution sympa et que tu as déja donné à wikipedia et consort, tu peux me faire un petit don (Paypal) ellis at redfight.com

{% include paypal_button.html %} 

Pour me suivre sur twitter [@ellis2323](https://twitter.com/ellis2323).

## Prerequis

Tu as besoins d'un autre mac et d'une clef usb > 8Go prète à être formattée. Je ne peux
malheureusement pas vous partager mon iso du à d'évidentes raisons légales... Avec ce mac, nous allons construire une clef usb bootable grace l'outil Unibeast.

 1. Téléchargez Yosemite sur l'[App Store](https://itunes.apple.com/fr/app/os-x-yosemite/id915041082?mt=12).
 2. Téléchargez [Unibeast](http://www.unibeast.com/) et placez le dans /Applications ou autre

## Préparation de la clef USB

 1. lancer **Utilitaire de disque** présent dans /Applications/Utilities 
 2. Selectionnez votre clef dans le panneau gauche (16.36GB UFD ... dans mon cas) ![disk utility 1](disk_utility_1.png)
 3. choississez l'onglet **Partition** et le schéma de formattage **1 Partition**, n'oubliez pas de sélectionner dans **Options** le choix **Master Boot Record** (PAS GUID). Enfin, on valide avec le bouton **Appliquer**![disk utility 2](disk_utility_2.png)

## Passage en Anglais

Pour le fonctionnement de Unibeast, il faut passer son mac en Anglais. Pour cela, il suffit
de lancer l'Application **Préferences Systèmes**. Dans la section **Langue et région**, on glisse Anglais en premier.

## Création de la clef USB

 1. Lancer l'application unibeast que vous avez placé dans /Applications probablement
 2. Cliquez sur **Continue** sur l'écran d'accueil ![unibeast startup](unibeast_startup.png)
 3. Cliquez encore **Continue** sur l'écran readme ![unibeast readme](unibeast_readme.png)
 4. Cliquez **Continue** sur l'écran de license ![unibeast license](unibeast_license.png)
 5. Cliquez **Agree** pour accepter la license ![unibeast agree](unibeast_agree.png)
 6. Selectionnez votre clef usb (l'icone blue indique votre choix) ![unibeast select](unibeast_selectusbkey.png)
 7. Selectionnez le système Yosemite et cliquez sur **Continue** ![unibeast yosemite](unibeast_yosemite.png)
 8. Selectionnez **Laptop Support** et cliquez sur **Continue** ![unibeast laptop](unibeast_laptop.png)
 9. Vérifier que vous avez les mêmes options que moi et cliquez sur **Continue** ![unibeast verify](unibeast_verify.png)
 10. Fournissez votre mot de passe pour créer la clef et attendez 20 minutes ![unibeast password](unibeast_password.png)
 11. L'installation est finie Finished ![unibeast password](unibeast_end.png)

## Installation de Yosemite

La seule astuce restante est le boot sur votre clef usb. Pour cette opération, démarrer votre macbook pro avec la touche **option** appuyée. Quand les lecteurs apparaissent,
selectionnez l'icone avec uefi (le lecteur est jaune chez moi) ![boot uefi](boot_uefi.png).

Maintenant il s'agit de l'installeur standard de Yosemite. Il est possible de mettre à jour
ou de reformatter. j'ai tenté avec succès les deux options.

## Une dernière Astuce

Enfin après le redemarrage et la création de votre compte, votre macbook pro va vous paraitre un poil lent. Il doit s'agir d'un élèment du macbook pro mal reconnu. En fait,
le mac se comporte comme si le cpu chauffait même si les ventilateurs ne sont pas fonctionnels. Il suffit de lancer le moniteur d'activité et d'afficher tous les processus
pour découvrir le fameux kernel_task process avec son occupation cpu > 250%... Denier effort:

 1. Lancer **Terminal** dans /Applications/Utilities
 2. ensuite tapez les lignes suivantes (ou copier/coller si vous êtes paresseux)

	$ mkdir -p ~/backup
	$ cd /System/Library/Extensions/IOPlatformPluginFamily.kext/Contents/PlugIns/ACPI_SMC_PlatformPlugin.kext/Contents/Resources
	$ sudo mv MacBookPro8_2.plist ~/backup/

3. redémarrer votre mac

Congratulations, votre macbook pro 2011 doit être fonctionnel. 

## Conclusion

Je ne recommande plus l'achat de mbp avec le  gpu dédié. Plus jamais!!! Il s'agit de mon second macbook à mourir du aux GPU (problème avec le macbook 2009 avec la carte nvidia). Si vous avez rencontré des difficultès avec ce tutoriel, vous pouvez m'envoyer un mail à ellis _at_ redfight _dot_ com. 


## Liens

- https://discussions.apple.com/thread/4766577?start=310
- http://blog.viktorpetersson.com/post/100148585299/how-to-fix-kernel-task-cpu-usage-on-yosemite
