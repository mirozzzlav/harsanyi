<?php
namespace CustomRestApi;
require_once \WP_PLUGIN_DIR . '/constants.php';
require_once \ABSPATH . 'wp-admin/includes/image.php';
require_once \ABSPATH . 'wp-admin/includes/file.php';


class ApplicationCreator
{
    public $invalidParamName = '';
    public function create(Array $postData):bool
    {
        global $wpdb;
        $applicationId = wp_insert_post(
            [
                'post_type' => \APPLICATION_POSTTYPE,
                'post_title' => $postData['applicantFullname'] . ' - ' . date(get_option('date_format')),
                'post_content' => '',
                'post_status' => 'publish',
                'comment_status' => 'closed',   // if you prefer
                'ping_status' => 'closed',      // if you prefer
                'post_author' => \APPLICATIONS_EDITOR_USER_ID
            ]
        );
        
        if ($applicationId === 0) {
            return false;
        }

        $applicationsTable = $wpdb->prefix . \APPLICATIONS_TABLE; 
        $insertData = array_merge(['id' => $applicationId], $postData);
        $insertData['attachments'] = !empty($insertData['attachments']) ? $insertData['attachments'] : [];
        
        if (!$this->attachFilesToApplication($insertData['attachments'], $applicationId)) {
            return false;
        };
        
        unset($insertData['attachments']);
        if ($wpdb->insert($applicationsTable, $insertData) === false) {
            return false;
        }

        return true;

    }

    private function attachFilesToApplication(Array $attachmens, int $postId): bool {

        foreach ($attachmens['tmp_name'] as $indx => $tmpName) {
            $fileData = [
                'name' => $attachmens['name'][$indx],
                'type' => $attachmens['type'][$indx],
                'tmp_name' => $tmpName,
                'error' => $attachmens['error'][$indx],
                'size' => $attachmens['size'][$indx],
            ];

            $wpFileData = \wp_handle_upload($fileData, array('test_form' => FALSE));
            if (!empty($wpFileData['error'])) {
                return false;
            }
            $attachment = [
                'guid' => $wpFileData['url'], 
                'post_mime_type' => $fileData['type'], 
                'post_title' => preg_replace('/\\.[^.]+$/', '', basename($fileData['name'])), 
                'post_content' => '', 
                'post_status' => 'inherit'
            ];
     
            $attachmentId = wp_insert_attachment($attachment, false, $postId);
            if (is_wp_error($attachmentId)) {
                return false;
            }
            $attachMeta = wp_generate_attachment_metadata($attachmentId, $wpFileData['file']);
            wp_update_attachment_metadata($attachmentId,  $attachMeta);
        }
        return true;
    }

    public function validate(Array $dataToValidate): bool {
        $this->invlidParamName = '';
        if (empty($dataToValidate)) {
          return false;
        }
        
        $validators = [
          'applicantFullname' => [new RequiredValidator(), new MinLengthValidator(3)],
          'applicantEmail' =>  [new EmailValidator()],                
          'applicantPhoneNr' => [new RequiredValidator(), new PhoneNrValidator()],
          'applicantAddress' => [],
          'recipientFullname' => [new RequiredValidator(), new MinLengthValidator(3)],
          'recipientRelToApplicant' => [],
          'recipientPurpose' => [],
          'requestText' => [new RequiredValidator(), new MinLengthValidator(30)],
          'attachments' => [new AttachmentsValidator()]
        ];
        
        foreach ($dataToValidate as $paramName => $paramValue) {        
          if (!isset($validators[$paramName])) {
              return false;
          }
          
          $paramValidators = $validators[$paramName];
          
          foreach($paramValidators as $validator) {
              $ret = $validator->validate($paramValue);
  
              if ($ret === false) {
                  $this->invlidParamName = $paramName;
                  return false;
              }
          }
        }
        return true;
      }
}