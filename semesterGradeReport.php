<?php
require 'includes/database-connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the form data
    $student_id = $_POST['studentId'];
    $course = $_POST['course'];
    $crn = $_POST['crn'];
    $midTerm = $_POST['midTerm'];
    $finalGrade = $_POST['finalGrade'];

    // Prepare the insert query
    $query = "
        INSERT INTO grades (student_id, course, crn, mid_term_grade, final_grade)
        VALUES (:student_id, :course, :crn, :mid_term_grade, :final_grade)
    ";

    // Execute the query
    $stmt = $pdo->prepare($query);
    $stmt->execute([
        ':student_id' => $student_id,
        ':course' => $course,
        ':crn' => $crn,
        ':mid_term_grade' => $midTerm,
        ':final_grade' => $finalGrade
    ]);

    echo "<script>alert('Grade saved successfully!');</script>";
    echo "<script>window.location.href = 'semesterGradeReport.php';</script>";
}
?>
