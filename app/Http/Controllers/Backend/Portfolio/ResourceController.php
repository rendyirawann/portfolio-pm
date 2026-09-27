<?php

namespace App\Http\Controllers\Backend\Portfolio;

use App\Http\Controllers\Controller;
use App\Support\ActivityRecorder;
use App\Support\Portfolio\Media;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * One CRUD implementation for every simple, repeatable portfolio block
 * (services, skills, stats, social links…). A subclass only describes its
 * fields and list columns; the forms and table are rendered from that, so
 * every menu in the admin looks and behaves the same.
 *
 * Field definition: name => [
 *     'label' => string, 'type' => text|textarea|number|url|image|toggle|select|icon,
 *     'rules' => array, 'options' => array (select), 'help' => string, 'col' => 6|12,
 * ]
 */
abstract class ResourceController extends Controller
{
    /** @var class-string<Model> */
    protected string $model;

    /** Route name prefix, e.g. "pf.services". */
    protected string $route;

    protected string $title;

    protected string $singular;

    protected string $icon = 'ki-element-11';

    protected string $description = '';

    /** Column searched by the list's search box. */
    protected string $searchColumn = 'id';

    /** Section of the website shown in the live preview panel (e.g. "#services"). */
    protected ?string $previewTarget = null;

    /** Step-by-step "Panduan" shown on the list and form pages. */
    protected array $guide = [];

    abstract protected function fields(): array;

    /** @return array<string,string> column => heading */
    abstract protected function columns(): array;

    public function index(Request $request): View
    {
        $search = trim((string) $request->query('q', ''));

        $items = $this->model::query()
            ->when($search !== '', fn ($q) => $q->where($this->searchColumn, 'ilike', '%' . addcslashes($search, '%_\\') . '%'))
            ->orderBy('sort_order')->orderBy('id')
            ->paginate(20)
            ->withQueryString();

        return view('backend.portfolio.resource.index', $this->viewData() + [
            'items' => $items,
            'columns' => $this->columns(),
            'search' => $search,
        ]);
    }

    public function create(): View
    {
        $item = new $this->model(['is_active' => true, 'sort_order' => ($this->model::max('sort_order') ?? 0) + 1]);

        return view('backend.portfolio.resource.form', $this->viewData() + ['item' => $item]);
    }

    public function store(Request $request): RedirectResponse
    {
        $item = new $this->model();
        $item->fill($this->payload($request, $item))->save();

        ActivityRecorder::created('portfolio', "Menambah {$this->singular}", $item->toArray(), $item);

        return redirect()->route("{$this->route}.index")->with('success', "{$this->singular} berhasil ditambahkan.");
    }

    public function show(string $id): RedirectResponse
    {
        return redirect()->route("{$this->route}.edit", $id);
    }

    public function edit(string $id): View
    {
        return view('backend.portfolio.resource.form', $this->viewData() + [
            'item' => $this->model::findOrFail($id),
        ]);
    }

    public function update(Request $request, string $id): RedirectResponse
    {
        $item = $this->model::findOrFail($id);
        $before = $item->toArray();

        $item->fill($this->payload($request, $item))->save();

        ActivityRecorder::updated('portfolio', "Mengubah {$this->singular}", $before, $item->fresh()->toArray(), $item);

        return redirect()->route("{$this->route}.index")->with('success', "{$this->singular} berhasil diperbarui.");
    }

    public function destroy(string $id): RedirectResponse
    {
        $item = $this->model::findOrFail($id);
        $before = $item->toArray();

        foreach ($this->fields() as $name => $field) {
            if ($field['type'] === 'image') {
                Media::delete($item->{$name});
            }
        }

        $item->delete();

        ActivityRecorder::deleted('portfolio', "Menghapus {$this->singular}", $before);

        return redirect()->route("{$this->route}.index")->with('success', "{$this->singular} berhasil dihapus.");
    }

    // ------------------------------------------------------------------

    protected function viewData(): array
    {
        return [
            'route' => $this->route,
            'title' => $this->title,
            'singular' => $this->singular,
            'icon' => $this->icon,
            'description' => $this->description,
            'fields' => $this->fields(),
            'previewTarget' => $this->previewTarget,
            'guide' => $this->guide,
            // Preview keys look like "services.5.title" — the resource's URI segment.
            'previewKey' => substr($this->route, strrpos($this->route, '.') + 1),
        ];
    }

    /** Validate, then turn the request into model attributes. */
    protected function payload(Request $request, Model $item): array
    {
        $fields = $this->fields();
        $rules = [];

        foreach ($fields as $name => $field) {
            $rules[$name] = match ($field['type']) {
                'image' => ['nullable', 'image', 'mimes:' . Media::IMAGE_MIMES, 'max:' . Media::MAX_KB],
                'toggle' => ['nullable', 'boolean'],
                default => $field['rules'] ?? ['nullable'],
            };
        }
        $rules['sort_order'] = ['nullable', 'integer', 'min:0', 'max:65000'];

        $validated = $request->validate($rules);
        $data = ['sort_order' => (int) ($validated['sort_order'] ?? 0)];

        foreach ($fields as $name => $field) {
            if ($field['type'] === 'toggle') {
                $data[$name] = $request->boolean($name);
            } elseif ($field['type'] === 'image') {
                if ($request->hasFile($name)) {
                    Media::delete($item->{$name});
                    $data[$name] = Media::storeImage($request->file($name), $this->route, 800, null)['path'];
                } elseif ($request->boolean("remove_{$name}")) {
                    Media::delete($item->{$name});
                    $data[$name] = null;
                }
            } else {
                $data[$name] = $validated[$name] ?? null;
            }
        }

        return $data;
    }
}
