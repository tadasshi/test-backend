<?php 
require './lib/connection.php';

/**
 * Retrieve data from the API
 * 
 * $url API's Url
 * 
 * return array with fetched data from the API
 */
function github_request($url)
{
    $ch = curl_init();

    $access = 'username:db1ff77b2eb06b3aa967e28f1943193ac831f064';

    curl_setopt($ch, CURLOPT_URL, $url);
    //curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/xml'));
    curl_setopt($ch, CURLOPT_USERAGENT, 'Agent smith');
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_USERPWD, $access);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    $output = curl_exec($ch);
    curl_close($ch);
    $result = json_decode(trim($output), true);
    return $result;
}

/**
 * Store the data retrieved from the API into the database
 * 
 * $repos Data to be retrieved
 * 
 * 
 */
function store_on_db($repos){

    global $dbh;

    try {

        foreach($repos['items'] as $item){
            $item['owner']['full_name'] = isset($item['owner']['full_name']) ? $item['owner']['full_name'] : '';
            $item['owner']['email'] = isset($item['owner']['email']) ? $item['owner']['email'] : '';
    
            $sth = $dbh->prepare("INSERT INTO repositories (repository_id, username,fullname,email,repository_name,repository_description,repository_creation_date,repository_stars,repository_watchers,repository_forks) 
            VALUES (:id,:owner_login,:owner_fullname,:owner_email,:repository_name,:repository_description,:repository_creation_date,:repository_stars,:repository_watchers,:repository_forks)
            ON DUPLICATE KEY UPDATE username = :owner_login, fullname = :owner_fullname,email = :owner_email,repository_name = :repository_name,repository_description = :repository_description,repository_creation_date = :repository_creation_date,repository_stars = :repository_stars,repository_watchers = :repository_watchers,repository_forks = :repository_forks ;");
            
            $sth->bindParam(':id', $item['id']);
            $sth->bindParam(':owner_login', $item['owner']['login']);
            $sth->bindParam(':owner_fullname', $item['owner']['full_name']);
            $sth->bindParam(':owner_email', $item['owner']['email']);
            $sth->bindParam(':repository_name', $item['name']);
            $sth->bindParam(':repository_description', $item['description']);
            $sth->bindParam(':repository_creation_date', $item['created_at']);
            $sth->bindParam(':repository_stars', $item['stargazers_count']);
            $sth->bindParam(':repository_watchers', $item['watchers']);
            $sth->bindParam(':repository_forks',  $item['forks']);
           
            $sth->execute();
        }
    
        return true;
    
    } catch (PDOException $e) {
        print "Error!: " . $e->getMessage() . "<br/>";
        die();
        return false;
    }
    
}