<?php

require('../TCPDF-main/TCPDF-main/tcpdf.php');

$pdf = new TCPDF();

$pdf->AddPage();

$pdf->SetFont('helvetica', '', 14);

$pdf->Cell(0,10,'TCPDF IS WORKING!',0,1,'C');

$pdf->Output('test.pdf','I');