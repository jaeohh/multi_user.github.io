<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Select Role - Sign In</title>
  <style>
    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    body, html {
      height: 100%;
      overflow: hidden;
    }

    body {
      background: url('12.jpg') no-repeat center center/cover;
      transition: background-image 0.5s ease-in-out;
      display: flex;
      align-items: center;
      justify-content: center;
      height: 100vh;
    }

    .overlay {
      position: absolute;
      top: 0; left: 0;
      width: 100%; height: 100%;
      background-color: rgba(0, 0, 0, 0.6);
      z-index: 1;
    }

    .container {
      position: relative;
      z-index: 2;
      text-align: center;
      color: white;
      width: 100%;
      max-width: 500px;
      padding: 20px;
    }

    h1 {
      margin-bottom: 40px;
      font-size: 2.5rem;
      font-weight: bold;
      letter-spacing: 1px;
    }

    .buttons {
      display: flex;
      justify-content: center;
      flex-wrap: wrap;
      gap: 20px;
    }

    .role-btn {
      padding: 20px 50px;
      font-size: 1.2rem;
      border: none;
      border-radius: 10px;
      cursor: pointer;
      background-color: rgba(255, 255, 255, 0.1);
      color: white;
      backdrop-filter: blur(6px);
      transition: all 0.3s ease;
      width: 200px;
    }

    .role-btn:hover {
      background-color: rgba(255, 255, 255, 0.25);
      transform: scale(1.05);
    }

    @media (max-width: 768px) {
      h1 {
        font-size: 2rem;
      }
      .role-btn {
        width: 80%;
      }
    }

    @media (max-width: 480px) {
      .role-btn {
        width: 90%;
        padding: 15px 40px;
        font-size: 1rem;
      }
    }
  </style>
</head>
<body>
  <div class="overlay"></div>

  <div class="container">
    <h1>Select Your Role to Sign In</h1>
    <div class="buttons">
      <button class="role-btn" id="studentBtn">Student</button>
      <button class="role-btn" id="teacherBtn">Teacher</button>
      <button class="role-btn" id="adminBtn">Admin</button>
    </div>
  </div>

  <script>
    const body = document.body;

    const defaultBg = "url('12.jpg')";
    const studentBg = "url('13.jpg')";
    const teacherBg = "url('14.jpg')";
    const adminBg = "url('15.jpg')";

    const studentBtn = document.getElementById('studentBtn');
    const teacherBtn = document.getElementById('teacherBtn');
    const adminBtn = document.getElementById('adminBtn');

    studentBtn.addEventListener("mouseenter", () => {
      body.style.backgroundImage = studentBg;
    });
    studentBtn.addEventListener("mouseleave", () => {
      body.style.backgroundImage = defaultBg;
    });

    teacherBtn.addEventListener("mouseenter", () => {
      body.style.backgroundImage = teacherBg;
    });
    teacherBtn.addEventListener("mouseleave", () => {
      body.style.backgroundImage = defaultBg;
    });

    adminBtn.addEventListener("mouseenter", () => {
      body.style.backgroundImage = adminBg;
    });
    adminBtn.addEventListener("mouseleave", () => {
      body.style.backgroundImage = defaultBg;
    });

    studentBtn.addEventListener("click", () => {
      window.location.href = "studentslogin.php";
    });

    teacherBtn.addEventListener("click", () => {
      window.location.href = "teacherlogin.php";
    });

    adminBtn.addEventListener("click", () => {
      window.location.href = "adminlogin.php";
    });
  </script>
</body>
</html>
