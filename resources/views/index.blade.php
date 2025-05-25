<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bem-vindo</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 30px;
            background-color: #f4f4f4;
        }
        .container {
            background-color: white;
            padding: 25px;
            border-radius: 8px;
            max-width: 600px;
            margin: 0 auto;
            text-align: center;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #333;
        }
        .user-info {
            margin-top: 20px;
            font-size: 18px;
        }
        .logout-btn {
            margin-top: 25px;
        }
        button {
            background-color: #e3342f;
            color: white;
            padding: 10px 25px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
        }
        button:hover {
            background-color: #cc1f1a;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Bem-vindo(a)!</h1>

        @if(Auth::check())
            <div class="user-info">
                <p><strong>Nome:</strong> {{ Auth::user()->name }}</p>
                <p><strong>Email:</strong> {{ Auth::user()->email }}</p>
            </div>

            <div class="logout-btn">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit">Sair</button>
                </form>
            </div>
        @else
            <p>Você não está logado.</p>
            <a href="{{ url('/login') }}">Fazer Login</a> |
            <a href="{{ url('/register') }}">Cadastrar</a>
        @endauth
    </div>
</body>
</html>
