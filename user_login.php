<?php 
	include 'header.php';
 ?>

<div style="display: flex; justify-content: center; align-items: center; min-height: calc(100vh - 156px);">
    <div style="max-width: 450px; width: 100%; margin: 0 auto;">
        <div class="login-container">

            <div class="icon-container">
                <i class="fas fa-user-lock"></i>
            </div>
            
            <h2 class="login-title text-center mb-4">Login</h2>
            
            <form action="proses/login.php" method="POST">
                <div class="form-group mb-4">
                    <label for="username">Username</label>
                    <div class="input-with-icon">
                        <i class="fas fa-user"></i>
                        <input type="text" class="form-control custom-input" id="username" placeholder="Masukkan username anda" name="username">
                    </div>
                </div>
                
                <div class="form-group mb-4">
                    <label for="password">Password</label>
                    <div class="input-with-icon">
                        <i class="fas fa-lock"></i>
                        <input type="password" class="form-control custom-input" id="password" placeholder="Masukkan password anda" name="pass">
                    </div>
                </div>
                
                <div class="button-group">
                    <button type="submit" class="btn custom-btn-login">
                        <span>Login</span>
                        <i class="fas fa-arrow-right"></i>
                    </button>
                    <a href="register.php" class="btn custom-btn-register">
                        <span>Daftar</span>
                    </a>
                </div>
            </form>
        </div>
        <div class="login-footer text-center">
            <p>Informasi Ulos &copy; 2025</p>
        </div>
    </div>
</div>

<style>

    @import url('https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Montserrat:wght@300;400;500;600&display=swap');
    
    .login-container {
        background-color: #fff;
        border-radius: 20px;
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
        padding: 45px 40px;
        position: relative;
        margin: 20px auto;
        border-top: 5px solid #ff8680;
        transform: translateY(-10px);
        transition: all 0.4s ease;
        backdrop-filter: blur(10px);
        background-image: linear-gradient(145deg, rgba(255, 255, 255, 0.95), rgba(255, 255, 255, 0.9));
    }
    
    .login-container:hover {
        box-shadow: 0 20px 40px rgba(230, 115, 109, 0.15);
        transform: translateY(-15px);
    }
    
    .icon-container {
        width: 90px;
        height: 90px;
        margin: 0 auto 30px;
        background: linear-gradient(135deg, #ff8680, #e6736d);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 10px 20px rgba(230, 115, 109, 0.3);
        position: relative;
        z-index: 2;
        transition: all 0.3s ease;
    }
    
    .icon-container:before {
        content: "";
        position: absolute;
        top: -5px;
        left: -5px;
        right: -5px;
        bottom: -5px;
        background: linear-gradient(135deg, rgba(255, 214, 212, 0.5), rgba(255, 134, 128, 0.1));
        border-radius: 50%;
        z-index: -1;
        animation: pulse 2.5s infinite;
    }
    
    @keyframes pulse {
        0% { transform: scale(1); opacity: 0.8; }
        50% { transform: scale(1.1); opacity: 0.5; }
        100% { transform: scale(1); opacity: 0.8; }
    }
    
    .icon-container i {
        font-size: 40px;
        color: white;
    }
    
    .login-title {
        font-family: 'Playfair Display', serif;
        font-weight: 600;
        color: #333;
        position: relative;
        padding-bottom: 15px;
        letter-spacing: 0.5px;
    }
    
    .login-title:after {
        content: "";
        position: absolute;
        width: 80px;
        height: 3px;
        background: linear-gradient(to right, #ff8680, #e6736d);
        bottom: 0;
        left: 50%;
        transform: translateX(-50%);
    }
    
    label {
        font-family: 'Montserrat', sans-serif;
        font-weight: 500;
        color: #555;
        margin-bottom: 8px;
        font-size: 14px;
        letter-spacing: 0.3px;
    }
    
    .input-with-icon {
        position: relative;
    }
    
    .input-with-icon i {
        position: absolute;
        left: 16px;
        top: 50%;
        transform: translateY(-50%);
        color: #999;
        font-size: 16px;
        transition: all 0.3s;
    }
    
    .custom-input {
        height: 55px;
        border-radius: 12px;
        border: 1px solid #e1e1e1;
        padding-left: 45px;
        font-family: 'Montserrat', sans-serif;
        transition: all 0.3s ease;
        font-size: 14px;
        background-color: #f9f9f9;
    }
    
    .custom-input:focus {
        border-color: #ff8680;
        box-shadow: 0 0 0 0.2rem rgba(255, 134, 128, 0.25);
        background-color: #fff;
    }
    
    .custom-input:focus + i {
        color: #ff8680;
    }
    
    .button-group {
        display: flex;
        gap: 15px;
        margin-top: 35px;
    }
    
    .custom-btn-login {
        flex: 1;
        height: 54px;
        border-radius: 12px;
        background: linear-gradient(135deg, #ff8680, #e6736d);
        border: none;
        color: white;
        font-family: 'Montserrat', sans-serif;
        font-weight: 500;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
    }
    
    .custom-btn-login i {
        transition: all 0.3s ease;
        transform: translateX(0);
        opacity: 0;
        width: 0;
    }
    
    .custom-btn-login:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 20px rgba(230, 115, 109, 0.4);
    }
    
    .custom-btn-login:hover i {
        transform: translateX(3px);
        opacity: 1;
        width: auto;
    }
    
    .custom-btn-register {
        flex: 1;
        height: 54px;
        border-radius: 12px;
        background-color: #fff;
        border: 2px solid #ff8680;
        color: #ff8680;
        font-family: 'Montserrat', sans-serif;
        font-weight: 500;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .custom-btn-register:hover {
        background-color: #ffd6d4;
        color: #e6736d;
        transform: translateY(-3px);
        box-shadow: 0 8px 20px rgba(255, 214, 212, 0.5);
    }
    
    .login-footer {
        margin-top: 20px;
        font-family: 'Montserrat', sans-serif;
        font-size: 13px;
        color: #999;
        opacity: 0.7;
    }
    
    /* Responsive adjustments */
    @media (max-width: 768px) {
        .login-container {
            padding: 30px 25px;
            margin: 15px;
        }
        
        .icon-container {
            width: 80px;
            height: 80px;
        }
        
        .button-group {
            flex-direction: column;
        }
        
        .custom-btn-login, .custom-btn-register {
            width: 100%;
        }
    }
</style>

<?php 
	include 'footer.php';
?>