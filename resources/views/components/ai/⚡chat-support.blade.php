<?php

use Livewire\Component;
use App\Ai\Agents\SupportAssistant;
use App\Models\AgentConversation;
use App\Models\AgentConversationMessage;

new class extends Component
{
    public $selectedConversationId;
    public $newMessage = ''; // Property to bind to the textarea

    public function getHistoryProperty() {
        return AgentConversation::where('user_id', auth()->id())->latest()->get();
    }

    public function getMessagesProperty() {
        if (!$this->selectedConversationId) {
            return collect();
        }

        return AgentConversationMessage::where('conversation_id', $this->selectedConversationId)
            ->orderBy('created_at', 'asc')
            ->get();
    }

    public function selectConversation($conversationId) {
        $this->selectedConversationId = $conversationId;
        $this->newMessage = ''; // Clear input when switching chats
    }

    public function sendMessage() {
        // 1. Validate that the message isn't empty
        if (empty(trim($this->newMessage))) {
            return;
        }

        // 2. Send the prompt to your SupportAssistant
        if(!empty($this->selectedConversationId)) {
            $response = (new SupportAssistant)
                ->continue($this->selectedConversationId, auth()->user())
                ->prompt($this->newMessage);
        } else {
            $response = (new SupportAssistant)
                ->forUser(auth()->user())
                ->prompt($this->newMessage);
        }

        // 4. Clear the input field for the next message
        $this->reset('newMessage');
    }
};
?>

<div>
    <div class="row">
        <div class="col-lg-2">
            <div class="card">
                <div class="card-header"><h3 class="card-title">Chats</h3></div>
                <div class="card-body" style="height: 300px; overflow-y: auto;">
                    {{-- Accessing computed property via $this->history --}}
                    @foreach($this->history as $conversation)
                        <div class="mb-3" wire:key="conv-{{ $conversation->id }}">
                            <a href="#"
                               wire:click.prevent="selectConversation('{{ $conversation->id }}')"
                               class="{{ $selectedConversationId == $conversation->id ? 'font-weight-bold text-primary' : '' }}">
                                {{ $conversation->title ?? 'Untitled Chat' }}
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="col-lg-10">
            <div class="card">
                <div class="card-header"><h3 class="card-title">Support Chat</h3></div>
                <div class="card-body">
                    <div style="height: 400px; overflow-y: auto;">
                        {{-- Accessing computed property via $this->messages --}}
                        @if($this->messages->isNotEmpty())
                            @foreach($this->messages as $message)
                                <div class="mb-3" wire:key="msg-{{ $message->id }}">
                                    <span class="badge {{ $message->role === 'user' ? 'bg-blue' : 'bg-green' }}">
                                        {{ ucfirst($message->role) }}
                                    </span>
                                    <div class="mt-1 p-2 bg-light border rounded">
                                        <pre>{{ $message->content }}</pre>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="text-center text-muted mt-5">Select a conversation to start.</div>
                        @endif
                    </div>
                </div>
                <div class="card-footer">
                    <div class="input-group">
                        <textarea
                            class="form-control"
                            placeholder="Type and press Enter..."
                            wire:model="newMessage"
                            wire:keydown.enter.prevent="sendMessage"
                        ></textarea>
                        <button
                            class="btn btn-primary"
                            wire:click="sendMessage"
                            wire:loading.attr="disabled"
                        >
                            <span wire:loading.remove>Send</span>
                            <span wire:loading>...</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
