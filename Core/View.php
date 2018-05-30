<?php

namespace Core;

/*
    View
*/
class View {

    
    /*
        Render a view file
        
        note: it is a static function, we will not create View objects.
        
        @param string $view : relative location of template file, e.g. Home/index.html
        @return void
    */
    
    public static function render($view, $args = []) {
            
        /*
            Create new Twig environment: 
            Twig will create a 'cache' folder in the root directory for caching.
            App/Views will be the parent directory for templates.
        */
        static $twig = null;
        if($twig === null) {
            $loader = new \Twig_Loader_Filesystem(dirname(__DIR__) . '/App/Views');
            /*
                For development, we will disable cache for twig.
                For production, we should enable cache by using the follwing line instead
            */
            $twig = new \Twig_Environment($loader);
            //$twig = new \Twig_Environment($loader, array('cache' => dirname(__DIR__) . '/cache'));
            
            // make Auth data available in twig
            $twig->addGlobal('user', \App\Auth::getUser());
            $twig->addGlobal('flash_messages', \App\Flash::getMessages());
            $twig->addGlobal('view', $view);
			$twig->addGlobal('campus', \App\Auth::getCampus());
			$twig->addGlobal('dept', \App\Auth::getDept());
			$twig->addGlobal('programme', \App\Auth::getProgramme());
            $twig->addGlobal('semester', \App\Auth::getSemester());
			$twig->addGlobal('messages', \App\Auth::getMessages());
        }
        
        // load the template
        $template = $twig->load($view);
        // render using twig
        echo $template->render($args);
        
    }
    

}// end class

?>