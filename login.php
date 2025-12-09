<?php
session_start();
require_once 'config/db.php';

if (isset($_SESSION['user_id'])) {
    header('Location: ./');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>PNK CLOUD - Login</title>
    <link href="https://cdn.jsdelivr.net/npm/daisyui@4.10.2/dist/full.min.css" rel="stylesheet" type="text/css" />
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Mitr:wght@200;300;400;500;600;700&display=swap" rel="stylesheet" />
    <style>
      body { 
        font-family: 'Mitr', sans-serif;
        background: linear-gradient(135deg, #0f0f1e 0%, #1a1a3e 50%, #2d1b4e 100%);
      }
    </style>
</head>
<body class="bg-primary min-h-screen flex items-center justify-center">
  <main class="w-full max-w-md mx-auto px-4 py-8">
    <div class="card bg-base-100 shadow-xl w-full">
      <div class="card-body">
        <h2 class="card-title justify-center text-2xl font-bold mb-4">เข้าสู่ระบบ</h2>
        <form id="loginForm" class="space-y-4">
          <div>
            <input type="text" id="username" name="username" placeholder="Username" class="input input-bordered w-full" required>
          </div>
          <div>
            <input type="password" id="password" name="password" placeholder="Password" class="input input-bordered w-full" required>
          </div>
          <button type="submit" class="btn btn-primary w-full">เข้าสู่ระบบ</button>
        </form>
        <div class="mt-4 flex flex-col items-center gap-2">
          <a href="forgot-password.html" class="link link-primary text-sm">ลืมรหัสผ่าน?</a>
          <a href="register" class="link link-secondary text-sm">ยังไม่มีบัญชี? สร้างบัญชีใหม่</a>
        </div>
      </div>
    </div>
  </main>
  
  <!-- Custom Notification Container -->
  <div id="notificationContainer" class="fixed top-4 right-4 z-50 space-y-2" style="z-index: 9999;"></div>

  <script>
    // Custom Notification System
    const Notify = {
        show: function(options) {
            const container = document.getElementById('notificationContainer');
            const notification = document.createElement('div');
            
            const icon = options.icon === 'success' ? '✓' : 
                        options.icon === 'error' ? '✕' : 
                        options.icon === 'warning' ? '⚠' : 'ℹ';
            
            const bgColor = options.icon === 'success' ? 'bg-green-500' : 
                           options.icon === 'error' ? 'bg-red-500' : 
                           options.icon === 'warning' ? 'bg-yellow-500' : 'bg-blue-500';
            
            notification.className = `${bgColor} text-white px-6 py-4 rounded-lg shadow-lg transform transition-all duration-300 max-w-md`;
            notification.innerHTML = `
                <div class="flex items-start gap-3">
                    <span class="text-2xl font-bold">${icon}</span>
                    <div class="flex-1">
                        <h4 class="font-bold text-lg">${options.title || ''}</h4>
                        <p class="text-sm mt-1">${options.text || ''}</p>
                    </div>
                </div>
            `;
            
            container.appendChild(notification);
            
            const timer = options.timer || 3000;
            if (timer && !options.showConfirmButton) {
                setTimeout(() => {
                    notification.style.opacity = '0';
                    notification.style.transform = 'translateX(100%)';
                    setTimeout(() => notification.remove(), 300);
                }, timer);
            }
            
            return {
                then: function(callback) {
                    setTimeout(() => callback({ isConfirmed: true }), timer);
                    return this;
                }
            };
        },
        
        fire: function(options) {
            if (typeof options === 'string') {
                return this.show({ title: arguments[0], text: arguments[1], icon: arguments[2] || 'info' });
            }
            return this.show(options);
        }
    };
    
    const Swal = Notify;

    document.getElementById("loginForm").addEventListener("submit", function(e) {
      e.preventDefault();
      const username = document.getElementById('username').value.trim();
      const password = document.getElementById('password').value;

      if (username.length < 3) {
        Notify.fire({icon:'error',title:'Invalid Username',text:'Username must be at least 3 characters long'});
        return;
      }

      fetch('api/auth.php',{
        method:'POST',
        headers:{'Content-Type':'application/x-www-form-urlencoded'},
        body: new URLSearchParams({
          action:'login',
          username,
          password
        })
      })
      .then(res=>res.json())
      .then(response=>{
        if(response.status==='success'){
          Notify.fire({icon:'success',title:'Success!',text:'Login successful',timer:1500,showConfirmButton:false})
          .then(()=>window.location.href='./');
        }else{
          Notify.fire({icon:'error',title:'เกิดข้อผิดพลาด!',text:response.message});
        }
      })
      .catch(()=>{
        Notify.fire({icon:'error',title:'Error',text:'An error occurred. Please try again later.'});
      });
    });
  </script>
</body>
</html>
