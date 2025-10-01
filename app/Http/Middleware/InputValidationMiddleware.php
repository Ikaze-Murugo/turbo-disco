<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class InputValidationMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check for suspicious patterns BEFORE sanitization
        if ($this->detectSuspiciousPatterns($request)) {
            $this->logSuspiciousActivity($request);
            return $this->handleSuspiciousActivity($request);
        }

        // Sanitize all input data
        $this->sanitizeInput($request);

        // Validate file uploads
        if ($request->hasFile('*')) {
            $this->validateFileUploads($request);
        }

        return $next($request);
    }

    /**
     * Sanitize all input data to prevent XSS.
     */
    private function sanitizeInput(Request $request): void
    {
        $inputs = $request->all();
        $sanitized = [];

        foreach ($inputs as $key => $value) {
            if (is_string($value)) {
                // Skip CSRF token and other system fields
                if (in_array($key, ['_token', '_method', '_previous'])) {
                    $sanitized[$key] = $value;
                    continue;
                }

                // Sanitize string values
                $sanitized[$key] = $this->sanitizeString($value);
            } elseif (is_array($value)) {
                // Recursively sanitize arrays
                $sanitized[$key] = $this->sanitizeArray($value);
            } else {
                $sanitized[$key] = $value;
            }
        }

        // Replace request data with sanitized data
        $request->replace($sanitized);
    }

    /**
     * Sanitize a string value.
     */
    private function sanitizeString(string $value): string
    {
        // Remove null bytes
        $value = str_replace("\0", '', $value);

        // Remove control characters except newlines and tabs
        $value = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', '', $value);

        // HTML encode special characters
        $value = htmlspecialchars($value, ENT_QUOTES | ENT_HTML5, 'UTF-8', false);

        // Remove potentially dangerous HTML tags
        $value = strip_tags($value, '<p><br><strong><em><u><ol><ul><li><h1><h2><h3><h4><h5><h6>');

        return $value;
    }

    /**
     * Recursively sanitize array values.
     */
    private function sanitizeArray(array $array): array
    {
        $sanitized = [];
        
        foreach ($array as $key => $value) {
            if (is_string($value)) {
                $sanitized[$key] = $this->sanitizeString($value);
            } elseif (is_array($value)) {
                $sanitized[$key] = $this->sanitizeArray($value);
            } else {
                $sanitized[$key] = $value;
            }
        }

        return $sanitized;
    }

    /**
     * Validate file uploads.
     */
    private function validateFileUploads(Request $request): void
    {
        $allowedMimeTypes = [
            'image/jpeg',
            'image/png',
            'image/gif',
            'image/webp',
            'application/pdf',
            'text/plain'
        ];

        $maxFileSize = 10 * 1024 * 1024; // 10MB

        foreach ($request->allFiles() as $fieldName => $files) {
            if (!is_array($files)) {
                $files = [$files];
            }

            foreach ($files as $file) {
                if ($file->isValid()) {
                    // Check file size
                    if ($file->getSize() > $maxFileSize) {
                        throw new \Exception("File {$fieldName} is too large. Maximum size is 10MB.");
                    }

                    // Check MIME type
                    $mimeType = $file->getMimeType();
                    if (!in_array($mimeType, $allowedMimeTypes)) {
                        throw new \Exception("File type {$mimeType} is not allowed for {$fieldName}.");
                    }

                    // Check file extension
                    $extension = strtolower($file->getClientOriginalExtension());
                    $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'pdf', 'txt'];
                    if (!in_array($extension, $allowedExtensions)) {
                        throw new \Exception("File extension {$extension} is not allowed for {$fieldName}.");
                    }

                    // Check for suspicious file content
                    $this->validateFileContent($file);
                }
            }
        }
    }

    /**
     * Validate file content for security.
     */
    private function validateFileContent($file): void
    {
        $path = $file->getPathname();
        
        // Check for executable content in image files
        if (str_starts_with($file->getMimeType(), 'image/')) {
            $content = file_get_contents($path, false, null, 0, 1024);
            
            // Check for PHP tags or executable content
            if (str_contains($content, '<?php') || 
                str_contains($content, '<script') || 
                str_contains($content, 'javascript:')) {
                throw new \Exception("File contains suspicious content.");
            }
        }
    }

    /**
     * Detect suspicious patterns in input.
     */
    private function detectSuspiciousPatterns(Request $request): bool
    {
        $inputs = $request->all();
        $suspiciousPatterns = [
            '/<script[^>]*>.*?<\/script>/is',
            '/javascript:/i',
            '/vbscript:/i',
            '/onload\s*=/i',
            '/onerror\s*=/i',
            '/onclick\s*=/i',
            '/onmouseover\s*=/i',
            '/<iframe[^>]*>/i',
            '/<object[^>]*>/i',
            '/<embed[^>]*>/i',
            '/<link[^>]*>/i',
            '/<meta[^>]*>/i',
            '/<style[^>]*>.*?<\/style>/is',
            '/expression\s*\(/i',
            '/url\s*\(/i',
            '/@import/i',
            '/eval\s*\(/i',
            '/exec\s*\(/i',
            '/system\s*\(/i',
            '/shell_exec\s*\(/i',
            '/passthru\s*\(/i',
            '/file_get_contents\s*\(/i',
            '/fopen\s*\(/i',
            '/fwrite\s*\(/i',
            '/fputs\s*\(/i',
            '/mysql_query\s*\(/i',
            '/mysqli_query\s*\(/i',
            '/pg_query\s*\(/i',
            '/sqlite_query\s*\(/i',
            '/SELECT\s+.*\s+FROM/i',
            '/INSERT\s+INTO/i',
            '/UPDATE\s+.*\s+SET/i',
            '/DELETE\s+FROM/i',
            '/DROP\s+TABLE/i',
            '/UNION\s+SELECT/i',
            '/OR\s+1\s*=\s*1/i',
            '/AND\s+1\s*=\s*1/i',
            '/\'\s*OR\s*\'\s*=\s*\'/i',
            '/\'\s*AND\s*\'\s*=\s*\'/i'
        ];

        foreach ($inputs as $key => $value) {
            if (is_string($value)) {
                foreach ($suspiciousPatterns as $pattern) {
                    if (preg_match($pattern, $value)) {
                        return true;
                    }
                }
            }
        }

        return false;
    }

    /**
     * Handle suspicious activity.
     */
    private function handleSuspiciousActivity(Request $request): Response
    {
        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Suspicious activity detected. Request blocked.',
                'error' => 'suspicious_activity'
            ], 400);
        }

        return redirect()->back()
            ->withErrors(['error' => 'Suspicious activity detected. Please check your input and try again.']);
    }

    /**
     * Log suspicious activity.
     */
    private function logSuspiciousActivity(Request $request): void
    {
        Log::warning('Suspicious Activity Detected', [
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'url' => $request->fullUrl(),
            'method' => $request->method(),
            'user_id' => auth()->id(),
            'inputs' => $request->except(['_token', 'password', 'password_confirmation']),
            'timestamp' => now()
        ]);
    }
}
