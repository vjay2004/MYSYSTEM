<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Donsal Express Corporation</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      line-height: 1.6;
      color: #333;
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }

    .header {
      background-color: orange;
      padding: 15px 20px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      color: black;
      font-weight: bold;
      box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }

    .header img {
      width: 50px;
      height: 50px;
      border-radius: 5px;
      object-fit: contain;
    }

    .company-name {
      font-size: 1.2rem;
      margin-left: 10px;
      white-space: nowrap;
    }

    .header button {
      background-color: white;
      border: none;
      border-radius: 5px;
      padding: 8px 15px;
      cursor: pointer;
      font-weight: bold;
      transition: all 0.3s ease;
    }

    .header button:hover {
      background-color: #f0f0f0;
      transform: translateY(-2px);
    }

    .container {
      padding: 30px 20px;
      text-align: center;
      max-width: 1200px;
      margin: 0 auto;
    }

    .cards {
      display: flex;
      flex-wrap: wrap;
      justify-content: center;
      gap: 20px;
      margin-top: 20px;
    }

    .card {
      width: 280px;
      padding: 15px;
      border-radius: 10px;
      background-color: #ed970c;
      text-align: center;
      box-shadow: 0 4px 8px rgba(0,0,0,0.1);
      transition: transform 0.3s ease;
      position: relative;
      overflow: hidden;
    }

    .card:hover {
      transform: translateY(-5px);
    }

    .card img {
      width: 100%;
      height: 250px;
      border-radius: 8px;
      object-fit: cover;
      border: 3px solid #785620;
    }

    .card h3 {
      position: absolute;
      top: 25px;
      left: 50%;
      transform: translateX(-50%);
      background: rgba(0,0,0,0.7);
      color: white;
      padding: 5px 15px;
      border-radius: 20px;
      font-size: 1.1rem;
      z-index: 1;
    }

    .card-buttons {
      margin-top: 15px;
      display: flex;
      justify-content: center;
      gap: 10px;
      transform: translateY(-75px);
    }

    .card button {
      background-color: orange;
      color: #000;
      border: none;
      padding: 8px 15px;
      cursor: pointer;
      font-weight: bold;
      border-radius: 5px;
      transition: all 0.3s ease;
      box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }

    .card button:hover,
    .card button a:hover {
      background-color: #e68a00;
      color: white;
    }

    .card button a {
      text-decoration: none;
      color: black;
    }

    .info {
      display: flex;
      flex-wrap: wrap;
      justify-content: space-around;
      padding: 40px 20px;
      background: #ed970c;
      gap: 30px;
    }

    .info div {
      flex: 1;
      min-width: 250px;
      max-width: 350px;
      padding: 15px;
    }

    .info h3 {
      color: #000;
      margin-bottom: 15px;
      font-size: 1.3rem;
      border-bottom: 2px solid #785620;
      padding-bottom: 5px;
    }

    .info p {
      color: #333;
      text-align: justify;
    }

    @media (max-width: 768px) {
      .header {
        flex-direction: column;
        text-align: center;
        gap: 10px;
      }

      .company-name {
        margin-left: 0;
      }

      .cards {
        flex-direction: column;
        align-items: center;
      }

      .info {
        flex-direction: column;
        align-items: center;
      }

      .info div {
        max-width: 100%;
      }
    }
  </style>
</head>
<body>
  <div class="header">
    <div style="display: flex; align-items: center;">
      <img src="logo.png" alt="Company Logo">
      <div class="company-name">DONSAL'S EXPRESS CORPORATION</div>
    </div>
    <button onclick="openContactModal()">📍 Contact Us</button>
  </div>

  <!-- Main content wrapper to blur when modal is open -->
  <div id="main-content">
    <div class="container">
      <div class="cards">
        <!-- Conductor Card -->
        <div class="card">
          <h3>CONDUCTOR</h3>
          <img src="donsal.jpg" alt="Conductor">
          <div class="card-buttons">
            <button><a href="conductor_login.php">Sign in</a></button>
          </div>
        </div>

        <!-- Admin Card -->
        <div class="card">
          <h3>ADMIN</h3>
          <img src="donsal2.jpg" alt="Admin">
          <div class="card-buttons">
            <button><a href="login.php">Sign in</a></button>
            <button><a href="registration.php">Register</a></button>
          </div>
        </div>

        <!-- Inspector Card -->
        <div class="card">
          <h3>INSPECTOR</h3>
          <img src="donsal1.jpg" alt="Inspector">
          <div class="card-buttons">
            <button><a href="inspector_login.php">Sign in</a></button>
          </div>
        </div>
      </div>
    </div>

    <div class="info">
      <div>
        <h3>Intro</h3>
        <p>Donsal Express Corporation is a premier delivery and cargo service provider based in Iligan City. Dedicated to efficiency and reliability, we help individuals and businesses send packages quickly and securely. Our reputation is built on fast service, dependable transportation, and exceptional customer care.</p>
      </div>
      <div>
        <h3>Vision</h3>
        <p>At Donsal Express Corporation, our vision is to become the most trusted delivery and cargo service provider in Iligan City and beyond. We are committed to offering safe, fast, and cost-effective logistics solutions while fostering strong connections between communities and businesses through reliable transportation.</p>
      </div>
      <div>
        <h3>Services</h3>
        <p>We specialize in delivering packages with speed and care, ensuring they arrive safely at your doorstep. Whether for personal or business needs, our dependable logistics solutions guarantee secure and timely transportation.</p>
      </div>
    </div>
  </div>

  <!-- Contact Modal -->
  <div id="contact-modal" class="modal">
    <div class="modal-content">
      <span class="close-btn" onclick="closeContactModal()">&times;</span>
      <h3>Contact Us</h3>
      <p><strong>📍 Address:</strong> Buru-un, Iligan City, Philippines</p>
      <p><strong>📞 Phone:</strong> (063) 36-880-9380</p>
      <p><strong>📧 Email:</strong> contact@donsalexpress.com</p>
    </div>
  </div>

  <!-- Modal Styles -->
  <style>
    .modal {
      display: none;
      position: fixed;
      z-index: 999;
      left: 0;
      top: 0;
      width: 100%;
      height: 100%;
      overflow: auto;
      background: rgba(0, 0, 0, 0.6);
      backdrop-filter: blur(5px);
    }

    .modal-content {
      background-color: #fff;
      margin: 10% auto;
      padding: 20px;
      border-radius: 10px;
      width: 90%;
      max-width: 400px;
      box-shadow: 0 5px 15px rgba(0,0,0,0.3);
      text-align: center;
    }

    .close-btn {
      float: right;
      font-size: 1.5rem;
      font-weight: bold;
      cursor: pointer;
    }

    .modal-content h3 {
      margin-bottom: 15px;
      font-size: 1.5rem;
      color: #ed970c;
    }

    .modal-content p {
      margin: 10px 0;
      font-size: 1rem;
    }

   
    .blur {
      filter: blur(5px);
      pointer-events: none;
      user-select: none;
    }
  </style>

  <!-- Modal Script -->
  <script>
    function openContactModal() {
      document.getElementById('contact-modal').style.display = 'block';
      document.getElementById('main-content').classList.add('blur');
    }

    function closeContactModal() {
      document.getElementById('contact-modal').style.display = 'none';
      document.getElementById('main-content').classList.remove('blur');
    }

    // Optional: Close modal on outside click
    window.onclick = function(event) {
      const modal = document.getElementById('contact-modal');
      if (event.target === modal) {
        closeContactModal();
      }
    }
  </script>
</body>

</html>
