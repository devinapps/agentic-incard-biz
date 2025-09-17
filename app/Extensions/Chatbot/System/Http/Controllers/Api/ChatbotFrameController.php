<?php

namespace App\Extensions\Chatbot\System\Http\Controllers\Api;

use App\Extensions\Chatbot\System\Models\Chatbot;
use App\Extensions\Chatbot\System\Models\ChatbotConversation;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Http;

class ChatbotFrameController extends Controller
{
    public function frame(Request $request, Chatbot $chatbot): View
    {
        $session = $this->getVisitor();

        $conversations = ChatbotConversation::query()
            ->where('chatbot_id', $chatbot->getAttribute('id'))
            ->where('session_id', $session)
            ->get();

        return view('chatbot::frame', compact('chatbot', 'session', 'conversations'));
    }

    protected function getVisitor(): string
    {
        $cookie = Cookie::has('CHATBOT_VISITOR');

        if ($cookie) {
            return Cookie::get('CHATBOT_VISITOR');
        }

        $sessionId = md5(uniqid(mt_rand(), true));

        Cookie::queue('CHATBOT_VISITOR', $sessionId, 60 * 24 * 365);

        return $sessionId;
    }

    public function publishImage(Request $request)
    {

        $request->validate([
            'drive_link' => 'required|url'
        ]);

        // Extract the file ID from the Google Drive link
        preg_match('/\/d\/([a-zA-Z0-9_-]+)/', $request->drive_link, $matches);
        if (!isset($matches[1])) {
            return response()->json(['error' => 'Invalid Google Drive link'], 400);
        }
        $fileId = $matches[1];

        // Build the direct download URL
        $directUrl = "https://drive.usercontent.google.com/download?id={$fileId}&export=download&authuser=0";

        // Fetch the image content
        $imageResponse = Http::get($directUrl);

        if (!$imageResponse->ok()) {
            return response()->json(['error' => 'Unable to fetch image'], 400);
        }

        // Get the content type from the response headers
        $contentType = $imageResponse->header('content-type', 'image/jpeg');

        // Return the image content with the correct headers
        return response($imageResponse->body(), 200)
            ->header('Content-Type', $contentType)
            ->header('Cache-Control', 'public, max-age=86400');
    }
}
