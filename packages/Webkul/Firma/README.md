# Firma E-Signature for Krayin CRM

Send a lead's document for electronic signature with [Firma](https://firma.dev), follow its
status on the lead timeline, and receive the signed PDF as a file on the lead.

## What it does

- **Settings > Other Settings > Firma E-Signature**: store the Firma API key and the webhook
  signing secret (both encrypted with the application key and never shown again), test the
  connection, and register the webhook.
- **Lead page > Signatures tab**: send a Firma template or one of the lead's quotes (rendered
  to PDF like the quote print action) to one recipient, prefilled from the lead's contact
  person. Templates must have exactly one signer or approver role; others are listed but
  cannot be chosen.
- **Timeline**: an entry when the request is sent, signed, declined, completed, cancelled or
  expired; the signed PDF is attached to the lead as a file activity.

## Setup

1. Run `php artisan migrate` (tables `firma_signature_requests` and `firma_webhook_events`).
2. In the Firma dashboard create an API key, then paste it in Settings > Other Settings >
   Firma E-Signature and click **Test Connection**.
3. In the Firma dashboard open **Settings > Webhook Configuration**, copy the **Webhook Signing
   Secret** into the settings and save. Deliveries are refused until the secret is stored, so
   **Register Webhook** stays unavailable until then.
4. Make sure `APP_URL` is the public `https://` address of the CRM (Firma delivers webhooks over
   HTTPS only), then click **Register Webhook**. The endpoint is `POST /firma/webhook`.
5. On the same Firma page turn on **Enable Webhooks**. It is off by default, and Firma delivers
   nothing until it is on.
6. Grant the **Leads > Send for Signature** permission to the roles that may send, and
   **Settings > Other Settings > Firma E-Signature** to the roles that manage the connection.
   Sending a quote also needs **Quotes > Mail**, the permission core requires to e-mail a quote.

The signed PDF is downloaded by the `FetchSignedDocument` job. With `QUEUE_CONNECTION=sync` it
runs after the webhook has answered; with a real queue, run a worker.

## Send safety

A send is never retried automatically. Each send dialog carries a key that is stored before the
request to Firma, so a repeated submission does not send twice. When Firma does not confirm the
outcome (a gateway error, a timeout, a network failure), the request is shown as "Outcome
unknown" and the user is asked to check the Firma dashboard before sending again. A send still
shown as sending after 10 minutes (the worker stopped after the request to Firma) is shown the
same way. When Firma reports that the request was already sent or kept, that request is tracked
instead.

A request with an unknown outcome has no Firma id in the CRM, so it is not followed in Krayin:
webhook events for it are acknowledged and not stored, its status and signed PDF do not reach
the lead, and it is followed in the Firma dashboard.

## Webhook verification

Each delivery is verified with HMAC-SHA256 over `<timestamp>.<raw body>` (header
`X-Firma-Signature: t=<unix seconds>,v1=<hex>`, and `X-Firma-Signature-Old` during a secret
rotation), refused when older than 5 minutes, de-duplicated by the payload `id`, and answered
before any slow work runs. Without a stored secret every delivery is refused.

Firma documentation: https://docs.firma.dev. Support: support@firma.dev.
