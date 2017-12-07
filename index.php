<?php
$dir = "uploads";
$myDirectory = opendir($dir);
//-- Check if directory has files and remove it
while ($file = readdir($myDirectory)) {
    if (preg_match("/[a-z]/i", $file)) {
        unlink($dir . '/' . $file);
    }
}
include "views/header.php";
?>
<div class="container">
    <div class="row">
        <div class="col-sm-10 col-sm-offset-1 w3-center" id="main">

            <h1 class="tlt2" data-in-effect="rollIn">Import your file here</h1>
            <form action="views/actions.php" method="post" enctype='multipart/form-data'>
                <div>
                    <h3>Click on folder icon below to choose your file</h3>
                </div>

                <input type="file" id="file1" name="document" capture style="display:none"/>
                <img src="img/folder.png" id="upfile1" style="cursor:pointer;width: 200px;"/>
                <input type="submit" class="btn btn-success" disabled id="import" value="Import">
            </form>
        </div>
        <div class="col-sm-10 col-sm-offset-1 w3-center" id="file_name" style="height: 60px;"></div>
        <div class="col-sm-7 col-sm-offset-3 col-xs-10 col-xs-offset-1" id="info_detail">
            <p class="w3-center" id="instruction">Instructions how to use</p>
            <p>
                1. Click on <img src="img/folder.png" style="cursor:pointer;width: 30px;"/> icon<br>
                2. Choose any <b>.csv</b> or <b>.xls</b> file<br>
                3. Import file and choose any available actions:<br>
                - Edit name of file<br>
                - Edit quantity of rows or columns<br>
                - Export as CSV file<br>
                - Export as ZIP archive<br>
                - Generate PDF format<br>
            </p>
        </div>
    </div>
</div>
<?php
include "views/footer.php";
?>
<script src="js/jquery.fittext.js"></script>
<script src="js/jquery.lettering.js"></script>
<script src="js/jquery.textillate.js"></script>
<script>
    $("#file1").change(function () {

        var val = $(this).val();

        switch (val.substring(val.lastIndexOf('.') + 1).toLowerCase()) {
            case 'csv':
            case 'xls':
                document.getElementById("import").disabled = false;
                $('#file_name').html('File ' + val + ' is chosen');
                $('#file_name').css('color', 'green');
                break;
            default:
                $(this).val('');
                // error message here
                $('#file_name').html("File format is not correct! Please try again.");
                $('#file_name').css('color', 'red');
                break;
        }
    });
</script>
<script>
    $(function () {
        $('.tlt').textillate();
        $('.tlt2').textillate({
            loop: false,
            in: {
                effect: 'fadeInUp', delay: 100,
                callback: function () {

                }
            },
        });
        $('.tlt3').textillate();
    })
</script>
<script>
    $(document).ready(function (e) {
        $(".showonhover").click(function () {
            $("#selectfile").trigger('click');
        });
    });


    var input = document.querySelector('input[type=file]'); // see Example 4

    input.onchange = function () {
        var file = input.files[0];

        drawOnCanvas(file);   // see Example 6
        displayAsImage(file); // see Example 7
    };

    function drawOnCanvas(file) {
        var reader = new FileReader();

        reader.onload = function (e) {
            var dataURL = e.target.result,
                c = document.querySelector('canvas'), // see Example 4
                ctx = c.getContext('2d'),
                img = new Image();

            img.onload = function () {
                c.width = img.width;
                c.height = img.height;
                ctx.drawImage(img, 0, 0);
            };

            img.src = dataURL;
        };

        reader.readAsDataURL(file);
    }

    function displayAsImage(file) {
        var imgURL = URL.createObjectURL(file),
            img = document.createElement('img');

        img.onload = function () {
            URL.revokeObjectURL(imgURL);
        };

        img.src = imgURL;
        document.body.appendChild(img);
    }

    $("#upfile1").click(function () {
        $("#file1").trigger('click');
    });
    $("#upfile2").click(function () {
        $("#file2").trigger('click');
    });
    $("#upfile3").click(function () {
        $("#file3").trigger('click');
    });
</script>