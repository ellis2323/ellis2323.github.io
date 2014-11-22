---
layout: page
title: "Episode 1: Afficher 30000 points"
description: ""
---

L'objectif de ce chapitre est la réalisation d'un premier exemple mais visuellement intéressant.

Création du squelette du programme
==================================

Comme toute application Android, il est nécessaire de définir une activity et d'y inclure la méthode onCreate pour y définir les layouts et les vues. Dans le cas présent, on s'intéresse à la partie OpenGL et l'on n'utilisera pas de layout. Il suffit donc d'écrire dans notre classe dérivée d'activity:

    //! OpenGL SurfaceView
    public GLSurfaceView mGLSurfaceView;
    
    @Override
    public void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        
        if (!isOGLES20Compatible()) {
            // C++ Reflex sorry
            mGLSurfaceView = null;
            showOGLES20ErrorDialogBox();
            return;
        }
        
        // We don't use Layout. But you can.
        // create an OpenGLView
        mGLSurfaceView = new GLSurfaceView(this);
        mGLSurfaceView.setEGLContextClientVersion(2);
        mGLSurfaceView.setRenderer(new GLES20Renderer(this));
        setContentView(mGLSurfaceView);
    }

Description détaillée:

 * isOGLES20Compatible [internal]: vérifie que notre téléphone dispose d'un GPU compatible avec l'openGL ES 2.0
 * showOGLES20ErroDialogBox [internal]: affiche une boite de dialogue vous indiquant d'acheter un vrai téléphone
 * setEGLContextClientVersion: méthode de GLSurfaceView qui indique que l'on veut travailler en OpenGL ES 2.0 par défaut on obtient un context EGL/GLES 1.0
 * setRenderer: méthode de GLSurfaceView permettant de spécifier la classe chargée de l'affichage.
 
La nomemclature [internal] indique cette fonction n'est pas fournie telle quelle par Android. Le code est dans le fichier source.

Comme toute activité, il faut définir également définir ce que l'on fait en cas de Pause et de reprise. Il faut donc définir le code des méthodes onResume et onPause. La classe GLSurfaceView contient les méthodes onResume et onPause qui suffisent dans un premier temps. On se contente donc de les appeler.

Enfin, après avoir défini l'Activity, il faut indiquer le code du renderer. Le renderer est une instance de la classe GLES20Renderer qui est exécuté par un thread définit par la GLSurfaceView. Il faut définir trois méthodes:

 * onSurfaceCreated: cette méthode est appelée lors de la création de la surface. les contextes OGLES20 et GL sont valides; il est possible de faire de l'openGL. Elle est appelée à chaque création de contexte; il ne faut pas oublier qu'en pause, on perd le contexte OpenGL et qu'il est recréé lors du resume.
 * onSurfaceChanged: cette méthode est appelée lors d'un changement de taille et lors de la création. Cela se produit notamment lors d'un changement d'orientation;
 * onDrawFrame: à chaque affichage cette méthode est appelée; le framerate est liée à cette méthode. Il est toutefois limité à une valeur spécifique pour chaque Device (le Samsung GS est à 56fps, le N1 à 60fps, certains sont limités à 30fps honteux et merci les ROMs alternatives pour faire sauter les limites stupides).

Le code:
    
    public class GLES20Renderer implements GLSurfaceView.Renderer {
         private Activity mActivity;
                  
         GLES20Renderer(Activity activity) {
             mActivity = activity;
         }
         
         @Override
         public void onSurfaceCreated(GL10 gl, EGLConfig eglConfig) {
         }
         
         @Override
         public void onSurfaceChanged(GL10 gl, int width, int height) {
             gl.glViewport(0, 0, width, height);
         }
         
         @Override
         public void onDrawFrame(GL10 gl) {
             gl.glClearColor(1.0f, 0.0f, 0.0f, 1.0f);
             gl.glClear( GLES20.GL_DEPTH_BUFFER_BIT | GLES20.GL_COLOR_BUFFER_BIT);
         }
     }
     

On découvre nos premières commandes OGLES 2.x:

 * glViewport: on indique que la fenêtre OpenGL est le rectangle (Pt1(0,0) Pt2(480, 800))
 * glClearColor: on indique la couleur du back-buffer OGLES 2.0 quand on efface tout
 * glClear: commande permettant d'effacer le back-buffer (GL_COLOR_BUFFER_BIT) et le Z-Buffer (GL_DEPTH_BUFFER_BIT)
 
Remarque: un benchmark qui fonctionne à 56fps sur un Samsung GS est le témoin que le GPU peut faire mieux! Bref ne pas tirer de conclusions hâtives quand on voit des benchmarks au dessus de 50fps pour comparer les devices.

Code de la première partie: [Episode 1 part 1](./episode1.part1.tgz)

Dessiner les points
===================

Dessiner en OpenGL
------------------

Pour dessiner en OpenGL, on dispose de très peu d'outils:

 * GL_POINTS: ce sont les pointsprites, des carrés gérés comme des points (utiles pour les particules)
 * GL_LINES: les lignes
 * GL_TRIANGLES: les triangles 

Il existe des versions optimisées FAN et STRIP pour les lignes et les triangles mais nous étudierons cela en détail prochainement.

La définition d'un pointsprite, d'une ligne ou d'un triangle est réalisée au travers de sommets (vertex, vertices au pluriel). Chaque sommet dispose de propriétés que vous définissez vous même. On trouve généralement:

 * la position en 3D du sommet (x, y, z)
 * la ou les coordonnées de texture (u,v)
 * la couleur du sommet (rgba)
 * la normale du sommet ...

Généralement, pour éviter un nombre trop important de transferts, on regroupe les vertices dans un tableau. Et dans un second, on place les indices définissant les formes. Bien évidemment, un point est constitué d'un indice, une ligne deux, le triangle trois.

Ainsi, dans notre exemple, on définit une classe Vertices, qui contient un tableau de Vertices et un tableau d'indices, et la classe P3FT2FR4FVertex qui permet de définir un Vertex avec un attribut position (3 float), une texture (2 float) et une couleur (4 float).

Les shaders
-----------

Comme il a été indiqué, OGLES 2.x utilise un pipeline programmable. Il est donc nécessaire de fournir le programme chargé du dessin.

Nous ne détaillerons pas toutes les étapes nécessaires à l'utilisation du programme. En quelques lignes:

 * création du programme
 * lecture du code source des shaders dans des fichiers textes "assets"
 * compilation des shaders
 * attachement des shaders au programme
 * link du programme

Dans l'API OGLES 2.x, il existe deux types de shaders:

 * Vertex Shader: function qui est réalisée pour chaque sommet
 * Fragment Shader: function qui est réalisée pour chaque point dessiné
 
### Vertex Shader ###

Comme il a été vu dans la section Dessiner en OpenGL, quand on dessine un triangle, on fournit trois Vertices. Chaque Vertex de ce triangle est alors soumis à une fonction "le vertex shader". 

Cette fonction dans notre exemple réalise la transformation du point en 3D en 2D.

Notre code de vertex shader:

    uniform mat4 uMvp;
    
    attribute vec3 aPosition;
    attribute vec2 aTexCoord; 
    attribute vec4 aColor;
    
    varying vec4 vColor;
    
    void main() {
        vec4 position = uMvp * vec4(aPosition.xyz, 1.);
        vColor = aColor;
    
        gl_PointSize = 1.;
    	gl_Position =  position;
    }

Si vous êtes curieux, vous pouvez tenter de changer la valeur de gl_pointsize.

Dans un chapitre futur, nous étudierons en détail le GLSL.

### Fragment Shader ###

Lorsque l'on dessine un triangle en 3D, on obtient un triangle en 2D. Ce triangle est un ensemble de points à l'écran. Pour chaque de ses points, OGLES 2.x exécute une fonction pour définir la couleur de ce point, le fragment shader.

Notre code de fragment shader:

    #ifdef GL_ES
    precision highp float;
    #endif 
    uniform sampler2D tex0;
    
    varying vec4 vColor;
    
    void main() 
    {
        gl_FragColor = vColor;
    }



### Utilisation du programme ###

La gestion du programme OGLES 2.x est confiée à la classe GLSLProgram. Elle contient les méthodes suivantes:

 * un constructeur avec un nom: ce nom permet de lire les fichiers nom.vsh (code du vertex shader) et nom.fsh (code du fragment shader)
 * la méthode make: elle charge les fichiers sources du vertex shader et du fragment shader, les compile et link le programme
 * la méthode use: elle indique au processeur d'utiliser ce programme
 * la méthode draw: elle réalise le rendu de notre objet Vertices.
 
Le code source
--------------

Afin de faire fonctionner le tout, il suffit d'assembler le puzzle:

 * dans la méthode onSurfaceCreated du GLES20Renderer: on place la création du GLSLProgram
 * dans la méthode onDrawFrame du GLES20Render: on efface l'écran et on dessine nos points




Pour rendre le programme plus intéressant, j'ai ajouté une rotation dans le GLSLProgramme:

    counter += 1.f;
    Matrix.setRotateEulerM(mRotation,0, 0.f, 0.f, counter);
    Matrix.multiplyMM(mMvp, 0, mProjection, 0, mRotation, 0);

Performance: Sur mon nexus One, je suis à 40fps pour 30 000 points.

Code de la seconde partie: [Episode 1 part 2](./episode1.part2.tgz)

J'utilise ant pour compiler les programmes sous Android. Si vous utilisez Eclipse, il suffit de créer un nouveau projet Android à partir de code source existant. Enfin, il faut définir une configuration pour pouvoir le lancer et voilà c'est tout.
 



