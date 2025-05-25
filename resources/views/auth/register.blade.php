<!DOCTYPE html>
<html>
<head>
    <title>Cadastro</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            background-color: #f2f2f2;
            padding-top: 80px;
        }
        .container {
            display: inline-block;
            background-color: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            width: 320px;
        }
        input[type="text"],
        input[type="email"],
        input[type="password"] {
            width: 90%;
            padding: 10px;
            margin: 6px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .error {
            color: red;
            font-size: 13px;
            text-align: left;
            margin: 2px 0 10px 18px;
        }
        button {
            background-color: #38c172;
            color: white;
            padding: 10px 25px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 15px;
        }
        button:hover {
            background-color: #2f9e65;
        }
        a {
            display: block;
            margin-top: 15px;
            color: #3490dc;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Criar Conta</h2>

        <form method="POST" action="{{ url('/register') }}">
            @csrf

            <input type="text" name="name" placeholder="Nome completo" value="{{ old('name') }}">
            @if ($errors->has('name'))
                <div class="error">{{ $errors->first('name') }}</div>
            @endif

            <input type="email" name="email" placeholder="Email" value="{{ old('email') }}">
            @if ($errors->has('email'))
                <div class="error">{{ $errors->first('email') }}</div>
            @endif

            <input type="text" name="user_image" placeholder="URL da imagem (opcional)" value="{{ old('user_image') }}">
            @if ($errors->has('user_image'))
                <div class="error">{{ $errors->first('user_image') }}</div>
            @endif

            <input type="password" name="password" placeholder="Senha">
            @if ($errors->has('password'))
                <div class="error">{{ $errors->first('password') }}</div>
            @endif

            <input type="password" name="password_confirmation" placeholder="Confirmar senha">

            <button type="submit">Cadastrar</button>
        </form>

        <a href="{{ url('/login') }}">Já tem uma conta? Entrar</a>
    </div>
</body>
</html>
