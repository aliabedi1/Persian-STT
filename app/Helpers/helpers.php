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
            "audio_url" => "http://farsi.voiceoversamples.com/FAR_F_Mitra.mp3", # todo add $file_url
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
