<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <link rel="shortcut icon" href="/img/logo.ico">
    <title>404 - Página no encontrada</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background: #f4f6f9;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: system-ui;
        }

        .error-box {
            background: white;
            padding: 60px;
            border-radius: 12px;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            max-width: 520px;
            width: 100%;
        }

        .error-code {
            font-size: 90px;
            font-weight: 700;
            color: #26C6DA;
        }

        .error-title {
            font-size: 26px;
            font-weight: 600;
            margin-bottom: 15px;
        }

        .error-text {
            color: #6c757d;
            margin-bottom: 35px;
        }

        .btn-home {
            background: #26C6DA;
            border: none;
            color: white;
        }

        .btn-home:hover {
            background: #1bb4c6;
        }

        .btn-back {
            border: 2px solid #D32F2F;
            color: #D32F2F;
        }

        .btn-back:hover {
            background: #D32F2F;
            color: white;
        }

        .footer-text {
            margin-top: 30px;
            font-size: 14px;
            color: #888;
        }
    </style>
</head>

<body>

    <div class="error-box">

        <img src="/img/logo.png" width="150" class="mb-4">

        <div class="error-code">
            404
        </div>

        <div class="error-title">
            PAGE NOT FOUND
        </div>

        <p class="error-text">
            La página que estás buscando no existe o la URL es incorrecta.
        </p>

        <div class="d-flex justify-content-center gap-3">

            <a href="{{ url('/') }}" class="btn btn-home px-4">
                Go Home
            </a>

            <a href="{{ url()->previous() }}" class="btn btn-back px-4">
                Volver
            </a>

        </div>

    </div>

</body>

</html>
