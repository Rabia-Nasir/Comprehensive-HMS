<?php
session_start();
require 'db.php';

// Check if user is logged in and is a patient
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'patient') {
    header("Location: index.php");
    exit;
}

// Fetch all hospitals with their available beds
$query = "SELECT * FROM hospitals ORDER BY hospital_name";
$hospitals = $conn->query($query)->fetchAll();

// For each hospital, fetch its doctors
$hospital_doctors = [];
foreach ($hospitals as $hospital) {
    $stmt = $conn->prepare("SELECT * FROM doctors WHERE hospital_id = ? ORDER BY doctor_name");
    $stmt->execute([$hospital['hospital_id']]);
    $hospital_doctors[$hospital['hospital_id']] = $stmt->fetchAll();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Hospitals</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .hospital-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
            padding: 20px;
            margin-bottom: 20px;
        }
        .doctor-info {
            border-left: 4px solid #4CAF50;
            margin: 10px 0;
            padding: 10px;
            background: #f8f9fa;
        }
        .search-box {
            margin-bottom: 20px;
        }
    </style>
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="#">Search Hospitals</a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="patient/dashboard.php">Dashboard</a>
                <a class="nav-link" href="logout.php">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="search-box">
            <input type="text" id="searchInput" class="form-control" placeholder="Search for hospitals or doctors...">
        </div>

        <div id="hospitalsList">
            <?php foreach ($hospitals as $hospital): ?>
                <div class="hospital-card searchable">
                    <div class="row">
                        <div class="col-md-8">
                            <h3><?php echo htmlspecialchars($hospital['hospital_name']); ?></h3>
                            <p><strong>Address:</strong> <?php echo htmlspecialchars($hospital['address']); ?></p>
                            <p><strong>Contact:</strong> <?php echo htmlspecialchars($hospital['contact_number']); ?></p>
                        </div>
                        <div class="col-md-4 text-end">
                            <p class="mb-0"><strong>Available Beds:</strong></p>
                            <h4 class="text-success"><?php echo $hospital['available_beds']; ?> / <?php echo $hospital['total_beds']; ?></h4>
                        </div>
                    </div>
                    
                    <button class="btn btn-primary mt-2" type="button" data-bs-toggle="collapse" 
                            data-bs-target="#doctors<?php echo $hospital['hospital_id']; ?>">
                        View Doctors
                    </button>

                    <div class="collapse mt-3" id="doctors<?php echo $hospital['hospital_id']; ?>">
                        <?php if (empty($hospital_doctors[$hospital['hospital_id']])): ?>
                            <p class="text-muted">No doctors available at this hospital.</p>
                        <?php else: ?>
                            <?php foreach ($hospital_doctors[$hospital['hospital_id']] as $doctor): ?>
                                <div class="doctor-info">
                                    <h5><?php echo htmlspecialchars($doctor['doctor_name']); ?></h5>
                                    <p class="mb-0"><strong>Specialization:</strong> <?php echo htmlspecialchars($doctor['specialization']); ?></p>
                                    <button class="btn btn-success mt-2" onclick="showBookingModal(<?php echo $doctor['doctor_id']; ?>, '<?php echo htmlspecialchars($doctor['doctor_name']); ?>')">
                                        Book Appointment
                                    </button>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Booking Modal -->
    <div class="modal fade" id="bookingModal" tabindex="-1" aria-labelledby="bookingModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="bookingModalLabel">Book Appointment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="appointmentForm">
                        <input type="hidden" id="doctorId" name="doctor_id">
                        <p id="doctorName" class="mb-3"></p>
                        <div class="mb-3">
                            <label for="appointmentDate" class="form-label">Appointment Date</label>
                            <input type="date" class="form-control" id="appointmentDate" name="appointment_date" required>
                        </div>
                        <div class="mb-3">
                            <label for="appointmentTime" class="form-label">Appointment Time</label>
                            <input type="time" class="form-control" id="appointmentTime" name="appointment_time" required>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" onclick="bookAppointment()">Book Appointment</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let bookingModal;
        document.addEventListener('DOMContentLoaded', function() {
            bookingModal = new bootstrap.Modal(document.getElementById('bookingModal'));

            // Set minimum date to today
            const today = new Date().toISOString().split('T')[0];
            document.getElementById('appointmentDate').min = today;

            document.getElementById('searchInput').addEventListener('keyup', function() {
                const searchText = this.value.toLowerCase();
                const hospitals = document.getElementsByClassName('searchable');

                Array.from(hospitals).forEach(hospital => {
                    const text = hospital.textContent.toLowerCase();
                    hospital.style.display = text.includes(searchText) ? '' : 'none';
                });
            });
        });

        function showBookingModal(doctorId, doctorName) {
            document.getElementById('doctorId').value = doctorId;
            document.getElementById('doctorName').textContent = 'Doctor: ' + doctorName;
            bookingModal.show();
        }

        function bookAppointment() {
            const form = document.getElementById('appointmentForm');
            const formData = new FormData(form);

            fetch('book_appointments.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Appointment booked successfully!');
                    bookingModal.hide();
                    form.reset();
                } else {
                    alert(data.error || 'Failed to book appointment');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while booking the appointment');
            });
        }
    </script>
</body>
</html>