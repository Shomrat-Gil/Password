<?php

/**
*  This class used to validate or generate a password by denied rules   
 *
 * 
 * @category Password
 * @package  Password
 * @author   Gil Shomrat 
*/
 

 
class password_rules {  
    
        protected static $instance ;     
  
        //Properties
        public  $arr_rules;
        private $arr_alpha;  
        private $arr_number;  
        private $arr_symbol;
        
        
        /**
        * set the construct
        *   
        * @param mixed $arr_rules
        * @return password_rules
        */
        public function __construct($arr_rules=array()) {  
                if(empty($arr_rules)){
                    $this->arr_rules = $this->default_rules();
                } else{
                    $this->arr_rules = $arr_rules;
                }
                $this->arr_alpha_upper  = range("A","Z");
                $this->arr_alpha_lower  = range("a","z");
                $this->arr_number       = range( 0,9 );
                //$this->arr_symbol       = array( "-", "_", "^", "~", "@", "&", "|", "=", "+", ";", "!", ",", "(", ")", "{", "}", "[", "]", ".", "?", "%", "*", "#" );       
                $this->arr_symbol       = str_split($arr_rules['acceptableSymbols']);      
        }  
        
        /**
        * get instance of password_rules
        * 
        * @param mixed $arr_rules = array of defined: minLength, mustUseUppercase, mustUseLowercase, number_include, mustUseSymbol
        * @return password_rules
        */
        public static function GetInstance($arr_rules=array()){  
            if(empty(self::$instance)){
                self::$instance = new password_rules($arr_rules);
            }
            return self::$instance;
        } 
        
        /**
        * default rules setting
        * 
        */
        private function default_rules(){
            return array(
                        'minLength'          =>    8,
                        'mustUseUppercase'   =>    true,
                        'mustUseLowercase'   =>    true,                        
                        'mustUseNumber'     =>    true,
                        'mustUseSymbol'      =>    true,    
                    );
        }
        
        
        /**
        * generate rundum password by password rules
        * 
        */
        public function generate_password() {     
                $str_password = '';
                $arr_values = array();
                $arr_might = array();
                // use Alpha Upper  
                if( password_rules::$instance->arr_rules['mustUseUppercase']  ) {
                    $arr_values[] = password_rules::$instance->arr_alpha_upper;  
                }else{
                    $arr_might[] = password_rules::$instance->arr_alpha_upper;
                }                    
                // use Alpha Lower 
                if( password_rules::$instance->arr_rules['mustUseLowercase'] ) {
                    $arr_values[] = password_rules::$instance->arr_alpha_lower ;  
                }else{
                    $arr_might[] = password_rules::$instance->arr_alpha_lower;
                }                    
                // use Numbers 
                if( password_rules::$instance->arr_rules['mustUseNumber'] ) {
                    $arr_values[] = password_rules::$instance->arr_number;   
                } else{
                    $arr_might[] = password_rules::$instance->arr_number;
                }                
                // use Symbols  
                if( password_rules::$instance->arr_rules['mustUseSymbol'] ) {
                    $arr_values[] = password_rules::$instance->arr_symbol;  
                } else{
                    $arr_might[] = password_rules::$instance->arr_symbol;
                } 
                             
                // mix the in use rules array
                if(!empty($arr_values)){
                     shuffle($arr_values);  
                }
                if(!empty($arr_might)){
                     shuffle($arr_might);
                    $arr_values = $arr_values + $arr_might; 
                }
                $loop = count($arr_values);
                $x=0;
                $length = rand(password_rules::$instance->arr_rules['minLength'], (password_rules::$instance->arr_rules['minLength']+2) );
                while ($length>0) {
                    $length--; 
                    $x = $x>=$loop?0:$x;
                    shuffle( $arr_values[$x] );  
                    $str_password .= $arr_values[$x][0];  
                    $x++;
                }     
                // mix the Password 
                return str_shuffle($str_password); 
    }    
    
    
    /**
    * validate exists password accordion to password rules
    *     
    * @param mixed $password
    */
   public function validate_password($password=null){       
                // validate: min minLength
                if(strlen($password) < password_rules::$instance->arr_rules['minLength']){
                     return false; 
                } 
                // validate: use Alpha Upper  
                if( password_rules::$instance->arr_rules['mustUseUppercase'] && !preg_match('@[A-Z]@', $password) ) {
                    return false;   
                }                    
                // validate: use Alpha Lower 
                if( password_rules::$instance->arr_rules['mustUseLowercase'] && !preg_match('@[a-z]@', $password )) {
                    return false;  
                }                   
                // validate: use Numbers 
                if( password_rules::$instance->arr_rules['mustUseNumber'] && !preg_match('@[0-9]@', $password ) ){
                    return false;  
                }                
                // validate: use Symbols  
                if( password_rules::$instance->arr_rules['mustUseSymbol'] ) {
                    $str_symbols =  implode("\\",password_rules::$instance->arr_symbol);
                    if(!preg_match('/['.$str_symbols.']/', $password  )){
                        return false;
                    }
                }
                // all validated
                return true;
   }    
   
   
          
                 
    
    
 //  END CLASS
}

  

 $obj_password_rules = password_rules::GetInstance(); 
  $mix_password =  $obj_password_rules->generate_password();
  echo "New password is: ".$mix_password;
  $password_vailed =  $obj_password_rules->validate_password($mix_password) ;
  echo "     ".($password_vailed?"Vailed":"UnVailed");
   
?>