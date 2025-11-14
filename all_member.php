<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Member Information</title>
    <link rel="stylesheet" href="mainpage.css">
    <style>
        .allmembercontainer {
            width: 100%;
            max-width: 1200px;
            margin: 130px auto 80px auto;
            background: #e5b7b7;
            padding: 16px;
            border-radius: 50px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            box-sizing: border-box;
        }

        .search-bar {
            margin-bottom: 20px;
        }

        .search-bar input {
            width: 300px;
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        .table-wrapper {
            max-height: 450px;
            overflow-y: auto;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table, th, td {
            border: 1px solid #ddd;
        }

        th, td {
            padding: 10px;
            text-align: left;
            word-wrap: break-word;
        }

        th {
            background-color: #f4f4f4;
            font-size: 16px;
        }

        td {
            font-size: 14px;
        }

        .action-buttons {
            display: flex;
            flex-direction: row;
            justify-content: center;
            align-items: center;
            gap: 8px;
        }

        .action-buttons button {
            padding: 8px 12px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
        }

        .action-buttons .edit-btn {
            background-color: #4CAF50;
            color: white;
        }

        .action-buttons .delete-btn {
            background-color: #f44336;
            color: white;
        }

        .pagination {
            display: flex;
            justify-content: center;
            margin-top: 20px;
        }

        .pagination a {
            padding: 10px 20px;
            margin: 0 5px;
            background-color: #007bff;
            color: white;
            border-radius: 4px;
            text-decoration: none;
            font-size: 14px;
        }

        .pagination a:hover {
            background-color: #0056b3;
        }

        @media screen and (max-width: 768px) {
            th, td {
                padding: 8px;
                font-size: 12px;
            }

            .action-buttons {
                gap: 6px;
            }

            .action-buttons button {
                font-size: 12px;
                padding: 6px 8px;
            }

            .allmembercontainer {
                padding: 12px;
            }

            .pagination a {
                padding: 8px 12px;
                font-size: 12px;
            }
        }

        @media screen and (max-width: 480px) {
            th, td {
                padding: 6px;
                font-size: 10px;
            }

            .action-buttons {
                gap: 4px;
            }

            .action-buttons button {
                font-size: 10px;
                padding: 4px 6px;
            }

            .pagination a {
                padding: 6px 10px;
                font-size: 10px;
            }

            .allmembercontainer {
                margin-top: 100px;
                margin-bottom: 50px;
                padding: 10px;
            }
        }
    </style>
</head>
<body style="overflow-y: auto;">
    <div class="allmembercontainer">
        <h1 style="text-align: center;">All Member Information</h1>

        <!-- Search Bar -->
        <div class="search-bar">
            <input type="text" id="searchInput" placeholder="Search by First Name...">
        </div>

        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>Contact Number</th>
                        <th>Email</th>
                        <th>Birthday</th>
                        <th>Age</th>
                        <th>Address</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="tableBody">
                <?php
                
                $config = parse_ini_file(__DIR__ . '/.env');

                // Check if environment variables are loaded
                if (!$config) {
                    die("Error: Could not load configuration file.");
                }
                $servername = $config['DB_SERVER'];
                $username = $config['DB_USERNAME'];
                $password = $config['DB_PASSWORD'];
                $dbname = $config['DB_NAME'];

                // Create connection
                $conn = new mysqli($servername, $username, $password, $dbname);

                // Check connection
                if ($conn->connect_error) {
                    die("Connection failed: " . $conn->connect_error);
                }

                // Pagination logic
                $records_per_page = 5;
                $current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
                $offset = ($current_page - 1) * $records_per_page;

                // Updated SQL query to order by first name alphabetically
                $sql = "SELECT id, first_name, last_name, contact_number, email, birthday, age, address 
                        FROM reg_member 
                        ORDER BY first_name ASC 
                        LIMIT $records_per_page OFFSET $offset";
                $result = $conn->query($sql);

                // Fetch all rows and store them in a JavaScript-friendly format
                $all_rows = [];
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $all_rows[] = $row;
                        echo "<tr>";
                        echo "<td><a href='profile.php?id=" . htmlspecialchars($row['id']) . "'>" . htmlspecialchars($row['first_name']) . "</a></td>";
                        echo "<td>" . htmlspecialchars($row['last_name']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['contact_number']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['email']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['birthday']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['age']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['address']) . "</td>";
                        echo "<td class='action-buttons'>";
                        echo "<form style='display:inline;' action='editrecord.php' method='POST'>";
                        echo "<input type='hidden' name='id' value='" . htmlspecialchars($row['id']) . "'>";
                        echo "<button class='edit-btn' type='submit'>Edit</button>";
                        echo "</form>";
                        echo "<form style='display:inline;' action='deletemember.php' method='POST'>";
                        echo "<input type='hidden' name='id' value='" . htmlspecialchars($row['id']) . "'>";
                        echo "<button class='delete-btn' type='submit'>Delete</button>";
                        echo "</form>";
                        echo "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='8'>No records found</td></tr>";
                }

                // Get total number of records
                $sql_count = "SELECT COUNT(id) AS total_records FROM reg_member";
                $result_count = $conn->query($sql_count);
                $total_records = $result_count->fetch_assoc()['total_records'];
                $total_pages = ceil($total_records / $records_per_page);

                // Close connection
                $conn->close();
                ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination Controls -->
        <div class="pagination">
            <?php if ($current_page < $total_pages): ?>
                <a href="?page=<?php echo $current_page + 1; ?>">Next</a>
            <?php endif; ?>
            <?php if ($current_page > 1): ?>
                <a href="?page=<?php echo $current_page - 1; ?>">Previous</a>
            <?php endif; ?>
            <a href="backoffice.php" class="button">Back</a>
        </div>
    </div>

    <script>
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('searchInput');
    const tableBody = document.getElementById('tableBody');
    const originalTableHTML = tableBody.innerHTML; // Store the original table HTML

    searchInput.addEventListener('input', function () {
        const searchValue = searchInput.value.trim();

        if (searchValue === '') {
            // Reset to original table if the input is empty
            tableBody.innerHTML = originalTableHTML;
            return;
        }

        // Perform the search dynamically
        fetch(`search.php?query=${encodeURIComponent(searchValue)}`)
            .then(response => response.json())
            .then(data => {
                tableBody.innerHTML = ''; // Clear the table
                if (data.length > 0) {
                    data.forEach(row => {
                        const tr = document.createElement('tr');
                        tr.innerHTML = `
                            <td><a href="profile.php?id=${row.id}">${row.first_name}</a></td>
                            <td>${row.last_name}</td>
                            <td>${row.contact_number}</td>
                            <td>${row.email}</td>
                            <td>${row.birthday}</td>
                            <td>${row.age}</td>
                            <td>${row.address}</td>
                            <td class="action-buttons">
                                <form style="display:inline;" action="edit.php" method="POST">
                                    <input type="hidden" name="id" value="${row.id}">
                                    <button class="edit-btn" type="submit">Edit</button>
                                </form>
                                <form style="display:inline;" action="deletemember.php" method="GET">
                                    <input type="hidden" name="id" value="${row.id}">
                                    <button class="delete-btn" type="submit">Delete</button>
                                </form>
                            </td>
                        `;
                        tableBody.appendChild(tr);
                    });
                } else {
                    const tr = document.createElement('tr');
                    tr.innerHTML = "<td colspan='8'>No records found</td>";
                    tableBody.appendChild(tr);
                }
            })
            .catch(error => console.error('Error:', error));
    });
});
</script>
</body>
</html>
