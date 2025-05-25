<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bem-vindo</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome (ícones) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

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
            max-width: 800px;
            margin: 0 auto;
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
        button.btn-danger {
            background-color: #e3342f;
        }
        .user-list-title {
            margin-top: 40px;
            margin-bottom: 20px;
        }
        .user-image {
            width: 40px;
            height: 40px;
            object-fit: cover;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="text-center">Bem-vindo(a)!</h1>

        @if(Auth::check())
            <div class="user-info">
                <p><strong>Nome:</strong> {{ Auth::user()->name }}</p>
                <p><strong>Email:</strong> {{ Auth::user()->email }}</p>
            </div>

            <div class="logout-btn">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn btn-danger">Sair</button>
                </form>
            </div>

            <div class="user-list-title">
                <h4>Usuários disponíveis para conexão</h4>
            </div>
            <div class="card-header">
                <input type="text" placeholder="Search user..." autocomplete="off" onkeyup="search_user('{{ Auth::id()}}', this.value);">
            </div>
            <div id="search_people_area">
                <p>Carregando usuários...</p>
            </div>
        @else
            <p>Você não está logado.</p>
            <a href="{{ url('/login') }}" class="btn btn-primary">Fazer Login</a>
            <a href="{{ url('/register') }}" class="btn btn-secondary">Cadastrar</a>
        @endif
    </div>

    <!-- Scripts -->
    <script>
        @if(Auth::check())
        var from_user_id = "{{ Auth::user()->id }}";
        var to_user_id = "";

        var conn = new WebSocket('ws://127.0.0.1:8090/?token={{ auth()->user()->token }}');

        conn.onopen = function(e){
            console.log("Conexão WebSocket estabelecida");
            load_unconnected_user(from_user_id);
        };

        conn.onmessage = function(e){
            var data = JSON.parse(e.data);

            if(data.response_load_unconnected_user || data.response_search_user){
                var html = '';

                if(data.data.length > 0){
                    html += '<ul class="list-group">';
                    for (var count = 0; count < data.data.length; count++) {
                        var user_image = '';

                        if (data.data[count].user_image != null) {
                            user_image = '<img src="' + data.data[count].user_image + '" class="rounded-circle user-image me-2" />';
                        } else {
                            user_image = '<img src="{{ asset('images/no-image.png') }}" class="rounded-circle user-image me-2" />';
                        }

                        html += '<li class="list-group-item">';
                        html += '<div class="row align-items-center">';
                        html += '<div class="col-9 d-flex align-items-center">' + user_image + data.data[count].name + '</div>';
                        html += '<div class="col-3 text-end">';
                        html += '<button type="button" name="send_request" class="btn btn-primary btn-sm">';
                        html += '<i class="fas fa-paper-plane"></i></button>';
                        html += '</div>';
                        html += '</div>';
                        html += '</li>';
                    }
                    html += '</ul>';
                } else {
                    html = '<div class="alert alert-warning">Nenhum usuário disponível encontrado.</div>';
                }

                document.getElementById('search_people_area').innerHTML = html;
            }
        };

        function load_unconnected_user(from_user_id){
            var data = {
                from_user_id: from_user_id,
                type: 'request_load_unconnected_user'
            };

            conn.send(JSON.stringify(data));
        }

        function search_user(from_user_id,search_query){
            if(search_query.length > 0){
                var data = {
                    from_user_id : from_user_id,
                    search_query : search_query,
                    type : 'request_search_user'
                };

                conn.send(JSON.stringify(data));

            }else{
                load_unconnected_user(from_user_id);
            }
        }

        @endif
    </script>

    <!-- Bootstrap JS (opcional) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
