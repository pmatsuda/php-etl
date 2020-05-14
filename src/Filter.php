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

            /**
             * Only keep alphanumeric, ',', '-', space.  '"' in names were replaced with non-space like the rest
             */
            $temp_name = preg_replace("/\'{2}/", " ", $this->_data['account_name']);
            $this->_filteredData['account_name']  = preg_replace("/[^[:alnum:],\- ]+/", "", $temp_name);
            //$this->_filteredData['account_name']  = preg_replace("/[^[:alnum:],\- ]+/", "", $this->_data['account_name']);

            /**
             * Check if length of account number is less than 10, if less than 10, pad with 0s at the beginning of the number
             */
            $length = strlen($this->_data['account_number']);
            if ($length < 10)
            {
                $this->_filteredData['account_number'] = str_pad($this->_data['account_number'], 10, "0", STR_PAD_LEFT);
            }
            else
            {
                $this->_filteredData['account_number'] = $this->_data['account_number'];
            }

            /**
             * Round the transaction fee to 2 digits for the cent value
             */
            $this->_filteredData['transaction_fee']  = number_format($this->_data['transaction_fee'], 2);

            /**
             * Remove all non-numeric characters, then only keep the last 10 - area code and the number.
             * Format sanitized phone number as xxx-xxx-xxxx
             */
            $temp  = substr(preg_replace("/[^[[:digit:]]+/", "", $this->_data['phone_number']), -10);
            $this->_filteredData['phone_number']  = substr($temp, 0, 3) . "-" . substr($temp, 3, 3) . "-" . substr($temp, 6);
        }
        return $this->_filteredData;
    }
}
?>