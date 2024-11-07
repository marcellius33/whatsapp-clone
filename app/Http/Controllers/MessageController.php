<?php

namespace App\Http\Controllers;

use App\Http\Requests\ReactMessageRequest;
use App\Http\Requests\StoreMessageRequest;
use App\Models\Message;
use App\Services\MessageService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

/**
 * @group [User] Message
 */
class MessageController extends Controller
{
    public function __construct(
        private MessageService $messageService,
    ) {

    }

    /**
     * Send
     *
     * @bodyParam chat_room_id string required Example: xxxxxxxx-xxxx-xxxx-xxxxxxxxxxxx
     * @bodyParam content string required Example: Hai
     * @bodyParam attachment file
     * @throws Exception
     */
    public function store(StoreMessageRequest $request): JsonResponse
    {
        $input = $request->validated();

        DB::beginTransaction();
        try {
            $this->messageService->storeMessage($input, auth()->user());
            DB::commit();
        } catch (Exception $exception) {
            DB::rollBack();
            throw $exception;
        }

        return response()->json([
            'message' => __('success.store_message_success'),
        ]);
    }
}
