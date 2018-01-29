<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class WatsonController extends Controller
{
    private $username = "51bf9e84-4cca-4f8b-892c-aa536187e4e8";
    private $password = "5Z8BQwI4vqCm";
    // workspaces-id
    // 98a6a830-56a8-4f18-8583-9deb2e3e3cfd


    public function getIndex()
    {
        return view("index");
    }

    public function postConversation(Request $req)
    {
        // validate input
        $this->validate($req, [
            'user_input' => "required"
        ]);

        $user_input = $req['user_input'];
//        $user_input = "I want to buy a computer";

        $url = "https://gateway.watsonplatform.net/conversation/api/v1/workspaces/98a6a830-56a8-4f18-8583-9deb2e3e3cfd/message?version=2017-05-26";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json',
                                                           'Accept: application/json'));
        // Disable SSL verification
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);

//        "{\"input\": {\"text\": \"I want to buy a computer\"}}"
        $field = array("input" => array("text" => $user_input));
        $field = json_encode($field);

//        var_dump($field);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $field);
        curl_setopt($ch, CURLOPT_USERPWD, "$this->username:$this->password");
        // receive server response ...
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $server_output = curl_exec($ch);
        curl_close ($ch);

        // parse json
        $res = $this->watsonResponseParser($server_output);
//        $res = "I want to |student";
//        var_dump($res);
        $tmp = "";
        $tmp2 = "";
        if (strpos($res, '|') !== false)
        {
            // parse
            $text = explode("|", $res);
//            $command = 'python D:\xampp\htdocs\chatBotShopAss\readfile.py '. '2>&1';
//            $tmp = shell_exec($command);

//            $tmp2 = $tmp. $text[1]. ",";

//            $command = 'python D:\xampp\htdocs\chatBotShopAss\writefile.py .$text[1]';
//            shell_exec($command);

            $command = 'python D:\xampp\htdocs\chatBotShopAss\findProduct.py '. $text[1]. ' 2>&1';
            $output = shell_exec($command);

            // contains recommend?
            if (strpos($text[0], 'recommend') !== false)
            {
                $res = $text[0] . " ". $output. ". Anything else?";
            }
            if (strpos($text[0], 'No') !== false)
            {
                $tmp = "";
//                $command = 'python D:\xampp\htdocs\chatBotShopAss\writefile.py '. $tmp2. ' 2>&1';
//                shell_exec($command);
            }
        }
        // get intents than query database

        return Response::json(['message' => $res], 200);
    }

    private function watsonResponseParser($json_string)
    {
        $obj = json_decode($json_string);
        return $obj->{'output'}->{'text'}[0];
    }
}
