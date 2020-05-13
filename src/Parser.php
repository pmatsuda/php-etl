<?php
class Parser 
{    
    protected $_keys = array('account_number', 'account_name', 'transaction_fee', 'phone_number');
    protected $_csvFile;

   /**
    * Open file for reading
    *
    * @param string $filepath
    */
   public function fileOpen($filepath)
   {
        $this->_csvFile = fopen($filepath, "r");
   }

   /**
    * Close file
    */
   public function fileClose()
   {
        fclose($this->_csvFile);
   }

   /**
    * Read one line of data from the file and return an associative array
    * with column headers as keys
    *
    * Return false when EOF is reached.
    *
    * @return array[
    *     'account_number'  => String,
    *     'account_name'    => String,
    *     'transaction_fee' => String,
    *     'phone_number'    => String
    * ]|false
    */
   public function parse()
   {
     
     if (!feof($this->_csvFile))
     {
          $data = fgetcsv($this->_csvFile, 500, "|");
          $data = array_combine($this->_keys, $data);

          return $data;
     }

   }
}
