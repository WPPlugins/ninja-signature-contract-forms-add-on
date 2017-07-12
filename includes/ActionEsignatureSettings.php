<?php if ( ! defined( 'ABSPATH' ) ) exit;



                               
              
return apply_filters( 'ninja_forms_action_esignature_settings', array(

    /*
    |--------------------------------------------------------------------------
    | Primary Settings
    |--------------------------------------------------------------------------
    */

    /*
     * Signer name
     */

    'signer_name' => array(
        'name' => 'signer_name',
        
        'type' => 'textbox',
        'group' => 'primary',
        'label' => __( 'Signer Name', 'ninja-forms' ),
        'placeholder' => __( 'Name or fields', 'ninja-forms' ),
        'value' => '',
         'help' => __( 'Please input a signer Name field this will be used as signer name', 'ninja-forms' ),
        'width' => 'full',
        'use_merge_tags' => TRUE,
    ),

    
    
    /*
     * Signer email
     */

    'signer_email' => array(
        'name' => 'signer_email',
        'type' => 'textbox',
        'group' => 'primary',
        'label' => __( 'Signer Email', 'ninja-forms' ),
        'placeholder' => __( 'Email or fields', 'ninja-forms' ),
        'help' => __( 'Please input a signer E-mail field this will be used as signer email address', 'ninja-forms' ),
        'value' => '',
        'width' => 'full',
        'use_merge_tags' => TRUE,
    ),

    /*
     * Signing Logic
     */

    'signing_logic' => array(
        'name' => 'signing_logic',
        'type' => 'select',
            'options' => array(
                array( 'label' => __( 'Redirect user to Contract/Agreement after Submission', 'ninja-forms' ), 'value' => 'redirect' ),
                array( 'label' => __( 'Send User an Email Requesting their Signature after Submission', 'ninja-forms' ), 'value' => 'email' )
            ),
        'group' => 'primary',
        'width' => 'full',
        'label' => __( 'Signing logic', 'ninja-forms' ),
        'value' => 'redirect'
        
    ),
    'underline_data' => array(
        'name' => 'underline_data',
        'type' => 'select',
            'options' => array(
                array( 'label' => __( 'Underline the data That was submitted from this ninja form', 'ninja-forms' ), 'value' => 'underline' ),
                array( 'label' => __( 'Do not underline the data that was submitted from the Ninja Form', 'ninja-forms' ), 'value' => 'not_under' )
            ),
        
        'group' => 'primary',
        'width' => 'full',
        'value' => 'underline',
    ),
    
    'select_sad' => array(
        'name' => 'select_sad',
        'type' => 'select',
            'options' =>  Esig_NF_Setting::get_sad_option()
            ,
        'group' => 'primary',
        'width' => 'full',
        'label' => __( 'Select Sad', 'ninja-forms' ),
    ),  
    'signing_reminder_email' => array(
        'name' => 'signing_reminder_email',
        'type' => 'toggle',  
        'value' =>1,
        'label' => __( 'Signing Reminder Email', 'ninja-forms' ),
        'width' => 'full',
        'group' => 'advanced',
        
    ),
     'reminder_email' => array(
        'name' => 'reminder_email',
        'type' => 'select',
        'group' => 'advanced',
        'width' => 'one-third',
        'label' => __( 'Send the reminder email to the signer in', 'ninja-forms' ),
        'options' =>  ESIG_NF_SETTING::generate_reminder_date()
       
    ),
    'first_reminder_send' => array(
        'name' => 'first_reminder_send',
        'type' => 'select',
     
        'group' => 'advanced',
       'width' => 'one-third',
        'label' => __( 'After the first Reminder send reminder every', 'ninja-forms' ),
        'options' =>  ESIG_NF_SETTING::generate_reminder_date(),
    ),
    'expire_reminder' => array(
        'name' => 'expire_reminder',
        'type' => 'select',
        'group' => 'advanced',
        'width' => 'one-third',
        'label' => __( 'Expire reminder in', 'ninja-forms' ),
        'options' =>  ESIG_NF_SETTING::generate_reminder_date()
    ),
   

   
));
