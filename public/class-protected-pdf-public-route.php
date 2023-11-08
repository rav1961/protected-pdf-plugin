<?php

class Protected_Pdf_Public_Route
{
    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $plugin_name    The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $version    The current version of this plugin.
     */
    private $version;

    private $api_version = 'v2';

    private $protectedPdfPublic;

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param      string    $plugin_name       The name of the plugin.
     * @param      string    $version    The version of this plugin.
     */
    public function __construct($plugin_name, $version, $protectedPdfPublic)
    {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
        $this->protectedPdfPublic = $protectedPdfPublic;
    }

    public function sign_in()
    {
        register_rest_route(
            "{$this->api_version}/{$this->plugin_name}",
            "sign-in",
            [
                "methods" => "POST",
                "callback" => [$this, 'sign_in_handler'],
                "permission_callback" => "__return_true",
            ]
        );
    }

    public function sign_in_handler($data)
    {
        try {
            $params = $data->get_params();

            if (!wp_verify_nonce($params['_wpnonce'], 'wp_rest')) {
                return new WP_REST_Response(__('Sorry! There was an error submitting your form.', 'protected-pdf'), 422);
            }

            if (!$this->validateForm($params)) {
                return new WP_REST_Response(__('Sorry! The form contains invalid fields.', 'protected-pdf'), 422);
            }

            if (!$this->protectedPdfPublic->isMemberExists($params['pdf_member_name'], $params['pdf_member_email'])) {
                $hash = $this->protectedPdfPublic->saveMember($params);
            } else {
                $hash = $this->protectedPdfPublic->updateMember($params);
            }

            $this->protectedPdfPublic->set_cookie(
                PROTECTED_PDF_COOKIE_NAME,
                $hash
            );

            $response = [
                'msg' => 'Success! You received access to the file for ' . str_replace('+', '', PROTECTED_PDF_ACCESS_TIME),
                'status' => 200,
            ];
        } catch (\Exception $exception) {
            $response = [
                'msg' => __('Unexceptred error!', 'protected-pdf'),
                'status' => $exception->getCode(),
            ];
        }

        return new WP_REST_Response($response['msg'], $response['status']);
    }

    private function validateForm($params)
    {
        if ($params['pdf_member_name'] === '') {
            return false;
        }

        if (!filter_var($params['pdf_member_email'], FILTER_VALIDATE_EMAIL)) {
            return false;
        }

        return true;
    }
}
