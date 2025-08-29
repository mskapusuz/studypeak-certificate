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

    // Draw a ruler (10cm physical length, 0-100 scale)
    $ruler_start_x = 50;
    $ruler_start_y = 50;
    $ruler_length = 100; // 10cm in PDF units (assuming 10 units = 1cm)
    $ruler_height = 15;
    
    // Reset text color to black
    $pdf->SetTextColor(0, 0, 0);
    
    // Draw ruler base line
    $pdf->SetDrawColor(0, 0, 0);
    $pdf->SetLineWidth(1);

    // Draw ruler markings
    $pdf->SetFont('helvetica', '', 8);
    
    // Major marks every 10 scale units (every 1cm physically)
    for ($i = 0; $i <= 100; $i += 20) {
        $x_pos = $ruler_start_x + ($i * $ruler_length / 100);

        $line_height = 5;
        $diff = 8;
        
        // Major marks with numbers
        $pdf->SetLineWidth(0.1);
        // Upper tick mark
        $pdf->Line($x_pos, $ruler_start_y, $x_pos, $ruler_start_y - $line_height);
        $pdf->SetLineWidth(0.1);
        $pdf->Line($x_pos, $ruler_start_y + $diff, $x_pos, $ruler_start_y + $diff + $line_height);
        
        // Center text between the tick marks at the ruler base line
        $pdf->SetXY($x_pos - 5, $ruler_start_y+2);
        $pdf->Cell(10, 4, $i, 0, 0, 'C', false, '', 0, false, 'T', 'M');
    }
    

    // Output PDF
    $pdf->Output('studypeak-certificate-chart.pdf', 'I');
    exit;
	
} );
