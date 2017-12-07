<?php

class FileConverter
{
    //-- Upload files directory
    private $strFileUploadDirectory = "uploads/";

    //-- Validate uploading file
    public function CheckUploadedFile($file)
    {
        $boolFileExist = false;
        $strDirectory = opendir($this->strFileUploadDirectory);
        while ($strFullName = readdir($strDirectory)) {
            $arrFullName = explode('.', $strFullName);
            $extension = $arrFullName[1];
            $strName = $arrFullName[0];

            if ($file == $strFullName) {
                $boolFileExist = true;
                $strExtension = $extension;
            }
        }
        closedir($strDirectory);
        return $arr = [
            'status' => $boolFileExist,
            'ext' => $strExtension,
            'name' => $strName,
        ];
    }

//-- Get array of customer list data from CSV format
    public function GetListCSV($filename)
    {
        $tmpLoc = $this->strFileUploadDirectory;
        $row = 1;
        $arr = array();
        if (($handle = fopen($tmpLoc . $filename, "r")) !== FALSE) {
            while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
                $arr[] = $data;
                $row++;
            }
        }
        return $arr;
    }

//-- Get fields headers from imported file
    public function GetFieldHeaders($arr, $filetype)
    {
        $arrHeaders = ($filetype == strtolower('csv')) ? $arr[0] : $arr[1];
        return $arrHeaders;
    }


//-- Check and register addresses
    public function DisplayImportDetails($array, $strExtension)
    {

        switch ($strExtension) {
            case'xls':
                $intNum = 1;
                break;
            default:
                $intNum = 0;
        }
        echo "<table><thead><tr>";
        for ($i = $intNum; $i < ($intNum + 1); ++$i) {
            for ($j = 0; $j < count($array[$i]); ++$j) {
                echo "
								<th style='background-color: gray;'>" . $array[$i][$j] . "</th>";
            }
        }
        echo "</tr></thead><tbody>";
        for ($i = ($intNum + 1); $i < count($array); ++$i) {
            echo "<tr>";
            for ($j = 0; $j < count($array[$i]); ++$j) {
                echo "
					<td>" . $array[$i][$j] . "</td>";
            }
            echo "<tr>";
        }
        echo "</tbody></table>";

    }


}
