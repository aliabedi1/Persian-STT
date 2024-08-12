<?php

if (!function_exists('getTextFromSpeech')) {
    /**
     * @throws \Exception
     */
    function getTextFromSpeech(string $file_url)
    {
        $config = config('assembly-ai');

        $headers = array(
            "authorization: " . $config['TOKEN'],
            "content-type: application/json"
        );

        $data = [
            "audio_url" => $file_url,
            'language_code' => $config['LANGUAGE_CODE'],
            'speech_model' => $config['SPEECH_MODEL']
        ];

        $curl = curl_init($config['API_URL']);

        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        $response = json_decode(curl_exec($curl), true);

        curl_close($curl);


        $polling_endpoint = $config['API_URL'] . "/" . $response['id'];

//        trying to get response from api
        while (true) {
            $polling_response = curl_init($polling_endpoint);

            curl_setopt($polling_response, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($polling_response, CURLOPT_RETURNTRANSFER, true);

            $transcriptionResult = json_decode(curl_exec($polling_response), true);

            if ($transcriptionResult['status'] === "completed") {
                return $transcriptionResult['text'];
            } elseif ($transcriptionResult['status'] === "error") {
                throw new Exception("Transcription operation failed: " . $transcriptionResult['error']);
            } else {
                sleep(3);
            }
        }
    }
}


if (!function_exists('sendSms')) {
    /**
     * @param string $mobileNumber
     * @param int $code
     * @return void
     */
    function sendSms(string $mobileNumber, int $code): void
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.sms.ir/v1/send/verify',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => '{
        "mobile": "' . $mobileNumber . '",
        "templateId": 100000,
        "parameters": [
          {
            "name": "CODE",
            "value": "' . $code . '"
          }
        
        ]
      }',
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'Accept: text/plain',
                'x-api-key: LcoQbDwBr2jyW76zLqF7nYxZsPk1sHxMEhYDTFobU1l55JhG8osXXLnJsfLDFxFC'
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
    }
}
