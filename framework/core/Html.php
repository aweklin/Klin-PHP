<?php

namespace Framework\Core;

use Framework\Infrastructure\Session;

/**
 * A helper class to ease the creation of HTML elements on the page. Contains methods to draw various html elements.
 * 
 * @author Akeem Aweda | akeem@aweklin.com | +2347085287169
 */
final class Html {

    /**
     * Returns an input type specified with a label control showing the caption of the input element.
     * 
     * @param string $type Specifies the element type. Example: text, password.
     * @param string $labelText Specifies the caption for the element.
     * @param string $id Indicates the id attribute of the element.
     * @param string $name Indicates the name attribute of the element. The default value is the value passed for parameter id.
     * @param string $value Indicates the value attribute of the element. The default value is empty string.
     * @param array $inputAttributes Indicates other attributes of the element. The default value is .form-control bootstrap css class.
     * @param array $containerDivAttributes Indicates the container div attributes of the element. The default value is form-group bootstrap css class.
     * @param bool $ignoreContainerDiv Indicates wether the element should be wrapped within a div. The default value is true.
     * 
     * @return string
     */
    public static function input(string $type, string $labelText, string $id, string $name = '', string $value = '', array $inputAttributes = ['class' => 'form-control'], array $containerDivAttributes = ['class' => 'form-group'], bool $ignoreContainerDiv = false) : string {
        $containerDivAttributesResult = self::_composeAttributes($containerDivAttributes);
        $inputAttributesResult = self::_composeAttributes($inputAttributes);

        if (!$name) $name = $id;

        $input = '<input type="' . $type . '" id="' . $id . '" name="' . $name . '" value="' . $value . '"' . $inputAttributesResult . '>';

        if (!$ignoreContainerDiv) {
            $html = 
            '<div' . $containerDivAttributesResult . '>
                <label for="' . $name . '">' . $labelText . '</label>' . PHP_EOL .
                $input . PHP_EOL . '
            </div>';
        } else {
            $html = $input;
        }

        return $html;
    }

    /**
     * Returns radio input type specified with a label control showing the caption of the input element.
     * 
     * @param string $name Indicates the name attribute of the element.
     * @param string $id Indicates the id attribute of the element.
     * @param string $value Indicates the value attribute of the element. The default value is empty string.
     * @param array $attributes Indicates other attributes of the element. The default value is empty array.
     * 
     * @return string
     */
    public static function radio(string $name, string $id, string $value = '', array $attributes = []) {
        return self::input('radio', '', $id, $name, $value, $attributes, [], true);
    }

    /**
     * Returns check input type specified with a label control showing the caption of the input element.
     * 
     * @param string $name Indicates the name attribute of the element.
     * @param string $id Indicates the id attribute of the element.
     * @param string $value Indicates the value attribute of the element. The default value is empty string.
     * @param array $attributes Indicates other attributes of the element. The default value is empty array.
     * 
     * @return string
     */
    public static function check(string $name, string $id, string $value = '', array $attributes = []) {
        return self::input('check', '', $id, $name, $value, $attributes, [], true);
    }

    /**
     * Returns an dropdown list with a label control showing the caption of the input element.
     * 
     * @param string $labelText Specifies the caption for the element.
     * @param string $id Indicates the id attribute of the element.
     * @param string $name Indicates the name attribute of the element. The default value is the value passed for parameter id.
     * @param array $data Indicates the data to be populated in the dropdown list. It can take a simple array or associative array or associative array of objects.
     * @param array $attributes Indicates other attributes of the element. The default value is .form-control bootstrap css class.
     * @param string $textField Indicates the text field displayed for each option in the list. You can ignore this if you are dealing with simple arrays.
     * @param string $valueField Indicates the value field for each option in the list. You can ignore this if you are dealing with simple arrays.
     * @param string $selected Indicates the default selected value.
     * @param array $containerDivAttributes Indicates the container div attributes of the element. The default value is form-group bootstrap css class.
     * @param bool $ignoreContainerDiv Indicates wether the element should be wrapped within a div. The default value is true.
     * 
     * @return string
     */
    public static function select(string $labelText, string $id, string $name, array $data, string $textField = '', string $valueField = '', string $selected = '', array $attributes = ['class' => 'form-control'], array $containerDivAttributes = ['class' => 'form-group'], bool $ignoreContainerDiv = false) : string {
        $containerDivAttributesResult = self::_composeAttributes($containerDivAttributes);
        $selectAttributesResult = self::_composeAttributes($attributes);

        if (!$name) $name = $id;

        $select = '<select id="' . $id . '" name="' . $name . '"' . $selectAttributesResult . '>';
        if ($data) {
            foreach($data as $item) {
                $text = (is_object($item) ? $item->{$textField} : (is_array($item) && array_key_exists($textField) ? $item[$textField] : $item));
                $value = (is_object($item) ? $item->{$valueField} : (is_array($item) && array_key_exists($valueField) ? $item[$valueField] : $item));
                $selectedAttribute = ($selected == $value ? ' selected="selected"' : '');
                $select .= '<option value="' . $value . '"'. $selectedAttribute . '>' . $text . '</option>' . PHP_EOL;
            }
        }
        $select .=    '</select>';

        if (!$ignoreContainerDiv) {
            $html = '<div' . $containerDivAttributesResult . '>
                        <label for="' . $name . '">' . $labelText . '</label>' . PHP_EOL .
                        $select . PHP_EOL .
                    '</div>';
        } else {
            $html = $select;
        }

        return $html;
    }

    /**
     * Returns a button type specified with a text showing the caption of the input button.
     * 
     * @param string $type Specifies the button type. Example: button, submit.
     * @param string $text Specifies the caption for the button.
     * @param string $id Indicates the id attribute of the button.
     * @param array $attributes Indicates other attributes of the button. The default value is .btn .btn-secondary bootstrap css classes.
     * 
     * @return string
     */
    public static function button(string $type = 'button', string $text = 'Button', string $id = '', array $attributes = ['class' => 'btn btn-secondary']) : string {
        $buttonAttributes = self::_composeAttributes($attributes);
        
        $html = '<button id="' . $id . '" type="' . $type . '"' . $buttonAttributes . '>' . $text . '</button>';

        return $html;
    }

    /**
     * Returns a submit button with a text showing the caption of the input button.
     * 
     * @param string $text Specifies the caption for the button.
     * @param string $id Indicates the id attribute of the button.
     * @param array $attributes Indicates other attributes of the button. The default value is .btn .btn-primary bootstrap css classes.
     * 
     * @return string
     */
    public static function submit(string $text = 'Submit', string $id = '', array $attributes = ['class' => 'btn btn-primary']) : string {
        return self::button('submit', $text, $id, $attributes);
    }

    /**
     * Returns a textarea.
     * 
     * @param string $id Indicates the id attribute of the element.
     * @param string $value Indicates the value attribute of the element. The default value is empty string.
     * @param array $attributes Indicates other attributes of the element. The default value is .form-control bootstrap css class.
     * @param array $containerDivAttributes Indicates the container div attributes of the element. The default value is form-group bootstrap css class.
     * @param bool $ignoreContainerDiv Indicates wether the element should be wrapped within a div. The default value is true.
     */
    public static function textarea(string $labelText, string $id = '', string $name = '', string $text = '', array $attributes = ['class' => 'form-control'], array $containerDivAttributes = ['class' => 'form-group'], bool $ignoreContainerDiv = false) : string {
        $containerDivAttributesResult = self::_composeAttributes($containerDivAttributes);
        $textAreaAttributes = self::_composeAttributes($attributes);
        
        $name = ($name ? $name : $id);
        $textArea = '<textarea id="' . $id . '" name="' . $name . '" ' . $textAreaAttributes . '>' . $text . '</textarea>';
        if (!$ignoreContainerDiv) {
            $html = 
            '<div' . $containerDivAttributesResult . '>' . PHP_EOL .
                '<label for="' . $name . '">' . $labelText . '</label>' . PHP_EOL .
                $textArea . PHP_EOL .
            '</div>';
        } else {
            $html = $textArea;
        }

        return $html;
    }

    /**
     * Returns an radio input type specified with a label control showing the caption of the input element.
     * 
     * @param string $id Indicates the id attribute of the element.
     * @param string $name Indicates the name attribute of the element.
     * @param string $value Indicates the value attribute of the element. The default value is empty string.
     * 
     * @return string
     */
    public static function hidden(string $id, string $name = '', string $value = '') : string {
        return '<input type="hidden" id="' . $id . '" name="' . $name . '" value="' . $value . '">';
    }

    /**
     * Returns a hidden field with a Cross Site Request Forgery (CSRF) token.
     * 
     * @return string
     */
    public static function formToken() : string {
        return self::hidden(SECURITY_FORM_TOKEN, SECURITY_FORM_TOKEN, self::generateFormToken());
    }

    /**
     * Generates a new Cross Site Request Forgery (CSRF) token.
     */
    public static function generateFormToken() : string {
        $formToken = base64_encode(openssl_random_pseudo_bytes(64));
        Session::set(SECURITY_FORM_TOKEN, $formToken);
        return $formToken;
    }

    /**
     * Creates and returns a new HTML form element with the specified method and action, also with a Cross Site Request Forgery (CSRF) token hidden element.
     */
    public static function beginForm(string $id = '', string $method = 'post', string $action = '', array $attributes = ['class' => 'form-horizontal']) : string {
        $formAttributes = self::_composeAttributes($attributes);
        
        $formToken = self::generateFormToken();
        $html = '<form id="' . $id . '" method="' . $method . '" action="' . $action . '"' . $formAttributes . '>' . PHP_EOL . 
            self::hidden(SECURITY_FORM_TOKEN, SECURITY_FORM_TOKEN, $formToken) . PHP_EOL;

        return $html;
    }

    /** Ends an HTML form element */
    public static function endForm() {
        return '</form>';
    }

    /**
     * Stringify the html attributes specified and return a string result.
     * 
     * @param array $attributes Specifies the attribute(s) to be stringified.
     * 
     * @return string
     */
    private static function _composeAttributes(array $attributes) : string {
        $attributesResult = '';

        if ($attributes) {
            foreach($attributes as $key => $value) {
                $attributesResult .= ' ' . $key . '="' . $value . '"';
            }
        }

        return $attributesResult;
    }

    /**
     * Displays a partial view/page stored in the app\src\views\shared folder directory.
     */
    public static function partial(string $viewName) {
        include_once PATH_APP_VIEWS_SHARED . DS . $viewName . '.php';
    }

}