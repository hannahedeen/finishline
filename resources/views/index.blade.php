<!DOCTYPE html>
<html>
<head>
    <title>IBM Watson Shopping Assistant</title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, shrink-to-fit=no, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.6.3/css/font-awesome.min.css">
    <link href="{{ URL::asset('css/style.css') }}" rel="stylesheet">
</head>
<body bgcolor=white>

<div class ="page-title">
    <h1 style="text-align: center">IBM Watson Shopping Assistant</h1>
</div>

<div class = "conversation" id="scrollable">
    <div class="speech-bubble-bot" style='width:400px !important'>
        <div class="arrow bottom right bot" ></div>
        <p> <font size="6">Welcome, to the conversation bot</font></p>
    </div>
</div>



<footer class="footer" >
    <div class="container">
        <p class="text-muted">Enter query</p>
        <div class="form-group">
            <input placeholder="text" name = "user_input" id = "in"class="form-control" type="text">
        </div>
        <button id = "query" type="submit" class="btn btn-success">Submit</button>
    </div>
</footer>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
<script type="text/javascript" src="{{ URL::asset('js/jss.js') }}"></script>


<script>
    {{-- laravel CSRF --}}
    var token = "{{ Session::token() }}";
    // find base url, later to attach parameters on it
    var root = "{{ URL::asset('/') }}";

    function addToChat(input,user) {
        // alert(input);
        var chatHistory = document.getElementById("scrollable").innerHTML;
        var preText_bot = "<div class='speech-bubble-bot' style='width:400px !important'><div class='arrow bottom right bot'></div><p><font size='6'>";
        var postText_bot = "</font></p></div>";
        var preText_user = "<div class='speech-bubble-user' style='width:400px !important'><div class='arrow bottom right me' ></div><p><font size='6'>";
        var postText_user = "</font></p></div>";
        if (user) {
            document.getElementById("scrollable").innerHTML = chatHistory + preText_user + input + postText_user;
        } else {
            document.getElementById("scrollable").innerHTML = chatHistory + preText_bot + input + postText_bot;
        }
    }

    $("#query").click(function(e) {
        var input = $("#in").val();
        addToChat(input,true);
        // clean user input
        document.getElementById("in").value = "";
        e.preventDefault();
        ajax("POST", 'watson/conservation', "user_input=" + encodeURIComponent(input));

    });

    function ajax(method, url, params) {
        var http;
        if (window.XMLHttpRequest) {
            // code for IE7+, Firefox, Chrome, Opera, Safari
            http = new XMLHttpRequest();
        } else {
            // code for IE6, IE5
            http = new ActiveXObject("Microsoft.XMLHTTP");
        }

        http.onreadystatechange = function() {
            if (http.readyState == XMLHttpRequest.DONE ) {
                if(http.status == 200){
                    // success
                    var obj = JSON.parse(http.responseText);
                    addToChat(obj['message'],false);

                }
                else if(http.status == 400) {
                    alert("Category could not be saved. Please try again!");
                }
                else {
                    // not unique category name
                    var obj = JSON.parse(http.responseText);
                    if (obj.message) {
                        alert(obj.message);
                    } else {
                        alert("Please check the name");
                    }
                }
            }
        };
        // baseUrl from index blade
        http.open(method, root + url, true);
        http.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        http.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
        // send request with parameters
        http.send(params + "&_token=" + token);
    }
</script>
</body>
</html>
