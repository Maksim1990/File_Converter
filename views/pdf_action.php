<?php
spl_autoload_register(function ($class) {
    include '../classes/' . $class . '.php';
});


require_once '../PHPExcel/Classes/PHPExcel/IOFactory.php';


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
    $intColumns = count($arrHeaders);
    $arrDiff = array_diff($arrHeaders, $arrHeadersToDelete);
    $arrToDelete = array_keys($arrDiff);
}


if ($_POST['senddata'] === "ok") {

    //-- Set quantities of columns to be displayed on PDF page
    $intColumnsResult = empty($_POST['intColumns']) ? $intColumns : $_POST['intColumns'];
    $intLinesToPdf = empty($_POST['maxLines']) ? $intMaxCountLines : $_POST['maxLines'];
    $strExtension = $_POST['extension'];

    $pdf = new PDFGenerator();
    $pdf->title = 'PDF format of ' . $file;
    $pdf->AliasNbPages();
    for ($i = 0; $i < count($arrHeaders); $i = $i + 5) {
        $pdf->AddPage('vertical');
        $pdf->SetFont('Times', '', 12);
//    for ($i = 1; $i < count($arrFileDetails); ++$i) {
//        for ($j = 0; $j < count($arrFileDetails[$i]); ++$j) {
//            $pdf->Cell(0, 10, $arrFileDetails[$i][$j], 0, 1);
//        }
//    }
        $pdf->FancyTable($arrHeaders, $arrFileDetails, $intColumnsResult, $i, $intLinesToPdf, $strExtension);
    }
    $pdf->Output();

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
            <h1 class='head_title'>PDF options</h1>
        </div>
        <div class="col-sm-10 col-sm-offset-1">

            <form action="pdf_action.php" method="post">
                <input type="hidden" name="senddata" value="ok">
                <input type="hidden" name="file" value="<?php echo $file; ?>">
                <input type="hidden" name="extension" value="<?php echo $filetype; ?>">
                <div class="col-sm-6">
                    <!--                    <div class="form-group">-->
                    <!--                        <label class="col-form-label" for="fileName">Export file name:</label>-->
                    <!--                        <input type="text" id="fileName" name="fileName">-->
                    <!--                    </div>-->
                    <div class="form-group">
                        <label class="col-form-label" for="exportLines">Choose how much lines to export:</label>
                        <input type="number" min="0" name="maxLines" max="<?php echo $intMaxCountLines; ?>"
                               class="form-control" id="exportLines" placeholder="<?php echo $intMaxCountLines; ?>">
                    </div>
                    <div class="form-group">
                        <label class="col-form-label" for="intColumns">Choose how much columns to show on PDF
                            page:</label>
                        <input type="number" min="0" name="intColumns" max="<?php echo $intColumns; ?>"
                               class="form-control" id="intColumns" placeholder="<?php echo $intColumns; ?>">
                    </div>
                    <input type="submit" name="submit" class="btn btn-success" id="import_file" value="Generate"
                           style="padding: 15px 15px;">
                </div>
                <div class="col-sm-6">
                    <h3>Choose what columns should be used</h3>
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
