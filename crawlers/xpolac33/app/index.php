<!DOCTYPE html>
<html> 
    <head>
        <title>Page Title</title>
        <!-- Latest compiled and minified CSS -->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">

        <!-- jQuery library -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>

        <!-- Latest compiled JavaScript -->
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    </head>
    <body>

        <form action="index.php" method="post">
            <div class="form-group">
                <label for="url">Url of web page:</label>
                <input type="text" class="form-control" id="url">
            </div>
            <button type="submit" class="btn btn-default">Parse</button>
        </form>
        <?php
        include 'database.php';
        $db = new Database();
        $url = $_POST('ulr');
        $parser = new Parser();
        $parser->parse($url);
        echo "parsing";
        ?>
    </body>
</html>
