<?php require_once __DIR__.'/../../../auth.php'; requirePermission('report.view','../../login/'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <title>All Assets(USD)</title>
</head>
<body class="bg-gray-100 p-6">

    <div class="w-3/4 mx-auto">
        <!-- Back to Dashboard Button -->
        <a href="../index.php" class="mb-4 inline-block bg-blue-500 text-white px-6 py-2 rounded shadow-md hover:bg-blue-600 transition duration-200">
          Back to Dashboard
        </a>

        <?php
            include "../datacon.php";

            // Get distinct asset classes for the filter dropdown
            $classQuery = "SELECT DISTINCT asset_class FROM assets";
            $classResult = $conn->query($classQuery);

            // Check if filter is applied
            $filter = "";
            if (isset($_GET['asset_class']) && !empty($_GET['asset_class'])) {
                $filter = " WHERE asset_class = '" . $conn->real_escape_string($_GET['asset_class']) . "'";
            }
            
            else{
                $filter="WHERE 1";
            }
            // Fetch assets based on filter
            $query = "SELECT * FROM assets  " . $filter . " ORDER BY acquisition_date ASC";

            $result = $conn->query($query);
        ?>

        <!-- Asset Class Filter -->
        <label for="asset-class" class="block text-lg font-semibold mb-2">Filter by Asset Class:</label>
        <select id="asset-class" class="w-1/3 p-2 border rounded mb-4">
            <option value="">All</option>
            <?php while ($classRow = $classResult->fetch_assoc()) { ?>
                <option value="<?= $classRow['asset_class']; ?>" <?= (isset($_GET['asset_class']) && $_GET['asset_class'] == $classRow['asset_class']) ? 'selected' : ''; ?>>
                    <?= $classRow['asset_class']; ?>
                </option>
            <?php } ?>
        </select>

        <!-- Table Container -->
        <div id="assets-table" class="h-full overflow-y-scroll bg-white p-4 shadow-md rounded">
            <?php if ($result->num_rows > 0) { ?>
                <table class="table-auto mb-8 w-full">
                    <thead>
                        <tr>
                            <th class="border px-4 py-2">Asset Name</th>
                            <th class="border px-4 py-2">Asset Class</th>
                            <th class="border px-4 py-2">Asset Type</th>
                            <th class="border px-4 py-2">Location</th>
                            <th class="border px-4 py-2">Acquisition Date</th>
                            <th class="border px-4 py-2">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()) { ?>
                            <tr id="<?= $row['asset_id']; ?>">
                                <td class="border px-4 py-2"><?= $row['asset_name']; ?></td>
                                <td class="border px-4 py-2"><?= $row['asset_class']; ?></td>
                                <td class="border px-4 py-2"><?= $row['asset_type']; ?></td>
                                <td class="border px-4 py-2"><?= $row['location']; ?></td>
                                <td class="border px-4 py-2"><?= date('d M Y', strtotime($row['acquisition_date'])); ?></td>
                                <td class="border px-4 py-2">
                                    <button class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 transition" 
                                        onclick="window.location.href='../individual_assets_usd/index.php?asset_id=<?= $row['asset_id']; ?>'">
                                        View Records
                                    </button>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            <?php } else { ?>
                <p class="text-center text-gray-500">No assets found.</p>
            <?php } ?>
        </div>
    </div>

    <script>
        document.getElementById("asset-class").addEventListener("change", function() {
            var selectedClass = this.value;
            window.location.href = "index.php?asset_class=" + encodeURIComponent(selectedClass);
        });
    </script>

</body>
</html>
