<?php

header("Content-Type: application/json; charset=utf-8");

$data = json_decode(file_get_contents(__DIR__ ."\\db.json"), true);

$default_id = count(json_decode(file_get_contents(__DIR__ ."\\ids.json"), true)) == 0 ? 'id' : json_decode(file_get_contents("ids.json"), true);

$routes = array_keys($data) ;

$resource = explode('/' , $_SERVER["PATH_INFO"])[1] ;
$id = str_replace('/' , '' , substr($_SERVER["PATH_INFO"], (strlen($resource) + 1))) ;

// echo __DIR__;
if($_SERVER['REQUEST_METHOD'] === 'GET'){

    if(in_array($resource, $routes)){

        if(strlen($id) > 0){
            for($i = 0 ;$i < count($data[$resource]) ; $i++):

                if($data[$resource][$i][$default_id[$resource]] == $id){
                    print_r(json_encode($data[$resource][$i]));
                }

            endfor;
        }else{
            print_r(json_encode($data[$resource]));
        }

    }elseif($resource === 'DB'){

        print_r(json_encode($data));

    }
    else{
        echo '404 not found'; 
    }
}elseif($_SERVER['REQUEST_METHOD'] === 'POST' && strlen($id) === 0){
    if(in_array($resource, $routes)){

        $post_data = [] ;
        $keys = array_keys($data[$resource][0]) ;
        $new_keys = [] ;

        // remove id key from keys
        for($i=0;$i<count($keys);$i++){
            str_contains($keys[$i] , 'id') ? "" : $new_keys[] =  $keys[$i] ;
        }
        // add new id
        $post_data[$default_id[$resource]] = (count($data[$resource]) + 1 ) ;

        for($i=0;$i<count($new_keys);$i++){
            $post_data[$new_keys[$i]] = $_POST[$new_keys[$i]] ;
        }

        $data[$resource][] = $post_data ;

        echo file_put_contents('db.json', json_encode($data));
        
    }else{
        echo 'error in post method';
    }
}elseif($_SERVER['REQUEST_METHOD'] === 'POST' && strlen($id) > 0){
    
    if(in_array($resource, $routes) ){
        $keys = array_keys($_POST) ;
        for($i=0;$i<count($keys);$i++){
            $data[$resource][$id -1][$keys[$i]] = $_POST[$keys[$i]] ;
        }
        echo file_put_contents('db.json', json_encode($data));
    }else{
        echo 'error in update method';
    }

}elseif($_SERVER['REQUEST_METHOD'] === "DELETE" && strlen($id) > 0){
    unset($data[$resource][$id -1]) ;
    // echo $_SERVER['REQUEST_METHOD'] ;
    echo file_put_contents('db.json', json_encode($data));
    echo "item deleted correctly";
}
else{
    echo "oops something is wrong";
}

