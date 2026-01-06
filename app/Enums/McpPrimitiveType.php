<?php

namespace App\Enums;

enum McpPrimitiveType: string
{
    case Tool = 'tool';
    case Resource = 'resource';
    case Prompt = 'prompt';
}
