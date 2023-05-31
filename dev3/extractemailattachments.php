<!-- This file reads the email inbox and saves the attachments into the folder ready for the data to be loaded

<?php
	date_default_timezone_set('Europe/London');

	require_once 'maillogin.php';

    $imap = imap_open($server, $user, $pass);
	imap_errors();
	imap_alerts();

    $message_count = imap_num_msg($imap);

    for ($i = 1; $i <= $message_count; ++$i) {
        $header = imap_header($imap, $i);
        $body = imap_fetchbody($imap, $i, "1.1");

        if ($body == "") {
            $body = imap_fetchbody($imap, $i, "1");
        }

        $body = trim(substr(quoted_printable_decode($body), 0, 100));
		
        $prettydate = date("jS F Y", $header->udate);

        if (isset($header->from[0]->personal)) {
            $personal = $header->from[0]->personal;
        } else {
            $personal = $header->from[0]->mailbox;
        }

        $email = "$personal <{$header->from[0]->mailbox}@{$header->from[0]->host}>";
		
	    $structure = imap_fetchstructure($imap, $i);

		$attachments = array();
		if (isset($structure->parts) && count($structure->parts)) {
			for ($j = 0; $j < count($structure->parts); $j++) {

				$attachments[$j] = array(
					'is_attachment' => false,
					'filename' => '',
					'name' => '',
					'attachment' => ''
				);

				if ($structure->parts[$j]->ifdparameters) {
					
					foreach($structure->parts[$j]->dparameters as $object) {
						if(strtolower($object->attribute) == 'filename') {
							$attachments[$j]['is_attachment'] = true;
							$attachments[$j]['filename'] = $object->value;
						}
					}
				}

				if ($structure->parts[$j]->ifparameters) {
					foreach($structure->parts[$j]->parameters as $object) {
						if(strtolower($object->attribute) == 'name') {
							$attachments[$j]['is_attachment'] = true;
							$attachments[$j]['name'] = $object->value;
						}
					}
				}

				if ($attachments[$j]['is_attachment']) {
					$attachments[$j]['attachment'] = imap_fetchbody($imap, $i, $j+1);
					if($structure->parts[$j]->encoding == 3) { // 3 = BASE64
						$attachments[$j]['attachment'] = base64_decode($attachments[$j]['attachment']);
					}
					elseif($structure->parts[$j]->encoding == 4) { // 4 = QUOTED-PRINTABLE
						$attachments[$j]['attachment'] = quoted_printable_decode($attachments[$j]['attachment']);
					}
				}
			}
		}

		foreach ($attachments as $key => $attachment) {
			$name = $attachment['name'];
			$contents = $attachment['attachment'];
			
			if($name != ""){
				echo $name. "\r\n"; //debug
				file_put_contents($name, $contents);
			}
		}
		
		imap_delete($imap, $i);
    }

    imap_expunge($imap);	
    imap_close($imap);
?>