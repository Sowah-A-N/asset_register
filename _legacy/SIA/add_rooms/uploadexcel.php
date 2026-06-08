<?php


include("datacon.php");
$username = mysqli_real_escape_string($conn, $_POST['id']);
                    

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
                    $counter=0;
					foreach($records as $row){
						// HTML content to render on webpage
						$index_number = isset($row[0]) ? $row[0] : '';
						$reason = isset($row[1]) ? $row[1] : '';
                        //Check for duplication
                        $query="SELECT * FROM blacklist_students WHERE index_number='$index_number'";
                        $result=mysqli_query($conn, $query);
                        $result_check=mysqli_num_rows($result);
                        if($result_check > 0)
                        {
                            echo "<script> alert('Duplicate entry found:$index_number'); window.location='index.php'; </script> ";  
                            exit();
                        }
                        else
                        {
                            // Insert into database
                            $query = "INSERT INTO blacklist_students (index_number, reason, blacklisted_by) 
                                    values('".$index_number."', '".$reason. "', '$username')";
                            $result=mysqli_query($conn, $query);
                            if(!$result)
                            {
                                echo "Not Inserted";
                                echo "Errormessage:".mysqli_error($conn);
                                exit();
                                
                            }
                            $counter+=1;
                        }
					}
				
                    unlink($file);
                    echo "<script> alert('Successfully uploaded $counter entries from your document.'); window.location='index.php';</script> "; 
                    
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