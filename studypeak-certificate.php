<?php
/**
 * Plugin Name:     Studypeak Certificate
 * Plugin URI:      PLUGIN SITE HERE
 * Description:     PLUGIN DESCRIPTION HERE
 * Author:          YOUR NAME HERE
 * Author URI:      YOUR SITE HERE
 * Text Domain:     studypeak-certificate
 * Domain Path:     /languages
 * Version:         0.1.0
 *
 * @package         Studypeak_Certificate
 */

add_action( 'admin_init', function() {
    if(!isset($_GET['studypeak-certificate']) ) {
        return;
    }

    // Include the Composer autoloader
    require_once plugin_dir_path(__FILE__) . 'vendor/autoload.php';

    // Create new PDF document
    $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

    // Set document information
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor('StudyPeak');
    $pdf->SetTitle('Certificate Bar Graph');
    $pdf->SetSubject('Performance Chart');

    // Remove default header/footer
    $pdf->setPrintHeader(false);
    $pdf->setPrintFooter(false);

    // Set margins
    $pdf->SetMargins(20, 20, 20);

    // Add a page
    $pdf->AddPage();

    // Set font
    $pdf->SetFont('helvetica', 'B', 16);
    $pdf->Cell(0, 10, 'Student Performance Chart', 0, 1, 'C');
    $pdf->Ln(10);

    // Draw a single bar (no graph)
    $bar_width = 100;
    $bar_height = 7;
    $x_pos = 50;
    $y_pos = 80;
    $color = [54, 162, 235];
    $pdf->SetFillColor($color[0], $color[1], $color[2]);
    $pdf->Rect($x_pos, $y_pos, $bar_width, $bar_height, 'F');
    // Optionally, add a label inside the bar
    $pdf->SetFont('helvetica', 'B', 12);
    $pdf->SetTextColor(255,255,255);
    $pdf->SetXY($x_pos, $y_pos + ($bar_height/2) - 5);
    $pdf->Cell($bar_width, 10, 'Single Bar', 0, 0, 'C');

     // Draw a single bar (no graph)
     $bar_width = 60;
     $bar_height = 7;
     $x_pos = 50;
     $y_pos = 100;
     $color = [54, 162, 235];
     $pdf->SetFillColor($color[0], $color[1], $color[2]);
     $pdf->Rect($x_pos, $y_pos, $bar_width, $bar_height, 'F');
     // Optionally, add a label inside the bar
     $pdf->SetFont('helvetica', 'B', 12);
     $pdf->SetTextColor(255,255,255);
     $pdf->SetXY($x_pos, $y_pos + ($bar_height/2) - 5);
     $pdf->Cell($bar_width, 10, 'Single Bar', 0, 0, 'C');

    // Output PDF
    $pdf->Output('studypeak-certificate-chart.pdf', 'I');
    exit;
	
} );
