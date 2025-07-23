<?php

namespace App\View\Components;

use App\Services\ImportService;
use Illuminate\View\Component;
use Illuminate\View\View;

/**
 * Component that renders a dropdown with available import types.
 *
 * @class ImportDataDropdown
 */
class ImportDataDropdown extends Component
{
    /**
     * List of available import types.
     *
     * @var array
     */
    public array $availableImportTypes;

    /**
     * Create a new component instance.
     *
     * @param ImportService $importService
     */
    public function __construct(ImportService $importService)
    {
        $this->availableImportTypes = $importService->getImportTypes(false);
    }

    /**
     * Render the component view.
     *
     * @return View
     */
    public function render(): View
    {
        return view('components.import-data-dropdown');
    }
}
