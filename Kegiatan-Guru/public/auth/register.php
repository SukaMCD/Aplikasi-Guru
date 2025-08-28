<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link rel="apple-touch-icon" sizes="76x76" href="../assets/img/apple-icon.png">
  <link rel="icon" type="image/png" href="../assets/img/favicon.png">
  <title>
    Register - Kegiatan Guru
  </title>
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet" />
  <link href="https://demos.creative-tim.com/argon-dashboard-pro/assets/css/nucleo-icons.css" rel="stylesheet" />
  <link href="https://demos.creative-tim.com/argon-dashboard-pro/assets/css/nucleo-svg.css" rel="stylesheet" />
  <script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>
  <link id="pagestyle" href="../assets/css/argon-dashboard.css?v=2.1.0" rel="stylesheet" />
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body class="">
  <div class="container position-sticky z-index-sticky top-0">
    <div class="row">
      <div class="col-12">
        </div>
    </div>
  </div>
  <main class="main-content  mt-0">
    <section>
      <div class="page-header min-vh-100">
        <div class="container">
          <div class="row">
            <div class="col-xl-4 col-lg-5 col-md-7 d-flex flex-column mx-lg-0 mx-auto">
              <div class="card card-plain">
                <div class="card-header pb-0 text-start">
                  <h4 class="font-weight-bolder">Sign Up</h4>
                  <!-- Updated description to mention approval process -->
                  <p class="mb-0">Enter your information to create an account. Your account will need admin approval.</p>
                </div>
                <div class="card-body">
                  <form role="form" id="register-form">
                    <div class="mb-3">
                      <input type="text" name="username" class="form-control form-control-lg" placeholder="Nama Lengkap" aria-label="Username" required>
                    </div>
                    <div class="mb-3">
                      <input type="email" name="email" class="form-control form-control-lg" placeholder="Email" aria-label="Email" required>
                    </div>
                    <!-- Added role selection dropdown -->
                    <div class="mb-3">
                      <select name="role" class="form-control form-control-lg" required>
                        <option value="">Pilih Role</option>
                        <option value="guru">Guru</option>
                        <option value="murid">Murid</option>
                      </select>
                    </div>
                    <div class="mb-3">
                      <input type="password" name="password" class="form-control form-control-lg" placeholder="Password" aria-label="Password" required minlength="6">
                    </div>
                    <div class="mb-3">
                      <input type="password" name="confirm_password" class="form-control form-control-lg" placeholder="Confirm Password" aria-label="Confirm Password" required>
                    </div>
                    <div class="form-check form-switch">
                      <input class="form-check-input" type="checkbox" id="agreeTerms" required>
                      <label class="form-check-label" for="agreeTerms">I agree to the <a href="#" class="text-primary">Terms and Conditions</a></label>
                    </div>
                    <div class="text-center">
                      <button type="button" class="btn btn-lg btn-primary btn-lg w-100 mt-4 mb-0" id="btn-register">Sign up</button>
                    </div>
                  </form>
                </div>
                <div class="card-footer text-center pt-0 px-lg-2 px-1">
                  <p class="mb-4 text-sm mx-auto">
                    Already have an account?
                    <a href="/KODINGAN/PWPB/Aplikasi-Guru/" class="text-primary text-gradient font-weight-bold">Sign in</a>
                  </p>
                </div>
              </div>
            </div>
            <div class="col-6 d-lg-flex d-none h-100 my-auto pe-0 position-absolute top-0 end-0 text-center justify-content-center flex-column">
              <div class="position-relative bg-gradient-primary h-100 m-3 px-7 border-radius-lg d-flex flex-column justify-content-center overflow-hidden" style="background-image: url('https://raw.githubusercontent.com/creativetimofficial/public-assets/master/argon-dashboard-pro/assets/img/signin-ill.jpg');
          background-size: cover;">
                <span class="mask bg-gradient-primary opacity-6"></span>
                <h4 class="mt-5 text-white font-weight-bolder position-relative">"Join our community today"</h4>
                <p class="text-white position-relative">Create your account and start your journey with us. Admin approval required.</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  </main>
  <script>
    document.getElementById("btn-register").addEventListener("click", function() {
      const form = document.getElementById("register-form");
      const formData = new FormData(form);
      
      // Check if terms are agreed
      const agreeTerms = document.getElementById("agreeTerms");
      if (!agreeTerms.checked) {
        Swal.fire({
          title: 'Error!',
          text: 'You must agree to the Terms and Conditions',
          icon: 'warning'
        });
        return;
      }

      // Tampilkan loading
      const btn = this;
      btn.disabled = true;
      btn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Memproses...';

      fetch("../../app/controllers/proses_register.php", {
          method: "POST",
          body: formData
        })
        .then(response => {
          if (!response.ok) {
            throw new Error('Network response was not ok');
          }
          return response.json();
        })
        .then(data => {
          if (data.status === "success") {
            Swal.fire({
              title: data.title,
              text: data.message,
              icon: data.icon,
              timer: 3000,
              showConfirmButton: true
            }).then(() => {
              window.location.href = data.redirect;
            });
          } else {
            Swal.fire({
              title: data.title,
              text: data.message,
              icon: data.icon
            });
          }
        })
        .catch(error => {
          console.error('Error:', error);
          Swal.fire({
            title: 'Error!',
            text: 'Terjadi kesalahan saat registrasi',
            icon: 'error'
          });
        })
        .finally(() => {
          btn.disabled = false;
          btn.innerHTML = 'Sign up';
        });
    });

    // Handle enter key
    document.getElementById("register-form").addEventListener("keypress", function(e) {
      if (e.key === "Enter") {
        e.preventDefault();
        document.getElementById("btn-register").click();
      }
    });

    // Password confirmation validation
    document.querySelector('input[name="confirm_password"]').addEventListener('input', function() {
      const password = document.querySelector('input[name="password"]').value;
      const confirmPassword = this.value;
      
      if (password !== confirmPassword && confirmPassword !== '') {
        this.setCustomValidity('Passwords do not match');
      } else {
        this.setCustomValidity('');
      }
    });
  </script>
  <script src="../assets/js/core/popper.min.js"></script>
  <script src="../assets/js/core/bootstrap.min.js"></script>
  <script src="../assets/js/plugins/perfect-scrollbar.min.js"></script>
  <script src="../assets/js/plugins/smooth-scrollbar.min.js"></script>
  <script>
    var win = navigator.platform.indexOf('Win') > -1;
    if (win && document.querySelector('#sidenav-scrollbar')) {
      var options = {
        damping: '0.5'
      }
      Scrollbar.init(document.querySelector('#sidenav-scrollbar'), options);
    }
  </script>
  <script async defer src="https://buttons.github.io/buttons.js"></script>
  <script src="../assets/js/argon-dashboard.min.js?v=2.1.0"></script>
</body>

</html>
