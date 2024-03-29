<?php if (!defined('APPLICATION')) exit();

defined( 'DS' ) or define( 'DS', DIRECTORY_SEPARATOR );
define( 'MAILBOXVALIDATOR_ROOT', dirname( __FILE__ ) . DS );

class MailboxValidatorPlugin extends Gdn_Plugin {
    public function SettingsController_MailboxValidator_Create($Sender, $Args) {
        $Sender->Permission('Garden.Settings.Manage');
        return $this->Dispatch($Sender);
    }
    
    public function Controller_Index($Sender) {
        $Sender->Permission('Garden.Settings.Manage');
        $Sender->Form = new Gdn_Form();
        $Validation = new Gdn_Validation();
        $ConfigurationModel = new Gdn_ConfigurationModel($Validation);
        $ConfigurationModel->SetField(array(
            'Plugins.MailboxValidator.APIKey',
            'Plugins.MailboxValidator.DebugModeOption',
            'Plugins.MailboxValidator.ValidEmailOption',
            'Plugins.MailboxValidator.DisposableEmailOption',
            'Plugins.MailboxValidator.FreeEmailOption',
            // 'Plugins.MailboxValidator.RoleEmailOption',
            // 'Plugins.MailboxValidator.CustomBlacklistDomains',
            // 'Plugins.MailboxValidator.InvalidErrorMessage',
            // 'Plugins.MailboxValidator.DisposableErrorMessage',
            // 'Plugins.MailboxValidator.FreeErrorMessage',
        ));

        $Sender->Form->SetModel($ConfigurationModel);
        $Sender->AddSideMenu('settings/MailboxValidator');
        

        // $Validation->ApplyRule('Plugins.IP2Location.ValidEmailOption', 'Required', T('You must select to switch on or off Valid Email Validator.'));
        // $Validation->ApplyRule('Plugins.IP2Location.DisposableEmailOption', 'Required', T('You must select to switch on or off Disposable Email Validator.'));
        // $Validation->ApplyRule('Plugins.IP2Location.FreeEmailOption', 'Required', T('You must select to switch on or off Free Email Validator.'));
        // $Validation->ApplyRule('Plugins.IP2Location.RoleEmailOption', 'Required', T('You must select to switch on or off Role Email Validator.'));

        if ($Sender->Form->AuthenticatedPostBack() === FALSE) {
            $Sender->Form->SetData($ConfigurationModel->Data);
        } else {
            $Data = $Sender->Form->FormValues();

            $Validation->ApplyRule('Plugins.MailboxValidator.APIKey', 'Required', T('Please enter API Key.'));
            // $Validation->ApplyRule('Plugins.IP2Location.ValidEmailOption', 'Required', T('You must select to switch on or off Valid Email Validator.'));
            // $Validation->ApplyRule('Plugins.IP2Location.DisposableEmailOption', 'Required', T('You must select to switch on or off Disposable Email Validator.'));
            // $Validation->ApplyRule('Plugins.IP2Location.FreeEmailOption', 'Required', T('You must select to switch on or off Free Email Validator.'));
            // $Validation->ApplyRule('Plugins.IP2Location.RoleEmailOption', 'Required', T('You must select to switch on or off Role Email Validator.'));

            if ($Sender->Form->Save() !== FALSE) {
                $Sender->StatusMessage = T('Your settings have been saved.');
            }
        }
        $Sender->Render('settings', '', 'plugins/MailboxValidator');
    }
    
    private function Http($url) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_TIMEOUT, 3);
        $Response = curl_exec($ch);
        curl_close($ch);

        return $Response;
    }
    
    private function MailboxValidator_Single($email, $api_key, $debugonoff) {
        try{
            $Response = $this->Http('http://api.mailboxvalidator.com/v2/validation/single?' . http_build_query(array(
                'key' => $api_key,
                'email' => $email,
                'format' => 'json',
                'source' => 'vanillaforums',
            )));
            if (is_null( $json = json_decode($Response, true)) === FALSE) {
				if ($debugonoff == 'on') {
					file_put_contents(MAILBOXVALIDATOR_ROOT . 'debug.log', date('Y-m-d G:i:s') . ' - ' . $Response . PHP_EOL, FILE_APPEND);
				}
                return $json;
            } else {
                // if connection error, let it pass
                return true;
            }
        } catch( Exception $e ) {
            return true;
        }
    }
    
    private function MailboxValidator_Is_Valid_Email($api_result) {
        if ( $api_result != '' ) {
            // if ( $api_result['error_message'] == '' ) {
			if ((! array_key_exists('error', $api_result))) {
                if ( $api_result['status'] ) {
                    return true;
                } else {
                    return false;
                }
            } else {
                // If error message occured, let it pass first.
                return true;
            }
        } else {
            // If error message occured, let it pass first.
            return true;
        }
    }
    
    private function MailboxValidator_Is_Role_Email($api_result) {
        if ( $api_result != '' ) {
            if ( (! array_key_exists('error', $api_result)) ) {
                if ( $api_result['is_role'] ) {
                    return true;
                } else {
                    return false;
                }
            } else {
                // If error message occured, let it pass first.
                return false;
            }
        } else {
            // If error message occured, let it pass first.
            return false;
        }
    }

    private function MailboxValidator_Is_Free( $email, $api_key, $debugonoff ) {
        try{
            $Response = $this->Http('http://api.mailboxvalidator.com/v2/email/free?' . http_build_query(array(
                'key' => $api_key,
                'email' => $email,
                'format' => 'json',
                'source' => 'vanillaforums',
            )));
            if (is_null( $json = json_decode($Response, true)) === FALSE) {
				if ($debugonoff == 'on') {
					file_put_contents(MAILBOXVALIDATOR_ROOT . 'debug.log', date('Y-m-d G:i:s') . ' - ' . $Response . PHP_EOL, FILE_APPEND);
				}
                if ( (! array_key_exists('error', $json)) ) {
                    if ( $json['is_free'] ) {
                        return true;
                    } else {
                        return false;
                    }
                } else {
                    // If error message occured, let it pass first.
                    return false;
                }
            } else {
                // if connection error, let it pass
                return false;
            }
        } catch( Exception $e ) {
            return false;
        }
    }


    private function MailboxValidator_Is_Disposable( $email, $api_key, $debugonoff ) {
        try{
            $Response = $this->Http('http://api.mailboxvalidator.com/v2/email/disposable?' . http_build_query(array(
                'key' => $api_key,
                'email' => $email,
                'format' => 'json',
                'source' => 'vanillaforums',
            )));
            if (is_null( $json = json_decode($Response, true)) === FALSE) {
				if ($debugonoff == 'on') {
					file_put_contents(MAILBOXVALIDATOR_ROOT . 'debug.log', date('Y-m-d G:i:s') . ' - ' . $Response . PHP_EOL, FILE_APPEND);
				}
                if ( (! array_key_exists('error', $json)) ) {
                    if ( $json['is_disposable'] ) {
                        return true;
                    } else {
                        return false;
                    }
                } else {
                    // If error message occured, let it pass first.
                    return false;
                }
            } else {
                // if connection error, let it pass
                return false;
            }
        } catch( Exception $e ) {
            return false;
        }

    }
    
    public function userModel_beforeRegister_handler($Sender,$args) {
        // Get mail provider from form.
        if (isset($args['RegisteringUser'])) {
            $email = $args['RegisteringUser']['Email'];
            // Things to do: Blacklist Feature
			// 20240131: The role email validator will stuck, skip.
            if ((C( 'Plugins.MailboxValidator.APIKey' ) != '') && (preg_match('/^[A-Z\d]+$/', C( 'Plugins.MailboxValidator.APIKey' )))) {
                $apikey = C( 'Plugins.MailboxValidator.APIKey' );
                $debugonoff = C( 'Plugins.MailboxValidator.DebugModeOption' );
                $validonoff = C( 'Plugins.MailboxValidator.ValidEmailOption' );
                $disposableonoff = C( 'Plugins.MailboxValidator.DisposableEmailOption' );
                $freeonoff = C( 'Plugins.MailboxValidator.FreeEmailOption' );
                // $roleonoff = C( 'Plugins.MailboxValidator.RoleEmailOption' );
                
                // $single_result = $validonoff == 'on' || $role_on_off == 'on' ? $this->MailboxValidator_Single( $email, $apikey ) : '';
                $single_result = $validonoff == 'on' ? $this->MailboxValidator_Single( $email, $apikey, $debugonoff ) : '';
                $is_valid_email = $validonoff == 'on' && $single_result != '' ? $this->MailboxValidator_Is_Valid_Email( $single_result ) : true;
                // $is_role = $roleonoff == 'on' && $single_result != '' ? $this->MailboxValidator_Is_Role_Email( $single_result ) : false;
                $is_disposable = ($validonoff == 'on' && is_array($single_result)) ? $single_result['is_disposable'] : ( $disposableonoff == 'on' ? $this->MailboxValidator_Is_Disposable( $email, $apikey, $debugonoff ) : false );
                $is_free = ($validonoff == 'on' && is_array($single_result)) ? $single_result['is_free'] : ( $freeonoff == 'on' ? $this->MailboxValidator_Is_Free( $email, $apikey, $debugonoff ) : false );
                
                $msg = '';
                if( $is_valid_email == false ){
                    // $msg = C( 'Plugins.MailboxValidator.InvalidErrorMessage' ) ?? 'Please enter a valid email address.';
                    // $msg = 'Please enter a valid email address.';
					if (($disposableonoff == 'on') && ($is_disposable == true)) {
						$msg = 'Please enter a non-disposable email address.';
					} else if (($freeonoff == 'on') && ($is_free == true)) {
						$msg = 'Please enter a non-free email address.';
					} else {
						$msg = 'Please enter a valid email address.';
					}
                } elseif( $is_disposable == true ){
                    // $msg = C( 'Plugins.MailboxValidator.DisposableErrorMessage' ) ?? 'Please enter a non-disposable email address.';
                    $msg = 'Please enter a non-disposable email address.';
                } elseif( $is_free == true ){
                    // $msg = C( 'Plugins.MailboxValidator.FreeErrorMessage' ) ?? 'Please enter a non-free email address.';
                    $msg = 'Please enter a non-free email address.';
                // } elseif( $is_role == true ){
                    // $msg = 'Please enter a non-role email address.';
                }
                if($msg != ''){
                    $Sender->Validation->AddValidationResult('Email',$msg);
                    $Sender->EventArguments['Valid'] = FALSE;
                }
            // } else {
                // $Sender->EventArguments['Valid'] = TRUE;
            }
        }
    }

}

?>