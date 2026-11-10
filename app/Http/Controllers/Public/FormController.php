<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;

/**
 * Public printable forms (Fomu).
 *
 * Every form's content lives in resources/lang/{locale}/forms.php so the same
 * view renders Kiswahili (default) and English through the language switch.
 */
class FormController extends Controller
{
    /**
     * Ordered list of printable forms: route key => sort order.
     */
    public const FORMS = [
        'tracking-simu' => 1,
        'pasipoti' => 2,
    ];

    public function index(): View
    {
        return view('forms.index', [
            'cards' => $this->collection(),
        ]);
    }

    public function show(string $form): View
    {
        abort_unless(isset(self::FORMS[$form]), 404);

        return view('forms.show', [
            'form' => $form,
            'content' => $this->translate($form),
        ]);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    protected function collection(): array
    {
        $cards = [];

        foreach (self::FORMS as $key => $order) {
            $content = $this->translate($key);

            $cards[] = [
                'key' => $key,
                'url' => route('public.forms.show', $key),
                'sort' => $order,
                'title' => $content['card']['title'] ?? $key,
                'summary' => $content['card']['summary'] ?? '',
                'meta' => $content['card']['meta'] ?? [],
            ];
        }

        usort($cards, fn (array $a, array $b) => $a['sort'] <=> $b['sort']);

        return $cards;
    }

    /**
     * Kiswahili is the source of truth. Any key a translation is still
     * missing falls back to the Kiswahili text instead of printing a raw key.
     */
    protected function translate(string $key): array
    {
        $locale = app()->getLocale();
        $content = __("forms.{$key}", [], $locale);

        if ($locale === 'sw' || ! is_array($content)) {
            return $content;
        }

        return $this->fill($content, __("forms.{$key}", [], 'sw'));
    }

    protected function fill(array $content, array $fallback): array
    {
        foreach ($fallback as $key => $value) {
            if (! array_key_exists($key, $content)) {
                $content[$key] = $value;
            } elseif (is_array($value) && is_array($content[$key])) {
                $content[$key] = $this->fill($content[$key], $value);
            }
        }

        return $content;
    }
}
