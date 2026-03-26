<?php

include_once "datacon.php";
include_once "./functions.php";

/**
* TODO
--BASIC INPUTS AND SETUP
*/

//*Check if asset class has been selected
// if(isset($_POST['asset_class'])){
//     $selectedAssetClass = $_POST['asset_class'];
// };

// if (!empty($selectedAssetClass)){
//     getAssetClassInitialData($selectedAssetClass);
// } else {
//     echo "<script>alert('No asset class has been selected')</script>";
// }

if ($_SERVER['REQUEST_METHOD'] == "POST"){
    try {
        $selectedAssetClass = $_POST['asset_class'];
        $selectedYear = isset($_POST['year']) ? $_POST['year'] : null;
    } catch (Exception $e) {
        echo "An error occurred: " . $e->getMessage();
    }
}
/**
* TODO
* --INITIAL DEPR. CALC.
*/

if ($selectedAssetClass)
{
    try {
        getAssetClassInitialData($selectedAssetClass);
        getAssetsPerClass($selectedAssetClass);
    } catch (Exception $e) {
        echo "An error occurred: " . $e->getMessage();
    }
}


/**
* TODO
--HANDLE DISPOSALS AND ADJUSTMENTS
*/

/**
* TODO
--NET BOOK VALUE & ADJUSTMENTS
*/

/**
* TODO
--FINAL RECONCILIATION & TOTALS
*/

/**
* TODO
*/
?>

<main>
    <form action="" method="POST">
        <div>
        <?php 
                $currentYear = date("Y");
                $years = range(2020, $currentYear);
            ?>

            <select name="year" id="year">
                <?php foreach ($years as $year) { ?>
                    <option value="<?php echo $year; ?>"><?php echo $year; ?></option>
                <?php } ?>
            </select>
        </div>

        <div>
            <label for="asset_class">Asset Class:</label>
            <select name="asset_class" id="asset_class">
                <?php 
                $query = "SELECT asset_class FROM asset_classes";
                $result = mysqli_query($conn, $query);
                while($row = mysqli_fetch_array($result)) { ?>
                    <option value="<?php echo $row['asset_class']; ?>"><?php echo $row['asset_class']; ?></option>
                <?php } ?>
            </select>
        </div>

        <button type="submit" name="submit" id="submit">Submit</button>
    </form>
   
</main>