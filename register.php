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
    <title>PNK CLOUD - Register</title>
    <link href="https://cdn.jsdelivr.net/npm/daisyui@4.10.2/dist/full.min.css" rel="stylesheet" type="text/css" />
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Mitr:wght@200;300;400;500;600;700&display=swap" rel="stylesheet" />
    <style>
        body { 
          font-family: 'Mitr', sans-serif;
          background: linear-gradient(135deg, #0f0f1e 0%, #1a1a3e 50%, #2d1b4e 100%);
        }
        .password-requirements { font-size: 0.85rem; color: #666;}
    </style>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-primary min-h-screen flex items-center justify-center">
<main class="w-full max-w-lg mx-auto p-4 py-8">
  <div class="card bg-base-100 shadow-lg w-full">
    <div class="card-body">
      <h2 class="card-title justify-center text-2xl font-bold mb-3">สร้างบัญชีใหม่</h2>
      <form id="registerForm" class="space-y-2">
        <div>
          <input type="text" id="username" name="username" placeholder="Username" required minlength="3" maxlength="20"
            pattern="^[a-zA-Z0-9_]{3,20}$"
            class="input input-bordered w-full" />
          <span class="password-requirements">Username 3-20 ตัว a-z, 0-9 หรือ _</span>
        </div>
        <div class="flex gap-2">
          <input type="text" id="realname" name="realname" placeholder="ชื่อจริง" required class="input input-bordered flex-1" />
          <input type="text" id="surname" name="surname" placeholder="นามสกุล" required class="input input-bordered flex-1" />
        </div>
        <div>
          <input type="email" id="email" name="email" placeholder="อีเมล" required class="input input-bordered w-full" />
        </div>
        <div class="flex gap-2">
          <div class="flex-1">
            <input type="password" id="password" name="password" placeholder="รหัสผ่าน" required minlength="8"
              class="input input-bordered w-full" />
            <span class="password-requirements block mt-1">
              อย่างน้อย 8 ตัว, a-z, A-Z, ตัวเลข, อักษรพิเศษ
            </span>
          </div>
          <div class="flex-1">
            <input type="password" id="repeatPassword" placeholder="ยืนยันรหัสผ่าน" required minlength="8"
            class="input input-bordered w-full" />
          </div>
        </div>
        <button type="submit" class="btn btn-primary w-full mt-3">สร้างบัญชี</button>
      </form>
      <div class="mt-3 flex flex-col items-center gap-2">
        <a href="login" class="link link-primary text-sm">มีบัญชีอยู่แล้ว? เข้าสู่ระบบ</a>
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
        if (timer && !options.showConfirmButton && options.confirmButtonText === undefined) {
            setTimeout(() => {
                notification.style.opacity = '0';
                notification.style.transform = 'translateX(100%)';
                setTimeout(() => notification.remove(), 300);
            }, timer);
        }
        
        return {
            then: function(callback) {
                if (options.confirmButtonText) {
                    setTimeout(() => callback({ isConfirmed: true }), 3000);
                } else {
                    setTimeout(() => callback({ isConfirmed: true }), timer);
                }
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

document.addEventListener("DOMContentLoaded",function(){
  function showInvalid(id,msg){
    document.getElementById(id).classList.add("input-error");
    Notify.fire({icon:'error',title:'ผิดพลาด',text:msg});
  }
  function clearError(id){ document.getElementById(id).classList.remove("input-error"); }

  document.getElementById("registerForm").addEventListener("submit",function(e){
    e.preventDefault();
    const username = document.getElementById('username').value;
    const password = document.getElementById('password').value;
    const repeatPassword = document.getElementById('repeatPassword').value;
    const realname = document.getElementById('realname').value;
    const surname = document.getElementById('surname').value;
    const email = document.getElementById('email').value;

    // Username
    if(!/^[a-zA-Z0-9_]{3,20}$/.test(username)){
      showInvalid('username','Username ต้อง 3-20 ตัว a-z,0-9 หรือ _');
      return;
    }
    clearError('username');

    // Repeat Password
    if(password!==repeatPassword){
      showInvalid('repeatPassword','รหัสผ่านไม่ตรงกัน');
      return;
    }
    clearError('repeatPassword');
    // Email
    if(!/\S+@\S+\.\S+/.test(email)){
      showInvalid('email','กรุณาใส่อีเมลที่ถูกต้อง');
      return;
    }
    clearError('email');

    fetch('api/auth.php',{
      method:'POST',
      headers:{'Content-Type':'application/x-www-form-urlencoded'},
      body:new URLSearchParams({
        action:'register',
        username,
        email,
        password,
        realname,
        surname
      })
    }).then(res=>res.json())
    .then(response=>{
      if(response.status==='success'){
        Notify.fire({icon:'success',title:'สร้างบัญชีแล้ว',text:'บัญชีถูกสร้างสำเร็จ',confirmButtonText:'เข้าสู่ระบบ'})
        .then(r=>{
          if(r.isConfirmed)
            window.location.href='login';
        });
      }else{
        Notify.fire({icon:'error',title:'เกิดข้อผิดพลาด!',text:response.message});
      }
    })
    .catch(()=>{
      Notify.fire({icon:'error',title:'เกิดข้อผิดพลาด!',text:'ไม่สามารถเชื่อมต่อกับเซิร์ฟเวอร์ได้'});
    });
  });
});
</script>
</body>
</html>
