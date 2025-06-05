<?php

namespace Webkul\RestApi\Docs\Models\Settings;

/**
 * @OA\Schema(
 *     title="WebForm",
 *     description="WebForm model",
 * )
 */
class WebForm
{
    /**
     * @OA\Property(
     *     title="ID",
     *     description="ID",
     *     format="int64",
     *     example=1
     * )
     *
     * @var int
     */
    private $id;

    /**
     * @OA\Property(
     *     title="Form ID",
     *     description="The ID of the form",
     *     format="int64",
     *     example=1
     * )
     *
     * @var int
     */
    private $form_id;

    /**
     * @OA\Property(
     *     title="Title",
     *     description="The title of the form",
     *     example="Contact Us"
     * )
     *
     * @var string
     */
    private $title;

    /**
     * @OA\Property(
     *     title="Description",
     *     description="A description of the form",
     *     example="This form allows users to contact us directly."
     * )
     *
     * @var string
     */
    private $description;

    /**
     * @OA\Property(
     *     title="Submit Button Label",
     *     description="The label of the submit button",
     *     example="Submit"
     * )
     *
     * @var string
     */
    private $submit_button_label;

    /**
     * @OA\Property(
     *     title="Submit Success Action",
     *     description="The action to take upon successful submission",
     *     example="redirect"
     * )
     *
     * @var string
     */
    private $submit_success_action;

    /**
     * @OA\Property(
     *     title="Submit Success Content",
     *     description="The content to display upon successful submission",
     *     example="Thank you for contacting us!"
     * )
     *
     * @var string
     */
    private $submit_success_content;

    /**
     * @OA\Property(
     *     title="Create Lead",
     *     description="Indicates whether a lead should be created",
     *     example=true
     * )
     *
     * @var bool
     */
    private $create_lead;

    /**
     * @OA\Property(
     *     title="Background Color",
     *     description="The background color of the form",
     *     example="#FFFFFF"
     * )
     *
     * @var string
     */
    private $background_color;

    /**
     * @OA\Property(
     *     title="Form Background Color",
     *     description="The background color of the form area",
     *     example="#F0F0F0"
     * )
     *
     * @var string
     */
    private $form_background_color;

    /**
     * @OA\Property(
     *     title="Form Title Color",
     *     description="The color of the form title",
     *     example="#333333"
     * )
     *
     * @var string
     */
    private $form_title_color;

    /**
     * @OA\Property(
     *     title="Form Submit Button Color",
     *     description="The color of the submit button",
     *     example="#FF5733"
     * )
     *
     * @var string
     */
    private $form_submit_button_color;

    /**
     * @OA\Property(
     *     title="Attribute Label Color",
     *     description="The color of the attribute labels",
     *     example="#888888"
     * )
     *
     * @var string
     */
    private $attribute_label_color;

    /**
     * @OA\Property(
     *     title="Created at",
     *     description="Created at",
     *     example="2020-01-27 17:50:45",
     *     format="datetime",
     *     type="string"
     * )
     *
     * @var \DateTime
     */
    private $created_at;

    /**
     * @OA\Property(
     *     title="Updated at",
     *     description="Updated at",
     *     example="2020-01-27 17:50:45",
     *     format="datetime",
     *     type="string"
     * )
     *
     * @var \DateTime
     */
    private $updated_at;
}
