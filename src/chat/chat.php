<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
*/

namespace chat;

use bravedave\dvc\logger;

class chat {

  const helpful_assistant = 0;
  const coding_assistant = 4;

  const chat_helpful_assistant = 'You are a helpful assistant.';
  const chat_coding_assistant = 'You are an expert web development assistant' .
    ' specializing in PHP, JavaScript, and MariaDB.' .
    ' Provide best practices, real-world examples, and efficient code snippets.';

  /**
   *
   *
   * @string
   */
  public array $messages;
  public string $model;
  public int $max_tokens;
  public ?string $response_format = null;
  public bool $debug = false;

  public function __construct(array $messages = []) {
    $this->messages = $messages;
    $this->model = config::chat_model;
    $this->max_tokens = config::chat_max_tokens;
  }

  public function __invoke(): array {

    $debug = true;
    $debug = false;

    // OpenAI API request
    $ch = curl_init("https://api.openai.com/v1/chat/completions");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
      sprintf("Authorization: Bearer %s", config::$OPENAI_API_KEY),
      "Content-Type: application/json"
    ]);

    $data = [
      "model" => "gpt-4",  // or "gpt-3.5-turbo" for cheaper responses
      "messages" => $this->messages
    ];

    if ($debug) logger::dump($data);

    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    $response = curl_exec($ch);
    curl_close($ch);

    if (!$response) return ["error" => "No response from OpenAI."];

    // Decode API response
    $responseData = json_decode($response, true);
    $botResponse = $responseData["choices"][0]["message"]["content"] ?? "Sorry, I couldn't process that.";

    // // Store in MariaDB
    // $stmt = $pdo->prepare("INSERT INTO messages (user_message, bot_response) VALUES (:user, :bot)");
    // $stmt->execute(["user" => $userMessage, "bot" => $botResponse]);

    // Return AI response
    return ['response' => $botResponse];
  }
}
