<?php
session_start();
require 'db.php';
?>

<html lang="en" >
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Hospital Management System - Mayo Clinic Style</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link
    rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css"
  />
  <style>
    /* Custom fonts similar to Mayo Clinic */
    @import url('https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&display=swap');
    body {
      font-family: 'Open Sans', sans-serif;
      background: #f7f9fa;
      color: #00338d;
    }
    /* Header underline accent */
    .header-underline {
      width: 60px;
      height: 4px;
      background-color: #0071bc;
      border-radius: 2px;
      margin-top: 8px;
      margin-bottom: 24px;
    }
    /* Button style */
    .btn-primary {
      background-color: #0071bc;
      color: white;
      font-weight: 600;
      transition: background-color 0.3s ease;
    }
    .btn-primary:hover {
      background-color: #005ea2;
    }
    .btn-outline-primary {
      border: 2px solid #0071bc;
      color: #0071bc;
      font-weight: 600;
      transition: background-color 0.3s ease, color 0.3s ease;
    }
    .btn-outline-primary:hover {
      background-color: #0071bc;
      color: white;
    }
    /* Card shadow and border */
    .card {
      background: white;
      border-radius: 12px;
      box-shadow: 0 4px 12px rgb(0 0 0 / 0.1);
      padding: 2.5rem 2rem;
      max-width: 480px;
      width: 100%;
    }
    /* Icon circle style */
    .icon-circle {
      background-color: #e6f0fa;
      color: #0071bc;
      border-radius: 50%;
      width: 56px;
      height: 56px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 28px;
      margin-bottom: 0.75rem;
      box-shadow: 0 2px 6px rgb(0 0 0 / 0.1);
      transition: background-color 0.3s ease, color 0.3s ease;
    }
    button.user-type-btn:hover .icon-circle {
      background-color: #005ea2;
      color: white;
      box-shadow: 0 4px 12px rgb(0 0 0 / 0.15);
    }
    button.user-type-btn.active .icon-circle {
      background-color: #005ea2;
      color: white;
      box-shadow: 0 4px 12px rgb(0 0 0 / 0.15);
    }
    button.user-type-btn {
      background: transparent;
      border: 2px solid #d1d9e6;
      border-radius: 12px;
      padding: 1.5rem 1rem 1rem;
      width: 100%;
      max-width: 110px;
      text-align: center;
      color: #00338d;
      font-weight: 600;
      cursor: pointer;
      transition: border-color 0.3s ease, box-shadow 0.3s ease;
      display: flex;
      flex-direction: column;
      align-items: center;
      user-select: none;
    }
    button.user-type-btn:hover {
      border-color: #0071bc;
      box-shadow: 0 4px 12px rgb(0 0 0 / 0.1);
    }
    button.user-type-btn.active {
      border-color: #0071bc;
      box-shadow: 0 6px 16px rgb(0 0 0 / 0.15);
    }
    /* Form inputs */
    input[type="text"],
    input[type="password"] {
      border: 2px solid #d1d9e6;
      border-radius: 8px;
      padding: 0.75rem 1rem;
      font-size: 1rem;
      color: #00338d;
      transition: border-color 0.3s ease;
      width: 100%;
    }
    input[type="text"]:focus,
    input[type="password"]:focus {
      outline: none;
      border-color: #0071bc;
      box-shadow: 0 0 6px #a3c4f3;
    }
    /* Login button */
    button.login-btn {
      background-color: #0071bc;
      color: white;
      font-weight: 700;
      padding: 0.75rem 0;
      border-radius: 8px;
      font-size: 1.1rem;
      width: 100%;
      margin-top: 1rem;
      box-shadow: 0 4px 12px rgb(0 0 0 / 0.1);
      transition: background-color 0.3s ease;
      cursor: pointer;
    }
    button.login-btn:hover {
      background-color: #005ea2;
    }
    /* Register links */
    .register-links a {
      border: 2px solid #0071bc;
      color: #0071bc;
      font-weight: 600;
      border-radius: 8px;
      padding: 0.75rem 1rem;
      text-align: center;
      display: block;
      margin-top: 0.75rem;
      transition: background-color 0.3s ease, color 0.3s ease;
      text-decoration: none;
    }
    .register-links a:hover {
      background-color: #0071bc;
      color: white;
    }
    /* Responsive */
    @media (max-width: 640px) {
      .user-types {
        grid-template-columns: repeat(2, 1fr);
        gap: 1rem;
      }
      .card {
        padding: 2rem 1.5rem;
        max-width: 100%;
      }
    }
  </style>
</head>
<body>
  <main class="min-h-screen flex items-center justify-center p-6">
    <section class="card" aria-label="Hospital Management System Login">
      <h1 class="text-3xl font-semibold text-center select-none">Hospital Management System</h1>
      <div class="header-underline mx-auto"></div>

      <a
        href="emergency.php"
        class="btn-primary flex items-center justify-center space-x-3 w-full max-w-xs mx-auto mb-8 shadow-md"
        aria-label="Emergency Portal"
      >
        <i class="fas fa-ambulance"></i>
        <span>Emergency Portal</span>
      </a>

      <div class="user-types grid grid-cols-4 gap-6 mb-8">
        <button
          type="button"
          class="user-type-btn"
          id="btn-patient"
          aria-pressed="false"
          aria-label="Patient Login"
          onclick="selectUserType('patient', this)"
        >
          <div class="icon-circle"><i class="fas fa-user-injured"></i></div>
          Patient
        </button>
        <button
          type="button"
          class="user-type-btn"
          id="btn-admin"
          aria-pressed="false"
          aria-label="Hospital Admin Login"
          onclick="selectUserType('admin', this)"
        >
          <div class="icon-circle"><i class="fas fa-user-md"></i></div>
          Hospital Admin
        </button>
        <button
          type="button"
          class="user-type-btn"
          id="btn-lab_admin"
          aria-pressed="false"
          aria-label="Lab Admin Login"
          onclick="selectUserType('lab_admin', this)"
        >
          <div class="icon-circle"><i class="fas fa-flask"></i></div>
          Lab Admin
        </button>
        <button
          type="button"
          class="user-type-btn"
          id="btn-pharmacy_admin"
          aria-pressed="false"
          aria-label="Pharmacy Admin Login"
          onclick="selectUserType('pharmacy_admin', this)"
        >
          <div class="icon-circle"><i class="fas fa-pills"></i></div>
          Pharmacy Admin
        </button>
      </div>

      <form
        method="POST"
        id="loginForm"
        action="login.php"
        class="hidden flex flex-col space-y-4 max-w-xs mx-auto"
        aria-live="polite"
        aria-label="Login form"
      >
        <input type="hidden" name="user_type" id="user_type_input" value="" />
        <input
          type="text"
          name="username"
          placeholder="Username"
          required
          autocomplete="username"
          aria-required="true"
        />
        <input
          type="password"
          name="password"
          placeholder="Password"
          required
          autocomplete="current-password"
          aria-required="true"
        />
        <button type="submit" class="login-btn">Login</button>
        <div id="errorMessage" class="text-red-600 text-center text-sm"></div>
      </form>

      <div class="register-links max-w-xs mx-auto mt-6">
        <a href="register_lab_admin.php" aria-label="Register as Lab Admin">Register as Lab Admin</a>
        <a href="pharmacy_admin/register.php" aria-label="Register as Pharmacy Admin">Register as Pharmacy Admin</a>
      </div>
    </section>
  </main>

  <script>
    function selectUserType(type, btn) {
      // Remove active styles from all buttons
      document.querySelectorAll(".user-type-btn").forEach((b) => {
        b.classList.remove("active");
        b.setAttribute("aria-pressed", "false");
      });

      // Add active styles to clicked button
      btn.classList.add("active");
      btn.setAttribute("aria-pressed", "true");

      // Show login form
      const loginForm = document.getElementById("loginForm");
      loginForm.classList.remove("hidden");

      // Set user type hidden input
      document.getElementById("user_type_input").value = type;

      // Focus username input for better UX
      document.querySelector("#loginForm input[name='username']").focus();
    }
  </script>
</body>
</html>