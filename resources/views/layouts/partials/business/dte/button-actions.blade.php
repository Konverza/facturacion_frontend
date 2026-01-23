<div class="mt-4 flex flex-col items-center justify-center gap-4 px-4 sm:flex-row">
    <x-button type="submit" typeButton="primary" icon="file-symlink" text="Generar documento" class="w-full sm:w-auto {{(($dte['status'] ?? null) === 'template') ? 'hidden' : ''}}"
        name="action" value="generate" id="generate-button" />
    <x-button type="submit" name="action" value="draft"
        text="{{ isset($dte['id']) ? 'Actualizar' : 'Guardar como' }} borrador" typeButton="secondary"
    class="w-full sm:w-auto {{(($dte['status'] ?? null) === 'template') ? 'hidden' : ''}}" icon="save" id="draft-button" />
    @if(($show_bolson_button ?? false) === true)
        <x-button type="submit" name="action" value="bag"
            text="Añadir al bolsón" typeButton="warning" icon="files" id="bag-button"
            class="w-full sm:w-auto {{(($dte['status'] ?? null) === 'template') ? 'hidden' : ''}}"
            formaction="{{ route('business.invoice-bags.store-from-dte') }}" />
    @endif
    <x-button type="submit" name="action" value="template"
        text="{{ isset($dte['id']) ? 'Actualizar' : 'Guardar' }} plantilla" typeButton="success"
    class="w-full sm:w-auto {{(($dte['status'] ?? null) === 'template') ? '' : 'hidden'}}" icon="files" id="template-button" />
</div>
