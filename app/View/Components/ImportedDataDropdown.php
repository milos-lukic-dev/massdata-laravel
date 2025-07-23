<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

/**
 * Component that renders a dropdown menu for imported data types and files.
 *
 * @class ImportedDataDropdown
 */
class ImportedDataDropdown extends Component
{
    /**
     * All import data configuration loaded from config files.
     *
     * @var array
     */
    public array $allImportData;

    /**
     * Flag indicating if the imported data dropdown should be open.
     *
     * @var bool
     */
    public bool $isImportedDataOpen;

    /**
     * Array of links with flags indicating if each is active.
     *
     * @var array
     */
    public array $importedDataLinks;

    /**
     * Create a new component instance.
     *
     * Initializes import data, active state, and generates the links array.
     */
    public function __construct()
    {
        $this->allImportData = config('imports');
        $this->isImportedDataOpen = request()->routeIs('imported-data.show');
        $this->importedDataLinks = $this->generateLinks();
    }

    /**
     * Generate an array of links with their active status.
     *
     * @return array<string, array<string, bool>>
     */
    protected function generateLinks(): array
    {
        $links = [];

        if (!empty($this->allImportData)) {
            foreach ($this->allImportData as $importType => $typeData) {
                foreach ($typeData['files'] ?? [] as $fileKey => $fileConfig) {
                    $links[$importType][$fileKey] = $this->isActiveRoute($importType, $fileKey);
                }
            }
        }

        return $links;
    }

    /**
     * Check if the given importType and fileKey matches the current route.
     *
     * @param string $importType
     * @param string $fileKey
     * @return bool True if this link is active.
     */
    protected function isActiveRoute(string $importType, string $fileKey): bool
    {
        return request()->routeIs('imported-data.show') &&
            request()->route('importType') === $importType &&
            request()->route('fileKey') === $fileKey;
    }

    /**
     * Get the view that represents the component.
     *
     * @return View
     */
    public function render(): View
    {
        return view('components.imported-data-dropdown');
    }
}
