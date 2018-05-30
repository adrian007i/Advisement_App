<?php 

namespace Core;

/*
    Error and Exception handler
*/
class Error {
    
    /*
        Error Handler.
        Functional PHP does not throw exceptions when errors occur.
        We will convert errors into exceptions by throwing our own ErrorException
        
        @param  int      $level      Error level
        @param  string   $message    The error message
        @param  string   $file       Filename the error was raised in
        @param  int      $line       Line number where the error occured
    */
    public static function errorHandler($level, $message, $file, $line) {
        
        if (error_reporting() !== 0) {
            throw new \ErrorException($message, 0, $level, $file, $line);
        }
        
    }
    
    /*
        Exception Handler.
        What to do when exceptions occur
        
        @param  Exception   $e  The exception
    */
    public static function exceptionHandler($e) {
        
        $cname = get_class($e);
        $msg = $e->getMessage();
        $trace = $e->getTraceAsString();
        $file = $e->getFile();
        $line = $e->getLine();
        
        // set the code to response code to 500 if it is not 404
        // you will get 404 from the Router class only.
        $code = $e->getCode();
        if($code != 404) {
            $code = 500;
        }
        http_response_code($code);
        
        
        // only if internal error and not 404 are we interesting in logging or viewing
        if ($code == 500 || $code == 404) { // remove 'or 404'
            
            // display error on page.
            if (\App\Config::SHOW_ERRORS) {

                echo '<div style="position: absolute; z-index:99999; "class="alert alert-sm alert-danger alert-dismissible fade show" role="alert">';

                echo "<h1>Fatal Error</h1>";
                echo "<p>Uncaught exception: '$cname'</p>";
                echo "<p>Message: '$msg'</p>";
                echo "<p>Stack trace:<pre>$trace</pre></p>";
                echo "<p>Thrown in '$file' on line #$line</p>";

                echo '<button type="button" class="close" data-dismiss="alert" aria-label="Close"> 
                <span aria-hidden="true">&times;</span> </button> </div>';

            } 
        
            // log error to log file
            if (\App\Config::LOG_TO_FILE) {

                //create the log file, file name will be date
                $log = dirname(__DIR__) . '/logs/' . date('Y-m-d') . '.log';
                ini_set('error_log', $log);

                $output =  "Fatal Error occured at " . date('r') . PHP_EOL; 
                $output .= "Uncaught exception: '$cname'" . PHP_EOL;
                $output .= "Message: '$msg'" . PHP_EOL;
                $output .= "Stack trace: $trace" . PHP_EOL;
                $output .= "Thrown in '$file' on line #$line" . PHP_EOL;

                error_log($output);
            }
        
        }
        
        //render either the 404 or 500 html file
        View::render("$code.html");
        
    }
    
    
}// end class

?>