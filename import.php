<?php
spl_autoload_register(function ($class) {
    include 'classes/' . $class . '.php';
});

$objUploader = new FileConverter();
require_once 'PHPExcel/Classes/PHPExcel/IOFactory.php';
require_once 'PHPExcel/Classes/PHPExcel/Writer/Excel5.php';


if (isset($_POST['submit'])) {
    $file = $_POST['file'];
    $intSize = $_POST['size'];
    $tmpLoc = "uploads/";
    $boolFileExist = $objUploader->CheckUploadedFile($file);
    if ($boolFileExist['status']) {
        //-- Work with CSV type of file
        $filetype = $boolFileExist['ext'];
        $filename = $file;
        if ($filetype == 'csv') {
            $arrFileDetails = $objUploader->GetListCSV($filename);
        } else {
            //-- Work with xls type of file
            $count = 0;
            $articlesArr = array();
            $objPHPExcel = PHPExcel_IOFactory::load($tmpLoc . $filename);
            $arrFileDetails = array();
            $worksheet = $objPHPExcel->setActiveSheetIndex(0);
            $highestRow = $worksheet->getHighestRow();
            $highestColumn = $worksheet->getHighestColumn();
            $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
            for ($row = 1; $row <= $highestRow; ++$row) {
                for ($col = 0; $col < $highestColumnIndex; ++$col) {
                    $cell = $worksheet->getCellByColumnAndRow($col, $row);
                    $val = $cell->getValue();
                    $arrFileDetails[$row][] = strval($val);
                }
            }
        }

        switch ($_POST['actionType']) {
            case'all':
                $objUploader->DisplayImportDetails($arrFileDetails, $filetype);
                $blnAll = true;
                break;
            case'header':
                switch ($strExtension) {
                    case'xls':
                        $intNum = 1;
                        break;
                    default:
                        $intNum = 0;
                }
                $blnHeader = true;
                break;

            case'detail':
                $blnDetail = true;
                break;
        }
    }
}
if (!isset($blnAll)) {
    include "views/header.php";
    ?>
    <div class="col-sm-12  w3-center" id="thumbl">
        <a href="/"><i class="fa fa-home" aria-hidden="true" data-toggle="tooltip" data-placement="bottom"
                       title="Go to home page"></i></a>
        <a><i class="fa fa-undo" aria-hidden="true" data-toggle="tooltip" data-placement="bottom" title="Go back"
              onclick="goBack()"></i></a>
    </div>
    <div class="container">
        <div class="row">
            <div class="col-sm-10 col-sm-offset-1 w3-center">


                <?php if (isset($blnHeader) && $blnHeader) {
                    echo "<h1 class='head_title'>List of column's titles</h1>";
                    echo "<div class=\"col-sm-4 col-sm-offset-1 w3-center\"><ol>";
                    for ($i = $intNum; $i < ($intNum + 1); ++$i) {
                        for ($j = 0; $j < count($arrFileDetails[$i]); ++$j) {
                            echo "<li>" . $arrFileDetails[$i][$j] . "</li>";
                        }
                    }
                    echo "</ol></div>";
                } elseif ((isset($blnDetail) && $blnDetail)) {
                    ?>
                    <h1 class='head_title'>File details</h1>
                    <table class="table table-striped">
                        <tbody>
                        <tr>
                            <th>Imported file name</th>
                            <td><?= $boolFileExist['name']; ?></td>
                        </tr>
                        <tr>
                            <th>Imported file extension</th>
                            <td><?= $filetype; ?></td>
                        </tr>
                        <tr>
                            <th>Imported file size</th>
                            <td><?= $intSize . " bytes"; ?></td>
                        </tr>
                        <tr>
                            <th>Imported file full name</th>
                            <td><?= $file; ?></td>
                        </tr>
                        <tr>
                            <th>Rows quantity</th>
                            <td><?php echo count($arrFileDetails); ?></td>
                        </tr>
                        <tr>
                            <th>Columns quantity</th>
                            <td><?php echo count($arrFileDetails[0]); ?></td>
                        </tr>
                        </tbody>
                    </table>
                    <?php
                }
                ?>
            </div>
        </div>
    </div>
<?php }
include "views/footer.php";
?>
