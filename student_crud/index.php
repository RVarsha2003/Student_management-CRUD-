<?php
$host = "127.0.0.1";
$user = "root";
$pass = "";
$port = "3308";
$db = "student_db";

// Connect with port included
$conn = new mysqli($host, $user, $pass, $db, $port);

// Check connection
if ($conn->connect_error) {
    die("❌ Connection failed: " . $conn->connect_error);
}

// Insert data
// if (isset($_POST['insert'])) {
//     $name = $_POST['name'];
//     $email = $_POST['email'];
//     $course = $_POST['course'];
    
//     // Perform the insert query
//     if ($name && $email && $course) {
//         $conn->query("INSERT INTO students (name, email, course) VALUES ('$name', '$email', '$course')");
//     }
//     // No need for redirect now, because the form will be processed as a POST
// }
if (isset($_POST['insert'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $course = $_POST['course'];
    
    if ($name && $email && $course) {
        $conn->query("INSERT INTO students (name, email, course) VALUES ('$name', '$email', '$course')");
        header("Location: " . $_SERVER['PHP_SELF']); // 🔁 redirect after insert
        exit();
    }
}


// Update data
if (isset($_POST['update'])) {
    $sr_no = $_POST['sr_no'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $course = $_POST['course'];
    $conn->query("UPDATE students SET name='$name', email='$email', course='$course' WHERE sr_no='$sr_no'");
    header("Location: " . $_SERVER['PHP_SELF']); // Refresh page to show updated data
    exit();
}

// Delete data with confirmation
if (isset($_POST['delete'])) {
    $sr_no = $_POST['sr_no'];
    $conn->query("DELETE FROM students WHERE sr_no='$sr_no'");
    header("Location: " . $_SERVER['PHP_SELF']); // Refresh page to show updated data
    exit();
}

// Fetch all students
$students = $conn->query("SELECT * FROM students");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Student Management</title>
    <style>
        body { font-family: Arial; padding: 20px; }
        input[type=text], input[type=email], input[type=number] {
            width: 100%; padding: 8px; margin: 5px 0;
        }
        input[type=submit] {
            padding: 10px 20px; margin-right: 10px;
        }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #000; padding: 10px; text-align: center; }
        .success { color: green; font-weight: bold; }
        /* Blur the background, not the modal */
        .background-blur {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.5); backdrop-filter: blur(5px); z-index: 999;
        }
        .modal {
            position: fixed; top: 20%; left: 50%; transform: translateX(-50%); background: white; padding: 20px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); z-index: 1000;
        }
        .modal input {
            width: 100%; padding: 8px; margin: 10px 0;
        }
    </style>
</head>
<body>

<h2>Student Data Management System</h2>

<p class="success">✅ Connection to database successful!</p>

<form method="POST">
    <label>Name</label>
    <input type="text" name="name" required>

    <label>Email</label>
    <input type="email" name="email" required>

    <label>Course</label>
    <input type="text" name="course" required>

    <input type="submit" name="insert" value="Insert">
</form>

<h3>Student List</h3>
<table>
    <tr>
        <th>Roll No</th>
        <th>Name</th>
        <th>Email</th>
        <th>Course</th>
        <th>Action</th>
    </tr>

    <?php $no = 1; ?>

    <?php while($row = $students->fetch_assoc()) { ?>
    <tr data-sr_no="<?php echo $row['sr_no']; ?>">
        <td><?php echo  $no; ?></td>
        <td><?php echo $row['name']; ?></td>
        <td><?php echo $row['email']; ?></td>
        <td><?php echo $row['course']; ?></td>
        <td>
            <button onclick="editRecord(<?php echo $row['sr_no']; ?>)">Edit</button>
            <button onclick="deleteRecord(<?php echo $row['sr_no']; ?>)">Delete</button>
        </td>
    </tr>
    <?php $no++; } ?>
</table>

<!-- Edit Modal -->
<div id="editModal" style="display: none;">
    <div class="background-blur" id="backgroundBlur"></div>
    <div class="modal">
        <form method="POST" id="editForm">
            <input type="hidden" name="sr_no" id="editRollNo">
            <label>Name</label>
            <input type="text" name="name" id="editName">
            <label>Email</label>
            <input type="email" name="email" id="editEmail">
            <label>Course</label>
            <input type="text" name="course" id="editCourse">
            <input type="submit" name="update" value="Save Changes">
        </form>
    </div>
</div>

<script>
    function editRecord(sr_no) {
        // Fetch the current student data and populate the modal
        const name = document.querySelector(`tr[data-sr_no="${sr_no}"] td:nth-child(2)`).innerText;
        const email = document.querySelector(`tr[data-sr_no="${sr_no}"] td:nth-child(3)`).innerText;
        const course = document.querySelector(`tr[data-sr_no="${sr_no}"] td:nth-child(4)`).innerText;

        document.getElementById("editRollNo").value = sr_no;
        document.getElementById("editName").value = name;
        document.getElementById("editEmail").value = email;
        document.getElementById("editCourse").value = course;

        // Show the modal
        document.getElementById("editModal").style.display = "block";
    }

    function deleteRecord(sr_no) {
        if (confirm('Are you sure you want to delete this record?')) {
            // Trigger form submission to delete
            const form = document.createElement("form");
            form.method = "POST";
            form.action = "";
            const input = document.createElement("input");
            input.type = "hidden";
            input.name = "delete";
            input.value = true;
            form.appendChild(input);
            const rollInput = document.createElement("input");
            rollInput.type = "hidden";
            rollInput.name = "sr_no";
            rollInput.value = sr_no;
            form.appendChild(rollInput);
            document.body.appendChild(form);
            form.submit();
        }
    }

    // Close the modal when clicking on the background
    document.getElementById("backgroundBlur").onclick = function() {
        document.getElementById("editModal").style.display = "none";
    };
</script>

</body>
</html>
