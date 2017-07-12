<?php

if (!class_exists('ESIG_NF_SETTING')):

    class ESIG_NF_SETTING {

        const ESIG_NF_COOKIE = 'esig-nf-redirect';

        public static function is_ninja_three() {

            if (defined('Ninja_Forms::VERSION') && version_compare(Ninja_Forms::VERSION, '3.0') >= 0) {
                return true;
            } else {
                return false;
            }
        }

        public static function get_field_type($field_id) {
            $fields = Ninja_Forms()->form()->get_field($field_id);
            return $fields->get_setting('type');
        }

        public static function get_html($post_id, $form_id, $field_id) {
            $value = self::genarate_all_value($post_id, $form_id, $field_id);
            if ($value) {
                return $value;
            }
            $fields = Ninja_Forms()->form()->get_field($field_id);
            return $fields->get_setting('default');
        }

        public static function get_country($post_id, $form_id, $field_id) {
            $value = self::genarate_all_value($post_id, $form_id, $field_id);
            $countrylist = Ninja_Forms()->config('CountryList');
            $country = array_search($value, $countrylist);
            return $country;
        }

        public static function get_state($post_id, $form_id, $field_id) {
            $value = self::genarate_all_value($post_id, $form_id, $field_id);
            $statelist = Ninja_Forms()->config('StateList');
            $state = array_search($value, $statelist);
            return $state;
        }

        public static function get_value($document_id, $field_id) {

            $post_id = WP_E_Sig()->meta->get($document_id, 'esig_ninja_entry_id');

            if (self::is_ninja_three()) {

                $type = self::get_field_type($field_id);
                
                $form_id = WP_E_Sig()->meta->get($document_id, 'esig_ninja_form_id');
                switch ($type) {
                    case 'html':
                        return self::get_html($post_id, $form_id, $field_id);
                        break;
                    case 'listcountry':
                        return self::get_country($post_id, $form_id, $field_id);
                        break;
                    case 'liststate':
                        return self::get_state($post_id, $form_id, $field_id);
                        break;
                    default :
                        return self::genarate_all_value($post_id, $form_id, $field_id);
                }
            } else {
                $nf_value = Ninja_Forms()->sub($post_id)->get_field(absint($field_id));
                return nf_wp_kses_post_deep($nf_value);
            }
        }

        public static function genarate_all_value($post_id, $form_id, $field_id) {
            $submission = new NF_Database_Models_Submission($post_id, $form_id);
            $nf_value = $submission->get_field_value($field_id);
            return $nf_value;
        }

        public static function display_value($notification_id, $form_id, $value) {
            if (self::is_ninja_three()) {
                $underline_data = Ninja_Forms()->form($form_id)->get_action($notification_id)->get_settings('underline_data');
            } else {
                $underline_data = Ninja_Forms()->notification($notification_id)->get_setting('underline_data');
            }
            $result = '';
            if ($underline_data == "underline") {
                if (is_array($value)) {
                    foreach ($value as $val) {
                        $result .= '<u>' . $val . '</u><br/>';
                    }
                } else {
                    $result = '<u>' . $value . '</u>';
                }
            } else {
                if (is_array($value)) {
                    foreach ($value as $val) {
                        $result .= $val . '<br/>';
                    }
                } else {
                    $result = $value;
                }
            }
            return $result;
        }

        public static function save_invite_url($invite_hash, $document_checksum) {
            $invite_url = WP_E_Invite::get_invite_url($invite_hash, $document_checksum);
            esig_setcookie(self::ESIG_NF_COOKIE, $invite_url, 600);
            $_COOKIE[self::ESIG_NF_COOKIE] = $invite_url;
        }

        public static function remove_invite_url() {
            setcookie(self::ESIG_NF_COOKIE, null, time() - YEAR_IN_SECONDS, COOKIEPATH, COOKIE_DOMAIN);
        }

        public static function get_invite_url() {
            return ESIG_COOKIE(self::ESIG_NF_COOKIE);
        }

        public static function page_title($page_id) {
            global $wpdb;
            return $wpdb->get_var($wpdb->prepare("SELECT post_title FROM $wpdb->posts WHERE ID = %d LIMIT 1", $page_id));
        }

        public static function get_sad_option() {
            global $wpdb;
            $options = array();
            $table_name = $wpdb->prefix . 'esign_documents_stand_alone_docs';


            $sad_pages = $wpdb->get_results("SELECT page_id, document_id FROM {$table_name}", OBJECT);
            $options[] = array('label' => __('Select an agreement page', 'esig-nfds'), 'value' => '');

            foreach ($sad_pages as $page) {

                if (self::page_title($page->page_id)) {
                    $options[] = array('label' => self::page_title($page->page_id), 'value' => $page->page_id);
                }
            }


            return $options;
        }

        public static function get_sub_id($data) {
            if (array_key_exists('actions', $data)) {
                return $data['actions']['save']['sub_id'];
            }
            return false;
        }

        /**
         * Generate ninja form list option version wise . 
         * @return string
         */
        public static function ninja_form_option() {
            $options = '';

            if (self::is_ninja_three()) {

                $forms = Ninja_Forms()->form()->get_forms();
                foreach ($forms as $form) {
                    $options .= '<option value="' . $form->get_id() . '">' . $form->get_setting('title') . '</option>';
                }
            } else {

                $nf_forms = new NF_Forms;
                $ninja_forms = $nf_forms->get_all();


                foreach ($ninja_forms as $form_id) {
                    $title = Ninja_Forms()->form($form_id)->get_setting('form_title');

                    $options .= '<option value="' . $form_id . '">' . $title . '</option>';
                }
            }
            return $options;
        }

        /**
         * Generate fields option using form id
         * @param type $form_id
         * @return string
         */
        public static function ninja_form_fields($form_id) {
            $html = '';
            if (self::is_ninja_three()) {
                $fields = Ninja_Forms()->form($form_id)->get_fields();

                foreach ($fields as $field) {
                    if ($field->get_setting('type') == 'submit')
                        continue;
                    $html .= '<option value="' . $field->get_id() . '">' . $field->get_setting('label') . '</option>';
                }
            } else {
                $all = Ninja_Forms()->form($form_id);
                foreach ($all->fields as $fields) {
                    if ($fields['data']['label'] == 'Submit') {

                        continue;
                    }
                    //$field_name = $fields['data']['label'];
                    $html .= '<option value=" ' . $fields['id'] . ' ">' . $fields['data']['label'] . '</option>';
                }
            }
            return $html;
        }

        public static function generate_reminder_date() {

            $options = array();
            for ($i = 1; $i < 32; $i++) {
                $options[] = array('label' => $i . " Days", "value" => $i);
            }

            return $options;
        }

    }

    

    

endif;