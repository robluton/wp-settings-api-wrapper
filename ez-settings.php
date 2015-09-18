<?php

class EZSettings {

    private $group_name;
    private $setting_name;
    private $page;
    private $section_id;

    function __construct($page) {
        $page = isset($page) ? $page : 'general'; //Place setting on general page if no page provided
        $this->page = $page;    
    }

    public function register($group_name, $setting_name) {

        $this->group_name = $group_name;
        $this->setting_name = $setting_name;

        register_setting($this->group_name, $this->setting_name );
    }

    public function add_section( $section_id, $admin_title, $render_cb ) {

        $this->section_id = $section_id;

        add_settings_section(
            $this->section_id,     // ID used to identify this section and with which to register options
            $admin_title,          // Title to be displayed on the administration page
            $render_cb,            // Callback used to render the description of the section
            $this->page            // Page on which to add this section of options
        );
    }

    public function get_option($field_id) {
        $options = (array) get_option( $this->setting_name );

        return isset($options[$field_id]) ? esc_attr( $options[$field_id] ) : '';
    }

    public function get_text_template($field_id) {

        $option = $this->get_option($field_id);

        $css_id = str_replace('_', '-', $field_id);

        return "<input type='text' name='$this->setting_name[$field_id]' id='$css_id' value='$option' />";
    }

    public function get_textarea_template($field_id, $rows, $cols) {

        $option = $this->get_option($field_id);

        // Render the output
        return "<textarea id='$field_id' name='$this->setting_name[$field_id]' rows='$rows' cols='$cols'>$option</textarea>";
    }

    public function get_checkbox_template($field_id) {

        $option = $this->get_option($field_id);
        $css_id = str_replace('_', '-', $field_id);

        $html = "<input type='checkbox' id='$css_id' name='$this->setting_name[$field_id]' value='1' " . checked( 1, $option, false ) . "/>";

        return $html;
    }


    public function get_select_template($field_id, $choices = []) {

        $option = $this->get_option($field_id);

        $html = "<select id='$field_id' name='$this->setting_name[$field_id]'>";
        $html .= "<option value='default'>Make a selection...</option>";

        foreach($choices as $key => $value) {

            $value = strtolower($value);

            $text_value = ucwords($value);
            $html .= "<option value='$key'" . selected( $option, $key, false) . ">$text_value</option>";
        }

        $html .= "</select>";

        return $html;
    }

    public function get_radio_template($field_id, $choices = []) {

        $html = '';

        $option = $this->get_option($field_id);

        foreach($choices as $key => $value) {

            $value = strtolower($value);
            $text_value = ucwords($value);

            $html .= "<input type='radio' id='$field_id' name='$this->setting_name[$field_id]' value='$key'" . checked( $key, $option, false ) . "/>";
            $html .= "<label for='$field_id'>$text_value</label><br>";
        }

        return $html;
    }

    public function add_field( $id, $label, $render_cb ) {

        add_settings_field( 
            $id,                  // ID used to identify the field throughout the theme
            $label,               // The label to the left of the option interface element
            $render_cb,           // The name of the function responsible for rendering the option interface
            $this->page,          // The page on which this option will be displayed
            $this->section_id     // The name of the section to which this field belongs
        );
    }
}
