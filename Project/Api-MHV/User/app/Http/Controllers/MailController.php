<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use SendGrid\Mail\Mail;
use Illuminate\Support\Facades\Log;
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
public function sendEmailTemplate(Request $request, $subject = null, $toEmail = null, $body = null, $emailType = null)
{
    if (!$subject || !$toEmail || !$body || !$emailType) {
        return response()->json([
            'status' => 400,
            'message' => 'Missing required parameters.',
        ], 400);
    }

    $templateName = 'emails.' . $emailType;

    if (!view()->exists($templateName)) {
        return response()->json([
            'status' => 404,
            'message' => "Email template '$emailType' not found.",
        ], 404);
    }

    $additionalData = $request->input('data', []);

    // Render HTML content
    $htmlContent = view($templateName, array_merge([
        'subject' => $subject,
        'body' => $body,
        'header_title' => ucfirst(str_replace('_', ' ', $emailType))
    ], $additionalData))->render();

    // Show preview in browser if flag is set
    if ($request->has('preview_only') && $request->boolean('preview_only')) {
        return response($htmlContent);
    }

    // Send the email
    $email = new \SendGrid\Mail\Mail();
    $email->setFrom(env("SENDGRID_MAIL_FROM_EMAIL"), env("SENDGRID_MAIL_FROM_APP_NAME"));
    $email->setSubject($subject);
    $email->addTo($toEmail);
    $email->addContent("text/plain", strip_tags($body));
    $email->addContent("text/html", $htmlContent);
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
public function previewEmailTemplate(Request $request)
{
    $subject = $request->input('subject');
    $emailType = $request->input('emailType');
    $data = $request->input('data', []);

    
    if (is_string($data)) {
        $data = json_decode($data, true) ?? [];
    }

    $templateName = 'email.' . $emailType;

    if (!view()->exists($templateName)) {
        return response("Email template not found: $emailType", 404);
    }

  
    return view($templateName, array_merge([
        'subject' => $subject,
        'header_title' => $data['header_title'] ?? 'Notification',
    ], $data));
}


}