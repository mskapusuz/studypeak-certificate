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

 defined( 'ABSPATH' ) || exit;

 function draw_normal_distribution($pdf, $start_x, $start_y, $width = 80, $height = 25) {
    // Draw the normal distribution graph
    $graph_start_x = $start_x;
    $graph_start_y = $start_y;
    $graph_width = $width;
    $graph_height = $height;
    
    // Draw axes
    $pdf->SetLineWidth(0.5);
    $pdf->Line($graph_start_x, $graph_start_y + $graph_height, $graph_start_x + $graph_width, $graph_start_y + $graph_height); // X-axis
    $pdf->Line($graph_start_x, $graph_start_y, $graph_start_x, $graph_start_y + $graph_height); // Y-axis
    
    // Draw normal distribution curve
    $pdf->SetLineWidth(1);
    $pdf->SetDrawColor(0, 0, 255); // Blue curve
    
    // Calculate points for normal distribution curve (simplified)
    $center_x = $graph_start_x + ($graph_width / 2);
    $center_y = $graph_start_y + $graph_height;
    
    // Draw curve using multiple line segments
    $prev_x = $graph_start_x;
    $prev_y = $center_y;
    
    for ($i = 0; $i <= 100; $i++) {
        $x = $graph_start_x + ($i * $graph_width / 100);
        $normalized_x = ($i - 50) / 16.67; // Scale to approximately -3 to +3
        $y = $center_y - (exp(-($normalized_x * $normalized_x) / 2) * $graph_height * 0.8);
        
        if ($i > 0) {
            $pdf->Line($prev_x, $prev_y, $x, $y);
        }
        $prev_x = $x;
        $prev_y = $y;
    }
    
    // Fill the area under the curve
    $pdf->SetFillColor(200, 200, 255); // Light blue fill
    $pdf->SetDrawColor(200, 200, 255);
    
    // Create filled area using polygon
    $points = [];
    $points[] = $graph_start_x; // Start point
    $points[] = $center_y;
    
    for ($i = 0; $i <= 100; $i++) {
        $x = $graph_start_x + ($i * $graph_width / 100);
        $normalized_x = ($i - 50) / 16.67;
        $y = $center_y - (exp(-($normalized_x * $normalized_x) / 2) * $graph_height * 0.8);
        $points[] = $x;
        $points[] = $y;
    }
    
    $points[] = $graph_start_x + $graph_width; // End point
    $points[] = $center_y;
    
    $pdf->Polygon($points, 'F');
    
    // Add labels
    $pdf->SetFont('helvetica', '', 8);
    $pdf->SetTextColor(0, 0, 0);
    
    // X-axis labels
    $pdf->SetXY($graph_start_x - 5, $center_y + 2);
    $pdf->Cell(10, 4, '-3', 0, 0, 'C');
    
    $pdf->SetXY($center_x - 5, $center_y + 2);
    $pdf->Cell(10, 4, '0', 0, 0, 'C');
    
    $pdf->SetXY($graph_start_x + $graph_width - 5, $center_y + 2);
    $pdf->Cell(10, 4, '+3', 0, 0, 'C');
    
    // Center label "m"
    $pdf->SetXY($center_x - 3, $center_y + 8);
    $pdf->Cell(6, 4, 'm', 0, 0, 'C');
    
    // Low/High labels
    $pdf->SetXY($graph_start_x - 8, $center_y + 8);
    $pdf->Cell(16, 4, 'niedrig', 0, 0, 'C');
    
    $pdf->SetXY($graph_start_x + $graph_width - 8, $center_y + 8);
    $pdf->Cell(16, 4, 'hoch', 0, 0, 'C');
    
    // Add observed value dot (removed yellow dot)
    // $pdf->SetFillColor(255, 255, 0); // Yellow
    // $pdf->SetDrawColor(255, 255, 0);
    // $observed_x = $center_x + 5; // Slightly above 0
    // $observed_y = $center_y - (exp(-(0.6 * 0.6) / 1) * $graph_height * 0.8);
    // $pdf->Circle($observed_x, $observed_y, 1.5, 0, 360, 'F');
}

function ruler($pdf) {
    // Calculate padding for positioning (matching main function)
    $page_width = $pdf->getPageWidth();
    $page_height = $pdf->getPageHeight();
    $padding_x = $page_width * 0.075; // 7.5% of page width (matching main function)
    $padding_y = $page_height * 0.05; // 5% of page height (matching main function)
    
    // Calculate available width after padding
    $available_width = $page_width - (2 * $padding_x);
    $quiz_title_width = 60; // Width for quiz title (increased to make right section narrower)
    $bar_area_width = $available_width - $quiz_title_width - 10; // 10 units gap between title and bars
    
    // Draw a ruler (10cm physical length, 0-100 scale)
    $ruler_start_x = $padding_x + $quiz_title_width + 10; // Start position for ruler (same as bars)
    $ruler_start_y = $padding_y + 80; // Start position above the bars (with spacing from graph)
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
        $pdf->SetFont('helvetica', '', 8); // Set small font size for ruler
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
    $padding_x = $page_width * 0.075; // 10% of page width
    $padding_y = $page_height * 0.1; // 10% of page height
    
    // Calculate available width after padding
    $available_width = $page_width - (2 * $padding_x);
    $quiz_title_width = 60; // Width for quiz title (increased to make right section narrower)
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
            [$base_blue[0] - 20, $base_blue[1] - 20, $base_blue[2] - 20], // Slightly darker
            [$base_blue[0] - 40, $base_blue[1] - 40, $base_blue[2] - 40], // Darker
            [$base_blue[0] - 60, $base_blue[1] - 60, $base_blue[2] - 60], // More darker
            [$base_blue[0] - 80, $base_blue[1] - 80, $base_blue[2] - 80]  // Darkest
        ];
        
        // Draw 5 gradient background pieces with rounded corners
        for ($i = 0; $i < 5; $i++) {
            $piece_x = $x_pos + ($i * ($piece_width + $padding));
            $pdf->SetFillColor($gradient_colors[$i][0], $gradient_colors[$i][1], $gradient_colors[$i][2]);
            $pdf->RoundedRect($piece_x, $y_pos, $piece_width, $background_height, 1, '1111', 'F');
        }
    } else {
        // Regular gray background
        $gray_color = [242, 240, 240]; // Light gray color
        $pdf->SetFillColor($gray_color[0], $gray_color[1], $gray_color[2]);
        
        // Draw 5 gray background pieces with rounded corners
        for ($i = 0; $i < 5; $i++) {
            $piece_x = $x_pos + ($i * ($piece_width + $padding));
            $pdf->RoundedRect($piece_x, $y_pos, $piece_width, $background_height, 1, '1111', 'F');
        }
    }
    
    // Add quiz title on the left side of the bars first
    $text_x = $padding_x; // Position text at the left edge with padding
    $text_y = $y_pos + (($background_height - 4) / 2); // Center text vertically with the bars
    $pdf->SetXY($text_x, $text_y);
    
    // Set font right before drawing the text
    if ($is_title) {
        $pdf->SetFont('helvetica', 'B', 12); // Bold font with larger size for title
    } else {
        $pdf->SetFont('helvetica', '', 10); // Normal font for regular bars
    }
    $pdf->SetTextColor(0, 0, 0);
    $pdf->Cell($quiz_title_width, 4, $quiz_title, 0, 0, 'L'); // Left align text
    
    // Add progress value before the bar
    if ($is_title) {
        $pdf->SetFont('helvetica', 'B', 9); // Bold font for title percentage
    } else {
        $pdf->SetFont('helvetica', '', 9); // Normal font for regular percentage
    }
    $pdf->SetTextColor(0, 0, 0);
    
    $progress_x = $x_pos - 15; // Position progress value to the left of the bar
    $progress_y = $y_pos + (($background_height - 4) / 2); // Center text vertically with the bars
    $pdf->SetXY($progress_x, $progress_y);
    $pdf->Cell(14, 4, $width . '', 0, 0, 'R'); // Right align progress value with % sign
    
    // Draw the current colored bar on top (centered vertically within the background)
    if ($is_title) {
        $color = [0, 51, 102]; // Parliament blue color for title bars
    } else {
        $color = [180, 180, 180]; // Darker pastel gray tone for non-title bars
    }
    $pdf->SetFillColor($color[0], $color[1], $color[2]);
    $bar_y_pos = $y_pos + (($background_height - $bar_height) / 2); // Center the bar vertically
    $pdf->RoundedRect($x_pos, $bar_y_pos, $bar_width, $bar_height, 0.5, '1111', 'F');
 }

// Add metabox for LearnDash courses
add_action('add_meta_boxes', function() {
    add_meta_box(
        'studypeak_pdf_certificate_sections',
        'PDF Certificate Sections',
        'studypeak_pdf_certificate_metabox_callback',
        'sfwd-courses',
        'normal',
        'high'
    );
});

// Metabox callback function
function studypeak_pdf_certificate_metabox_callback($post) {
    // Add nonce for security
    wp_nonce_field('studypeak_pdf_certificate_metabox', 'studypeak_pdf_certificate_nonce');
    
    // Get saved sections data
    $sections = get_post_meta($post->ID, '_studypeak_pdf_sections', true);
    if (!is_array($sections)) {
        $sections = [];
    }
    
    // Get all lessons for this course
    $lessons = learndash_get_course_lessons_list($post->ID);
    if (!is_array($lessons)) {
        $lessons = [];
    }
    
    ?>
    <div id="studypeak-pdf-sections">
        <style>
            .studypeak-section {
                border: 1px solid #ddd;
                margin: 10px 0;
                padding: 15px;
                background: #f9f9f9;
            }
            .studypeak-section h4 {
                margin-top: 0;
                color: #333;
            }
            .studypeak-section input[type="text"] {
                width: 100%;
                padding: 8px;
                margin: 5px 0;
            }
            .studypeak-section select {
                width: 100%;
                min-height: 100px;
                padding: 5px;
            }
            .studypeak-subsection {
                border: 1px solid #ccc;
                margin: 10px 0;
                padding: 10px;
                background: #fff;
            }
            .studypeak-subsection h5 {
                margin-top: 0;
                color: #666;
            }
            .studypeak-remove-section, .studypeak-remove-subsection {
                background: #dc3232;
                color: white;
                border: none;
                padding: 5px 10px;
                cursor: pointer;
                float: right;
            }
            .studypeak-remove-section:hover, .studypeak-remove-subsection:hover {
                background: #a00;
            }
            #add-section-btn, .add-subsection-btn {
                background: #0073aa;
                color: white;
                border: none;
                padding: 10px 20px;
                cursor: pointer;
                margin: 10px 0;
            }
            #add-section-btn:hover, .add-subsection-btn:hover {
                background: #005a87;
            }
            .add-subsection-btn {
                background: #28a745;
                padding: 5px 15px;
                font-size: 12px;
            }
            .add-subsection-btn:hover {
                background: #218838;
            }
        </style>
        
        <div id="sections-container">
            <?php foreach ($sections as $index => $section): ?>
                <div class="studypeak-section" data-index="<?php echo $index; ?>">
                    <h4>Section <?php echo $index + 1; ?></h4>
                    <button type="button" class="studypeak-remove-section" onclick="removeSection(<?php echo $index; ?>)">Remove Section</button>
                    
                    <label for="section_title_<?php echo $index; ?>">Section Title:</label>
                    <input type="text" 
                           id="section_title_<?php echo $index; ?>" 
                           name="studypeak_sections[<?php echo $index; ?>][title]" 
                           value="<?php echo esc_attr($section['title'] ?? ''); ?>" 
                           placeholder="Enter section title">
                    
                    <div class="subsections-container" data-section="<?php echo $index; ?>">
                        <?php if (isset($section['subsections']) && is_array($section['subsections'])): ?>
                            <?php foreach ($section['subsections'] as $sub_index => $subsection): ?>
                                <div class="studypeak-subsection" data-section="<?php echo $index; ?>" data-subsection="<?php echo $sub_index; ?>">
                                    <h5>Sub-section <?php echo $sub_index + 1; ?></h5>
                                    <button type="button" class="studypeak-remove-subsection" onclick="removeSubsection(<?php echo $index; ?>, <?php echo $sub_index; ?>)">Remove Sub-section</button>
                                    
                                    <label for="subsection_title_<?php echo $index; ?>_<?php echo $sub_index; ?>">Sub-section Title:</label>
                                    <input type="text" 
                                           id="subsection_title_<?php echo $index; ?>_<?php echo $sub_index; ?>" 
                                           name="studypeak_sections[<?php echo $index; ?>][subsections][<?php echo $sub_index; ?>][title]" 
                                           value="<?php echo esc_attr($subsection['title'] ?? ''); ?>" 
                                           placeholder="Enter sub-section title">
                                    
                                    <label for="subsection_lessons_<?php echo $index; ?>_<?php echo $sub_index; ?>">Select Lessons:</label>
                                    <select id="subsection_lessons_<?php echo $index; ?>_<?php echo $sub_index; ?>" 
                                            name="studypeak_sections[<?php echo $index; ?>][subsections][<?php echo $sub_index; ?>][lessons][]" 
                                            multiple>
                                        <?php foreach ($lessons as $lesson): ?>
                                            <option value="<?php echo $lesson['post']->ID; ?>" 
                                                    <?php selected(in_array($lesson['post']->ID, $subsection['lessons'] ?? [])); ?>>
                                                <?php echo esc_html($lesson['post']->post_title); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                    
                    <button type="button" class="add-subsection-btn" onclick="addSubsection(<?php echo $index; ?>)">Add Sub-section</button>
                </div>
            <?php endforeach; ?>
        </div>
        
        <button type="button" id="add-section-btn" onclick="addSection()">Add Section</button>
        
        <script>
            let sectionIndex = <?php echo intval(count($sections)); ?>;
            const lessons = <?php echo wp_json_encode($lessons); ?>;
            const subsectionCounters = {};
            
            function addSection() {
                const container = document.getElementById('sections-container');
                const sectionDiv = document.createElement('div');
                sectionDiv.className = 'studypeak-section';
                sectionDiv.setAttribute('data-index', sectionIndex);
                
                subsectionCounters[sectionIndex] = 0;
                
                sectionDiv.innerHTML = 
                    '<h4>Section ' + (sectionIndex + 1) + '</h4>' +
                    '<button type="button" class="studypeak-remove-section" onclick="removeSection(' + sectionIndex + ')">Remove Section</button>' +
                    '<label for="section_title_' + sectionIndex + '">Section Title:</label>' +
                    '<input type="text" ' +
                           'id="section_title_' + sectionIndex + '" ' +
                           'name="studypeak_sections[' + sectionIndex + '][title]" ' +
                           'placeholder="Enter section title">' +
                    '<div class="subsections-container" data-section="' + sectionIndex + '"></div>' +
                    '<button type="button" class="add-subsection-btn" onclick="addSubsection(' + sectionIndex + ')">Add Sub-section</button>';
                
                container.appendChild(sectionDiv);
                sectionIndex++;
            }
            
            function addSubsection(sectionIndex) {
                const container = document.querySelector('.subsections-container[data-section="' + sectionIndex + '"]');
                if (!container) return;
                
                if (!subsectionCounters[sectionIndex]) {
                    subsectionCounters[sectionIndex] = 0;
                }
                
                const subsectionIndex = subsectionCounters[sectionIndex];
                const subsectionDiv = document.createElement('div');
                subsectionDiv.className = 'studypeak-subsection';
                subsectionDiv.setAttribute('data-section', sectionIndex);
                subsectionDiv.setAttribute('data-subsection', subsectionIndex);
                
                let lessonsOptions = '';
                if (lessons && lessons.length > 0) {
                    lessons.forEach(function(lesson) {
                        if (lesson && lesson.post) {
                            lessonsOptions += '<option value="' + lesson.post.ID + '">' + lesson.post.post_title + '</option>';
                        }
                    });
                }
                
                subsectionDiv.innerHTML = 
                    '<h5>Sub-section ' + (subsectionIndex + 1) + '</h5>' +
                    '<button type="button" class="studypeak-remove-subsection" onclick="removeSubsection(' + sectionIndex + ', ' + subsectionIndex + ')">Remove Sub-section</button>' +
                    '<label for="subsection_title_' + sectionIndex + '_' + subsectionIndex + '">Sub-section Title:</label>' +
                    '<input type="text" ' +
                           'id="subsection_title_' + sectionIndex + '_' + subsectionIndex + '" ' +
                           'name="studypeak_sections[' + sectionIndex + '][subsections][' + subsectionIndex + '][title]" ' +
                           'placeholder="Enter sub-section title">' +
                    '<label for="subsection_lessons_' + sectionIndex + '_' + subsectionIndex + '">Select Lessons:</label>' +
                    '<select id="subsection_lessons_' + sectionIndex + '_' + subsectionIndex + '" ' +
                            'name="studypeak_sections[' + sectionIndex + '][subsections][' + subsectionIndex + '][lessons][]" ' +
                            'multiple>' +
                        lessonsOptions +
                    '</select>';
                
                container.appendChild(subsectionDiv);
                subsectionCounters[sectionIndex]++;
            }
            
            function removeSection(index) {
                const section = document.querySelector('[data-index="' + index + '"]');
                if (section) {
                    section.remove();
                    delete subsectionCounters[index];
                }
            }
            
            function removeSubsection(sectionIndex, subsectionIndex) {
                const subsection = document.querySelector('[data-section="' + sectionIndex + '"][data-subsection="' + subsectionIndex + '"]');
                if (subsection) {
                    subsection.remove();
                }
            }
            
            // Initialize subsection counters for existing sections
            <?php foreach ($sections as $index => $section): ?>
                subsectionCounters[<?php echo $index; ?>] = <?php echo isset($section['subsections']) ? count($section['subsections']) : 0; ?>;
            <?php endforeach; ?>
        </script>
    </div>
    <?php
}

function get_lesson_quiz_avg_score($quiz_attemps, $lesson_id) {
    // Validate inputs
    if (!is_array($quiz_attemps) || empty($quiz_attemps) || !$lesson_id) {
        return 0;
    }
    
    $last_attempt = null;
    $latest_time = 0;
    
    // Find the most recent attempt for the specific lesson
    foreach ($quiz_attemps as $attempt) {
        // Check if this attempt belongs to the specified lesson
        if (isset($attempt['lesson']) && $attempt['lesson'] == $lesson_id) {
            // Check if this is the most recent attempt based on completion time
            if (isset($attempt['completed']) && $attempt['completed'] > $latest_time) {
                $latest_time = $attempt['completed'];
                $last_attempt = $attempt;
            }
        }
    }
    
    // Return percentage from the last attempt or 0 if no attempts found
    if ($last_attempt) {
        // Use percentage if available, otherwise calculate from points
        if (isset($last_attempt['percentage']) && is_numeric($last_attempt['percentage'])) {
            return $last_attempt['percentage'];
        } elseif (isset($last_attempt['points']) && isset($last_attempt['total_points']) && 
                 is_numeric($last_attempt['points']) && is_numeric($last_attempt['total_points']) && 
                 $last_attempt['total_points'] > 0) {
            // Calculate percentage from points
            return round(($last_attempt['points'] / $last_attempt['total_points']) * 100, 2);
        }
    }
    
    return 0;
}

function get_subsection_avg_score($quiz_attempts, $lesson_ids) {
    // Validate inputs
    if (!is_array($quiz_attempts) || empty($quiz_attempts) || !is_array($lesson_ids) || empty($lesson_ids)) {
        return 0;
    }
    
    $total_score = 0;
    $lesson_count = 0;
    
    // Calculate average score for all lessons in this sub-section
    foreach ($lesson_ids as $lesson_id) {
        $lesson_score = get_lesson_quiz_avg_score($quiz_attempts, $lesson_id);
        if ($lesson_score > 0) { // Only count lessons that have quiz attempts
            $total_score += $lesson_score;
            $lesson_count++;
        }
    }
    
    // Return average score or 0 if no lessons have quiz attempts
    return $lesson_count > 0 ? round($total_score / $lesson_count, 2) : 0;
}

// Save metabox data
add_action('save_post', function($post_id) {
    // Check if this is an autosave
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    
    // Check if this is a course post type
    if (get_post_type($post_id) !== 'sfwd-courses') {
        return;
    }
    
    // Check nonce
    if (!isset($_POST['studypeak_pdf_certificate_nonce']) || 
        !wp_verify_nonce($_POST['studypeak_pdf_certificate_nonce'], 'studypeak_pdf_certificate_metabox')) {
        return;
    }
    
    // Check user permissions
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }
    
    // Save sections data
    if (isset($_POST['studypeak_sections'])) {
        $sections = [];
        foreach ($_POST['studypeak_sections'] as $section_data) {
            if (!empty($section_data['title'])) {
                $section = [
                    'title' => sanitize_text_field($section_data['title']),
                    'subsections' => []
                ];
                
                // Process sub-sections
                if (isset($section_data['subsections']) && is_array($section_data['subsections'])) {
                    foreach ($section_data['subsections'] as $subsection_data) {
                        if (!empty($subsection_data['title'])) {
                            $section['subsections'][] = [
                                'title' => sanitize_text_field($subsection_data['title']),
                                'lessons' => array_map('intval', $subsection_data['lessons'] ?? [])
                            ];
                        }
                    }
                }
                
                $sections[] = $section;
            }
        }
        update_post_meta($post_id, '_studypeak_pdf_sections', $sections);
    } else {
        delete_post_meta($post_id, '_studypeak_pdf_sections');
    }
});

add_action( 'init', function() {
    if(!isset($_GET['certificate']) || !isset($_GET['course-id'])) {
        return;
    }

    $course_id = intval( $_GET['course-id'] );

    // Get all quiz attempts for the current user.
    $current_user_id = get_current_user_id();

    if( $current_user_id < 1 ) {
        return false;
    }

    $quiz_attempts = get_user_meta( $current_user_id, '_sfwd-quizzes', true );
    if ( empty( $quiz_attempts ) || ! is_array( $quiz_attempts ) ) {
        return false;
    }

    $sections = get_post_meta($course_id, '_studypeak_pdf_sections', true);

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
    $padding_x = $page_width * 0.075; // 10% of page width
    $padding_y = $page_height * 0.05; // 10% of page height
    
    // Set margins to 0 and we'll handle positioning manually
    $pdf->SetMargins(0, 0, 0);

    // Logo dimensions and position
    $logo_width = 50; // Further reduced from 70
    $logo_height = 9; // Further reduced from 13
    $logo_x = $padding_x; // 5 units from left edge
    $logo_y = $padding_y; // 5 units from top edge

    // Add the SVG logo
    $svg_content = '<?xml version="1.0" encoding="UTF-8"?> <svg xmlns="http://www.w3.org/2000/svg" width="143" height="27" viewBox="0 0 143 27" fill="none"><g id="Group"><g id="Group_2"><path id="Vector" d="M90.2197 5.99241H93.1611L93.3697 7.94511C94.1514 6.4605 95.843 5.60156 97.7957 5.60156C101.415 5.60156 103.81 8.23092 103.81 12.2661C103.81 16.3013 101.622 19.1917 97.7957 19.1917C95.8693 19.1917 94.2024 18.4363 93.396 17.1355V24.8165H90.2197V5.99241ZM97.0418 16.3275C99.2803 16.3275 100.609 14.7131 100.609 12.4221C100.609 10.1311 99.2819 8.49045 97.0418 8.49045C94.8018 8.49045 93.4222 10.1048 93.4222 12.4221C93.4222 14.7394 94.8543 16.3275 97.0418 16.3275Z" fill="#1A3A27"></path><path id="Vector_2" d="M104.541 12.4221C104.541 8.38694 107.17 5.60156 110.946 5.60156C114.721 5.60156 117.245 8.17839 117.245 12.1888V13.1528L107.561 13.1791C107.796 15.4439 108.993 16.5902 111.102 16.5902C112.846 16.5902 113.992 15.9135 114.357 14.69H117.298C116.751 17.5016 114.408 19.1948 111.049 19.1948C107.222 19.1948 104.541 16.4094 104.541 12.4252V12.4221ZM107.638 11.1476H114.095C114.095 9.3772 112.872 8.20619 110.972 8.20619C109.072 8.20619 107.952 9.22116 107.64 11.1476H107.638Z" fill="#1A3A27"></path><path id="Vector_3" d="M118.053 15.1812C118.053 12.8114 119.771 11.3284 122.818 11.0935L126.671 10.8077V10.5219C126.671 8.77779 125.629 8.07488 124.015 8.07488C122.141 8.07488 121.1 8.85658 121.1 10.2099H118.392C118.392 7.42449 120.683 5.60156 124.171 5.60156C127.659 5.60156 129.768 7.47547 129.768 11.0426V18.8534H126.983L126.748 16.9532C126.201 18.2802 124.457 19.1917 122.452 19.1917C119.717 19.1917 118.052 17.6036 118.052 15.1828L118.053 15.1812ZM126.697 13.6441V12.9675L124.015 13.176C122.036 13.3583 121.282 14.0087 121.282 15.0499C121.282 16.2209 122.064 16.7941 123.494 16.7941C125.447 16.7941 126.697 15.6231 126.697 13.6441Z" fill="#1A3A27"></path><path id="Vector_4" d="M130.867 18.8535V0H134.017V11.381L139.016 5.99096H143L138.106 11.0936L142.897 18.8519H139.252L135.894 13.4372L134.02 15.3899V18.8519H130.87L130.867 18.8535Z" fill="#1A3A27"></path></g><path id="Vector_5" d="M33.8246 14.9464C33.8508 16.0664 34.6835 16.7694 36.1418 16.7694C37.6002 16.7694 38.4329 16.1699 38.4329 15.2338C38.4329 14.5834 38.0946 14.1137 36.9483 13.8542L34.631 13.3073C32.3137 12.7867 31.1937 11.6929 31.1937 9.63674C31.1937 7.11089 33.3287 5.60156 36.2963 5.60156C39.264 5.60156 41.1395 7.26846 41.1642 9.7665H38.144C38.1177 8.67274 37.3886 7.96983 36.165 7.96983C34.9415 7.96983 34.1861 8.54297 34.1861 9.50542C34.1861 10.2346 34.7592 10.7027 35.853 10.9638L38.1703 11.5107C40.3315 12.005 41.4253 12.9953 41.4253 14.9727C41.4253 17.5758 39.2115 19.1901 36.0353 19.1901C32.859 19.1901 30.8013 17.4723 30.8013 14.9464H33.8215H33.8246Z" fill="#1A3A27"></path><path id="Vector_6" d="M44.0036 18.8539V8.64854H41.5303V5.99293H44.0036V2.5H47.1798V5.99293H49.6794V8.64854H47.1798V18.8539H44.0036Z" fill="#1A3A27"></path><path id="Vector_7" d="M61.9955 5.99373V18.8547H59.054L58.8192 17.1368C58.0375 18.3603 56.3722 19.193 54.6527 19.193C51.6851 19.193 49.9409 17.1878 49.9409 14.0378V5.99219H53.1172V12.9178C53.1172 15.3649 54.0811 16.3551 55.8516 16.3551C57.8568 16.3551 58.8192 15.1841 58.8192 12.7355V5.99219H61.9955V5.99373Z" fill="#1A3A27"></path><path id="Vector_8" d="M62.6997 12.4732C62.6997 8.46429 65.0433 5.60012 68.9224 5.60012C70.7191 5.60012 72.3072 6.35555 73.1136 7.6316V0H76.2636V18.8535H73.3484L73.1399 16.8482C72.3597 18.3329 70.6928 19.1918 68.7401 19.1918C65.017 19.1918 62.6997 16.4574 62.6997 12.4747V12.4732ZM73.0873 12.3697C73.0873 10.0524 71.6553 8.43803 69.443 8.43803C67.2308 8.43803 65.8759 10.0787 65.8759 12.3697C65.8759 14.6607 67.2292 16.2751 69.443 16.2751C71.6568 16.2751 73.0873 14.687 73.0873 12.3697Z" fill="#1A3A27"></path><path id="Vector_9" d="M76.8892 22.1607H78.7894C80.0392 22.1607 80.8193 21.8749 81.3662 20.364L81.7308 19.4L76.5493 5.99219H79.9078L83.2138 15.3649L86.7021 5.99219H89.9819L83.6804 21.7173C82.7179 24.1119 81.3631 25.1021 79.2544 25.1021C78.3692 25.1021 77.5875 24.9986 76.8846 24.8163V22.1607H76.8892Z" fill="#1A3A27"></path><g id="Group_3"><path id="Vector_10" d="M6.19643 16.64L2.51194 20.3245L0 17.8126L3.6814 14.1312L2.9445 13.3958C2.07629 12.5276 1.59893 11.3751 1.59893 10.147C1.59893 8.91882 2.07629 7.76635 2.9445 6.89814C4.735 5.10764 7.65015 5.1061 9.44064 6.89814C10.3089 7.76635 10.7862 8.91882 10.7862 10.147C10.7862 11.3751 10.3089 12.5276 9.44064 13.3958L8.70838 14.1281L12.2739 17.6844L9.76506 20.2009L6.19643 16.6416V16.64ZM5.45645 9.41163C5.26025 9.60782 5.15211 9.86891 5.15211 10.147C5.15211 10.4251 5.26025 10.6861 5.4549 10.8808L6.19334 11.6177L6.9287 10.8823C7.1249 10.6861 7.23304 10.4251 7.23304 10.147C7.23304 9.86891 7.1249 9.60782 6.9287 9.41163C6.7325 9.21543 6.47142 9.10729 6.19334 9.10729C5.91527 9.10729 5.65419 9.21543 5.45799 9.41163H5.45645Z" fill="#18C867"></path><path id="Vector_11" d="M6.58288 25.0053C5.71467 24.1371 5.2373 22.9846 5.2373 21.7565C5.2373 20.5283 5.71467 19.3758 6.58288 18.5076L14.9869 10.1036L14.2516 9.3667C13.3834 8.49849 12.906 7.34601 12.906 6.11785C12.906 4.88969 13.3834 3.73723 14.2516 2.86901C15.1198 2.0008 16.2722 1.52344 17.5004 1.52344C18.727 1.52344 19.881 2.0008 20.7492 2.86901C21.6175 3.73723 22.0948 4.88969 22.0948 6.11785C22.0948 7.34601 21.6175 8.49849 20.7492 9.3667L20.0124 10.1036L27.7397 17.8387L25.2263 20.3491L17.4989 12.6155L12.3313 17.7831L13.0574 18.4845L13.079 18.5061C13.9472 19.3743 14.4246 20.5268 14.4246 21.7549C14.4246 22.9831 13.9472 24.1356 13.079 25.0038C12.183 25.8998 11.0074 26.3478 9.83018 26.3478C8.65454 26.3478 7.47735 25.8998 6.58134 25.0038L6.58288 25.0053ZM9.81936 20.2981L9.09637 21.0211C8.90017 21.2173 8.79203 21.4784 8.79203 21.7565C8.79203 22.0346 8.90017 22.2956 9.09637 22.4918C9.50267 22.8981 10.1623 22.8981 10.5686 22.4918C10.7648 22.2956 10.873 22.0346 10.873 21.7565C10.873 21.4784 10.7679 21.225 10.5763 21.0288L9.81936 20.2981ZM16.7651 5.38404C16.5689 5.58024 16.4607 5.84132 16.4607 6.1194C16.4607 6.39747 16.5689 6.65856 16.7666 6.8563L17.502 7.59165L18.2389 6.85475C18.435 6.65856 18.5432 6.39747 18.5432 6.1194C18.5432 5.84132 18.435 5.58024 18.2389 5.38404C18.0427 5.18784 17.7816 5.07971 17.5035 5.07971C17.2254 5.07971 16.9643 5.18784 16.7681 5.38404H16.7651Z" fill="#18C867"></path></g></g></svg>';
    $pdf->ImageSVG($file='@'.$svg_content, $x=$logo_x, $y=$logo_y, $w=$logo_width, $h=$logo_height, $link='', $align='', $palign='', $border=0, $fitonpage=false);

    // Adjust the starting Y position for the main content to be below the logo
    $effective_content_start_y = max($logo_y + $logo_height + 10, $padding_y);

    // Set font and position title at left top (below logo)
    $pdf->SetFont('helvetica', 'B', 16);
    $pdf->SetXY($padding_x, $effective_content_start_y);
    $pdf->Cell(0, 10, 'Student Performance Chart', 0, 1, 'L');

    // Transform $sections into $bars_data format
    $bars_data = [];
    
    if (!empty($sections) && is_array($sections)) {
        foreach ($sections as $section) {
            if (isset($section['title']) && isset($section['subsections']) && is_array($section['subsections'])) {
                $section_title = $section['title'];
                $subsections = $section['subsections'];
                
                // Create progress bars for each sub-section in the section
                $subsection_bars = [];
                foreach ($subsections as $subsection) {
                    if (isset($subsection['title']) && isset($subsection['lessons']) && is_array($subsection['lessons'])) {
                        $subsection_title = $subsection['title'];
                        $lesson_ids = $subsection['lessons'];
                        
                        // Calculate average score for all lessons in this sub-section
                        $progress = get_subsection_avg_score($quiz_attempts, $lesson_ids);
                        $subsection_bars[] = [
                            'title' => $subsection_title,
                            'progress' => $progress
                        ];
                    }
                }
                
                // Only add section if it has sub-sections
                if (!empty($subsection_bars)) {
                    $bars_data[$section_title] = $subsection_bars;
                }
            }
        }
    }
    
    $margin_top = 7; // Margin top for each bar
    $current_y = $effective_content_start_y + 80; // Starting Y position (below logo and title)
    
    // Draw the normal distribution graph (smaller, above ruler)
    // Calculate available width after padding
    $available_width = $page_width - (2 * $padding_x);
    $quiz_title_width = 60; // Width for quiz title (increased to make right section narrower)
    $bar_area_width = $available_width - $quiz_title_width - 10; // 10 units gap between title and bars
    
    $graph_start_x = $padding_x + $quiz_title_width + 10; // Align with ruler and bars
    $graph_start_y = $effective_content_start_y + 20;
    $graph_width = 40;
    $graph_height = 25;
    
    // Call the normal distribution function
    //draw_normal_distribution($pdf, $graph_start_x, $graph_start_y, $graph_width, $graph_height);
    
    // Update starting position for bars to be below the graph with spacing
    $current_y = $effective_content_start_y + 10; // Starting Y position (below logo, title, and graph with spacing)
    
    // Draw ruler first
    //ruler($pdf);
    
    // Add spacing between ruler and first bar
    $current_y += 15; // Extra spacing between ruler and first bar
    
    // Draw each category and its sub-quizzes
    $is_first_category = true;
    foreach ($bars_data as $category_title => $sub_quizzes) {
        // Add margin top for each category (except the first one)
        if (!$is_first_category) {
            $current_y += $margin_top * 2; // Extra spacing between categories
        }
        
        // Draw category title bar
        $category_progress = round(array_sum(array_column($sub_quizzes, 'progress')) / count($sub_quizzes)); // Average progress rounded
        bar_with_position($pdf, $category_progress, true, $category_title, $current_y);
        $current_y += $margin_top;
        
        // Draw sub-quiz bars
        foreach ($sub_quizzes as $sub_quiz) {
            bar_with_position($pdf, $sub_quiz['progress'], false, $sub_quiz['title'], $current_y);
            $current_y += $margin_top;
        }
        
        $is_first_category = false;
    }

    // Output PDF
    $pdf->Output('studypeak-certificate-chart.pdf', 'I');
    exit;
	
} );
