<?php

function sendEmail($sendEmailTo, $emailFrom, $requiredFields, $optionalFields, $emailTitle)
{
    if (!empty($_POST)) {

        $fieldsSendByUser               = [];
        $allRequiredFieldsArePresent    = true;

        foreach ($requiredFields as $requiredField) {

            if (!array_key_exists($requiredField, $_POST) || empty($_POST[$requiredField])) {

                echo  json_encode(array(
                    'success' => false,
                    'message' => 'The field ' . $requiredField . ' is required'
                ));

                return;

            } else {

                $fieldsSendByUser[$requiredField] = $_POST[$requiredField];
            }
        }

        foreach ($optionalFields as $optionalField) {

            if (array_key_exists($optionalField, $_POST) && !empty($_POST[$optionalField])) {
                $fieldsSendByUser[$optionalField] = $_POST[$optionalField];
            }
        }

        $email['to'] = $sendEmailTo;
        $email['from'] = $emailFrom;
        $email['subject'] = $emailTitle;
        $email['textBody'] = prepareMessage($email, $fieldsSendByUser);

        send($email);
    }
}

function prepareMessage($email, $fieldsSentByUser = null)
{

    $linebreak = "\r\n";
    $message = "";
    
    foreach ($email as $field => $value) {
        
        $message .= $field . ': ' . $value . $linebreak;
    }

    if (!empty($fieldsSentByUser)) {

        $message .= "------------------------------------------" . $linebreak;
        foreach ($fieldsSentByUser as $field => $value) {
            $message .= $field . ': ' . $value . $linebreak;
        }
    }

    return $message;
}

function send($email) {

    $to      = $email['to'];
    $from    = $email['from'];
    $subject = $email['subject'];
    $body    = $email['textBody'];
    $headers = 'From:' . $from . 'Content-type: text/plain; charset=utf-8';
    
    try {

        mail($to, $subject, $body, $headers);

        echo json_encode(array(
            'success' => true,
            'message' => 'The email has been successfully send'
        ));

    } catch (Exception $e) {

        echo json_encode(array(
            'success' => false,
            'message' => 'Something went wrong sending the email'
        ));
    }
}