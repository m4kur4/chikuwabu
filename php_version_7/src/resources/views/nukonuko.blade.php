<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ねこブレード！</title>
    <style>
        body {
            background-color: #f8fafc;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            font-family: 'Courier New', Courier, monospace;
        }
        .container {
            text-align: center;
            background: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        pre {
            font-size: 1.2rem;
            line-height: 1.2;
            color: #333;
            margin-bottom: 1rem;
        }
        .message {
            color: #666;
            font-weight: bold;
        }
    </style>
</head>
<body>

    <div class="container">
        <pre>
      |\      _,,,---,,_
ZZZzz /,`.-'`'    -.  ;-;;,_
     |,4-  ) )-,_. ,\ (  `'-'
    '---''(_/--'  `-'\_)
        </pre>

        <div class="message">
            Laravel 6 が正常に動いていますにゃん
        </div>
        <br>
        <a href="{{ url('/') }}" style="color: #3490dc; text-decoration: none;">← 戻る</a>
    </div>

</body>
</html>