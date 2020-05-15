<?php
class Filter implements FilterInterface
{
    /**
     * @var array - array to hold original unsanitized data
     */
    protected $_data;

    /**
     * @var array - array to hold sanitized data
     */
    protected $_filteredData;
    /**
     * Set line of data from file
     *
     * @param array $data - original unsanitized data
     * @return Filter
     */
    public function setData(array $data)
    {
        $this->_data = $data;

        $this->_filteredData = [];
        return $this;
    }

    /**
     * Return filtered values for one line of file data
     *
     * @return array [
     *     'account_number'  => String,
     *     'account_name'    => String,
     *     'transaction_fee' => Float,
     *     'phone_number'    => String
     * ]
     */
    public function getFilteredData()
    {
        if ($this->_data && empty($this->_filteredData)) {

            $this->_filteredData['account_number'] = $this->filterAccountNumber($this->_data['account_number']);
            $this->_filteredData['account_name']= $this->filterAccountName($this->_data['account_name']);
            $this->_filteredData['transaction_fee']  = $this->filterTransactionFee($this->_data['transaction_fee']);
            $this->_filteredData['phone_number']  = $this->filterPhoneNumber($this->_data['phone_number']);
        }
        return $this->_filteredData;
    }
   
    public function filterAccountNumber($accountNumber)
    {        
        /**
         * Check if length of account number is less than 10, if less than 10, pad with 0s at the beginning of the number
         */
        $length = strlen($accountNumber);
        if ($length < 10)
        {
            return str_pad($accountNumber, 10, "0", STR_PAD_LEFT);
        }
            return $accountNumber;
    }

    public function filterAccountName($accountName)
    {
        /**
         * Only keep alphanumeric, ',', '-', space.  '"' in names were replaced with non-space like the rest
         */
        $temp_name = preg_replace("/\'{2}/", " ", $accountName);
        return preg_replace("/[^[:alnum:],\- ]+/", "", $temp_name);
    }

    public function filterTransactionFee($transactionFee)
    {
        /**
         * Round the transaction fee to 2 digits for the cent value
         */
        return number_format(floatval($transactionFee), 2);
    }

    public function filterPhoneNumber($phoneNumber)
    {
        /**
         * Remove all non-numeric characters, then only keep the last 10 - area code and the number.
         * Format sanitized phone number as xxx-xxx-xxxx
         */
        $temp  = substr(preg_replace("/[^[[:digit:]]+/", "", $phoneNumber), -10);
        return substr($temp, 0, 3) . "-" . substr($temp, 3, 3) . "-" . substr($temp, 6);
    }
}
?>