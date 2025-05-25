<!DOCTYPE html>
<html>
<head>
    <title>Bem-vindo</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            padding-top: 100px;
            background-color: #f2f2f2;
        }
        .container {
            display: inline-block;
            background-color: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        h1 {
            margin-bottom: 30px;
        }
        a.button {
            display: inline-block;
            padding: 10px 25px;
            margin: 10px;
            background-color: #3490dc;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-size: 16px;
        }
        a.button:hover {
            background-color: #2779bd;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Bem-vindo ao Sistema</h1>
        <a href="{{ url('/login') }}" class="button">Login</a>
        <a href="{{ url('/register') }}" class="button">Cadastro</a>
    </div>
</body>
</html>
