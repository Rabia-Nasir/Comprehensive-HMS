<?php
session_start();
require 'db.php';

// Check if user is logged in and is a patient
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'patient') {
    echo json_encode(['success' => false, 'error' => 'Unauthorized access']);
    exit;
}

// Check if required data is provided
if (!isset($_POST['doctor_id']) || !isset($_POST['appointment_date']) || !isset($_POST['appointment_time'])) {
    echo json_encode(['success' => false, 'error' => 'Missing required data']);
    exit;
}

$patient_id = $_SESSION['user_id'];
$doctor_id = $_POST['doctor_id'];
$appointment_date = $_POST['appointment_date'];
$appointment_time = $_POST['appointment_time'];

try {
    // Check if doctor exists
    $stmt = $conn->prepare("SELECT * FROM doctors WHERE doctor_id = ?");
    $stmt->execute([$doctor_id]);
    $doctor = $stmt->fetch();

    if (!$doctor) {
        echo json_encode(['success' => false, 'error' => 'Doctor not found']);
        exit;
    }

    // Check if slot is available
    $stmt = $conn->prepare("SELECT * FROM appointments 
        WHERE doctor_id = ? AND appointment_date = ? AND appointment_time = ? 
        AND status != 'cancelled'");
    $stmt->execute([$doctor_id, $appointment_date, $appointment_time]);
    
    if ($stmt->rowCount() > 0) {
        echo json_encode(['success' => false, 'error' => 'This time slot is already booked']);
        exit;
    }

    // Create new appointment
    $stmt = $conn->prepare("INSERT INTO appointments 
        (patient_id, doctor_id, appointment_date, appointment_time, status) 
        VALUES (?, ?, ?, ?, 'pending')");
    $stmt->execute([$patient_id, $doctor_id, $appointment_date, $appointment_time]);

    echo json_encode(['success' => true, 'message' => 'Appointment booked successfully']);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => 'Database error: ' . $e->getMessage()]);
}