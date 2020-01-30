<?php

use Slim\App;
use Slim\Http\Request;
use Slim\Http\Response;

return function (App $app) {
    $container = $app->getContainer();

    $app->get('/[{name}]', function (Request $request, Response $response, array $args) use ($container) {
        // Sample log message
        $container->get('logger')->info("Slim-Skeleton '/' route");

        // Render index view
        return $container->get('renderer')->render($response, 'index.phtml', $args);
    });
    
    // Method Post
    $app->post("/createuser/", function (Request $request, Response $response){

        if(!haveEmptyParameters(array('nama', 'alamat'), $request, $response)){
            $request_data = $request->getParsedBody();

            $nama = $request_data['nama'];
            $alamat = $request_data['alamat'];

            $sql = "INSERT INTO users (nama, alamat) VALUE ('$nama', '$alamat')";
            $stmt = $this->db->prepare($sql);

            if($stmt->execute()){
                $message = array(); 
                $message['error'] = false; 
                $message['message'] = 'User created successfully';

                $response->write(json_encode($message));

                return $response
                                ->withHeader('Content-type', 'application/json')
                                ->withStatus(201);}
            else{
                $message = array(); 
                $message['error'] = true; 
                $message['message'] = 'Some error occurred';

                $response->write(json_encode($message));

                return $response
                            ->withHeader('Content-type', 'application/json')
                            ->withStatus(422);
            }
        }

        return $response
                        ->withHeader('Content-type', 'application/json')
                        ->withStatus(422);
        
    });

    // Method Search
    $app->post("/searchuser/", function (Request $request, Response $response){
        $request_data = $request->getParsedBody();
        
        $nama = $request_data['nama'];

        $sql = "SELECT * FROM users where nama LIKE '%$nama%' ORDER BY nama ASC";
        $stmt = $this->db->prepare($sql);

        if($stmt->execute()){
            $result = $stmt->fetchAll();

            $response_data = array(); 
            $response_data['error'] = false; 
            $response_data['message'] = 'User Search Successfully';
            $response_data['users'] = $result;

            $response->write(json_encode($response_data));

            return $response
                            ->withHeader('Content-type', 'application/json')
                            ->withStatus(200);

        }else{
            $response_data = array(); 
            $response_data['error'] = true; 
            $response_data['message'] = 'Please try again later';

            $response->write(json_encode($response_data));

            return $response
                            ->withHeader('Content-type', 'application/json')
                            ->withStatus(200);
        }

        return $response
                        ->withHeader('Content-type', 'application/json')
                        ->withStatus(422);


    });

    // Method Update
    $app->post("/updateuser/{id}", function (Request $request, Response $response, array $args){

        $id = $args['id'];

        if(!haveEmptyParameters(array('nama', 'alamat'), $request, $response)){
            $request_data = $request->getParsedBody();

            $nama = $request_data['nama'];
            $alamat = $request_data['alamat'];

            $sql = "UPDATE users SET nama = '$nama', alamat = '$alamat' WHERE id = '$id'";
            $stmt = $this->db->prepare($sql);

            if($stmt->execute()){
                $response_data = array(); 
                $response_data['error'] = false; 
                $response_data['message'] = 'User Updated Successfully';

                $response->write(json_encode($response_data));

                return $response
                ->withHeader('Content-type', 'application/json')
                ->withStatus(200);
            }else{
                $response_data = array(); 
                $response_data['error'] = true; 
                $response_data['message'] = 'Please try again later';

                $response->write(json_encode($response_data));

                return $response
                ->withHeader('Content-type', 'application/json')
                ->withStatus(200);

            }
            
        }

        return $response
                        ->withHeader('Content-type', 'application/json')
                        ->withStatus(422);
        
    });

    // Method Read
    $app->get("/readuser/", function (Request $request, Response $response){

        $sql = "SELECT * FROM users";
        $stmt = $this->db->prepare($sql);

        if($stmt->execute()){
            $result = $stmt->fetchAll();

            $response_data = array(); 
            $response_data['error'] = false; 
            $response_data['message'] = 'User Read Successfully';
            $response_data['users'] = $result;

            $response->write(json_encode($response_data));

            return $response
                            ->withHeader('Content-type', 'application/json')
                            ->withStatus(200);

        }else{
            $response_data = array(); 
            $response_data['error'] = true; 
            $response_data['message'] = 'Please try again later';

            $response->write(json_encode($response_data));

            return $response
                            ->withHeader('Content-type', 'application/json')
                            ->withStatus(200);
        }

        return $response
                        ->withHeader('Content-type', 'application/json')
                        ->withStatus(422);

    });

    // Method Delete
    $app->delete("/deleteuser/{id}", function (Request $request, Response $response, array $args){
        
        $id = $args["id"];

        $sql = "DELETE FROM users WHERE id = '$id' ";
        $stmt = $this->db->prepare($sql);

        if($stmt->execute()){

            $response_data = array(); 
            $response_data['error'] = false; 
            $response_data['message'] = 'User Delete Successfully';

            $response->write(json_encode($response_data));

            return $response
                            ->withHeader('Content-type', 'application/json')
                            ->withStatus(200);
        }else{

            $response_data = array(); 
            $response_data['error'] = true; 
            $response_data['message'] = 'Please try again later';

            $response->write(json_encode($response_data));

            return $response
                            ->withHeader('Content-type', 'application/json')
                            ->withStatus(200);
        }

        return $response
                        ->withHeader('Content-type', 'application/json')
                        ->withStatus(422);

    });
    
    // Method Untuk Parameter Kosong
    function haveEmptyParameters($required_params, $request, $response){
        
        $error = false; 
        $error_params = '';
        $request_params = $request->getParsedBody(); 

        foreach($required_params as $param){
            if(!isset($request_params[$param]) || strlen($request_params[$param])<=0){
                $error = true; 
                $error_params .= $param . ', ';
            }
        }

        if($error){
            $error_detail = array();
            $error_detail['error'] = true; 
            $error_detail['message'] = 'Required parameters ' . substr($error_params, 0, -2) . ' are missing or empty';
            $response->write(json_encode($error_detail));
        }
        return $error; 
    }

};
