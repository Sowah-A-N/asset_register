<?php  

if(isset($_POST['submit'])) {
     if(isset($_FILES['uploadFile']['name']) && $_FILES['uploadFile']['name'] != "") {
        $allowedExtensions = array("xls","xlsx");
        $ext = pathinfo($_FILES['uploadFile']['name'], PATHINFO_EXTENSION);
		
        if(in_array($ext, $allowedExtensions)) {
				// Uploaded file
               $file = "uploads/".$_FILES['uploadFile']['name'];
               $isUploaded = copy($_FILES['uploadFile']['tmp_name'], $file);
			   // check uploaded file
               if($isUploaded) {
					// Include PHPExcel files and database configuration file
                    include("datacon.php");
					require_once __DIR__ . '/vendor/autoload.php';
                    include(__DIR__ .'/vendor/phpoffice/phpexcel/Classes/PHPExcel/IOFactory.php');
                    try {
                        // load uploaded file
                        $objPHPExcel = PHPExcel_IOFactory::load($file);
                    } catch (Exception $e) {
                         die('Error loading file "' . pathinfo($file, PATHINFO_BASENAME). '": ' . $e->getMessage());
                    }
                    
                    // Specify the excel sheet index
                    $sheet = $objPHPExcel->getSheet(0);
                    $total_rows = $sheet->getHighestRow();
					$highestColumn      = $sheet->getHighestColumn();	
					$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);		
					
					//	loop over the rows
					for ($row = 1; $row <= $total_rows; ++ $row) {
						for ($col = 0; $col < $highestColumnIndex; ++ $col) {
							$cell = $sheet->getCellByColumnAndRow($col, $row);
							$val = $cell->getValue();
							$records[$row][$col] = $val;
						}
					}
					foreach($records as $row){
						// HTML content to render on webpage
						$lecturer_name = isset($row[0]) ? $row[0] : '';
						$name_of_course = isset($row[1]) ? $row[1] : '';
						$course_code = isset($row[2]) ? $row[2] : '';
						// Insert into database
						$query = "INSERT INTO lecturers_data (lecturer_name, name_of_course, course_code) 
								values('".$lecturer_name."','".$name_of_course."','".$course_code."')";
						$result=mysqli_query($conn, $query);
                        echo "<script> alert('Upload of Document Successful'); window.location='index.php'; </script> "; 
					}
				
                    unlink($file);
                } else {
                    echo '<span class="msg">File not uploaded!</span>';
                }
        } else {
            echo '<span class="msg">Please upload excel sheet.</span>';
        }
    } else {
        echo '<span class="msg">Please upload excel file.</span>';
    }
}
?>