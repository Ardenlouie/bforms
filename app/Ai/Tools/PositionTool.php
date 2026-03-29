<?php

namespace App\Ai\Tools;

use App\Models\Position;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Tools\Request;
use Stringable;

class PositionTool implements Tool
{
    /**
     * Get the description of the tool's purpose.
     */
    public function description(): Stringable|string
    {
        return 'Use this tool to manage positions (find, list, update, or create).';
    }

    /**
     * Execute the tool.
     */
    public function handle(Request $request): Stringable|string
    {
        $action = $request['action'] ?? null;
        $name = $request['name'] ?? null;
        $id = $request['id'] ?? null;

        return match ($action) {
            'list'   => Position::all()->toJson(),
            'find'   => Position::where('position', 'like', "%{$name}%")->first()?->toJson() ?? 'Position not found.',
            'create' => $this->createPosition($name),
            'update' => $this->updatePosition($id, $name),
            default  => 'Invalid action provided.',
        };
    }

    protected function createPosition(string $name): string
    {
        // Check for uniqueness before creating
        if (Position::where('position', $name)->exists()) {
            return "Error: The position '{$name}' already exists. Use the list tool to see all current positions.";
        }

        $position = Position::create(['position' => $name]);
        return "Success: Position '{$position->position}' has been created with ID {$position->id}.";
    }

    protected function updatePosition($id, $name): string
    {
        $position = Position::find($id);
        if (!$position) return "Error: Position with ID {$id} not found.";

        $position->update(['position' => $name]);
        return "Success: Position updated to {$name}.";
    }

    /**
     * Get the tool's schema definition.
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'action' => $schema->string()
                ->description('The operation to perform: list, find, create, or update')
                ->required(),
            'name' => $schema->string()
                ->description('The name of the position (required for find, create, update)'),
            'id' => $schema->string()
                ->description('The UUID/ID of the position (required for update)'),
        ];
    }
}
