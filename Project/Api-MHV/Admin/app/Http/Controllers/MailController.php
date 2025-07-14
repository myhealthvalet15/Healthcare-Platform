<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use SendGrid\Mail\Mail;

class MailController extends Controller
{
    /**
     * Send an email using SendGrid.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendEmail(Request $request, $subject = null, $toEmail = null, $body = null, $emailType = null)
    {
        if (!$subject || !$toEmail || !$body || !$emailType) {
            return response()->json([
                'status' => 400,
                'message' => 'Missing required parameters.',
            ], 400);
        }
        $email = new Mail();
        $email->setFrom(env("SENDGRID_MAIL_FROM_EMAIL"), env("SENDGRID_MAIL_FROM_APP_NAME"));
        $email->setSubject($subject);
        $email->addTo($toEmail);
        $email->addContent("text/plain", $body);
        $email->addContent("text/html", "<strong>$body</strong>");
        $email->addHeader("X-Email-Type", $emailType);
        
        $sendgrid = new \SendGrid(env('SENDGRID_MAIL_API_KEY'));

        try {
            $response = $sendgrid->send($email);
            return response()->json([
                'status' => $response->statusCode(),
                'message' => 'Email sent successfully.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Failed to send email.',
                'error' => $e->getMessage(),
            ]);
        }
    }
}
