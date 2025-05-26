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
                <input type="text" id="search_people" placeholder="Search user..." autocomplete="off" onkeyup="search_user('{{ Auth::id() }}', this.value);">
            </div>

            <div id="search_people_area">
                <p>Carregando usuários...</p>
            </div>

            <div class="card mt-4">
                <div class="card-header">
                    <b>Notification</b>
                </div>
                <div class="card-body">
                    <ul class="list-group" id="notification_area"></ul>
                </div>
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

        var conn = new WebSocket('ws://127.0.0.1:8090/?token={{ auth()->user()->token }}');

        conn.onopen = function(e){
            console.log("Conexão WebSocket estabelecida");
            load_unconnected_user(from_user_id);
            load_unread_notification(from_user_id);
        };

        conn.onmessage = function(e){
            var data = JSON.parse(e.data);

            if(data.response_load_unconnected_user || data.response_search_user){
                var html = '';

                if(data.data.length > 0){
                    html += '<ul class="list-group">';
                    for (var count = 0; count < data.data.length; count++) {
                        var user_image = data.data[count].user_image
                            ? `<img src="${data.data[count].user_image}" class="rounded-circle user-image me-2" />`
                            : `<img src="{{ asset('images/no-image.png') }}" class="rounded-circle user-image me-2" />`;

                        html += `
                            <li class="list-group-item">
                                <div class="row align-items-center">
                                    <div class="col-9 d-flex align-items-center">
                                        ${user_image}${data.data[count].name}
                                    </div>
                                    <div class="col-3 text-end">
                                        <button type="button" class="btn btn-primary btn-sm" onclick="send_request(this, ${from_user_id}, ${data.data[count].id})">
                                            <i class="fas fa-paper-plane"></i>
                                        </button>
                                    </div>
                                </div>
                            </li>`;
                    }
                    html += '</ul>';
                } else {
                    html = '<div class="alert alert-warning">Nenhum usuário disponível encontrado.</div>';
                }

                document.getElementById('search_people_area').innerHTML = html;
            }

            if(data.response_from_user_chat_request){
                search_user(from_user_id, document.getElementById('search_people').value);
                load_unread_notification(from_user_id);
            }

            if(data.response_to_user_chat_request){
                load_unread_notification(data.user_id);
            }
            if(data.response_process_chat_request){
                load_unread_notification(data.data);
            }

            if(data.response_load_notification){
                var html = '';
                for (var count = 0; count < data.data.length; count++) {
                    var user_image = data.data[count].user_image
                        ? `<img src="${data.data[count].user_image}" class="rounded-circle user-image me-2" />`
                        : `<img src="{{ asset('images/no-image.png') }}" class="rounded-circle user-image me-2" />`;

                    html += `
                        <li class="list-group-item">
                            <div class="row">
                                <div class="col-8">${user_image}${data.data[count].name}</div>
                                <div class="col-4 text-end">`;

                    if(data.data[count].notification_type === 'Send Request'){
                        html += data.data[count].status === 'Pending'
                            ? '<button class="btn btn-warning btn-sm">Request Send</button>'
                            : '<button class="btn btn-danger btn-sm">Request Rejected</button>';
                    } else {
                        if(data.data[count].status === 'Pending'){
                            html += '<button class="btn btn-warning btn-sm me-1" onclick="process_chat_request('+data.data[count].id+','+data.data[count].from_user_id+','+data.data[count].to_user_id+',`Reject`)">NO</button>';
                            html += '<button class="btn btn-success btn-sm" onclick="process_chat_request('+data.data[count].id+','+data.data[count].from_user_id+','+data.data[count].to_user_id+',`Approve`)">YES</button>';
                        } else {
                            html += '<button class="btn btn-danger btn-sm">Request Rejected</button>';
                        }
                    }

                    html += `</div></div></li>`;
                }

                document.getElementById('notification_area').innerHTML = html;
            }
        };

        function load_unconnected_user(from_user_id){
            conn.send(JSON.stringify({
                from_user_id: from_user_id,
                type: 'request_load_unconnected_user'
            }));
        }

        function search_user(from_user_id, search_query){
            if(search_query.length > 0){
                conn.send(JSON.stringify({
                    from_user_id: from_user_id,
                    search_query: search_query,
                    type: 'request_search_user'
                }));
            } else {
                load_unconnected_user(from_user_id);
            }
        }

        function send_request(element, from_user_id, to_user_id){
            element.disabled = true;
            conn.send(JSON.stringify({
                from_user_id: from_user_id,
                to_user_id: to_user_id,
                type: 'request_chat_user'
            }));
        }

        function load_unread_notification(user_id){
            var data = {
                user_id : user_id,
                type : 'request_load_unread_notification'
            };
            conn.send(JSON.stringify( data ));
        }

        function process_chat_request(chat_request_id, from_user_id,to_user_id,action){
            var data = {
                chat_request_id : chat_request_id,
                from_user_id : from_user_id,
                to_user_id : to_user_id,
                action : to_user_id,
                type: 'request_process_chat_request'
            };

            conn.send( JSON.stringify(data) );
        }

        @endif
    </script>

    <!-- Bootstrap JS (opcional) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
