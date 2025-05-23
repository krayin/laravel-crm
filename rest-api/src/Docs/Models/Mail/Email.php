<?php

namespace Webkul\RestApi\Docs\Models\Mail;

/**
 * @OA\Schema(
 *     title="Email",
 *     description="Email model",
 * )
 */
class Email
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
     *     title="Subject",
     *     description="The subject of the item",
     *     type="string",
     *     example="Meeting Notes"
     * )
     *
     * @var string
     */
    protected $subject;

    /**
     * @OA\Property(
     *     title="Source",
     *     description="The source of the item",
     *     type="string",
     *     example="Email"
     * )
     *
     * @var string
     */
    protected $source;

    /**
     * @OA\Property(
     *     title="Name",
     *     description="Name associated with the item",
     *     type="string",
     *     example="John Doe"
     * )
     *
     * @var string
     */
    protected $name;

    /**
     * @OA\Property(
     *     title="User Type",
     *     description="Type of the user",
     *     type="string",
     *     example="admin"
     * )
     *
     * @var string
     */
    protected $user_type;

    /**
     * @OA\Property(
     *     title="Is Read",
     *     description="Read status of the item",
     *     type="boolean",
     *     example=true
     * )
     *
     * @var bool
     */
    protected $is_read;

    /**
     * @OA\Property(
     *     title="Folders",
     *     description="Folders associated with the item",
     *     type="array",
     *
     *     @OA\Items(type="string"),
     *     example={"Inbox", "Archive"}
     * )
     *
     * @var string[]
     */
    protected $folders;

    /**
     * @OA\Property(
     *     title="From",
     *     description="Sender's email address",
     *     type="string",
     *     example="sender@example.com"
     * )
     *
     * @var string
     */
    protected $from;

    /**
     * @OA\Property(
     *     title="Sender",
     *     description="Sender's name",
     *     type="string",
     *     example="Jane Smith"
     * )
     *
     * @var string
     */
    protected $sender;

    /**
     * @OA\Property(
     *     title="Reply To",
     *     description="Reply-to email address",
     *     type="string",
     *     example="reply@example.com"
     * )
     *
     * @var string
     */
    protected $reply_to;

    /**
     * @OA\Property(
     *     title="CC",
     *     description="CC email addresses",
     *     type="array",
     *
     *     @OA\Items(type="string"),
     *     example={"cc1@example.com", "cc2@example.com"}
     * )
     *
     * @var string[]
     */
    protected $cc;

    /**
     * @OA\Property(
     *     title="BCC",
     *     description="BCC email addresses",
     *     type="array",
     *
     *     @OA\Items(type="string"),
     *     example={"bcc1@example.com", "bcc2@example.com"}
     * )
     *
     * @var string[]
     */
    protected $bcc;

    /**
     * @OA\Property(
     *     title="Unique ID",
     *     description="Unique identifier for the item",
     *     type="string",
     *     example="123456"
     * )
     *
     * @var string
     */
    protected $unique_id;

    /**
     * @OA\Property(
     *     title="Message ID",
     *     description="Message identifier",
     *     type="string",
     *     example="msg-78910"
     * )
     *
     * @var string
     */
    protected $message_id;

    /**
     * @OA\Property(
     *     title="Reference IDs",
     *     description="Reference identifiers",
     *     type="array",
     *
     *     @OA\Items(type="string"),
     *     example={"ref-111", "ref-222"}
     * )
     *
     * @var string[]
     */
    protected $reference_ids;

    /**
     * @OA\Property(
     *     title="Reply",
     *     description="Reply content",
     *     type="string",
     *     example="Thank you for your email."
     * )
     *
     * @var string
     */
    protected $reply;

    /**
     * @OA\Property(
     *     title="Person ID",
     *     description="ID of the person",
     *     type="integer",
     *     format="int64",
     *     example=1
     * )
     *
     * @var int
     */
    protected $person_id;

    /**
     * @OA\Property(
     *     title="Parent ID",
     *     description="ID of the parent item",
     *     type="integer",
     *     format="int64",
     *     example=2
     * )
     *
     * @var int
     */
    protected $parent_id;

    /**
     * @OA\Property(
     *     title="Lead ID",
     *     description="ID of the lead",
     *     type="integer",
     *     format="int64",
     *     example=3
     * )
     *
     * @var int
     */
    protected $lead_id;

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
