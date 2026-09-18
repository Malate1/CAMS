<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Adds the current CodeIgniter CSRF token to legacy manually-written POST forms.
 *
 * CAMS predates consistent use of form_open(), so enabling CodeIgniter CSRF
 * protection directly would otherwise break many existing forms. This hook
 * lets the application use the framework's CSRF verification without rewriting
 * every view at once.
 */
class CsrfFormInjector
{
    public function inject()
    {
        $CI =& get_instance();
        $output = $CI->output->get_output();

        if ($output === '' || stripos($output, '<form') === false) {
            return;
        }

        $tokenName = $CI->security->get_csrf_token_name();
        $tokenHash = $CI->security->get_csrf_hash();

        if ($tokenName === '' || $tokenHash === '') {
            return;
        }

        $hiddenField = '<input type="hidden" name="'
            . htmlspecialchars($tokenName, ENT_QUOTES, 'UTF-8')
            . '" value="'
            . htmlspecialchars($tokenHash, ENT_QUOTES, 'UTF-8')
            . '">';

        $output = preg_replace_callback(
            '/<form\b[^>]*\bmethod\s*=\s*(["\'])post\1[^>]*>/i',
            static function ($matches) use ($hiddenField) {
                return $matches[0] . $hiddenField;
            },
            $output
        );

        $CI->output->set_output($output);
    }
}
