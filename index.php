<?php 

require './lib/functions.php';

if (!empty($_GET['language'])) {


    $starting_date = date('Y-m-d', time() - 86400 * 60);
    $language = $_GET['language'];
    $page = isset($_GET['page']) ? $_GET['page'] : 1;

    $app = new stdClass();

    $app->repositories = github_request("https://api.github.com/search/repositories?q=language:$language+pushed:>=$starting_date&page=$page&per_page=20");

    if(isset($app->repositories)){
        //print_r($repos);
        store_on_db($app->repositories);
        $pages = $app->repositories['total_count'] / 20;
    
    }
}
?>
<!DOCTYPE html>
<html>
    <head>
        <title>BackEnd Test</title>
        <meta charset="UTF-8">
        <link rel="icon" type="image/png" href="./img/favicon.ico">
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
        <link rel="stylesheet" type="text/css" href="css/main.css">
    </head>
    
    <body>
        <section class="container">
            <div>
            <h3>What language do you wanna search for</h3>
            <form action="?">
                <div class="form-group">
                    <input placeholder='Language' class='form-control' name='language' value="<?= isset($language) ? $language : '' ?>" maxlength="10" require type="text"></lavel>
                    <small class="form-text text-muted">Type what language do you wanna search on the repository.</small>
                </div>
            </form>

            
            <br>

            <?php if (isset($app->repositories)) {?>
    
            
            <a type="button" class="btn btn-secondary float-right" aria-label="Close" href='./'>
                Clean list
            </a>

            <h4>Repositories for: <?=$language?></h4>
            <small class='float-left'>Were found a total of: <?=$app->repositories['total_count']?> repositories</small>
            <br>
            <table class="table">
                <thead>
                    <tr>
                        <th>repositories</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($app->repositories['items'] as $item){ ?>
                        <?php if(strlen($item['owner']['login']) < 6){ continue; }?>
                        <tr><td><a href="<?= $item['html_url']?>" target='_blank'><?= $item['full_name']?></a></td></tr>
                    <?php }?>
                </tbody>
            </table>
                  <h4>Pages</h4>          
                <?php 
                    for($i = 1 ; $i < $pages; $i++){

                        if($page == $i){
                            echo "<a href='#' class='text-secondary'>{$i}</a> -";
                            continue;
                        }

                        echo "<a href='?language=$language&page=$i'>{$i}</a> -";
                    }
                ?>
            <?php }else{ ?>
                <p></p>
            <?php } ?>
            </div>
        </section>
    </body>
</html>
