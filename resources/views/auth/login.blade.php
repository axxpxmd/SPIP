<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Login - SPIP</title>
    <link href="{{ asset('images/template/tangsel.png') }}" rel="icon" type="image/png">

    <!-- LINEARICONS -->
    <link rel="stylesheet" href="{{ asset('assetLogin/fonts/linearicons/style.css') }}">

    <!-- STYLE CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
    <link rel="stylesheet" href="{{ asset('assetLogin/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('assetLogin/css/util.css') }}">

    <!-- Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">

</head>
<body style="font-family: 'Poppins', sans-serif">
    <div class="wrapper">
        <div class="inner">
            <img src="{{ asset('assetLogin/images/survey.png') }}" alt="" class="image-1" width="900px" style="margin-left: -190px !important; margin-bottom: 17px !important">
            <div class="text-center">
                <div class="row">
                    <div class="col-4 p-0">
                        <img src="{{ asset('images/template/tangsel.png') }}" class="img-fluid mb-2" width="110" alt="">
                    </div>
                    <div class="col-8 p-0 text-left fs-25 text-white" style="font-weight: bolder">
                        <p>SPIP</p>
                        <p>TANGERANG SELATAN</p>
                    </div>
                </div>
            </div>
            <form method="POST" action="{{ route('login') }}" class="needs-validation" style="border-radius: 20px" novalidate>
                @csrf
                <div class="text-center" style="margin-top: -40px !important;">
                    <p class="fw-bold fs-25 text-black">SPIP</p>
                    <p class="text-black fs-14 mt-1">Sistem Pengendalian Intern Pemerintah</p>
                </div>
                <div class="mt-4">
                    <div class="form-holder">
                        <span class="lnr lnr-user text-black-50"></span>
                        <input type="text" class="form-control-l" name="username" autocomplete="username" placeholder="Masukan Username" value="{{ old('username') }}" autofocus required>
                        <div class="invalid-feedback fw-bold fs-10 mt-2 mb-3" style="position: absolute;">
                            Username wajib diisi.
                        </div>
                    </div>
                    <div class="form-holder mt-4">
                        <span class="lnr lnr-lock text-black-50"></span>
                        <input type="password" class="form-control-l" name="password" placeholder="Masukan Password" required>
                        <div class="invalid-feedback fs-10 fw-bold mt-2" style="position: absolute;">
                            Password wajib diisi.
                        </div>
                    </div>
                    @if (session('error'))
                    <div class="alert alert-danger fs-14 text-center fw-bold" style="margin-bottom: -20px !important; margin-top: 35px !important" role="alert">
                        Username / Password Salah.
                    </div>
                    @endif
                    @if (count($errors) > 0)
                    <div class="alert alert-danger fs-12 text-center" style="margin-bottom: -25px !important" role="alert">
                        Username / Password salah!
                    </div>
                    @endif
                    <button type="submit" class="rounded">MASUK</button>
                </div>
            </form>
            <p class="text-center text-white fw-bold fs-14 mt-2">SPIP © {{ date('Y') }}</p>
            <img src="{{ asset('assetLogin/images/image3.png') }}" alt="" class="image-2" style="margin-right: -100px !important; margin-bottom: -5px !important" width="300">
        </div>
    </div>

    <script src="{{ asset('assetLogin/js/jquery-3.3.1.min.js') }}"></script>
    <script src="{{ asset('assetLogin/js/main.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous"></script>
    <script>
        // Example starter JavaScript for disabling form submissions if there are invalid fields
        (() => {
        'use strict'

        // Fetch all the forms we want to apply custom Bootstrap validation styles to
        const forms = document.querySelectorAll('.needs-validation')

        // Loop over them and prevent submission
        Array.from(forms).forEach(form => {
            form.addEventListener('submit', event => {
            if (!form.checkValidity()) {
                event.preventDefault()
                event.stopPropagation()
            }

            form.classList.add('was-validated')
            }, false)
        })
        })()
    </script>
</body>
</html>
