<?php 
date_default_timezone_set("Asia/Dhaka");   
class Logger {
    private $lName = null;
    private $handle = null;
 
    public function __construct($logName = null) {
        if ($logName) $this->lName = $logName; //Define Log Name!
        else $this->lName = "Log"; //Default name
        $this->logOpen(); //Begin logging.
    }
 
    function __destruct() {
           fclose($this->handle); //Close when php script ends (always better to be proper.)
    }
 
    //Open Logfile
    private function logOpen(){
        $today = date('Y-m-d'); //Current Date
        $this->handle = fopen($this->lName . '_' . $today.'.log', 'a') or exit("Can't open " . $this->lName . "_" . $today); //Open log file for writing, if it does not exist, create it.
        //$this->handle = fopen($this->lName.'.log', 'a') or exit("Can't open " . $this->lName); //Open log file for writing, if it does not exist, create it.
      }
 
      //Write Message to Logfile
      public function logWrite($message){
        $time = date('m-d-Y H:i:s -'); //Grab Time
        //fwrite($this->handle, $time . "  " . $message . "\n"); //Output to logfile
        fwrite($this->handle, "$time   $message\r\n"); //Output to logfile
      }
 
      //Clear Logfile
      public function logClear(){
        ftruncate($this->handle, 0);
    }
}
?>