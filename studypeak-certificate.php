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
    $ruler_start_y = $padding_y + 25; // Start position above the bars
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

 function bar_with_position($pdf, $width, $is_title = false, $quiz_title = "Quiz Title", $custom_y = null) {
    $bar_width = $width;
    $background_height = 5;
    $bar_height = 3; // Lower height for the blue bar
    
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
    $y_pos = $custom_y !== null ? $custom_y : $padding_y + 60; // Use custom Y or default
    
    // Draw background with 5 pieces with padding between each
    $total_width = $bar_area_width; // Use available width for bars
    $padding = 1; // More padding between pieces
    $piece_width = ($total_width - (4 * $padding)) / 5; // Calculate piece width to fit 5 pieces with 4 paddings
    
    if ($is_title) {
        // Gradient background: light blue to darker blue
        $base_blue = [173, 216, 230]; // Light blue base
        $gradient_colors = [
            [$base_blue[0], $base_blue[1], $base_blue[2]], // Lightest
            [$base_blue[0] - 10, $base_blue[1] - 10, $base_blue[2] - 10], // Slightly darker
            [$base_blue[0] - 20, $base_blue[1] - 20, $base_blue[2] - 20], // Darker
            [$base_blue[0] - 30, $base_blue[1] - 30, $base_blue[2] - 30], // More darker
            [$base_blue[0] - 40, $base_blue[1] - 40, $base_blue[2] - 40]  // Darkest
        ];
        
        // Draw 5 gradient background pieces
        for ($i = 0; $i < 5; $i++) {
            $piece_x = $x_pos + ($i * ($piece_width + $padding));
            $pdf->SetFillColor($gradient_colors[$i][0], $gradient_colors[$i][1], $gradient_colors[$i][2]);
            $pdf->Rect($piece_x, $y_pos, $piece_width, $background_height, 'F');
        }
    } else {
        // Regular gray background
        $gray_color = [242, 240, 240]; // Light gray color
        $pdf->SetFillColor($gray_color[0], $gray_color[1], $gray_color[2]);
        
        // Draw 5 gray background pieces
        for ($i = 0; $i < 5; $i++) {
            $piece_x = $x_pos + ($i * ($piece_width + $padding));
            $pdf->Rect($piece_x, $y_pos, $piece_width, $background_height, 'F');
        }
    }
    
    // Add quiz title on the left side of the bars
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
    
    // Draw background with 5 pieces with padding between each
    $total_width = $bar_area_width; // Use available width for bars
    $padding = 1; // More padding between pieces
    $piece_width = ($total_width - (4 * $padding)) / 5; // Calculate piece width to fit 5 pieces with 4 paddings
    
    if ($is_title) {
        // Gradient background: light blue to darker blue
        $base_blue = [173, 216, 230]; // Light blue base
        $gradient_colors = [
            [$base_blue[0], $base_blue[1], $base_blue[2]], // Lightest
            [$base_blue[0] - 10, $base_blue[1] - 10, $base_blue[2] - 10], // Slightly darker
            [$base_blue[0] - 20, $base_blue[1] - 20, $base_blue[2] - 20], // Darker
            [$base_blue[0] - 30, $base_blue[1] - 30, $base_blue[2] - 30], // More darker
            [$base_blue[0] - 40, $base_blue[1] - 40, $base_blue[2] - 40]  // Darkest
        ];
        
        // Draw 5 gradient background pieces
        for ($i = 0; $i < 5; $i++) {
            $piece_x = $x_pos + ($i * ($piece_width + $padding));
            $pdf->SetFillColor($gradient_colors[$i][0], $gradient_colors[$i][1], $gradient_colors[$i][2]);
            $pdf->Rect($piece_x, $y_pos, $piece_width, $background_height, 'F');
        }
    } else {
        // Regular gray background
        $gray_color = [242, 240, 240]; // Light gray color
        $pdf->SetFillColor($gray_color[0], $gray_color[1], $gray_color[2]);
        
        // Draw 5 gray background pieces
        for ($i = 0; $i < 5; $i++) {
            $piece_x = $x_pos + ($i * ($piece_width + $padding));
            $pdf->Rect($piece_x, $y_pos, $piece_width, $background_height, 'F');
        }
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
    $padding_y = $page_height * 0.05; // 10% of page height
    
    // Set margins to 0 and we'll handle positioning manually
    $pdf->SetMargins(0, 0, 0);

    // Set font and position title at left top
    $pdf->SetFont('helvetica', 'B', 16);
    $pdf->SetXY($padding_x, $padding_y);
    $pdf->Cell(0, 10, 'Student Performance Chart', 0, 1, 'L');

    // Draw multiple bars
    $bars_data = [
        ['title' => 'Mathematics', 'progress' => 80, 'is_title' => true],
        ['title' => 'Science', 'progress' => 60, 'is_title' => false],
        ['title' => 'English', 'progress' => 45, 'is_title' => false],
        ['title' => 'History', 'progress' => 70, 'is_title' => false],
        ['title' => 'Geography', 'progress' => 35, 'is_title' => false]
    ];
    
    $margin_top = 7; // Margin top for each bar
    $current_y = $padding_y + 40; // Starting Y position
    
    // Draw ruler first
    ruler($pdf);
    
    // Draw each bar
    foreach ($bars_data as $index => $bar_data) {
        // Add margin top for each bar (except the first one)
        if ($index > 0) {
            $current_y += $margin_top;
        }
        
        // Create a modified bar function call with custom Y position
        bar_with_position($pdf, $bar_data['progress'], $bar_data['is_title'], $bar_data['title'], $current_y);
        
        // Move to next position: margin top + bar height (7)
        $current_y += 0; // Height of the bar
    }

    // Output PDF
    $pdf->Output('studypeak-certificate-chart.pdf', 'I');
    exit;
	
} );
