<?php
spl_autoload_register(function ($class) {
    include '../classes/' . $class . '.php';
});

require_once '../PHPExcel/Classes/PHPExcel/IOFactory.php';
require_once '../PHPExcel/Classes/PHPExcel/Writer/Excel5.php';


if (isset($_POST['file']) || ($_POST['senddata'] === "ok")) {

    $arrHeadersToDelete = array();
    if (!empty($_POST['check_list'])) {
        foreach ($_POST['check_list'] as $check) {
            $arrHeadersToDelete[] = $check;
        }
    }


    $file = $_POST['file'];
    $tmpLoc = "../uploads/";
    $arrFullName = explode('.', $file);
    $filetype = $arrFullName[1];;
    $filename = $file;
    if ($filetype == 'csv') {
        $row = 1;
        $arrFileDetails = array();
        if (($handle = fopen($tmpLoc . $filename, "r")) !== FALSE) {
            while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
                $arrFileDetails[] = $data;
                $row++;
            }
        }
    } else {
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
    $intMaxCountLines = count($arrFileDetails);
    $arrHeaders = ($filetype == strtolower('csv')) ? $arrFileDetails[0] : $arrFileDetails[1];
    $arrDiff = array_diff($arrHeaders, $arrHeadersToDelete);
    $arrToDelete = array_keys($arrDiff);

}
if ($_POST['senddata'] === "ok") {

    $strFileName = empty($_POST['fileName']) ? 'default' : $_POST['fileName'];
    $intLinesToExport = empty($_POST['maxLines']) ? $intMaxCountLines : $_POST['maxLines'];
    $strExtension = $_POST['extension'];
    $xls = new PHPExcel();
//-- Set index of active list
    $xls->setActiveSheetIndex(0);
//-- Get active list
    $sheet = $xls->getActiveSheet();
//-- Set list title
    $sheet->setTitle($file);


//-- Add additional sheets
//    $i = 1;
//    while ($i < 10) {
//
//        // Add new sheet
//        $objWorkSheet = $xls->createSheet($i); //Setting index when creating
//
//        //Write cells
//        $objWorkSheet->setCellValue('A1', 'Hello' . $i)
//            ->setCellValue('B2', 'world!')
//            ->setCellValue('C1', 'Hello')
//            ->setCellValue('D2', 'world!');
//
//        // Rename sheet
//        $objWorkSheet->setTitle("$i");
//
//        $i++;
//    }

    switch ($strExtension) {
        case'xls':
            $intNum = 1;
            $intAddCount = 2;
            break;
        default:
            $intNum = 0;
            $intAddCount = 1;
    }


    $arrDetails = array();

    if (!empty($arrToDelete)) {
        for ($i = $intNum; $i < count($arrFileDetails); ++$i) {
            $t = 0;
            for ($j = 0; $j < count($arrFileDetails[$i]); ++$j) {
                if (!in_array($j, $arrToDelete)) {
                    $arrDetails[$i][$t] = $arrFileDetails[$i][$j];
                    $t++;
                }
            }
        }
    }


    if (empty($arrToDelete)) {
        $arrDetails = $arrFileDetails;
    }
    $k = 1;

    for ($i = $intNum; $i < ($intLinesToExport + $intAddCount); ++$i) {
        for ($j = 0; $j < count($arrDetails[$i]); ++$j) {
            $xls->getActiveSheet()
                ->getStyle(1, $j)
                ->getFill()
                ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
                ->getStartColor()
                ->setARGB('FF808080');


            $sheet->setCellValueByColumnAndRow(
                $j,
                $k,
                $arrDetails[$i][$j]);
            //-- Set layout
            $sheet->getStyleByColumnAndRow($i, $j)->getAlignment();
        }

        $k++;

    }
    $objWriter = new PHPExcel_Writer_Excel5($xls);
    if (!isset($_POST['zip'])) {
        header("Expires: Mon, 1 Apr 1974 05:00:00 GMT");
        header("Last-Modified: " . gmdate("D,d M YH:i:s") . " GMT");
        header("Cache-Control: no-cache, must-revalidate");
        header("Pragma: no-cache");
        header("Content-type: application/vnd.ms-excel");
        header("Content-Disposition: attachment; filename=" . $strFileName . ".xls");
        //-- Display file content
        $objWriter->save('php://output');
    } else {

       $objWriter->save('zip/' . $strFileName . '.xls');
        $rootPath = realpath('zip');
        $filename = 'file.zip';
//-- Initialize archive object
        $zip = new ZipArchive();
        $zip->open($filename, ZipArchive::CREATE | ZipArchive::OVERWRITE);

//-- Create recursive directory iterator
        /** @var SplFileInfo[] $files */
        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($rootPath),
            RecursiveIteratorIterator::LEAVES_ONLY
        );

        foreach ($files as $name => $fileItem) {
            //-- Skip directories (they would be added automatically)
            if (!$fileItem->isDir()) {
                //-- Get real and relative path for current file
                $filePath = $fileItem->getRealPath();
                $relativePath = substr($filePath, strlen($rootPath) + 1);
                $path_parts = pathinfo($filePath);

                //-- Add current file to archive
                if ($path_parts['basename'] == $strFileName . ".xls") {
                    $zip->addFile($filePath, $relativePath);
                }
            }
        }
//-- Zip archive will be created only after closing object
        $zip->close();

        header("Content-type: application/zip");
        header("Content-Disposition: attachment; filename=$filename");
        header("Content-length: " . filesize($filename));
        header("Pragma: no-cache");
        header("Expires: 0");
        readfile("$filename");
        //-- Delete temporary file after downloading zip
        unlink('zip/' . $strFileName . '.xls');
        //-- Delete zip archive on server after downloading
        unlink('file.zip');

    }


}
include "header.php";
?>
<div class="col-sm-12  w3-center" id="thumbl">
    <a href="/"><i class="fa fa-home" aria-hidden="true" data-toggle="tooltip" data-placement="bottom"
                   title="Go to home page"></i></a>
    <a><i class="fa fa-undo" aria-hidden="true" data-toggle="tooltip" data-placement="bottom" title="Go back"
          onclick="goBack()"></i></a>
</div>
<div class="container">
    <div class="row">
        <div class="col-sm-10 col-sm-offset-1 w3-center " style="margin-bottom: 15px;">
            <h1 class='head_title'>Export options</h1>
        </div>
        <div class="col-sm-10 col-sm-offset-1">

            <form action="export.php" method="post">
                <input type="hidden" name="senddata" value="ok">
                <input type="hidden" name="file" value="<?php echo $file; ?>">
                <input type="hidden" name="extension" value="<?php echo $filetype; ?>">
                <div class="col-sm-6">
                    <div class="form-group">
                        <label class="col-form-label" for="fileName">Export file name:</label>
                        <input type="text" id="fileName" name="fileName">
                    </div>
                    <div class="form-group">
                        <label class="col-form-label" for="exportLines">Choose how much lines to export:</label>
                        <input type="number" min="0" max="<?php echo $intMaxCountLines; ?>" name="maxLines"
                               class="form-control" id="exportLines" placeholder="<?php echo $intMaxCountLines; ?>">
                    </div>
                    <input type="submit" name="submit" class="btn btn-success" id="import_file" value="Export">
                    <input type="submit" name="zip" class="btn btn-success" id="zip" value="Download as ZIP"
                           style="padding: 15px 15px;">
                </div>
                <div class="col-sm-6">
                    <h3>Choose what columns should be exported</h3>
                    <?php foreach ($arrHeaders as $strItem) { ?>
                        <?php echo $strItem; ?> <input type="checkbox" name="check_list[]" checked="checked"
                                                       value="<?php echo $strItem; ?>"><br/>
                    <?php } ?>
                    <div class="col-sm-12" style="margin-bottom: 40px;">

                        <a class="btn btn-success btn-small" id="btn_uncheck" style="display: block;">Uncheck all</a>
                        <a class="btn btn-success btn-small" id="btn_check" style="display: none;">Check all</a>
                    </div>
                </div>


            </form>
        </div>
    </div>
</div>
<?php
include "footer.php";
?>
<script>
    $("#exportLines").keypress(function () {
        var val = $(this).val();
        var maxExportLines = '<?php echo $intMaxCountLines; ?>';
        if (val > maxExportLines) {
            document.getElementById("import_file").disabled = true;
            document.getElementById("zip").disabled = true;
        } else {
            document.getElementById("import_file").disabled = false;
            document.getElementById("zip").disabled = false;
        }
    });
</script>
<script>
    $(document).ready(function () {
        $('#btn_uncheck').click(function () {
            $('input:checkbox').removeAttr('checked');
            $('#btn_uncheck').css('display', 'none');
            $('#btn_check').css('display', 'block');
        });

        $('#btn_check').click(function () {
            $('input:checkbox').attr('checked', 'checked');
            $('#btn_check').css('display', 'none');
            $('#btn_uncheck').css('display', 'block');
        });

    });
</script>
</body>
