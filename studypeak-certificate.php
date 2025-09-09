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

 function ruler($pdf) {
    // Calculate 10% padding for positioning
    $page_width = $pdf->getPageWidth();
    $page_height = $pdf->getPageHeight();
    $padding_x = $page_width * 0.1; // 10% of page width
    $padding_y = $page_height * 0.1; // 10% of page height
    
    // Calculate available width after padding
    $available_width = $page_width - (2 * $padding_x);
    $quiz_title_width = 40; // Width for quiz title
    $bar_area_width = $available_width - $quiz_title_width - 10; // 10 units gap between title and bars
    
    // Draw a ruler (10cm physical length, 0-100 scale)
    $ruler_start_x = $padding_x + $quiz_title_width + 10; // Start position for ruler (same as bars)
    $ruler_start_y = $padding_y + 40; // Start position with padding
    $ruler_length = $bar_area_width; // Use available width for ruler
    $ruler_height = 15;

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
 }

 function bar($pdf,$width, $is_title = false) {
    $bar_width = $width;
    $background_height = 7;
    $bar_height = 4; // Lower height for the blue bar
    
    // Calculate 10% padding for positioning
    $page_width = $pdf->getPageWidth();
    $page_height = $pdf->getPageHeight();
    $padding_x = $page_width * 0.1; // 10% of page width
    $padding_y = $page_height * 0.1; // 10% of page height
    
    // Calculate available width after padding
    $available_width = $page_width - (2 * $padding_x);
    $quiz_title_width = 40; // Width for quiz title
    $bar_area_width = $available_width - $quiz_title_width - 10; // 10 units gap between title and bars
    
    $x_pos = $padding_x + $quiz_title_width + 10; // Start position for bars (after title + gap)
    $y_pos = $padding_y + 60; // Start position with padding
    
    // Draw gray background with 5 pieces with padding between each
    $gray_color = [242, 240, 240]; // Light gray color
    $pdf->SetFillColor($gray_color[0], $gray_color[1], $gray_color[2]);
    
    $total_width = $bar_area_width; // Use available width for bars
    $padding = 1; // More padding between pieces
    $piece_width = ($total_width - (4 * $padding)) / 5; // Calculate piece width to fit 5 pieces with 4 paddings
    
    // Draw 5 gray background pieces with padding
    for ($i = 0; $i < 5; $i++) {
        $piece_x = $x_pos + ($i * ($piece_width + $padding));
        $pdf->Rect($piece_x, $y_pos, $piece_width, $background_height, 'F'); // 'FD' = Fill and Draw border
    }
    
    // Add quiz title on the left side of the bars
    $quiz_title = "Quiz Title"; // You can modify this or pass it as a parameter
    $font_style = $is_title ? 'B' : ''; // Bold if title, normal if not
    $pdf->SetFont('helvetica', $font_style, 10);
    $pdf->SetTextColor(0, 0, 0);
    
    $text_x = $padding_x; // Position text at the left edge with padding
    $text_y = $y_pos + (($background_height - 4) / 2); // Center text vertically with the bars
    $pdf->SetXY($text_x, $text_y);
    $pdf->Cell($quiz_title_width, 4, $quiz_title, 0, 0, 'L'); // Left align text
    
    // Draw the current colored bar on top (centered vertically within the background)
    $color = [54, 162, 235];
    $pdf->SetFillColor($color[0], $color[1], $color[2]);
    $bar_y_pos = $y_pos + (($background_height - $bar_height) / 2); // Center the blue bar vertically
    $pdf->Rect($x_pos, $bar_y_pos, $bar_width, $bar_height, 'F');
 }

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

    // Add a page first
    $pdf->AddPage();
    
    // Calculate 10% padding
    $page_width = $pdf->getPageWidth();
    $page_height = $pdf->getPageHeight();
    $padding_x = $page_width * 0.1; // 10% of page width
    $padding_y = $page_height * 0.1; // 10% of page height
    
    // Set margins to 0 and we'll handle positioning manually
    $pdf->SetMargins(0, 0, 0);

    // Set font and position title with padding
    $pdf->SetFont('helvetica', 'B', 16);
    $pdf->SetXY($padding_x, $padding_y + 10);
    $pdf->Cell($page_width - (2 * $padding_x), 10, 'Student Performance Chart', 0, 1, 'C');

    // Draw a single bar (no graph)
    bar($pdf, 60, true);

    // Reset text color to black
    $pdf->SetTextColor(0, 0, 0);
    
    // Draw ruler base line
    $pdf->SetDrawColor(0, 0, 0);
    $pdf->SetLineWidth(1);

    // Draw ruler markings
    $pdf->SetFont('helvetica', '', 8);
    
    ruler($pdf);

    // Output PDF
    $pdf->Output('studypeak-certificate-chart.pdf', 'I');
    exit;
	
} );
