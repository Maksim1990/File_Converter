<?php
require('../libraries/fpdf/fpdf.php');

class PDFGenerator extends FPDF
{

    private $strFileName;


// Page header
    function Header()
    {
        // Logo
        $this->Image('../img/logo.png', 10, 6, 20);
        // Arial bold 15
        $this->SetFont('Arial', 'B', 15);
        // Move to the right
        $this->Cell(80);
        // Title
        $this->Cell(180, 10, $this->title, 1, 0, 'C');
        // Line break
        $this->Ln(20);
    }

// Page footer
    function Footer()
    {
        // Position at 1.5 cm from bottom
        $this->SetY(-15);
        // Arial italic 8
        $this->SetFont('Arial', 'I', 8);
        // Page number
        $this->Cell(0, 10, 'Page ' . $this->PageNo() . '/{nb}', 0, 0, 'C');
    }

// Simple table
    function BasicTable($header, $data)
    {
        // Header
        foreach ($header as $col)
            $this->Cell(40, 7, $col, 1);
        $this->Ln();
        // Data
        foreach ($data as $row) {
            foreach ($row as $col)
                $this->Cell(40, 6, $col, 1);
            $this->Ln();
        }
    }

// Better table
    function ImprovedTable($header, $data)
    {
        // Column widths
        $w = array(40, 35, 40, 45);
        // Header
        for ($i = 0; $i < count($header); $i++)
            $this->Cell($w[$i], 7, $header[$i], 1, 0, 'C');
        $this->Ln();
        // Data
        foreach ($data as $row) {
            $this->Cell($w[0], 6, $row[0], 'LR');
            $this->Cell($w[1], 6, $row[1], 'LR');
            $this->Cell($w[2], 6, number_format($row[2]), 'LR', 0, 'R');
            $this->Cell($w[3], 6, number_format($row[3]), 'LR', 0, 'R');
            $this->Ln();
        }
        // Closing line
        $this->Cell(array_sum($w), 0, '', 'T');
    }

// Colored table
    function FancyTable($header, $data, $intColumns, $intCount,$intLinesToPdf,$strExtension)
    {
        // Colors, line width and bold font
        $this->SetFillColor(255, 0, 0);
        $this->SetTextColor(255);
        $this->SetDrawColor(128, 0, 0);
        $this->SetLineWidth(.3);
        $this->SetFont('', 'B');
        // Header
        $w = array(40, 35, 40, 45);

        for ($i = $intCount; $i < (5 + $intCount); $i++)
            //-- Set fixed 50px width for column
            $this->Cell(50, 7, $header[$i], 1, 0, 'C', true);
        $this->Ln();
        // Color and font restoration
        $this->SetFillColor(224, 235, 255);
        $this->SetTextColor(0);
        $this->SetFont('Times', '', 9);
        // Data
        $fill = false;
        switch ($strExtension) {
            case'xls':
                $intNum = 2;
                break;
            default:
                $intNum = 1;
        }
        for ($i =$intNum; $i < ($intLinesToPdf+$intNum); ++$i) {
            for ($j = $intCount; $j < (5 + $intCount); ++$j) {
                //-- Set fixed 50px width for column
                $this->Cell(50, 6, $data[$i][$j], 'LR', 0, 'L', $fill);
            }
            $this->Ln();
            $fill = !$fill;

        }
        // Closing line
        $this->Cell(array_sum($w), 0, '', 'T');
    }


}


?>
