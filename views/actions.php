<?php
$uploads_dir = '../uploads';

$tmp_name = $_FILES["document"]["tmp_name"];
$name = basename($_FILES["document"]["name"]);
$size = basename($_FILES["document"]["size"]);
move_uploaded_file($tmp_name, "$uploads_dir/$name");
include "header.php";
?>
<div class="col-sm-12  w3-center" id="thumbl">
    <a href="/"><i class="fa fa-home" aria-hidden="true" data-toggle="tooltip" data-placement="bottom" title="Go to home page"></i></a>
        <a><i class="fa fa-undo" aria-hidden="true" data-toggle="tooltip" data-placement="bottom" title="Go back" onclick="goBack()"></i></a>
</div>
<div class="container">
    <div class="row">

        <div class="col-sm-10 col-sm-offset-1 w3-center " style="margin-bottom: 15px;">
            <h1 class='head_title'>Choose any available action</h1>
        </div>
        <div class="col-sm-4 col-sm-offset-2 w3-center item">
            <form action="../import.php" method="post">
                <input type="hidden" name="actionType" value="all">
                <input type="hidden" name="file" value="<?php echo $name ?>">
                <i class="fa fa-table" aria-hidden="true"></i><br>
                <input type="submit" name="submit" class="btn btn-success" value="Show imported details in table">
            </form>
        </div>
        <div class="col-sm-4 col-sm-offset-1 w3-center item">
            <form action="../import.php" method="post">
                <input type="hidden" name="actionType" value="header">
                <input type="hidden" name="file" value="<?php echo $name ?>">
                <i class="fa fa-file-text" aria-hidden="true"></i><br>
                <input type="submit" name="submit" class="btn btn-success" value="Show header's details in table">
            </form>
        </div>
        <div class="col-sm-4 col-sm-offset-2 w3-center item">
            <form action="../import.php" method="post">
                <input type="hidden" name="actionType" value="detail">
                <input type="hidden" name="file" value="<?php echo $name ?>">
                <input type="hidden" name="size" value="<?php echo $size ?>">
                <i class="fa fa-info-circle" aria-hidden="true"></i><br>
                <input type="submit" name="submit" class="btn btn-success" value="Show imported file details">
            </form>
        </div>
        <div class="col-sm-4 col-sm-offset-1 w3-center item">
            <form action="export.php" method="post">
                <input type="hidden" name="actionType" value="export">
                <input type="hidden" name="file" value="<?php echo $name ?>">
                <i class="fa fa-file-excel-o" aria-hidden="true"></i><br>
                <input type="submit" name="submit" class="btn btn-default" value="Export all data in Excel">
            </form>
        </div>
        <div class="col-sm-4 col-sm-offset-4 w3-center item" >
            <form action="pdf_action.php" method="post">
                <input type="hidden" name="file" value="<?php echo $name ?>">
                <i class="fa fa-file-pdf-o" aria-hidden="true" ></i><br>
                <input type="submit" name="submit" class="btn btn-default" value="Generate PDF">
            </form>
        </div>

    </div>
</div>
<?php
include "footer.php";
?>

</body>
</html>